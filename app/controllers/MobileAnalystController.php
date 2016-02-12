<?php
use DataProviders\IAnalystDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class MobileAnalystController  extends BaseController
{
    function __construct(IAnalystDataProvider  $analystDataProvider){
        $this->DataProvider = $analystDataProvider;
    }

    public function postAllanalyst(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse=$this->DataProvider->Allanalyst($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postAnalystdetails(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse=$this->DataProvider->AnalystDetails($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postAllanalystdetails(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse=$this->DataProvider->AllAnalystDetails($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
}