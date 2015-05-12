<?php namespace ThunderID\Schedule\Models\Observers;

use DB, Validator;
use ThunderID\Schedule\Models\Schedule;
use ThunderID\Log\Models\ProcessLog;
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
		//
	}

	public function saving($model)
	{
		$validator 				= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			if(isset($model['attributes']['calendar_id']))
			{
				$schedule 			= new Schedule;
				if(isset($model['attributes']['id']))
				{
					$data 			= $schedule->ondate([$model['attributes']['on'], $model['attributes']['on']])->calendarid($model['attributes']['calendar_id'])->notid($model['attributes']['id'])->first();
				}
				else
				{
					$data 			= $schedule->ondate([$model['attributes']['on'], $model['attributes']['on']])->calendarid($model['attributes']['calendar_id'])->first();
				}

				if(count($data))
				{
					$errors 		= new MessageBag;
					$errors->add('ondate', 'Tidak dapat menyimpan dua jadwal di hari yang sama. Silahkan edit jadwal sebelumnya tambahkan jadwal khusus pada orang yang bersangkutan.'.$data->calendar_id);
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
		if(date('Y-m-d', strtotime($model['attributes']['on']))<=date('Y-m-d') && isset($model['attributes']['calendar_id']) && $model['attributes']['calendar_id'] != 0)
		{
			$processlogs 		= ProcessLog::ondate([date('Y-m-d', strtotime($model['attributes']['on'])), date('Y-m-d', strtotime($model['attributes']['on']))])->hasnoschedule(['on' => date('Y-m-d', strtotime($model['attributes']['on']))])->Calendar(['id' => $model['attributes']['calendar_id'], 'start' => date('Y-m-d', strtotime($model['attributes']['on']))])->get();
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
			else
			{
				$processlogs 	= ProcessLog::ondate([date('Y-m-d', strtotime($model['attributes']['on'])), date('Y-m-d', strtotime($model['attributes']['on']))])->hasnoschedule(['on' => date('Y-m-d', strtotime($model['attributes']['on']))])->WorkCalendar(['id' => $model['attributes']['calendar_id'], 'start' => date('Y-m-d', strtotime($model['attributes']['on']))])->get(['id']);
				if(count($processlogs))
				{
					foreach ($processlogs as $key => $value) 
					{
						$data					= ProcessLog::ID($value->id)->first();
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

				return true;
			}
		}
	}

	public function updating($model)
	{
		if(date('Y-m-d', strtotime($model['attributes']['on']))<date('Y-m-d'))
		{
			$errors 		= new MessageBag;
			$errors->add('ondate', 'Tidak dapat mengubah jadwal yang sudah lewat atau sedang berlangsung. Silahkan tambahkan ke jadwal khusus perorangan.');
			$model['errors'] = $errors;

			return false;
		}

		return true;
		//
	}

	public function deleting($model)
	{
		$model['errors'] 	= ['Tidak dapat menghapus jadwal.'];

		return false;
	}
}
