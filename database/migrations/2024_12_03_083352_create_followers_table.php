<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index(); // 팔로우를 당하는 사람
            $table->foreignId('follower_id')->index(); // 팔로우를 하는 사람
            $table->timestamps();

            $table->unique(['user_id', 'follower_id']); // 인덱스 설정
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
