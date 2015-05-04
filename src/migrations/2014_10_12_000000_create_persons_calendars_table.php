<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsCalendarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('persons_calendars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('calendar_id')->unsigned()->index();
			$table->integer('person_id')->unsigned()->index();
			$table->datetime('start');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'start']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('persons_calendars');
	}

}
