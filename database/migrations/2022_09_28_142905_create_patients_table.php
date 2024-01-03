<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedBigInteger('gender_id')->nullable(); //gender table needs creating
            $table->string('postcode')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('marital_status_id')->nullable(); //marital_status table needs creating
            $table->string('occupation')->nullable();
            $table->boolean('hiv_status')->default(0)->nullable();
            $table->text('past_history')->nullable();
            $table->text('current_issue');
            $table->date('date_of_birth')->nullable();
            $table->date('date_joined')->nullable();
            $table->softDeletes($columns = 'deleted_at');
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
        Schema::dropIfExists('patients');
    }
}
