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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('author')->nullable();
            $table->string('source_link')->nullable();
            $table->string('custom_date')->nullable();
            $table->string('read_duration')->nullable();
            $table->text('mini_review')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('image_path')->nullable();
            $table->json('references')->nullable();
            $table->json('faqs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
