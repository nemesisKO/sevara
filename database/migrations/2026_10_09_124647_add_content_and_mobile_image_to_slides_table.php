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
        Schema::table('slides', function (Blueprint $table) {
            $table->string('mobile_image_path')->nullable()->after('image_path');
            $table->string('title')->nullable()->after('link');
            $table->text('description')->nullable()->after('title');
            $table->json('title_style')->nullable()->after('description');
            $table->json('description_style')->nullable()->after('title_style');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn([
                'mobile_image_path',
                'title',
                'description',
                'title_style',
                'description_style',
            ]);
        });
    }
};
