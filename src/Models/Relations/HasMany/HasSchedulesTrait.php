<?php namespace ThunderID\Schedule\Models\Relations\HasMany;

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

	/* ------------------------------------------------------------------- RELATIONSHIP IN SCHEDULE PACKAGE -------------------------------------------------------------------*/
	public function Schedules()
	{
		return $this->hasMany('ThunderID\Schedule\Models\Schedule');
	}
}