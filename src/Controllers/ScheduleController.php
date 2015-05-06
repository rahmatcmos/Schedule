<?php namespace ThunderID\Schedule\Controllers;

use \App\Http\Controllers\Controller;
use \ThunderID\Schedule\Models\Schedule;
use \ThunderID\Schedule\Models\PersonSchedule;
use \ThunderID\Organisation\Models\Organisation;
use \ThunderID\Commoquent\Getting;
use \ThunderID\Commoquent\Saving;
use \ThunderID\Commoquent\Deleting;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App;

class ScheduleController extends Controller {

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

		$contents 								= $this->dispatch(new Getting(new Schedule, $search, $sort ,(int)$page, $per_page));
		
		return $contents;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($id = null, $attributes = null)
	{
		$id 									= $attributes['document']['id'];
		$org_id 								= $attributes['organisation']['id'];

		DB::beginTransaction();
		
		$content 								= $this->dispatch(new Saving(new Schedule, $attributes['document'], $id, new Organisation, $org_id));

		$is_success 							= json_decode($content);
		if(!$is_success->meta->success)
		{
			DB::rollback();
			return $content;
		}

		if(isset($attributes['templates']))
		{
			foreach ($attributes['templates'] as $key => $value) 
			{
				$template['field']			= $value['field'];
				$template['type']			= $value['type'];
				if(isset($value['id']) && $value['id']!='' && !is_null($value['id']))
				{
					$template['id']			= $value['id'];
				}
				else
				{
					$template['id']			= null;
				}

				$saved_template 			= $this->dispatch(new Saving(new Template, $template, $template['id'], new Schedule, $is_success->data->id));
				$is_success_2 				= json_decode($saved_template);
				if(!$is_success_2->meta->success)
				{
					DB::rollback();
					return $saved_template;
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
		$content 						= $this->dispatch(new Getting(new Schedule, ['ID' => $id, 'organisationid' => $org_id], ['created_at' => 'asc'] ,1, 1));
		
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
		$content 						= $this->dispatch(new Getting(new Schedule, ['ID' => $id, 'organisationid' => $org_id], ['created_at' => 'asc'] ,1, 1));
		$result 						= json_decode($content);
		
		if($result->meta->success)
		{
			$content 					= $this->dispatch(new Deleting(new Schedule, $id));
		}

		return $content;
	}
}