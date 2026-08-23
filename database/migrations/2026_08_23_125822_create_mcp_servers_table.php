<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mcp_servers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('type', ['manual', 'discovered', 'registry'])->default('manual');
            $table->enum('transport', ['stdio', 'sse', 'streamable-http'])->default('stdio');
            $table->string('command')->nullable();
            $table->json('args')->nullable();
            $table->text('env')->nullable();
            $table->string('url')->nullable();
            $table->enum('status', ['connected', 'disconnected', 'error'])->default('disconnected');
            $table->json('capabilities')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mcp_servers');
    }
};
