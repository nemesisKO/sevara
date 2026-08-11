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
        Schema::create('blog_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->unsignedInteger('order')->default(0);
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->string('image_path')->nullable();
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->json('table_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_blocks');
    }
};
