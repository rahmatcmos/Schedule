<?php namespace ThunderID\Schedule\Models\Observers;

use DB, Validator;
use ThunderID\Schedule\Models\Calendar;

/* ----------------------------------------------------------------------
 * Event:
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class CalendarObserver 
{
	public function saving($model)
	{
		$validator 				= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			return true;
		}
		else
		{
			$model['errors'] 	= $validator->errors();

			return false;
		}
	}

	public function saved($model)
	{
		if(isset($model['attributes']['organisation_id']) && $model->getDirty('organisation_id') != $model['attributes']['organisation_id'])
		{
			$model['errors'] 	= ['Organisasi tidak valid'];

			return false;
		}
		return true;
	}

	public function deleting($model)
	{
		if($model->charts->count())
		{
			$model['errors'] 	= ['Tidak dapat menghapus kalender yang berkaitan dengan karyawan'];

			return false;
		}
		if($model->schedules->count())
		{
			$model['errors'] 	= ['Tidak dapat menghapus kalender yang memiliki jadwal'];

			return false;
		}
	}
}
