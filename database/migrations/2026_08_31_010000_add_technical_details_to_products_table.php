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
        Schema::table('products', function (Blueprint $table): void {
            $table->string('net_piece_weight')->nullable()->after('description');
            $table->string('pieces_per_pack')->nullable()->after('net_piece_weight');
            $table->string('shelf_life')->nullable()->after('pieces_per_pack');
            $table->string('storage')->nullable()->after('shelf_life');
            $table->string('allergens')->nullable()->after('storage');
            $table->string('moq')->nullable()->after('allergens');
            $table->string('technical_data_sheet_path')->nullable()->after('moq');
            $table->string('pieces_per_pack_path')->nullable()->after('technical_data_sheet_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'net_piece_weight',
                'pieces_per_pack',
                'shelf_life',
                'storage',
                'allergens',
                'moq',
                'technical_data_sheet_path',
                'pieces_per_pack_path',
            ]);
        });
    }
};
