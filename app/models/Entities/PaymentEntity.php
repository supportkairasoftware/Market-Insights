<?php


class PaymentEntity extends Eloquent{

    public $timestamps = false;
    public $table = 'paymentplanshistory';
    public $primaryKey = 'PaymentHistoryID';
    public $incrementing = 'PaymentHistoryID';

    public $Model_Types = array(
        'PaymentHistoryID'=>'int',
        'UserID'=>'int',
        'Amount'=>'float',
        'SubscriptionAmount'=>'float',
        'ReferenceNo'=>'string',
        'StartDate'=>'string',
        'EndDate'=>'string',
        'PlanName'=>'string',
        'NoOfDays'=>'int',
        'IsTrial'=>'int',
        'IsActive'=>'int'
    );
}
