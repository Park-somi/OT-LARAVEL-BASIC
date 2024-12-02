<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function create() {
        return view('articles/create');
    }

    public function store(Request $request){

        // 유효성 검사
        $input = $request->validate([
            'body' => [
                'required', // 필수값
                'string', // 문자열이어야 함
                'max:255' // 255자까지만 입력 가능
            ]
        ]);
    
        // $host = config('database.connections.mysql.host'); // config 파일에서 데이터베이스 호스트 가져오기
        // $dbname = config('database.connections.mysql.database'); // 데이터베이스 이름 가져오기
        // $username = config('database.connections.mysql.username'); // 데이터베이스 사용자 이름 가져오기
        // $password = config('database.connections.mysql.password'); // 데이터베이스 비밀번호 가져오기
     
        // 1. PDO를 이용한 데이터베이스 연결 및 쿼리 실행
        // PDO : PHP의 기본 데이터베이스 연결 객체
        // $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
        // 쿼리 준비
        // $stmt = $conn->prepare("INSERT INTO articles (body, user_id) VALUES (:body, :userId)");
    
        // 요청에서 'body' 값 가져오기
        // $body = $request->input('body');
        
        // 쿼리 바인딩을 통해 'body' 값을 설정
        // $stmt->bindValue(':body', $input['body']);
        // 쿼리 바인딩을 통해 'user_id' 값을 설정 (현재 사용자 ID)
        // $stmt->bindValue(':userId', Auth::id());
        
        // 실행
        // $stmt->execute();
    
        // 2. DB 파사드를 이용한 쿼리 실행
        // DB::statement("INSERT INTO articles (body, user_id) VALUES (:body, :userId)",[
        //     'body' => $input['body'],
        //     'userId' => Auth::id()
        // ]);
        
        // 3. 쿼리 빌더를 이용한 데이터 삽입
        // DB::table('articles')->insert([
        //     'body' => $input['body'],
        //     'user_id' => Auth::id()
        // ]);
    
        // 4. Eloquent ORM을 사용한 데이터 삽입(1)
        // $article = new Article; // Article 모델의 새 인스턴스를 생성
        // $article->body = $input['body']; // 'body' 필드에 값 설정
        // $article->user_id = Auth::id(); // 'user_id' 필드에 현재 사용자 ID 설정
        // $article->save(); // 저장
    
        // // Eloquent ORM을 이용한 데이터 삽입(2)
        Article::create([
            'body' => $input['body'], // 입력된 'body' 값 저장
            'user_id' => Auth::id() // 현재 사용자 ID 저장
        ]);
    
        return redirect()->route('articles.index');
    }

    public function index(){
        $articles = Article::with('user')
    ->latest()
    ->paginate();

    // $articles->load('user'); // Eager Loading 두 번째 방법 : load()

    // $now = Carbon::now(); // Carbon 라이브러리를 통해 현재 시간을 가져옴
    // $past = clone $now; // 현재 시간 객체를 복사하여 'past'라는 객체에 저장
    // $past->subHours(3); // 'past'객체에서 3시간을 뺌

    // dd($now->diff($past)); // 'now'와 'past' 객체의 시간 차이 출력
    // dd($now->diffInHours($past)); // 시간 차이를 시간 단위로 출력
    // dd($now->diffInMinutes($past)); // 시간 차이를 분 단위로 출력

    // 수동으로 페이지네이션 설정 방법
    // ->skip($skip) // 페이지네이션에서 건너뛸 항목 수
    // ->take($perPage) // 한 페이지에서 보여줄 항목 수
    // ->get(); // 결과를 컬렉션으로 가져옴

    // $articles->withQueryString(); // 링크에 문자열을 붙여줌
    // $articles->appends(['filter' => 'name']); // 기존 문자열 이외의 문자열 추가해줌

    // $totalCount = Article::count(); // 전체 Article 개수

    // dd(Carbon::now()); // 현재 시간
    // dd(Carbon::now()->addHour()); // 현재 시간 + 1시간
    // dd(Carbon::now()->addHour(1)->addMinutes(10)); // 현재 시간 + 1시간 10분
    // dd(Carbon::now()->subHour(1)->addMinutes(10)); // 현재 시간 - 1시간 10분
    
    // $results = DB::table('articles as a') // DB 쿼리빌더를 사용해서 'articles' 테이블의 데이터 가져오기
    // ->join('users as u', 'a.user_id', '=', 'u.id') // 'articles' 테이블과 'users' 테이블을 조인
    // ->select(['a.*'], 'u.name') // 'articles'의 모든 필드와 'users' 테이블의 'name' 필드를 선택
    // ->latest()
    // ->paginate();

    // 1. 'view'로의 데이터 전달 방식 : 배열
    return view(
        'articles.index', 
        [
            'articles' => $articles // 뷰에 'articles' 데이터 전달
            // 'results' => $results
            // 'totalCount' => $totalCount,
            // 'page' => $page,
            // 'perPage' => $perPage
        ]);
    }

    public function show(Article $article){
        return view('articles.show', ['article' => $article]);
    }

    public function edit(Article $article){
        return view('articles.edit', ['article' => $article]);
    }

    public function update(Request $request, Article $article){
        // 유효성 검사
    $input = $request->validate([
        'body' => [
            'required', // 필수값
            'string', // 문자열이어야 함
            'max:255' // 255자까지만 입력 가능
        ]
    ]);

    $article->body = $input['body'];
    $article->save();

    return redirect()->route('articles.index');
    }

    public function destroy(Article $article){
        $article->delete();

        return redirect()->route('articles.index');
    }
}