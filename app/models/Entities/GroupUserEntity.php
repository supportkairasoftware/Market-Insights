<?php
class GroupUserEntity extends Eloquent {

    public $timestamps = false;
    public $table = 'usergroups';
    public $primaryKey = 'UserGroupID';
    public $incrementing = 'UserGroupID';

    public $Model_Types = array(
        'UserGroupID'=>'int',
        'GroupID'=>'int',
        'UserID'=>'int',
        'CreatedDate'=>'string',
        'ModifiedDate'=>'string',
        'IsEnable'=>'int',
    );
    public static $Add_rules = array(
        'GroupID'=>'required',
        'UserID'=>'required',
        'IsEnable' => 'required'
    );
    public static $niceNameArray = array(
        'GroupID'=>'Group ID',
        'UserID'=>'User ID'
    );

}
