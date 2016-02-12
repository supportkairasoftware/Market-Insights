<?php

class PaymentPlansHistoryEntity extends Eloquent {

    public $timestamps = false;
    public $table = 'paymentplanshistory';
    public $primaryKey = 'PaymentHistoryID';
    public $incrementing = 'PaymentHistoryID';


    public $Model_Types = array(
        'PaymentHistoryID'=>'int',
        'UserID'=>'int',
		'Amount'=>'float',
        'SubscriptionAmount'=>'float',
        'ReferenceNo'=>'float',
        'StartDate'=>'string',
        'EndDate'=>'string',
		'PlanName'=>'string',
		'NoOfDays'=>'int',
		'IsTrial'=>'int',
		'IsActive'=>'int',
    );

    public $booleanType = array();
    public $nullable = array('ReferenceNo');

    public static $Add_rules = array(
        'PlanName'=>'required|max:50',
        'Amount'=>'required',
        'NoOfDays'=>'required'
        /*'IsEnable' => 'required'*/
    );
    public static $AddPayment_rules = array(
        'UserID'=>'required',
        'PlanID'=>'required',
        //'StartDate'=>'required',
        'SubscriptionAmount'=>'required'
    );


    public static $niceNameArray = array(
        'Plan'=>'Plan ID',
        'PlanName'=>'Plan Name',
        'Amount'=>'Amount',
        'NoOfDays'=>'No Of Days',
        'CreatedDate'=>'Created Date',
        'UserID'=>'Select User',
        'PlanID'=>'Select Plan',
//        /'StartDate'=>'Start Date',
        'SubscriptionAmount'=>'Subscription Amount'
    );



}
