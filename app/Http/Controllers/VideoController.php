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

    public function uploadFileView()
    {
        return view('videos/upload');
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file('file');

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

        return redirect()->route('videos.index');
    }

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

    public function show(Video $video)
    {
        $videos = Video::with('user')->get();

        $fullPath = $video->file_path;
            
        $video->is_recent = $video->created_at > Carbon::now()->subDay();

        return view('videos.show', ['videos' => $videos, 'video' => $video, 'full_path' => $fullPath]);
    }
}
