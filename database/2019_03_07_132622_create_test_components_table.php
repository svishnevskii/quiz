<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_components', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('template_id')->unsigned();
            $table->integer('method_id')->unsigned()->nullable();
            $table->integer('lang_id')->unsigned();
            $table->integer('count')->unsigned()->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('test_components');
    }
}
