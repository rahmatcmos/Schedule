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
	public function index($page = 1, $search = null, $sort = null, $per_page)
	{
		$contents 								= $this->dispatch(new Getting(new Schedule, $search, $sort ,(int)$page, $per_page));
		
		return $contents;
	}
}