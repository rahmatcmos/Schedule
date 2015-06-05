<?php namespace ThunderID\Schedule\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	organisation_id 				: Foreign Key From Organisation, Integer, Required
 * 	name 		 					: Required max 255
 * 	workdays 		 				: Text
 * 	start 		 					: Required time
 * 	end 		 					: Required time
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship hasMany 
	{
		Schedules
	}

 * 	//other package
 	1 Relationship belongsTo 
	{
		Organisation
	}

 * 	//other package
 	1 Relationship belongsToMany 
	{
		Charts
	}

 * ---------------------------------------------------------------------- */

use Str, Validator, DateTime, Exception;

class Calendar extends BaseModel {

	use SoftDeletes;
	use \ThunderID\Schedule\Models\Relations\BelongsTo\HasOrganisationTrait;
	use \ThunderID\Schedule\Models\Relations\HasMany\HasSchedulesTrait;
	use \ThunderID\Schedule\Models\Relations\BelongsToMany\HasChartsTrait;

	public 		$timestamps 		= 	true;

	protected 	$table 				= 	'calendars';
	
	protected 	$fillable			= 	[
											'name' 						,
											'workdays' 					,
											'start' 					,
											'end' 						,
										];

	protected 	$rules				= 	[
											'name'						=> 'required|max:255',
											'start'						=> 'required|date_format:"H:i:s"',
											'end'						=> 'required|date_format:"H:i:s"',
										];

	public $searchable 				= 	[
											'id' 						=> 'ID', 
											'organisationid' 			=> 'OrganisationID', 
											'name' 						=> 'Name', 
											'orname' 					=> 'OrName', 
											'charttag' 					=> 'ChartTag', 
											'branchname' 				=> 'BranchName', 
											'branchid' 					=> 'BranchID', 
											'withattributes' 			=> 'WithAttributes'
										];

	public $sortable 				= 	['created_at', 'name'];

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
		return $query->where('calendars.id', $variable);
	}

	public function scopeOrganisationID($query, $variable)
	{
		return $query->where('organisation_id', $variable);
	}

	public function scopeName($query, $variable)
	{
		return $query->where('name', 'like', '%'.$variable.'%');
	}

	public function scopeOrName($query, $variable)
	{
		if(is_array($variable))
		{
			foreach ($variable as $key => $value) 
			{
				$query = $query->orwhere('name', 'like', '%'.$value.'%');
			}
			return $query;
		}
		return $query->where('name', 'like', '%'.$variable.'%');
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
