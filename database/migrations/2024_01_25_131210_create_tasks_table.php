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
            $table->bigIncrements('id');
            $table->string('title', 255);
            $table->text('description');
            $table->date('task_deadline_date');
            $table->unsignedBigInteger('task_creator_user_id');
            $table->unsignedBigInteger('assigned_user_id');
            $table->unsignedBigInteger('assigned_tester_user_id');
            $table->unsignedBigInteger('task_type_id');
            $table->unsignedBigInteger('task_status_id');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('task_creator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_tester_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_type_id')->references('id')->on('task_types')->onDelete('cascade');
            $table->foreign('task_status_id')->references('id')->on('task_statuses')->onDelete('cascade');

            $table->index(['task_creator_user_id', 'assigned_user_id', 'assigned_tester_user_id', 'task_type_id', 'task_status_id'], 'task_main_index');

            $table->index(['title', 'task_deadline_date'], 'task_title_deadline_index');

            $table->index(['deleted_at', 'created_at'], 'task_deletion_creation_index');
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
