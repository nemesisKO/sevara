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
        Schema::create('private_label_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('company_name');
            $table->string('country');
            $table->string('product_format');
            $table->string('estimated_quantity');
            $table->text('message')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_label_inquiries');
    }
};
