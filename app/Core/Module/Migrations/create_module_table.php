<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->boolean('loaded')->default(false);
            $table->timestamp('loaded_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
