<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class vwGroupUserEntity extends Eloquent implements UserInterface,   RemindableInterface {

    protected $guarded = array();
    use UserTrait, RemindableTrait;

    public $timestamps = false;
    public $table = 'vw_groupuser';
    public $primaryKey = 'GroupID';
    public $incrementing = 'GroupID';

    public $Model_Types = array(
        'GroupID'=>'int',
        'GroupName'=>'string',
        'IsEnable'=>'int'
    );

    public static $niceNameArray = array(

        'GroupID'=>'District ID',
        'GroupName'=>'Group Name',
        'IsActive'=>'IsActive'
    );

}
