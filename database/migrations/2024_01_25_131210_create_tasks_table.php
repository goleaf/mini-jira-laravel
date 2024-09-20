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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('task_deadline_date');
            $table->foreignId('task_creator_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_tester_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('task_type_id')->constrained('task_types')->onDelete('cascade');
            $table->foreignId('task_status_id')->constrained('task_statuses')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();

            $table->index('task_creator_user_id');
            $table->index('assigned_user_id');
            $table->index('assigned_tester_user_id');
            
            $table->index('task_type_id');
            $table->index('task_status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
