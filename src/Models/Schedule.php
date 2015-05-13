<?php namespace ThunderID\Schedule\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	calendar_id 					: Foreign Key From Calendar, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 	 						: Required, Time
 * 	end		 						: Required, Time
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship belongsTo 
	{
		Calendar
	}

 * ---------------------------------------------------------------------- */

use Str, Validator, DateTime, Exception;

class Schedule extends BaseModel {

	use SoftDeletes;
	use \ThunderID\Schedule\Models\Relations\BelongsTo\HasCalendarTrait;

	public 		$timestamps 		= 	true;

	protected 	$table 				= 	'schedules';

	protected 	$fillable			= 	[
											'name' 							,
											'on' 							,
											'start' 						,
											'end' 							,
										];

	protected 	$rules				= 	[
											'name'							=> 'required|max:255',
											'on'							=> 'required|date_format:"Y-m-d"',
											'start'							=> 'required|date_format:"H:i:s"',
											'end'							=> 'required|date_format:"H:i:s"',
										];
										
	public $searchable 				= 	[
											'id' 							=> 'ID', 
											'calendarid' 					=> 'CalendarID', 
											'name' 							=> 'Name', 
											'ondate' 						=> 'OnDate', 
											'notid' 						=> 'NotID', 
											'chartname' 					=> 'ChartName', 
											'branchname' 					=> 'BranchName', 
											'withattributes' 				=> 'WithAttributes'
										];

	public $sortable 				= ['created_at', 'name'];

	/* ---------------------------------------------------------------------------- CONSTRUCT ----------------------------------------------------------------------------*/
	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/
	static function boot()
	{
		parent::boot();

		Static::saving(function($data)
		{
			$validator = Validator::make($data->toArray(), $data->rules);

			if ($validator->passes())
			{
				return true;
			}
			else
			{
				$data->errors = $validator->errors();
				return false;
			}
		});
	}

	/* ---------------------------------------------------------------------------- QUERY BUILDER ---------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ---------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR --------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS -------------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- SCOPE -------------------------------------------------------------------------------*/
	public function scopeID($query, $variable)
	{
		return $query->where('id', $variable);
	}

	public function scopeCalendarID($query, $variable)
	{
		return $query->where('calendar_id', $variable);
	}

	public function scopeName($query, $variable)
	{
		return $query->where('name', 'like', '%'.$variable.'%');
	}

	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			if(!is_null($variable[1]))
			{
				return $query->where('on', '<=', date('Y-m-d', strtotime($variable[1])))
							 ->where('on', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			elseif(!is_null($variable[0]))
			{
				return $query->where('on', 'like', date('Y-m', strtotime($variable[0])).'%');
			}
			else
			{
				return $query->where('on', 'like', date('Y-m').'%');
			}
		}
		return $query->where('on', 'like', date('Y-m', strtotime($variable)).'%');
	}

	public function scopeNotID($query, $variable)
	{
		return $query->where('id', '<>',$variable);
	}

	public function scopeWithAttributes($query, $variable)
	{
		if(!is_array($variable))
		{
			$variable 			= [$variable];
		}

		return $query->with($variable);
	}
}
