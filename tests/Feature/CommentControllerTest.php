<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase; // 테스트 실행할 때마다 데이터 삭제 -> 데이터는 남지 않음

    /**
     * @test
    */ 
    public function 댓글을_작성할_수_있다(): void{
        $user = User::factory()->create();

        $article = Article::factory()->create();

        $payload = ['article_id' => $article->id, 'body' => 'hello'];

        $this->actingAs($user)
        ->post(route('comments.store'), $payload)
        ->assertStatus(302)
        ->assertRedirectToRoute('articles.show', ['article' => $article->id]);

        $this->assertDatabaseHas('comments', $payload);
    }
}
