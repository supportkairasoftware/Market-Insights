<?php
class ScriptEntity extends Eloquent{


    public $timestamps = false;
    public $table = 'scripts';
    public $primaryKey = 'ScriptID';
    public $incrementing = 'ScriptID';


    public $Model_Types = array(
        'ScriptID'=>'int',
        'SegmentID'=>'int',
        'Script'=>'string',
        'IsEnable'=>'int',
    );

    public static $Add_rules = array(
        'Script'=>'required',
    );

    public static $niceNameArray = array(
        'ScriptID'=>'Script ID',
        'Script'=>'Script'
    );

}
