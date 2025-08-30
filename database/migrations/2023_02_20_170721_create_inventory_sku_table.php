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
        Schema::create('inventory_skus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            //$table->unsignedBigInteger('inventory_id');
            $table->foreignId('inventory_id');
            $table->decimal('unit_price', $precision = 9, $scale = 2);
            $table->date('stocking_date');
            $table->date('expiry_date');
            $table->float('units', 8, 2)->unsigned();
            $table->boolean('out_of_stock');

            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_sku');
    }
};
