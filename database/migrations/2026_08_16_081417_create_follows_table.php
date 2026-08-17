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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // the user_id is the id of the user that is considered as follower
            $table->foreignId('following_user_id')->constrained('users')->cascadeOnDelete(); // the following_user_id is the id of the user the is followed
            $table->boolean('confirmed')->default(false);
            $table->unique(['user_id', 'following_user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
