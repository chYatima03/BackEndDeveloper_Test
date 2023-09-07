<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql')->create('beers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('type_beer_id');
            $table->foreign('type_beer_id')->references('id')->on('type_beers')->onDelete('cascade');
            $table->string('beer_name',50);
            $table->string('beer_image')->nullable();
            $table->string('beer_detail',300)->nullable();
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
        Schema::connection('mysql')->dropIfExists('beers');
    }
};
