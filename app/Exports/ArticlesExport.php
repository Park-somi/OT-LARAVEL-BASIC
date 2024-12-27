<?php

namespace App\Exports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\WithStyles; // 스타일 적용을 위한 인터페이스
use Maatwebsite\Excel\Concerns\WithHeadings; // 헤더 추가를 위한 인터페이스
use Maatwebsite\Excel\Concerns\FromCollection; // 데이터 출력을 위한 인터페이스
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // 열 크기 자동 조정을 위한 인터페이스
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; // 스타일 작업을 위한 Worksheet 객체

class ArticlesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // 데이터 컬렉션 반환
    public function collection()
    {
        // Article 모델의 모든 데이터를 가져와 반환
        return Article::all();
    }

    // 헤더 추가
    public function headings(): array
    {
        // Excel 파일의 첫 번째 행에 표시될 헤더 설정
        return [
            'ID',
            'Title',
            'Body',
            'User ID',
            'Created At',
            'Updated At',
        ];
    }

    // 스타일 설정정
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // 글씨 굵기
        $sheet->getStyle('A1:F1')->getFont()->setSize(20); // 글씨 크기
        $sheet->getStyle('A1:F1')->getFill()->applyFromArray([ // 배경색
            'fillType' => 'solid', // 채우기 유형
            'rotation' => 0, // 회전 각도
            'color' => ['rgb' => 'FFFF00'], // 배경색
        ]);
    }
}
