<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::create('cms_logger', function (Blueprint $table) {
            $table->id();

            $table->enum('level', ['debug', 'info', 'success', 'warning', 'error']);
            $table->string('category', 50);
            $table->string('action');
            $table->longText('message');

            $table->json('context')->nullable();
            $table->nullableMorphs('subject');

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('url')->nullable();

            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['level', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_logger');
    }
};
