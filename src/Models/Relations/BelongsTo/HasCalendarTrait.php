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

	/* ------------------------------------------------------------------- RELATIONSHIP IN SCHEDULE PACKAGE -------------------------------------------------------------------*/
	public function Calendar()
	{
		return $this->belongsTo('ThunderID\Schedule\Models\Calendar');
	}

	public function scopeChartName($query, $variable)
	{
		return $query->WhereHas('calendar.charts', function($q)use($variable){$q->where('name', $variable);});
	}

	public function scopeBranchName($query, $variable)
	{
		return $query->WhereHas('calendar.charts.branch', function($q)use($variable){$q->where('name', $variable);});
	}
}