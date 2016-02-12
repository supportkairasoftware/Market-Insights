<?php
use DataProviders\IPlanDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;

class PlanController  extends BaseController
{
    function __construct(IPlanDataProvider  $PlanDataProvider){
        $this->PlanDataProvider = $PlanDataProvider;
    }

    /* Dev_RB region Start */
    public function getAddPlan($encryptedPlanID = 0){
        $isEditMode = false;
        if($encryptedPlanID){
            $isEditMode = true;
        }

        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');

        if($isEditMode){
            $decryptPlanID = Common::getEncryptDecryptValue('decrypt',$encryptedPlanID);
            $planID =  Common::getExplodeValue($decryptPlanID,Constants::$QueryStringPlanID);
        }else{
            $planID=0;
        }
        $serviceResponse = $this->PlanDataProvider->getPlanDetails($planID);
        return View::make('admin.addplan',(array)$serviceResponse->Data);
    }

    public function postSavePlan(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->PlanDataProvider->SavePlan($serviceRequest->Data,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getPlanList()
    {
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.planlist');
    }
    public function postplanlist(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->PlanDataProvider->getPlanList($serviceRequest->Data);

        if(count($serviceResponse->Data->PlanListArray)>0){

            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->PlanListArray as $plans){
                $planID =Constants::$QueryStringPlanID."=".$plans->PlanID;
                $plans->EncryptPlanID=Common::getEncryptDecryptID('encrypt', $planID);
                $plans->DisplayName = Common::GetSubString($plans->PlanName);
                $plans->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postUpdateplan(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->PlanDataProvider->UpdatePlan($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postUpdateptrial(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->PlanDataProvider->UpdateTrial($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeleteplan(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->PlanDataProvider->DeletePlan($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }


    /* Dev_RB region End */
}