<?php

class UserDevicesEntity extends Eloquent {
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $timestamps = false;
	public $table = 'userdevices';
	public $primaryKey = 'UserDeviceID';
	public $incrementing = 'UserDeviceID';
	//public $timestamps = false;
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */

	protected $hidden = array('Password', 'Remember_Token');

    public $Model_Types = array(
        'UserDeviceID'=>'int',
        'UserID'=>'int',
        'DeviceID'=>'string',
    );

}
