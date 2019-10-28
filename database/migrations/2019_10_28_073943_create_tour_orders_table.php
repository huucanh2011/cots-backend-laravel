<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTourOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tour_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('date_order')->nullable();
            $table->tinyInteger('quantity_people')->unsigned();
            $table->double('total', 15, 2)->unsigned();
            $table->text('note');
            $table->boolean('allow');
            $table->dateTime('date_allow')->nullable();
            $table->bigInteger('tour_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
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
        Schema::dropIfExists('tour_orders');
    }
}
