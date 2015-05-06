<?php namespace ThunderID\Schedule\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Schedule\Models\Calendar;
use \ThunderID\Schedule\Models\Schedule;
use \ThunderID\Schedule\Models\PersonCalendar;
use \ThunderID\Organisation\Models\Organisation;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App;

class CalenderController extends Controller {

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
		$id 									= $attributes['calendar']['id'];
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
				$schedule					= $value;
				if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
				{
					$schedule['id']			= $value['id'];
				}
				else
				{
					$schedule['id']			= null;
				}

				$saved_schedule 			= $this->dispatch(new Saving(new Schedule, $template, $template['id'], new Calendar, $is_success->data->id));
				$is_success_2 				= json_decode($saved_schedule);
				if(!$is_success_2->meta->success)
				{
					DB::rollback();
					return $saved_schedule;
				}
			}
		}

		DB::commit();

		return $content;
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($org_id, $id)
	{
		$content 						= $this->dispatch(new Getting(new Calendar, ['ID' => $id, 'organisationid' => $org_id], ['created_at' => 'asc'] ,1, 1));
		
		return $content;
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function delete($org_id, $id)
	{
		$content 						= $this->dispatch(new Getting(new Calendar, ['ID' => $id, 'organisationid' => $org_id], ['created_at' => 'asc'] ,1, 1));
		$result 						= json_decode($content);
		
		if($result->meta->success)
		{
			$content 					= $this->dispatch(new Deleting(new Calendar, $id));
		}

		return $content;
	}
}