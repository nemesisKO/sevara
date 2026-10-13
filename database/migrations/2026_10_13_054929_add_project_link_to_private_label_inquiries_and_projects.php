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
            $table->string('project_access_token', 64)->nullable()->unique();
        });

        Schema::table('private_label_projects', function (Blueprint $table): void {
            $table->foreignId('private_label_inquiry_id')->nullable()->unique()
                ->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_label_projects', function (Blueprint $table): void {
            $table->dropUnique(['private_label_inquiry_id']);
            $table->dropConstrainedForeignId('private_label_inquiry_id');
        });

        Schema::table('private_label_inquiries', function (Blueprint $table): void {
            $table->dropUnique(['project_access_token']);
            $table->dropColumn('project_access_token');
        });
    }
};
