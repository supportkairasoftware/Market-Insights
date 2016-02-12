<?php


class CallEntity extends Eloquent{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $timestamps = false;
    public $table = 'calls';
    public $primaryKey = 'CallID';
    public $incrementing = 'CallID';
    //public $timestamps = false;
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
     
     public $Model_Types = array(
        'CallID'=>'int',
        'ScriptID'=>'string',
        'Action'=>'string',
        'InitiatingPrice'=>'string',
        'T1'=>'string',
        'T2'=>'string',
        'SL'=>'string',
    );
     
    public $booleanType = array("IsOpen");
    public $nullable = array("Image");
    
    public static $updateCall_rules = array(
        'ResultID'=>'required',
    );
    
    public static $niceNameArray = array(
    	'ResultID' => 'Result'
    );
}
