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

	public function scopeChartName($query, $variable)
	{
		return $query->WhereHas('charts', function($q)use($variable){$q->where('name', $variable);});
	}

	public function scopeBranchName($query, $variable)
	{
		return $query->WhereHas('charts.branch', function($q)use($variable){$q->where('name', $variable);});
	}
}