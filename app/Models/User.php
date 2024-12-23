<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'username',
        'postcode',
        'address',
        'detailAddress',
        'google2fa_secret'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google2fa_secret'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // google2faSecret 속성의 Getter와 Setter를 정의
    // 이유? google2faSecret는 중요한 정보(예: Google OTP 시크릿 키)를 저장하는 속성이므로 데이터베이스에 암호화된 상태로 저장
    // Getter: 모델에서 데이터를 읽을 때 동작. 저장된 값($value)을 복호화해서 반환
    // Setter: 모델에 데이터를 저장할 때 동작. 입력된 값($value)을 암호화해서 저장
    protected function google2faSecret(): Attribute
    {
        return new Attribute(
            get: fn($value) => $value ? decrypt($value) : null,
            set: fn($value) => $value ? encrypt($value) : null,
        );
    }

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

    // video와의 관계 설정
    public function videos() : HasMany
    {
        return $this->hasmany(Video::class);
    }
}
