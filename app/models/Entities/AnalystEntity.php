<?php
class AnalystEntity extends Eloquent{


    public $timestamps = false;
    public $table = 'analyst';
    public $primaryKey = 'AnalystID';
    public $incrementing = 'AnalystID';


    public $Model_Types = array(
        'AnalystID'=>'int',
        'Title'=>'string',
        'Description'=>'string',
        'Image'=>'string',
        'IsEnable'=>'int',
        'CreatedDate'=>'string',
        'ModifiedDate'=>'string',
    );

    public static $Add_rules = array(
        'Title'=>'required|max:100',
    );
    
    public static $Publish_rules = array(
        'Title'=>'required|max:100',
        'Description'=>'required',
        'Image'=>'required|max:500',
    );

    public static $niceNameArray = array(
        'AnalystID'=>'Analyst ID',
        'Title'=>'Title',
        'Description'=>'Description',
        'Image'=>'Image',
        'CreatedDate'=>'Created Date',
		'ModifiedDate'=>'ModifiedDate'
    );

}
