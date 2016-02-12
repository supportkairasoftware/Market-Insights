<?php


class vwUserPaymentEntity extends Eloquent  {

    public $timestamps = false;
    public $table = 'vw_userpaymentlist';
    public $primaryKey = 'PaymentHistoryID';
    public $incrementing = 'PaymentHistoryID';

    public $Model_Types = array(
        'PaymentHistoryID'=>'int',
        'UserID'=>'int',
        'Amount'=>'int',
        'SubscriptionAmount'=>'int',
        'ReferenceNo'=>'string',
        'StartDate'=>'string',
        'EndDate'=>'string',
        'PlanName'=>'string',
        'NoOfDays'=>'int',
        'IsTrial'=>'int',
        'IsActive'=>'int',
        'FirstName'=>'string',
        'LastName'=>'string',
        'Email'=>'string',
        'Mobile'=>'int',
        'City'=>'string',
        'State'=>'string',
        'IsEnable'=>'int'
    );
}
