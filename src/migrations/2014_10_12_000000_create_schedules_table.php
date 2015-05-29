<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('schedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('calendar_id')->unsigned()->index();
			$table->string('name', 255);
			$table->enum('status', ['presence_indoor', 'presence_outdoor', 'absence_workleave', 'absence_not_workleave']);
			$table->date('on');
			$table->time('start');
			$table->time('end');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'calendar_id', 'on', 'status']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('schedules');
	}

}
