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
            $table->string('primary_cta_text')->nullable()->after('link');
            $table->string('primary_cta_link', 2048)->nullable()->after('primary_cta_text');
            $table->string('secondary_cta_text')->nullable()->after('primary_cta_link');
            $table->string('secondary_cta_link', 2048)->nullable()->after('secondary_cta_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('slides', function (Blueprint $table) {
            $table->dropColumn([
                'primary_cta_text',
                'primary_cta_link',
                'secondary_cta_text',
                'secondary_cta_link',
            ]);
        });
    }
};
