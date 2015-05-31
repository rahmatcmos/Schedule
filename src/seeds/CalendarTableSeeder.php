<?php namespace ThunderID\Schedule\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Schedule\Models\Calendar;
use ThunderID\Person\Models\Person;
use ThunderID\Organisation\Models\Organisation;
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
		$total_orgs 		 						= Organisation::count();
		$total_positions 	 						= Chart::count();
		$total_branches 	 						= Branch::get();
		$calendar 									= ['wib', 'wita', 'wit'];
		try
		{
			foreach(range(0, count($total_branches)-1) as $index)
			{
				foreach(range(0, count($total_branches[$index]->charts)-1) as $index2)
				{
					$start 								=  date('H:i:s', strtotime('+ '.rand(2,5).' hours'.' + '.rand(2,59).' minutes'.' + '.rand(2,59).' seconds'));
					$data 								= new Calendar;
					$data->fill([
						'organisation_id'				=> rand(1, $total_orgs),
						'name'							=> $faker->country,
						'workdays'						=> 'monday,tuesday,wednesday,thursday,friday',
						'start'							=> $start,
						'end'							=> date('H:i:s', strtotime($start.' + 7 hours')),
					]);

					$chart 								= $total_branches[$index]->charts[$index2]->id;

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