<?php

class NewsEntity extends Eloquent {

    public $timestamps = false;
    public $table = 'news';
    public $primaryKey = 'NewsID';
    public $incrementing = 'NewsID';
	
    public $Model_Types = array(
        'NewsID'=>'int',
        'Description'=>'string',
        'Image'=>'string',
        'CreatedDate'=>'string',
        'ModifiedDate'=>'string',
        'GroupID'=>'string',
    );

    public static $Add_rules = array(
        'Description'=>'required',
        'GroupID'=>'required'
    );

    public static $niceNameArray = array(
        'GroupID'=>'Group',
        'Description'=>'Description'
    );

}
