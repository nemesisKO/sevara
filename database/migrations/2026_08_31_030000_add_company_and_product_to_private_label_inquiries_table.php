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
        Schema::table('private_label_inquiries', function (Blueprint $table): void {
            $table->string('company_name')->nullable()->after('full_name');
            $table->string('country')->nullable()->change();
            $table->string('product_format')->nullable()->change();
            $table->string('product_name')->nullable()->after('product_format');
            $table->string('estimated_quantity')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_label_inquiries', function (Blueprint $table): void {
            $table->dropColumn(['company_name', 'product_name']);
            $table->string('country')->nullable(false)->change();
            $table->string('product_format')->nullable(false)->change();
            $table->string('estimated_quantity')->nullable(false)->change();
        });
    }
};
