<?php namespace ThunderID\Schedule\seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use ThunderID\Schedule\Models\Calendar;
use ThunderID\Person\Models\Person;
use ThunderID\Organisation\Models\Organisation;
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
		$total_orgs 	 							= Organisation::get();
		$calendar 									= ['wib', 'wita', 'wit'];
		try
		{
			foreach(range(1, count($total_orgs)) as $index)
			{
				foreach(range(1, $index->charts) as $index2)
				{
					$data 								= new Calendar;
					$data->fill([
						'name'							=> $calendar[rand(0,2)],
					]);

					if($index==1)
					{
						$person 						= 1;
					}
					else
					{
						$chart 							= $index2->id;
						$person 						= rand(2,$total_persons);
					}

					$data->Person()->attach($person);
					if(isset($chart))
					{
						$data->Chart()->attach($chart);
					}

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