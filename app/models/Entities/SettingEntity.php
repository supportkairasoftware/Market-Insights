<?php

class SettingEntity extends Eloquent {

    public $timestamps = false;
    public $table = 'setting';
    public $primaryKey = 'SettingID';
    public $incrementing = 'SettingID';



    public $Model_Types = array(
        'SettingID'=>'int',
        'SMSUrl'=>'string',
        'SMSUserName'=>'string',
        'SMSPassword'=>'string',
        'SMSTemplates'=>'string'
    );

    public $booleanType = array();
    public $nullable = array();

    public static $Add_rules = array(
        
    );

    public static $niceNameArray = array(
       
    );

}
