<?php
namespace DataProviders;

Interface IPlanDataProvider {

    public function getPlanDetails($planID);
    public function SavePlan($planModel,$loginUserID);
    public function getPlanList($planModel);
    public function Updateplan($planModel);
    /*Mobile Serivce Method Start*/
    public function Allplanlist($model);
    public function UserTrialPlan($user);
    public function ActivateTrial($model,$user);
    public function PaymentHistory($model,$user);
    public function UpdateTrial($trialModel);
    public function CloseHistoryPlan();
    public function NotifyUserForPlanExpire();
    public function Addnews($user);
    public function SaveNews($newsmodel,$newsImage,$user);
    public function SavePayment($paymentModel,$user);
    public function GetNewsList($seachModel, $userID);
    public function DeletePlan($planID);
    /*Mobile Serivce Method End*/
}
