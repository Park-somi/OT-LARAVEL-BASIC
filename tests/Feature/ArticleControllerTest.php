<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase; // 테스트 실행할 때마다 데이터 삭제 -> 데이터는 남지 않음
    
    /**
     * @test
    */ 
    public function 글쓰기_화면을_볼_수_있다(): void
    {
        $response = $this->get(route('articles.create'))
        ->assertStatus(200)
        ->assertSee('글쓰기');
    }

    /**
     * @test
    */ 
    public function 글을_작성할_수_있다(): void{
        
        $testData = [
            'body' => 'test article'
        ];

        $user = User::factory()->create(); // 테스트를 위해 임의의 데이터를 넣어서 만들어줌
        // database/factories/UserFactory.php 토대로 생성

        $this->actingAs($user) // 로그인된 유저 상태로 요청을 보낼 수 있음
        ->post(route('articles.store'), $testData)
        ->assertRedirect(route('articles.index')); // response-응답이 주어진 URI로 리다이렉트되는지 여부를 확인
            
        $this->assertDatabaseHas('articles', $testData); // 데이터베이스에 데이터가 잘 들어갔는지
    }

    /**
     * @test
    */ 
    public function 글_목록을_확인할_수_있다(): void{
        // 같은 시간에 만들어져서 정렬이 안되므로 두 시간 객체 생성해줘서 생성 시간을 다르게 넣어줌
        $now = Carbon::now();
        $afterOneSecond = (clone $now)->addSecond();

        $article1 = Article::factory()->create(['created_at' => $now]);
        $article2 = Article::factory()->create(['created_at' => $afterOneSecond]);

        $this->get(route('articles.index'))
        ->assertSeeInOrder([ // 최신순 정렬(article2->article1이므로 최신순)
            $article2->body,
            $article1->body
        ]);
    }
    
    /**
     * @test
    */ 
    public function 개별_글을_조회할_수_있다(): void{
        $article = Article::factory()->create();

        $this->get(route('articles.show', ['article' => $article->id]))
        ->assertSuccessful()
        ->assertSee($article->body);
    }
    
    /**
     * @test
    */ 
    public function 글수정_화면을_볼_수_있다(): void{
        $article = Article::factory()->create();

        $response = $this->get(route('articles.edit', ['article' => $article->id]))
        ->assertStatus(200)
        ->assertSee('글수정');
    }

    /**
     * @test
    */     
    public function 글을_수정할_수_있다(): void{
        $payload = ['body' => '수정된 글'];
        $article = Article::factory()->create();

        $this->patch(route('articles.update', ['article' => $article->id]), $payload)
        ->assertRedirect(route('articles.index'));
        
        // 데이터베이스 확인 (1) - 특정 조건에 맞는 레코드가 데이터베이스에 존재하는지 확인
        $this->assertDatabaseHas('articles', $payload);

        // 데이터베이스 확인 (2) - 두 값이 같은지 확인
        $this->assertEquals($payload['body'], $article->refresh()->body);
    }

    /**
     * @test
    */ 
    public function 글을_삭제할_수_있다(): void{
        $article = Article::factory()->create();

        $this->delete(route('articles.delete', ['article' => $article->id]))
        ->assertRedirect(route('articles.index'));

        // 특정 조건에 해당하는 데이터가 데이터베이스에 없는지 확인
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
