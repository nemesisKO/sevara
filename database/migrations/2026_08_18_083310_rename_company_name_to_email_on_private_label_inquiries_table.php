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
            $table->renameColumn('company_name', 'email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_label_inquiries', function (Blueprint $table): void {
            $table->renameColumn('email', 'company_name');
        });
    }
};
