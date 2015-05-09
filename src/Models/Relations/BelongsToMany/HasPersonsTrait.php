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

	public function scopeStart($query, $variable)
	{
		if(is_array($variable))
		{
			if(!is_null($variable[1]))
			{
				return $query->where('persons_calendars.start', '<=', date('Y-m-d', strtotime($variable[1])))
							 ->where('persons_calendars.start', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			elseif(!is_null($variable[0]))
			{
				return $query->where('persons_calendars.start', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			else
			{
				return $query->where('persons_calendars.start', '>=', date('Y-m-d'));
			}
		}
		return $query->where('persons_calendars.start', '>=', date('Y-m-d', strtotime($variable)));
	}
}