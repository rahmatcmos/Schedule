<?php namespace ThunderID\Schedule\Models\Relations\BelongsTo;

trait HasSchedulesTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasSchedulesTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN ORGANISATION PACKAGE -------------------------------------------------------------------*/
	public function Schedules()
	{
		return $this->belongsTo('ThunderID\Schedule\Models\Schedule');
	}

}