<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // 현재 인증된 사용자가 요청이 실행할 권한이 존재하는지 검사
    public function authorize(): bool
    {
        // Article 출력
        // dd($this->route('article'));

        // User 출력
        // dd($this->user());

        // Article모델에 대한 update 권한이 유저한테 있는지 확인
        return $this->user()->can('update', $this->route('article'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    // 요청한 데이터에 적용해야 하는 유효성 검사 규칙을 반환
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:30'
            ],
            'body' => [
                'required', // 필수값
                'string', // 문자열이어야 함
                'max:255' // 255자까지만 입력 가능
            ]
        ];
    }
}
