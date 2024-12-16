<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @brief Comment 모델을 위한 클래스이다.
 * @author Parksomi
 * @data 2024-12-12
 * @version 1.0.0
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['body', 'user_id', 'article_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
