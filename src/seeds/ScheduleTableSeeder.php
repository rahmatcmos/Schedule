<?php namespace ThunderID\Schedule\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Schedule\Models\Calendar;
use ThunderID\Schedule\Models\Schedule;
use \Faker\Factory, Illuminate\Support\Facades\DB;

class ScheduleTableSeeder extends Seeder
{
	function run()
	{

		DB::table('schedules')->truncate();
		$faker 										= Factory::create();
		$total_cals  								= Calendar::count();
		$schedule 									= ['shift pagi', 'shift malam', 'jam normal', 'hari sabtu', 'hari jumat', 'minggu', 'lembur'];
		$start 										= ['08:00:00', '16:00:00', '08:00:00', '08:00:00', '08:00:00', '08:00:00', '20:00:00'];
		$end 										= ['16:00:00', '00:00:00', '16:00:00', '12:00:00', '15:00:00', '10:00:00', '00:00:00'];
		try
		{
			foreach(range(1, count($total_cals)) as $index)
			{
				foreach (range(1, rand(1,5)) as $value) 
				{
					$rand 							= rand(0,2);
					$data 							= new Schedule;
					$data->fill([
						'on'						=> date('Y-m-d'),
						'name'						=> $schedule[$rand],
						'start'						=> $start[$rand],
						'end'						=> $end[$rand],
					]);

					$calendar 						= Calendar::find($index);

					$data->Calendar()->associate($calendar);

					if (!$data->save())
					{
						print_r($data->getError());
						exit;
					}
				}
			} 
		}
		catch (Exception $e) 
		{
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
	}
}