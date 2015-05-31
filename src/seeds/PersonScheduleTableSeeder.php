<?php namespace ThunderID\Schedule\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Person\Models\Person;
use ThunderID\Schedule\Models\PersonSchedule;
use \Faker\Factory, Illuminate\Support\Facades\DB;

class PersonScheduleTableSeeder extends Seeder
{
	function run()
	{

		DB::table('person_schedules')->truncate();
		$faker 									= Factory::create();
		$total_persons  						= Person::count();
		$status 								= ['presence_indoor', 'presence_outdoor', 'absence_workleave', 'absence_not_workleave'];
		$schedule 								= ['shift pagi', 'shift malam', 'cuti tahunan', 'cuti melahirkan', 'hari jumat', 'minggu', 'lembur', 'cuti tahunan'];
		$start 									= ['08:00:00', '16:00:00', '00:00:00', '00:00:00', '08:00:00', '08:00:00', '20:00:00', '00:00:00'];
		$end 									= ['16:00:00', '00:00:00', '00:00:00', '00:00:00', '15:00:00', '10:00:00', '00:00:00', '00:00:00'];
		try
		{
			foreach(range(1, 500) as $index)
			{
				$rand 							= rand(0,3);
				$randay 						= rand(2,40);
				$data 							= new PersonSchedule;
				$data->fill([
					'on'						=> date('Y-m-d', strtotime('+ '.$randay.' days')),
					'name'						=> $schedule[$rand],
					'status'					=> $status[$rand],
					'start'						=> $start[$rand],
					'end'						=> $end[$rand],
				]);

				$person 						= Person::find(rand($randay+1, $total_persons));

				$data->Person()->associate($person);

				if (!$data->save())
				{
					// print_r($data->getError());
					// exit;
				}
			} 
		}
		catch (Exception $e) 
		{
    		echo 'Caught exception: ',  $e->getMessage(), "\n";
		}	
	}
}