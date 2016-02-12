<?php
use DataProviders\IFundamentalDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class MobileFundamentalController  extends BaseController
{
    function __construct(IFundamentalDataProvider  $fundamentalDataProvider){
        $this->DataProvider = $fundamentalDataProvider;
    }

    public function postAllfundamentals(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse=$this->DataProvider->AllFundamentals($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postFundatmentaldetails(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse=$this->DataProvider->FundatmentalDetails($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postAllfundamentaldetails(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse=$this->DataProvider->AllFundamentalDetails($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
}