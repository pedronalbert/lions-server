<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('events_resources', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('event_id');
        $table->integer('resource_id');
        $table->integer('amount');
        $table->integer('member_id')->nullable();
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
      Schema::drop('events_resources');
    }
}
