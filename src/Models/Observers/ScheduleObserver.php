<?php namespace ThunderID\Schedule\Models\Observers;

use DB, Validator;
use ThunderID\Schedule\Models\Schedule;
use \Illuminate\Support\MessageBag as MessageBag;

/* ----------------------------------------------------------------------
 * Event:
 * 	Creating						
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class ScheduleObserver 
{
	public function creating($model)
	{
		$schedule 			= new Schedule;
		$data 				= $schedule->ondate([$model['attributes']['on'], $model['attributes']['on']])->first();

		if(count($data))
		{
			$errors 		= new MessageBag;
			$errors->add('ondate', 'Tidak dapat menyimpan dua jadwal di hari yang sama. Silahkan edit jadwal sebelumnya tambahkan jadwal khusus pada orang yang bersangkutan.');
			$model['errors'] = $errors;

			return false;
		}
	}

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

	public function updating($model)
	{
		//
	}

	public function deleting($model)
	{
		$model['errors'] 	= ['Tidak dapat menghapus jadwal.'];

		return false;
	}
}
