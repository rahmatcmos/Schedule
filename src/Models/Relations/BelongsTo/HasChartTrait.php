<?php namespace ThunderID\Schedule\Models\Relations\BelongsTo;

trait HasChartTrait {

	/**
	 * boot
	 *
	 * @return void
	 * @author 
	 **/

	function HasChartTraitConstructor()
	{
		//
	}

	/* ------------------------------------------------------------------- RELATIONSHIP IN ORGANISATION PACKAGE -------------------------------------------------------------------*/
	public function Chart()
	{
		return $this->belongsTo('ThunderID\Organisation\Models\Chart', 'chart_id');
	}

}