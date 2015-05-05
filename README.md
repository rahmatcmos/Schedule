# Schedule Overview

Package contain migrations, seeds and models for Schedule (HR). Documentation will be written in wiki.

# Installation

composer.json:
```
	"thunderid/schedule": "dev-master"
```

run
```
	composer update
```

```
	composer dump-autoload
```

# Usage

service provider
```
'ThunderID\Schedule\ScheduleServiceProvider'
```

migration
```
  php artisan migrate --path=vendor/thunderid/schedule/src/migrations
```

seed (run in mac or linux)
```
  php artisan db:seed --class=ThunderID\\Schedule\\seeds\\DatabaseSeeder
```

seed (run in windows)
```
  php artisan db:seed --class='\ThunderID\Schedule\seeds\DatabaseSeeder'
```

# Developer Notes for UI
## Table Calendar

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	organisation_id 				: Foreign Key From Organisation, Integer, Required
 * 	name 		 					: Required max 255
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship hasMany 
	{
		Schedules
	}

 * 	//other package
 	1 Relationship belongsTo 
	{
		Organisation
	}

 * 	//other package
 	2 Relationships belongsToMany 
	{
		Chart
		Person
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	name

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	organisationid 					: Search by organisation_id, parameter => string, organisation_id
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------


## Table Schedule

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	calendar_id 					: Foreign Key From Calendar, Integer, Required
 * 	name 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 	 						: Required, Time
 * 	end		 						: Required, Time
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship belongsTo 
	{
		Calendar
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	name
 	on
 	start
 	end

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	calendarid 						: Search by calendar_id, parameter => string, calendar_id
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------


## Table Follow

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	chart_id 						: Foreign Key From Chart, Integer, Required
 * 	calendar_id 					: Foreign Key From Calendar, Integer, Required
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
* 	//this package
 	1 Relationship belongsTo 
	{
		Calendar
	}

 * 	//other package
 	1 Relationship belongsTo 
	{
		Chart
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	calendar_id

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	calendarid 						: Search by calendar_id, parameter => string, calendar_id
	chartid 						: Search by chart_id, parameter => string, chart_id
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------

## Table PersonCalendar

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	calendar_id 					: Foreign Key From Calendar, Integer, Required
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	start 							: Required, Datetime
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
* 	//this package
 	1 Relationship belongsTo 
	{
		Calendar
	}

 * 	//other package
 	1 Relationship belongsTo 
	{
		Person
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	calendar_id
 	start

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	calendarid 						: Search by calendar_id, parameter => string, calendar_id
	personid 						: Search by person_id, parameter => string, person_id
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------

## Table PersonSchedule

/* ----------------------------------------------------------------------
 * Document Model:
 * 	ID 								: Auto Increment, Integer, PK
 * 	person_id 						: Foreign Key From Person, Integer, Required
 * 	name 		 					: Required max 255
 * 	status 		 					: Required max 255
 * 	on 		 						: Required, Date
 * 	start 	 						: Required, Time
 * 	end		 						: Required, Time
 *	created_at						: Timestamp
 * 	updated_at						: Timestamp
 * 	deleted_at						: Timestamp
 * 
/* ----------------------------------------------------------------------
 * Document Relationship :
 * 	//this package
 	1 Relationship belongsTo 
	{
		Person
	}

/* ----------------------------------------------------------------------
 * Document Fillable :
 * 	name
 	status
 	on
 	start
 	end

/* ----------------------------------------------------------------------
 * Document Searchable :
 * 	id 								: Search by id, parameter => string, id
	personid 						: Search by person_id, parameter => string, person_id
	withattributes					: Search with relationship, parameter => array of relationship (ex : ['chart', 'person'], if relationship is belongsTo then return must be single object, if hasMany or belongsToMany then return must be plural object)

/* ----------------------------------------------------------------------