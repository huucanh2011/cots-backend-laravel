<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateToursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tour_name');
            $table->string('image_cover');
            $table->text('description');
            $table->string('from_place');
            $table->string('to_place');
            $table->tinyInteger('number_days');
            $table->tinyInteger('number_persons');
            $table->double('tour_price', 15, 2)->unsigned();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('tourcate_id')->unsigned();
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
        Schema::dropIfExists('tours');
    }
}
