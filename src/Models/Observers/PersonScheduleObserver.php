<?php namespace ThunderID\Schedule\Models\Observers;

use DB, Validator;
use ThunderID\Schedule\Models\PersonSchedule;
use ThunderID\Log\Models\ProcessLog;
use ThunderID\Person\Models\Person;
use ThunderID\Log\Models\Log;
use \Illuminate\Support\MessageBag as MessageBag;

/* ----------------------------------------------------------------------
 * Event:
 * 	Saving						
 * 	Saved						
 * 	Deleting						
 * ---------------------------------------------------------------------- */

class PersonScheduleObserver 
{
	public function saving($model)
	{
		$validator 							= Validator::make($model['attributes'], $model['rules']);

		if ($validator->passes())
		{
			if(isset($model['attributes']['person_id']))
			{
				// $schedule 					= new PersonSchedule;
				// if(isset($model['attributes']['id']))
				// {
				// 	$data 					= $schedule->ondate([$model['attributes']['on'], $model['attributes']['on']])->personid($model['attributes']['person_id'])->notid($model['attributes']['id'])->first();
				// }
				// else
				// {
				// 	$data 					= $schedule->ondate([$model['attributes']['on'], $model['attributes']['on']])->personid($model['attributes']['person_id'])->first();
				// }

				// if(count($data))
				// {
				// 	$errors 				= new MessageBag;
				// 	$errors->add('ondate', 'Tidak dapat menyimpan dua jadwal di hari yang sama. Silahkan edit jadwal sebelumnya.');
				
				// 	$model['errors'] 		= $errors;

				// 	return false;
				// }

				if(strtolower($model['attributes']['status'])=='absence_workleave')
				{
					$person 				= new Person;
					$data					= $person->id($model['attributes']['person_id'])->CheckWork(true)->CheckWorkleave([date('Y-m-d',strtotime('first day of january this year')), date('Y-m-d',strtotime('last day of december this year'))])->withattributes(['personworkleaves', 'personworkleaves.workleave'])->first();
					
					if(count($data))
					{
						$quota 				= 0;
						foreach($data->personworkleaves as $key => $value)
						{
							$quota 			= $quota + $value->workleave->quota;
						}

						$on 				= [date('Y-m-d',strtotime('first day of january this year')), date('Y-m-d',strtotime('last day of december this year'))];
						$data				= $person->id($model['attributes']['person_id'])->takenworkleave(['status' => 'workleave', 'on' => $on])->first();
						if(count($data))
						{
							if(count($data->takenworkleave) + 1 <= $quota)
							{
								return true;
							}
							else
							{
								$errors 	= new MessageBag;
								$errors->add('ondate', 'Jatah cuti tidak mencukupi. Sisa jatah cuti : '.$quota-count($data->workleaves).' hari');
								
								$model['errors'] = $errors;
								return false;
							}
						}
						return true;
					}
					else
					{
						$errors 			= new MessageBag;
						$errors->add('ondate', 'Karyawan tidak memiliki jatah cuti.');

						$model['errors'] 	= $errors;
					}
				}
			}

			return true;
		}
		else
		{
			$model['errors'] 				= $validator->errors();

			return false;
		}
	}

	public function saved($model)
	{
		if(date('Y-m-d', strtotime($model['attributes']['on']))<=date('Y-m-d') && isset($model['attributes']['person_id']) && $model['attributes']['person_id'] != 0)
		{
			$processlogs 					= ProcessLog::ondate([date('Y-m-d', strtotime($model['attributes']['on'])), date('Y-m-d', strtotime($model['attributes']['on']))])->personid($model['attributes']['person_id'])->get();
			if($processlogs->count())
			{
				foreach ($processlogs as $key => $value) 
				{
					$data					= ProcessLog::ID($value->id)->first();
					$on 					= date('Y-m-d', strtotime($model['attributes']['on']));

					$result 				= json_decode($data->tooltip);
					$tooltip 				= json_decode(json_encode($result), true);

					$pschedulee 			= Person::ID($model['attributes']['person_id'])->maxendschedule(['on' => [$on, $on]])->first();
					$pschedules 			= Person::ID($model['attributes']['person_id'])->minstartschedule(['on' => [$on, $on]])->first();
				
					if($pschedulee && $pschedules)
					{
						$schedule_start		= $pschedules->schedules[0]->start;
						$schedule_end		= $pschedulee->schedules[0]->end;
						if($model['attributes']['status']=='presence_outdoor')
						{
							if(!in_array($model['attributes']['status'], $tooltip))
							{
								$tooltip[] 		= $model['attributes']['status'];
							}						
						}
						else
						{
							foreach($pschedules->schedules as $key => $value)
							{
								if(!in_array($value->status, $tooltip))
								{
									$tooltip[] 		= $value->status;
								}
							}
						}
					}
					else
					{
						$schedule_end		= $data->schedule_start;
						$schedule_start 	= $data->schedule_end;
					}

					//hitung margin start
					list($hours, $minutes, $seconds) = explode(":", $schedule_start);

					$schedule_start			= $hours*3600+$minutes*60+$seconds;

					$start 					= $data->start;
					list($hours, $minutes, $seconds) = explode(":", $start);

					$start 					= $hours*3600+$minutes*60+$seconds;

					$margin_start			= $schedule_start - $start;

					//hitung margin end
					list($hours, $minutes, $seconds) = explode(":", $schedule_end);

					$schedule_end			= $hours*3600+$minutes*60+$seconds;

					$end 					= $data->end;
					list($hours, $minutes, $seconds) = explode(":", $end);

					$end 					= $hours*3600+$minutes*60+$seconds;

					$margin_end				= $schedule_end - $end;

					$data->fill(['schedule_start' => gmdate('H:i:s', $schedule_start), 'schedule_end' => gmdate('H:i:s', $schedule_end), 'margin_end' => $margin_end, 'margin_start' => $margin_start, 'tooltip' => json_encode($tooltip)]);
					if(!$data->save())
					{
						$model['errors']	= $data->getError();
						return false;
					}
				}

				// $log 						= new Log;
				// $log->fill([
				// 			'name'			=> 'previous schedules',
				// 			'on'			=> date('Y-m-d H:i:s', strtotime($model['attributes']['on'].' '.$model['attributes']['start'])),
				// 			'pc'			=> 'web'
				// 	]);

				// if(!$log->save())
				// {

				// 	$model['errors'] 	= $log->getError();

				// 	return false;
				// }

				// $log 						= new Log;
				// $log->fill([
				// 			'name'			=> 'previous schedules',
				// 			'on'			=> date('Y-m-d H:i:s', strtotime($model['attributes']['on'].' '.$model['attributes']['end'])),
				// 			'pc'			=> 'web'
				// 	]);

				// if(!$log->save())
				// {

				// 	$model['errors'] 	= $log->getError();

				// 	return false;
				// }

				// return true;
			}
		}
		if(strtolower($model['attributes']['status'])=='presence_outdoor' && isset($model['attributes']['person_id']))
		{
			$person 						= Person::find($model['attributes']['person_id']);
			$log 							= new Log;
			$log->fill([
						'name' 				=> 'presence_outdoor',
						'on' 				=> date('Y-m-d H:i:s', strtotime($model['attributes']['on'].' '.$model['attributes']['start'])),
						'pc' 				=> 'web',
			]);

			$log->Person()->associate($person);

			if(!$log->save())
			{

				$model['errors'] 	= $log->getError();

				return false;
			}

			$log 							= new Log;
			$log->fill([
						'name' 				=> 'presence_outdoor',
						'on' 				=> date('Y-m-d H:i:s', strtotime($model['attributes']['on'].' '.$model['attributes']['end'])),
						'pc' 				=> 'web',
			]);

			$log->Person()->associate($person);

			if(!$log->save())
			{

				$model['errors'] 	= $log->getError();

				return false;
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
			$model['errors'] 				= ['Tidak dapat menghapus jadwal pribadi seseorang pada waktu yang telah lewat atau hari ini. Silahkan tambahkan jadwal perorangan yang baru.'];

			return false;
		}

		$logs 								= Log::personid($model['attributes']['person_id'])->ondate([date('Y-m-d',strtotime($model['attributes']['on'])), date('Y-m-d',strtotime($model['attributes']['on'].' + 1 Day'))])->get();

		foreach ($logs as $key => $value) 
		{
			$log 							= Log::find($value->id);

			if(!$log->delete())
			{
				$model['errors'] 			= $log->getError();
				
				return false;
			}
		}
	}
}
