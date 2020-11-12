<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('test_id')->unsigned();
            $table->integer('min_wieght')->unsigned()->nullable();
            $table->integer('max_wieght')->unsigned()->nullable();
            $table->boolean('is_right')->deafault(0);
            $table->text('text');
            $table->integer('lang_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('test_id')
                ->references('id')
                ->on('tests')
                ->onDelete('cascade')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_results');
    }
}
