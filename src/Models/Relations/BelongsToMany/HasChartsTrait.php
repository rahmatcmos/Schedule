<?php namespace ThunderID\Schedule\Models\Relations\BelongsToMany;

trait HasChartsTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasChartsTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN CHART PACKAGE -------------------------------------------------------------------*/

	public function Charts()
	{
		return $this->belongsToMany('ThunderID\Organisation\Models\Chart', 'follows', 'calendar_id', 'chart_id');
	}
}