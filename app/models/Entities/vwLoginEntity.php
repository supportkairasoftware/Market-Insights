<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class vwLoginEntity extends Eloquent implements UserInterface,   RemindableInterface {

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    public $table = 'vwlogin';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public $Model_Types = array(
		'UserID'=>'int',
        'FirstName'=>'string',
        'Email'=>'string',
        'Password'=>'string',
        'IsEnable'=>'int',
        'Mobile'=>'string',
        'City'=>'string',
        'State'=>'string',
        'FbID'=>'int',
        'DeviceID'=>'int',
        'IsAndroid'=>'int',
        'IsSocial'=>'int',
        'Photo'=>'string',
        'GoogleID'=>'int',
        'IsVerified'=>'int',
		'RoleID'=>'int',
        'RoleName'=>'string'
    );

}
