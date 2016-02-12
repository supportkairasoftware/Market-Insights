<?php
class vwNotificationListEntity extends Eloquent{

    public $timestamps = false;
    public $table = 'vw_notificationlist';
    public $primaryKey = 'NotificationID';
    public $incrementing = 'NotificationID';

    public $Model_Types = array(
        'NotificationID'=>'int',
        'FirstName'=>'string',
        'LastName'=>'string',
        'Email'=>'string',
        'Name'=>'string',
        'GroupID'=>'string',
        'Mobile'=>'string',
        'City'=>'string',
        'State'=>'string',
        'IsEnable'=>'int',
        'Message'=>'string',
        'Key'=>'int',
        'IsSent'=>'int',
        'fromDate'=>'string',
        //'CreatedDate'=>'string',
        'SentDate'=>'string',
    );


}