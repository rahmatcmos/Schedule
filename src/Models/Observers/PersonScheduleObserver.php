<?php namespace ThunderID\Schedule\Models\Observers;

use DB, Validator;
use ThunderID\Schedule\Models\PersonSchedule;
use ThunderID\Log\Models\ProcessLog;
use \Illuminate\Support\MessageBag as MessageBag;

/* ----------------------------------------------------------------------
 * Event:
 * 	Creating						
 * 	Saving						
 * 	Updating						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class PersonScheduleObserver 
{
	public function creating($model)
	{
		//
	}

	public function saving($model)
	{
		$validator 				= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			if(isset($model['attributes']['person_id']))
			{
				$schedule 			= new PersonSchedule;
				$data 				= $schedule->ondate([$model['attributes']['on'], $model['attributes']['on']])->personid($model['attributes']['person_id'])->notid($model['attributes']['id'])->first();

				if(count($data))
				{
					$errors 		= new MessageBag;
					$errors->add('ondate', 'Tidak dapat menyimpan dua jadwal di hari yang sama. Silahkan edit jadwal sebelumnya.');
					$model['errors'] = $errors;

					return false;
				}
			}

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
		if(date('Y-m-d', strtotime($model['attributes']['on']))<=date('Y-m-d') && isset($model['attributes']['person_id']) && $model['attributes']['person_id'] != 0)
		{
			$processlogs 		= ProcessLog::ondate([date('Y-m-d', strtotime($model['attributes']['on'])), date('Y-m-d', strtotime($model['attributes']['on']))])->personid($model['attributes']['person_id'])->get();
			if($processlogs->count())
			{
				foreach ($processlogs as $key => $value) 
				{
					$data		= ProcessLog::ID($value->id)->first();
				
					//hitung margin start
					$schedule_start 		= $model['attributes']['start'];
					list($hours, $minutes, $seconds) = explode(":", $schedule_start);

					$schedule_start			= $hours*3600+$minutes*60+$seconds;

					$start 					= $data->start;
					list($hours, $minutes, $seconds) = explode(":", $start);

					$start 					= $hours*3600+$minutes*60+$seconds;

					$margin_start			= $schedule_start - $start;

					//hitung margin end
					$schedule_end 			= $model['attributes']['end'];
					list($hours, $minutes, $seconds) = explode(":", $schedule_end);

					$schedule_end			= $hours*3600+$minutes*60+$seconds;

					$end 					= $data->end;
					list($hours, $minutes, $seconds) = explode(":", $end);

					$end 					= $hours*3600+$minutes*60+$seconds;

					$margin_end				= $schedule_end - $end;

					$data->fill(['schedule_start' => $model['attributes']['start'], 'schedule_end' => $model['attributes']['end'], 'margin_end' => $margin_end, 'margin_start' => $margin_start]);

					if(!$data->save())
					{
						$model['errors']	= $data->getError();
						return false;
					}
				}
				return true;
			}
		}
	}

	public function updating($model)
	{
		// consider to add firewall
		// if(date('Y-m-d',strtotime($model['attributes']['on'])) <= date('Y-m-d'))
		// {
		// 	$errors 			= new MessageBag;
		// 	$errors->add('ondate', 'Tidak dapat mengubah jadwal pribadi seseorang pada waktu yang telah lewat atau hari ini. Silahkan tambahkan jadwal perorangan yang baru.');
		// 	$model['errors'] 	= $errors;

		// 	return false;
		// }
	}

	public function deleting($model)
	{
		if(date('Y-m-d',strtotime($model['attributes']['on'])) <= date('Y-m-d'))
		{
			$model['errors'] 	= ['Tidak dapat menghapus jadwal pribadi seseorang pada waktu yang telah lewat atau hari ini. Silahkan tambahkan jadwal perorangan yang baru.'];

			return false;
		}
	}
}
