<?php namespace ThunderID\Schedule\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Schedule\Models\Calendar;
use \ThunderID\Schedule\Models\Schedule;
use \ThunderID\Schedule\Models\PersonCalendar;
use \ThunderID\Schedule\Models\Follow;
use \ThunderID\Organisation\Models\Organisation;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App, DateTime, DateInterval, DatePeriod;

class CalendarController extends Controller {

	public function __construct()
	{
		//
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($page = 1, $search = null, $sort = null, $per_page = 12)
	{
		$contents 								= $this->dispatch(new Getting(new Calendar, $search, $sort ,(int)$page, $per_page));
		
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
		
		$content 								= $this->dispatch(new Saving(new Calendar, $attributes['calendar'], $id, new Organisation, $org_id));

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
					$begin 						= new DateTime( $value['on'][0] );
					$end 						= new DateTime( ($value['on'][1].' + 1 day') );

					$interval 					= DateInterval::createFromDateString('1 day');
					$periods 					= new DatePeriod($begin, $interval, $end);

					foreach ( $periods as $period )
					{
						$schedule				= $value;
						$schedule['on']			= $period->format('Y-m-d');
						if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
						{
							$schedule['id']		= $value['id'];
						}
						else
						{
							$schedule['id']		= null;
						}

						$saved_schedule 		= $this->dispatch(new Saving(new Schedule, $schedule, $schedule['id'], new Calendar, $is_success->data->id));
						$is_success_2 			= json_decode($saved_schedule);

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

					$saved_schedule 			= $this->dispatch(new Saving(new Schedule, $schedule, $schedule['id'], new Calendar, $is_success->data->id));
					$is_success_2 				= json_decode($saved_schedule);

					if(!$is_success_2->meta->success)
					{
						DB::rollback();
						return $saved_schedule;
					}
				}
			}
		}

		if(isset($attributes['persons']))
		{
			foreach ($attributes['persons'] as $key => $value) 
			{
				$personcalendar					= $value;
				if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
				{
					$personcalendar['id']		= $value['id'];
				}
				else
				{
					$personcalendar['id']		= null;
				}

				$saved_person 					= $this->dispatch(new Saving(new PersonCalendar, $personcalendar, $personcalendar['id'], new Calendar, $is_success->data->id));
				$is_success_2 					= json_decode($saved_person);
				if(!$is_success_2->meta->success)
				{
					DB::rollback();
					return $saved_person;
				}
			}
		}

		if(isset($attributes['charts']))
		{
			foreach ($attributes['charts'] as $key => $value) 
			{
				$follow							= $value;
				if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
				{
					$follow['id']				= $value['id'];
				}
				else
				{
					$follow['id']				= null;
				}

				$saved_chart 					= $this->dispatch(new Saving(new Follow, $follow, $follow['id'], new Calendar, $is_success->data->id));
				$is_success_2 					= json_decode($saved_chart);
				if(!$is_success_2->meta->success)
				{
					DB::rollback();
					return $saved_chart;
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
	public function destroy($org_id, $id)
	{
		$content 								= $this->dispatch(new Getting(new Calendar, ['ID' => $id, 'organisationid' => $org_id], ['created_at' => 'asc'] ,1, 1));
		$result 								= json_decode($content);
		
		if($result->meta->success)
		{
			$content 							= $this->dispatch(new Deleting(new Calendar, $id));
		}

		return $content;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	function followIndex($page = 1, $search = null, $sort = null, $all = false)
	{
		$per_page 								= 12;
		if($all)
		{
			$per_page 							= 100;
		}

		$contents 								= $this->dispatch(new Getting(new Follow, $search, $sort ,(int)$page, $per_page));
		
		return $contents;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function followDestroy($chart_id, $id)
	{
		$content 								= $this->dispatch(new Getting(new Follow, ['ID' => $id, 'chartid' => $chart_id], ['created_at' => 'asc'] ,1, 1));
		$result 								= json_decode($content);
		
		if($result->meta->success)
		{
			$content 							= $this->dispatch(new Deleting(new Follow, $id));
		}

		return $content;
	}
}