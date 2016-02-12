<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class vwUserListEntity extends Eloquent implements UserInterface,   RemindableInterface {

    protected $guarded = array();
    use UserTrait, RemindableTrait;

    public $timestamps = false;
    public $table = 'vw_userlist';
    public $primaryKey = 'UserID';
    public $incrementing = 'UserID';

    public $Model_Types = array(
        'UserID'=>'int',
        'RoleName'=>'string',
        'IsEnable'=>'int',
        'FirstName'=>'string',
        'RoleID'=>'string',
        'GroupID'=>'string',
        'PlanName'=>'string'
    );


}