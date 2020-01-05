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
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone_number');
            $table->string('customer_address');
            $table->tinyInteger('quantity_people')->unsigned();
            $table->double('total', 15, 2)->unsigned();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('date_active')->nullable();
            $table->bigInteger('date_departure_tour_id')->unsigned();
            $table->bigInteger('user_id')->unsigned()->nullable();
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
