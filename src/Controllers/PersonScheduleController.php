<?php namespace ThunderID\Schedule\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Person\Models\Person;
use \ThunderID\Schedule\Models\Schedule;
use \ThunderID\Schedule\Models\PersonSchedule;
use \ThunderID\Organisation\Models\Organisation;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App, DateTime, DateInterval, DatePeriod;

class PersonScheduleController extends Controller {

	public function __construct()
	{
		//
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	function index($page = 1, $search = null, $sort = null, $all = false)
	{
		$per_page 								= 12;
		if($all)
		{
			$per_page 							= 100;
		}

		$contents 								= $this->dispatch(new Getting(new PersonSchedule, $search, $sort ,(int)$page, $per_page));
		
		return $contents;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id = null, $attributes = null)
	{
		$org_id 								= $attributes['organisation']['id'];
		
		DB::beginTransaction();
		
		$content 								= $this->dispatch(new Saving(new Person, $attributes['person'], $id));

		$is_success 							= json_decode($content);
		if(!$is_success->meta->success)
		{
			DB::rollback();
			return $content;
		}

		if(isset($attributes['schedules']))
		{
			foreach ($attributes['schedules'] as $key => $value) 
			{
				if(is_array($value['on']))
				{
					$begin 		= new DateTime( $value['on'][0] );
					$end 		= new DateTime( ($value['on'][1].' + 1 day') );

					$interval 	= DateInterval::createFromDateString('1 day');
					$periods 	= new DatePeriod($begin, $interval, $end);

					foreach ( $periods as $period )
					{
						$schedule					= $value;
						$schedule['on']				= $period->format('Y-m-d');
						if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
						{
							$schedule['id']			= $value['id'];
						}
						else
						{
							$schedule['id']			= null;
						}

						$saved_schedule 			= $this->dispatch(new Saving(new PersonSchedule, $schedule, $schedule['id'], new Person, $is_success->data->id));
						$is_success_2 				= json_decode($saved_schedule);

						if(!$is_success_2->meta->success)
						{
							DB::rollback();
							return $saved_schedule;
						}
					}
				}
				else
				{
					$schedule					= $value;
					if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
					{
						$schedule['id']			= $value['id'];
					}
					else
					{
						$schedule['id']			= null;
					}

					$saved_schedule 			= $this->dispatch(new Saving(new PersonSchedule, $schedule, $schedule['id'], new Calendar, $is_success->data->id));
					$is_success_2 				= json_decode($saved_schedule);

					if(!$is_success_2->meta->success)
					{
						DB::rollback();
						return $saved_schedule;
					}
				}
			}
		}

		DB::commit();

		return $content;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete($person_id, $id)
	{
		$content 						= $this->dispatch(new Getting(new PersonSchedule, ['ID' => $id, 'personid' => $person_id], ['created_at' => 'asc'] ,1, 1));
		$result 						= json_decode($content);
		
		if($result->meta->success)
		{
			$content 					= $this->dispatch(new Deleting(new PersonSchedule, $id));
		}

		return $content;
	}
}