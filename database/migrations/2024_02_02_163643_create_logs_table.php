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
        Schema::create('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('action', 50)->index();
            $table->unsignedBigInteger('object_id')->index();
            $table->string('type', 50)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->index();
            $table->timestamps();

            $table->index(['action', 'object_id', 'type']);
            $table->index(['created_at', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
