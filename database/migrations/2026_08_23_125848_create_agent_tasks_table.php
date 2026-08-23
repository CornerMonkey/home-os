<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->nullable()->constrained('conversations')->nullOnDelete();
            $table->enum('type', ['mcp_call', 'coding_task', 'automation']);
            $table->string('target');
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_tasks');
    }
};
