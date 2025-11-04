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
            $table->foreignId('project_id')->constrained('projects', 'id')
                ->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['todo', 'progress', 'review', 'complete'])->default('todo');
            $table->enum('priority', ['urgent', 'high', 'medium', 'low'])->default('low');
            $table->foreignId('assigned_to')->nullable()
                ->constrained('users', 'id')
                ->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users', 'id')
                ->onDelete('cascade');
            $table->date('due_date')->nullable();
            $table->timestamps();
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
