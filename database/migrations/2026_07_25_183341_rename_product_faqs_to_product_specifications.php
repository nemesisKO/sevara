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
        Schema::rename('product_faqs', 'product_specifications');

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('faq_image_path', 'spec_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('product_specifications', 'product_faqs');

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('spec_image_path', 'faq_image_path');
        });
    }
};
