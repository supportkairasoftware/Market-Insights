<?php

class UserNotificationEntity extends Eloquent{

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'usernotifications';
	public $primaryKey = 'UserNotificationID';
	public $incrementing = 'UserNotificationID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
}
