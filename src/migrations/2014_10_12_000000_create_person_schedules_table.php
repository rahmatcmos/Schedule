<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonSchedulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('person_schedules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('person_id')->unsigned()->index();
			$table->string('name', 255);
			$table->string('status', 255);
			$table->date('on');
			$table->time('start');
			$table->time('end');
			$table->boolean('is_affect_workleave');
			$table->timestamps();
			$table->softDeletes();
			
			$table->index(['deleted_at', 'on']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('person_schedules');
	}

}
