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
        Schema::create('private_label_projects', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name');
            $table->string('company_name');
            $table->string('email');
            $table->json('details');
            $table->json('files')->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_label_projects');
    }
};
