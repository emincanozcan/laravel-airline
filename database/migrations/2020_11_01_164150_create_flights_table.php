<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('capacity');
            $table->unsignedBigInteger('departure_airport');
            $table->timestamp('departure_time');
            $table->unsignedBigInteger('arrival_airport');
            $table->timestamp('arrival_time');

            $table->foreign('departure_airport')->references('id')->on('airports');
            $table->foreign('arrival_airport')->references('id')->on('airports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
}
