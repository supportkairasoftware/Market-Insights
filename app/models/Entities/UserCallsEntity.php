<?php

class UserCallsEntity extends Eloquent{

    /**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'usercalls';
	public $primaryKey = 'UserCallID';
	public $incrementing = 'UserCallID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
}
