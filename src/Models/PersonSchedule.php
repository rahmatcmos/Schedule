<?php namespace ThunderID\Schedule\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 	 						: Required, Time
 * 	end		 						: Required, Time
 * 	is_furlough		 				: Boolean
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship belongsTo 
	{
		Person
	}

 * ---------------------------------------------------------------------- */

use Str, Validator, DateTime, Exception;

class PersonSchedule extends BaseModel {

	use SoftDeletes;
	use \ThunderID\Schedule\Models\Relations\BelongsTo\HasPersonTrait;

	public 		$timestamps 		= true;

	protected 	$table 				= 'person_schedules';
	protected 	$fillable			= [
										'name' 							,
										'on' 							,
										'start' 						,
										'end' 							,
										'is_furlough' 					,
									];

	protected 	$rules				= [
										'name'							=> 'required|max:255',
										'on'							=> 'required|date_format:"Y-m-d"',
										'start'							=> 'required|date_format:"H:i:s"',
										'end'							=> 'required|date_format:"H:i:s"',
										'is_furlough' 					=> 'bool',
									];
	public $searchable 				= 	[
											'id' 						=> 'ID', 
											'personid' 					=> 'PersonID', 
											'withattributes' 			=> 'WithAttributes'
										];
	public $sortable 				= ['created_at'];

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

	public function scopePersonID($query, $variable)
	{
		return $query->where('person_id', $variable);
	}
}
