<?php namespace ThunderID\Schedule\Models\Relations\BelongsToMany;

trait HasPersonsTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasPersonsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN PERSON PACKAGE -------------------------------------------------------------------*/

	public function Persons()
	{
		return $this->belongsToMany('ThunderID\Person\Models\Person', 'persons_calendars', 'calendar_id', 'person_id')
					->withPivot('start');
	}
}