<?php

class PlanEntity extends Eloquent {

    public $timestamps = false;
    public $table = 'paymentplans';
    public $primaryKey = 'PlanID';
    public $incrementing = 'PlanID';



    public $Model_Types = array(
        'PlanID'=>'int',
        'PlanName'=>'string',
        'Amount'=>'decimal',
        'Discount'=>'decimal',
        'NoOfDays'=>'int',
        'IsTrial'=>'int',
        'IsEnable'=>'int',
        'IsDeleted'=>'int'
    );

    public $booleanType = array();
    public $nullable = array();

    public static $Add_rules = array(
        'PlanName'=>'required|max:100',
        'Amount'=>'required',
        'NoOfDays'=>'required'
        /*'IsEnable' => 'required'*/
    );

    public static $niceNameArray = array(
        'Plan'=>'Plan ID',
        'PlanName'=>'Plan Name',
        'Amount'=>'Amount',
        'NoOfDays'=>'No Of Days',
        'CreatedDate'=>'Created Date'
    );

}
