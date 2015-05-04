<?php namespace ThunderID\Schedule;

use View, Validator, App, Route, Auth, Request, Redirect;
use Illuminate\Support\ServiceProvider;

class ScheduleServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		\ThunderID\Schedule\Models\Calendar::observe(new \ThunderID\Schedule\Models\Observers\CalendarObserver);
		\ThunderID\Schedule\Models\Schedule::observe(new \ThunderID\Schedule\Models\Observers\ScheduleObserver);
		\ThunderID\Schedule\Models\PersonCalendar::observe(new \ThunderID\Schedule\Models\Observers\PersonCalendarObserver);
		\ThunderID\Schedule\Models\PersonSchedule::observe(new \ThunderID\Schedule\Models\Observers\PersonScheduleObserver);
		\ThunderID\Schedule\Models\Follow::observe(new \ThunderID\Schedule\Models\Observers\FollowObserver);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		\ThunderID\Schedule\Models\Calendar::observe(new \ThunderID\Schedule\Models\Observers\CalendarObserver);
		\ThunderID\Schedule\Models\Schedule::observe(new \ThunderID\Schedule\Models\Observers\ScheduleObserver);
		\ThunderID\Schedule\Models\PersonCalendar::observe(new \ThunderID\Schedule\Models\Observers\PersonCalendarObserver);
		\ThunderID\Schedule\Models\PersonSchedule::observe(new \ThunderID\Schedule\Models\Observers\PersonScheduleObserver);
		\ThunderID\Schedule\Models\Follow::observe(new \ThunderID\Schedule\Models\Observers\FollowObserver);
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
