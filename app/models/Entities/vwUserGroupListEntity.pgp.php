<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class vwUserGroupListEntity extends Eloquent implements UserInterface,   RemindableInterface {

    protected $guarded = array();
    use UserTrait, RemindableTrait;

    public $timestamps = false;
    public $table = 'vw_usergrouplist';
    public $primaryKey = 'GroupID';
    public $incrementing = 'GroupID';

    public $Model_Types = array(
        'GroupID'=>'int',
        'UserID'=>'int',
        'GroupName'=>'string',
        'FirstName'=>'string',
        'LastName'=>'string',
        'IsEnable'=>'int',
        'GroupEnable'=>'int',
        'Mobile'=>'int',
        'Email'=>'string'
    );

    public static $niceNameArray = array(

        'GroupID'=>'Group ID',
        'UserID'=>'User ID',
        'GroupName'=>'Group Name',
        'FirstName'=>'First Name',
        'LastName'=>'Last Name',
        'Mobile'=>'Mobile No.',
        'Email'=>'Email',
        'IsEnable'=>'IsEnable',
        'GroupEnable'=>'IsEnable'
    );

}
