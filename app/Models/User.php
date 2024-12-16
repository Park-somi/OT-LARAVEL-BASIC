<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @brief 사용자 모델을 위한 클래스이다.
 * @author Parksomi
 * @data 2024-12-12
 * @version 1.0.0
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // One to Many(Inverse)
    // public function articles(){
    //     return $this->hasMany(Article::class);
    // }

    public function getRouteKeyName()
    {
        return 'username';   
    }

    // article과의 관계 설정
    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }

    // 팔로워
    public function followers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // 팔로잉 
    public function followings() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    // 팔로워 중에 해당 유저가 존재하는지 확인
    public function isFollowing(User $user) : bool
    {
        return $this->followings()->where('user_id', $user->id)->exists();
    }
}
