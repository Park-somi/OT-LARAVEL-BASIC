<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    public function __construct(){
        $this->middleware('auth')->except('index');
    }

    /**
     * @brief 동영상 업로드 페이지로 이동하는 메소드
     * @details 동영상 업로드 화면을 출력
     */
    public function uploadFileView()
    {
        return view('videos/upload');
    }

    /**
     * @brief 동영상을 업로드하는 메소드
     * @details 동영상을 업로드하고 DB에 저장
     */
    public function uploadFile(Request $request)
    {
        $files = $request->file('file');

        foreach ($files as $file){
            $filePath = 'video/';
            $storagePath = public_path($filePath); // 전체 경로
    
            $fileName = $file->getClientOriginalName();
    
            $file->move($storagePath, $fileName);
    
            $fullPath = $filePath.$fileName;
    
            Video::create([
                'title' => $fileName,
                'file_path' => $fullPath,
                'user_id' => Auth::id()
            ]);
        }

        return redirect()->route('videos.index');
    }
    
    /**
     * @brief 동영상 목록을 출력하는 메소드
     * @details 동영상 목록을 출력
     */
    public function index()
    {
        $videos = Video::with('user')
        ->latest()
        ->paginate(5);

        foreach ($videos as $video){
            $video->is_recent = $video->created_at > Carbon::now()->subDay();
        }

        return view('videos.index', ['videos' => $videos]);
    }

    /**
     * @brief 동영상 상세 페이지로 이동하는 메소드
     * @details 동영상 상세 페이지를 출력
     */
    public function show(Video $video)
    {
        $videos = Video::all();

        $fullPath = $video->file_path;
            
        $video->is_recent = $video->created_at > Carbon::now()->subDay();

        return view('videos.show', ['videos' => $videos, 'video' => $video, 'full_path' => $fullPath]);
    }
}
