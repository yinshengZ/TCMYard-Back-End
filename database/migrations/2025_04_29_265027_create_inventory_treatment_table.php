<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTreatmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_treatment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id');
            $table->foreignId('treatment_id');
            $table->foreignid('sku_id');
            //$table->unsignedBigInteger('units');


            $table->foreign('inventory_id')->references('id')->on('inventories');
            $table->foreign('treatment_id')->references('id')->on('treatments');
            $table->foreign('sku_id')->references('id')->on('inventory_skus');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_treatment');
    }
}
