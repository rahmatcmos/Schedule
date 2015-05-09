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

	public function scopeChartTag($query, $variable)
	{
		return $query->WhereHas('charts', function($q)use($variable){$q->where('tag', $variable);});
	}

	public function scopeBranchName($query, $variable)
	{
		return $query->WhereHas('charts.branch', function($q)use($variable){$q->where('name', $variable);});
	}

	public function scopeFollow($query, $variable)
	{
		if(is_array($variable))
		{
			if(!is_null($variable[1]))
			{
				return $query->where('follows.start', '<=', date('Y-m-d', strtotime($variable[1])))
							 ->where('follows.start', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			elseif(!is_null($variable[0]))
			{
				return $query->where('follows.start', '>=', date('Y-m-d', strtotime($variable[0])));
			}
			else
			{
				return $query->where('follows.start', '>=', date('Y-m-d'));
			}
		}
		return $query->where('follows.start', '>=', date('Y-m-d', strtotime($variable)));
	}
}