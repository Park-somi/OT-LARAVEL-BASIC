<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'body', 'user_id']; // body와 user_id만 허용(화이트리스트방식)
    // protected $guarded = ['id';] // id빼고는 다 허용하겠음(블랙리스트방식)

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class); // user와의 관계 설정
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

}
