<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class UserHistoryEntity extends Eloquent implements UserInterface,   RemindableInterface {

    protected $guarded = array();
    use UserTrait, RemindableTrait;

    public $timestamps = false;
    public $table = 'userhistory';
    public $primaryKey = 'UserHistoryID';
    public $incrementing = 'UserHistoryID';

    public $Model_Types = array(
        'UserHistoryID'=>'int',
        'UserID'=>'string',
        'LoginTime'=>'string',
        'LogoutTime'=>'string'
    );

    public static $Add_rules = array(
        'UserID'=>'required'
    );

    public static $niceNameArray = array(

        'UserHistoryID'=>'History ID',
        'UserID'=>'User ID',
        'LoginTime'=>'Last Login Time',
        'LogoutTime'=>'Last Logout Time',
    );

}
