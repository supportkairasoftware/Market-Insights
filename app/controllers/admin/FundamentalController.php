<?php
use DataProviders\IFundamentalDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class FundamentalController  extends BaseController
{
    function __construct(IFundamentalDataProvider  $fundamentalDataProvider){
        $this->DataProvider = $fundamentalDataProvider;
    }
    /* Dev_kr region Start */
    public function getFundamentalList()
    {
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.fundamentallist');
    }
    public function postFundamentalList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->getFundamentalList($serviceRequest->Data);

        if(count($serviceResponse->Data->FundamentalListArray)>0){

            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;

            foreach($serviceResponse->Data->FundamentalListArray as $fundamentals){
                $fundamentalID =Constants::$QueryStringFundamentalID."=".$fundamentals->FundamentalID;
                $fundamentals->EncryptFundamentalID=Common::getEncryptDecryptID('encrypt', $fundamentalID);
                $fundamentals->Description = Common::GetSubString($fundamentals->Description);
                $fundamentals->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postEnableFundamental(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->EnableFundamental($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }

    /*Dev_kr region End */

    /*Dev_RB region Start*/
    public function getAddFundamental($encryptedFundamentalID = 0){
        $isEditMode = false;
        if($encryptedFundamentalID){
            $isEditMode = true;
        }
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        if($isEditMode){
            $decryptFundamentalID = Common::getEncryptDecryptValue('decrypt',$encryptedFundamentalID);
            $fundamentalID =  Common::getExplodeValue($decryptFundamentalID,Constants::$QueryStringFundamentalID);
        }else{
            $fundamentalID=0;
        }
        $serviceResponse = $this->DataProvider->getFundamentalDetails($fundamentalID);
        return View::make('admin.addfundamental',(array)$serviceResponse->Data);
    }

    public function postSaveFundamental(){
        $serviceRequest = Input::all();
        $serviceResponse = $this->DataProvider->SaveFundamental($serviceRequest,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeletefundamental(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->DeleteFundamental($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }

    /*Dev_RB region End*/



}