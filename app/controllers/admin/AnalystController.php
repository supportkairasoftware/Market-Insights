<?php
use DataProviders\IAnalystDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class AnalystController  extends BaseController
{
    function __construct(IAnalystDataProvider  $analystDataProvider){
        $this->DataProvider = $analystDataProvider;
    }
    /* Dev_RB region Start */
    public function getAnalystList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.analystlist');
    }

    public function postAnalystList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->getAnalystList($serviceRequest->Data);
        if(count($serviceResponse->Data->AnalystListArray)>0){
            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->AnalystListArray as $analyst){
                $analystID = Constants::$QueryStringAnalystID."=".$analyst->AnalystID;
                $analyst->EncryptAnalystID=Common::getEncryptDecryptID('encrypt', $analystID);
                $analyst->Description = Common::GetSubString($analyst->Description);
                $analyst->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postEnableanalyst(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->EnableAnalyst($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getAddAnalyst($encryptedAnalystID = 0){
        $isEditMode = false;
        if($encryptedAnalystID){
            $isEditMode = true;
        }
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        if($isEditMode){
            $decryptAnalystID = Common::getEncryptDecryptValue('decrypt',$encryptedAnalystID);
            $analystID =  Common::getExplodeValue($decryptAnalystID,Constants::$QueryStringAnalystID);
        }else{
            $analystID = 0;
        }

        $serviceResponse = $this->DataProvider->getAnalystDetails($analystID);
        return View::make('admin.addanalyst',(array)$serviceResponse->Data);
    }

    public function postSaveAnalyst(){
        $serviceRequest = Input::all();
        $serviceResponse = $this->DataProvider->SaveAnalyst($serviceRequest ,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeleteanalyst(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->DeleteAnalyst($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }

   /* public function postRemoveimage(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->RemoveAnalystImage($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }*/

	/* Dev_RB region Start */

}