<?php

class GroupEntity extends Eloquent{

  
    public $timestamps = false;
    public $table = 'groups';
    public $primaryKey = 'GroupID';
    public $incrementing = 'GroupID';



    public $Model_Types = array(
        'GroupID'=>'int',
        'GroupName'=>'string',
        'CreatedDate'=>'string',
        'ModifiedDate'=>'string',
        'IsEnable'=>'int',
        'CreatedBy'=>'int',
        'ModifiedBy'=>'int',
    );

	public $booleanType = array("IsEnable");
	
    public static $Add_rules = array(
        'GroupName'=>'required|max:50',
    );

    public static $niceNameArray = array(

        'GroupID'=>'District ID',
        'GroupName'=>'District Name',
        'CreatedDate'=>'Created Date'
    );

}
