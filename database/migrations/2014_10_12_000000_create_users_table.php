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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->string('email', 191)->unique();
            $table->string('password', 60);
            $table->boolean('is_admin')->default(false)->index();
            $table->string('api_token', 80)->unique()->nullable()->default(null);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
