<?php

class vwUserDevicesEntity extends Eloquent{

    public $timestamps = false;
    public $table = 'vw_userdevices';
    public $primaryKey = 'UserDeviceID';
    public $incrementing = 'UserDeviceID';

    public $Model_Types = array(
        'UserDeviceID'=>'int',
        'UserID'=>'int',
        'DeviceID'=>'string',
        'FirstName'=>'string',
        'LastName'=>'string',
        'City'=>'string',
        'Mobile'=>'string',
		'Email'=>'string'
    );


}