<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('url');
            $table->longtext('text');
            $table->text('overview')->nullable();
            $table->text('image')->nullable();
            $table->timestamp('public_start')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('published')->default(1);
            $table->integer('lang_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
