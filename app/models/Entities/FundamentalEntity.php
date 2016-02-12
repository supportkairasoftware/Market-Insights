<?php


class FundamentalEntity extends Eloquent  {


    public $timestamps = false;
    public $table = 'fundamentals';
    public $primaryKey = 'FundamentalID';
    public $incrementing = 'FundamentalID';



    public $Model_Types = array(
        'FundamentalID'=>'int',
        'Title'=>'string',
        'Description'=>'string',
        'PDF'=>'string',
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
        'Image'=>'required|max:200',
        'PDF'=>'required|max:200',
    );

    public static $niceNameArray = array(
        'FundamentalID'=>'Fundamental ID',
        'Title'=>'District Name',
        'Description'=>'Description',
        'PDF'=>'PDF',
        'Image'=>'Image',
        'CreatedDate'=>'Created Date'
    );

}
