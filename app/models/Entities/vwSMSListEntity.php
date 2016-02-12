<?php
class vwSMSListEntity extends Eloquent{

    public $timestamps = false;
    public $table = 'vw_smslist';
    public $primaryKey = 'MessageID';
    public $incrementing = 'MessageID';

    public $Model_Types = array(
        'MessageID'=>'int',
        'FirstName'=>'string',
        'LastName'=>'string',
        'Email'=>'string',
        'Name'=>'string',
        'Mobile'=>'string',
        'City'=>'string',
        'State'=>'string',
        'IsEnable'=>'int',
        'Message'=>'string',
        'IsSent'=>'int'
        //'CreatedDate'=>'string',
        //'SentDate'=>'string',
    );


}