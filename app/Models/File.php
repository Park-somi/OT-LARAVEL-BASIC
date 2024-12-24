<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'file_name',
        'file_path',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
