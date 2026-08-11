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
        Schema::create('private_label_items', function (Blueprint $table) {
            $table->id();
            $table->string('section');
            $table->string('icon_path')->nullable();
            $table->string('title');
            $table->string('description');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['section', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_label_items');
    }
};
