<?php


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();

            $table->string('label');
            $table->string('path');
            $table->string('url');
            $table->foreignId('uploader_id')->constrained('users')->nullOnDelete();

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    public function down()
    {
        Schema::dropIfExists('medias');
    }
};
