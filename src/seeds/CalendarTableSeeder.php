<?php namespace ThunderID\Schedule\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Schedule\Models\Calendar;
use ThunderID\Person\Models\Person;
use ThunderID\Organisation\Models\Branch;
use ThunderID\Organisation\Models\Chart;
use \Faker\Factory, Illuminate\Support\Facades\DB;

class CalendarTableSeeder extends Seeder
{
	function run()
	{

		DB::table('calendars')->truncate();
		$faker 										= Factory::create();
		$total_persons  							= Person::count();
		$total_positions 	 						= Chart::count();
		$total_branches 	 						= Branch::get();
		$calendar 									= ['wib', 'wita', 'wit'];
		try
		{
			foreach(range(0, count($total_branches)-1) as $index)
			{
				foreach(range(1, count($total_branches[$index]->charts)) as $index2)
				{
					$data 								= new Calendar;
					$data->fill([
						'organisation_id'				=> 1,
						'name'							=> $faker->country,
					]);

					$chart 							= $total_branches[$index]->charts[$index2-1]->id;
					$person 						= rand(2,$total_persons);

					if (!$data->save())
					{
						print_r($data->getError());
						exit;
					}
					
					$data->Charts()->attach($chart);

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