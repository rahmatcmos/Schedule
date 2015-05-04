<?php namespace ThunderID\Schedule\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('\ThunderID\Schedule\seeds\CalendarTableSeeder');
		$this->call('\ThunderID\Schedule\seeds\ScheduleTableSeeder');
		$this->call('\ThunderID\Schedule\seeds\PersonScheduleTableSeeder');
	}

}
