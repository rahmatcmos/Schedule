<?php namespace ThunderID\Schedule\Models\Relations\BelongsTo;

trait HasCalendarTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasCalendarTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN ORGANISATION PACKAGE -------------------------------------------------------------------*/
	public function Calendar()
	{
		return $this->belongsTo('ThunderID\Schedule\Models\Calendar');
	}

}