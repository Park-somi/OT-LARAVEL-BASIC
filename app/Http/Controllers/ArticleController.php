<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Article;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ArticlesExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EditArticleRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\CreateArticleRequest;
use App\Http\Requests\DeleteArticleRequest;
use App\Http\Requests\updateArticleRequest;

/**
 * @brief 게시글을 위한 Controller
 * @detail 게시글 목록 조회, 상세 조회, 작성, 수정, 삭제
 * @author Parksomi
 * @date 2024-12-12
 * @version 1.0.0
 */
class ArticleController extends Controller
{
    /**
     * @brief Article 클래스의 생성자
     * @details index와 show 메소드를 제외하고 인증된 사용자만 접근할 수 있도록 제한
     */
    public function __construct(){
        $this->middleware('auth')->except('index', 'show');
    }

    /**
     * @brief 게시글 작성 페이지로 이동하는 메소드
     * @details 글쓰기 화면을 출력
     */
    public function create() {
        return view('articles/create');
    }

    /**
     * @brief 게시글을 저장하는 메소드
     * @details 로그인한 사용자의 경우, 유효성 검사 후 글 저장
     */
    public function store(CreateArticleRequest $request){    
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
        $input = $request->validated();

        $article = Article::create([
            'title' => $input['title'],
            'body' => $input['body'], // 입력된 'body' 값 저장
            'user_id' => Auth::id(), // 현재 사용자 ID 저장
        ]);

        // 파일 저장
        if ($request->hasFile('file')) {
            $files = $request->file('file');

            foreach ($files as $file) {
                $filePath = 'file/';
                $storagePath = public_path($filePath); // 전체 경로
                $fileName = $file->getClientOriginalName();
                $file->move($storagePath, $fileName);
                $fullPath = $filePath.$fileName;

                File::create([
                    'article_id' => $article->id,
                    'file_name' => $fileName,
                    'file_path' => $fullPath,
                ]);
            }
        }
    
        return redirect()->route('articles.index');
    }

    /**
     * @brief 게시글 목록을 조회하는 메소드
     * @details 모든 사용자의 게시글 목록 조회
     */
    public function index(Request $request){
        $q = $request->input('q');
        $type = $request->input('type', '제목+내용');
        $sort = $request->input('sort', 'newest');

        $articles = Article::with('user') // Eloquent 관계
        ->withCount('comments') // 댓글 수 표시
        ->withExists(['comments as recent_comments_exists' => function($query){ // 24시간이 안지난 댓글이 존재하는지
            $query->where('created_at', '>', Carbon::now()->subDay());
        }])
        // ->when($q, function($query, $q){
        //     $query->where('title', 'like', "%$q%") // 제목 검색
        //     ->orWhere('body', 'like', "%$q%") // 내용 검색
        //     ->orWhereHas('user', function(Builder $query) use ($q) {
        //         $query->where('username', 'like', "%$q%");
        //     });
        // })
        ->when($q, function($query, $q) use ($type){
            switch ($type){
                case '제목':
                    $query->where('title', 'like', "%$q%");
                case '제목+내용':
                    $query->where(function($queryBuilder) use ($q){
                        $queryBuilder->where('title', 'like', "%$q%")
                                    ->orwhere('body', 'like', "%$q%");
                    });              
                case '작성자':
                    $query->orWhereHas('user', function(Builder $queryBuilder) use ($q) {
                        $queryBuilder->where('username', 'like', "%$q%");
                    });
                    break;
            }
        })
        ->when($sort === 'newest', function ($query) {
            $query->latest(); // 최신순 정렬
        })
        ->when($sort === 'oldest', function ($query) {
            $query->oldest(); // 오래된순 정렬
        })
        ->when($sort === 'comments', function ($query) {
            $query->orderBy('comments_count', 'desc'); // 댓글 수 순 정렬
        })
        ->latest()
        ->paginate(5);

        // 페이지네이션 데이터에 "최근 글" 여부 추가
        foreach ($articles as $article) {
            $article->is_recent = $article->created_at > Carbon::now()->subDay();
        }

        $articles->appends(['q' => $q, 'sort' => $sort]);

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
                'articles' => $articles, // 뷰에 'articles' 데이터 전달
                // 'results' => $results,
                // 'totalCount' => $totalCount,
                // 'page' => $page,
                // 'perPage' => $perPage
                'q' => $q,
                'sort' => $sort // 현재 정렬 기준 전달
            ]);
    }

    /**
     * @brief 게시글 상세조회 메소드
     * @details 댓글 작성이 가능한 게시글 상세조회 페이지
     */
    public function show(Article $article){
        $article->load('comments.user');
        $article->loadCount('comments');
        $article->loadCount('comments as recent_comments_exists');
        
        // "최근 글" 여부를 추가
        $article->is_recent = $article->created_at > Carbon::now()->subDay();

        // 해당 article ID를 가진 파일들을 가져옴
        $files = File::where('article_id', $article->id)->get();

        return view('articles.show', ['article' => $article, 'files' => $files]);
    }

    /**
     * @brief 게시글 수정 페이지로 이동하는 메소드
     * @details 글수정 화면을 출력
     */
    public function edit(EditArticleRequest $request, Article $article){
        // $this->authorize('update', $article);

        $files = File::where('article_id', $article->id)->get();

        return view('articles.edit', ['article' => $article, 'files' => $files]);
    }

    /**
     * @brief 게시글을 수정하는 메소드
     * @details 기존 게시글의 정보를 불러오고 수정 버튼 클릭 시, 유효성 검사 후 수정
     */
    public function update(updateArticleRequest $request, Article $article){
        // 권한설정(1) - 모델
        // 권한 실패 시, 직접 응답을 정의해줘야 함
        // if(!Auth::user()->can('update', $article)){
        //     abort(403);
        // };

        // 권한설정(2) - 컨트롤러 헬퍼
        // 권한 실패 시, 자동으로 응답까지 만들어서 내보내줌
        // $this->authorize('update', $article);

        // 유효성 검사가 완료된 요청 가져오기
        $input = $request->validated();

        // 삭제할 파일 처리
        if ($request->has('files_to_delete')){ // 삭제할 파일이 있을 경우
            $filesToDelete = $request->input('files_to_delete'); // 삭제할 파일 ID
            File::whereIn('id', $filesToDelete)->each(function($file){ // 삭제할 파일 ID를 가진 파일을 찾아서 삭제
                // 실제 파일 삭제
                if(file_exists(public_path($file->file_path))){
                    unlink(public_path($file->file_path));
                }
                // 데이터베이스 삭제
                $file->delete();
            });
        }
        
        // 새로운 파일 저장
        if ($request->hasFile('new_files')){ // 새로운 파일이 있을 경우
            foreach ($request->file('new_files') as $file) { 
                $filePath = 'file/';
                $storagePath = public_path($filePath); // 전체 경로
                $fileName = $file->getClientOriginalName();
                $file->move($storagePath, $fileName);
                $fullPath = $filePath.$fileName;

                File::create([
                    'article_id' => $article->id,
                    'file_name' => $fileName,
                    'file_path' => $fullPath,
                ]);
            }
        }

        $article->title = $input['title'];
        $article->body = $input['body'];
        $article->save();

        return redirect()->route('articles.index');
    }

    /**
     * @brief 게시글을 삭제할 수 있는 메소드
     * @details 삭제버튼 클릭 시, 삭제 후 글목록 페이지로 이동
     */
    public function destroy(DeleteArticleRequest $request, Article $article){
        $article->delete();
        $article->comments()->delete();
        $article->files()->delete();

        return redirect()->route('articles.index');
    }

    /**
     * @brief 파일 다운로드 메소드
     * @details 파일 다운로드
     */
    public function download(File $file)
    {
        $filePath = public_path($file->file_path);

        return response()->download($filePath);
    }

    /**
     * @brief 엑셀 다운로드 메소드
     * @details 엑셀 파일 다운로드
     */
    public function excel()
    {
        $headings = [
            'ID',
            'Title',
            'Body',
            'User ID',
            'Created At',
            'Updated At',
        ];
        // 사용할 Models::class, $headings, '파일명' 순으로 넣어줌
        return Excel::download(new ExcelExport(Article::class, $headings), 'articles.xlsx');
    }

}