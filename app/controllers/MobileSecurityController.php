<?php

use DataProviders\SecurityDataProvider;
use DataProviders\ISecurityDataProvider;
use ViewModels\LoginModel;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use Illuminate\Support\Facades\URL;


class MobileSecurityController extends BaseController {

	protected $securityDataProvider;
	function __construct(ISecurityDataProvider $securityDataProvider){
		$this->securityDataProvider = $securityDataProvider;
	}
	
	public function postSignup(){
        $serviceResponse= new ServiceResponse();
		$securityDataProvider = new SecurityDataProvider();
        if(Input::file('UserImage'))
            $userimage=Input::file('UserImage');
        else
            $userimage='';

        $serviceRequest = Input::all();
        $serviceRequest = $this->GetObjectFromJsonRequest(json_decode($serviceRequest['UserData']));
        $serviceResponse = $this->securityDataProvider->Signup($serviceRequest->Data,$userimage);
        
        if($serviceResponse->IsSuccess){
            $token = $this->GenerateToken($serviceResponse->Data->Email);
            $response=$this->ValidateToken($token,false,$serviceResponse->Data);
            $serviceResponse->Token=$token;
            $serviceResponse->IsSuccess = $response->IsSuccess;
        }
        return $this->GetJsonResponse($serviceResponse);
	}
	
    public function postAuthenticate(){
        $serviceResponse= new ServiceResponse();
        $securityDataProvider = new SecurityDataProvider();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->securityDataProvider->postAuthenticate($serviceRequest->Data);
        if($serviceResponse->IsSuccess){
            $token = $this->GenerateToken($serviceResponse->Data->Email);
            $response=$this->ValidateToken($token,false,$serviceResponse->Data);
            $serviceResponse->Token=$token;
            $serviceResponse->IsSuccess = $response->IsSuccess;
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postSendotpformobile(){
    	$serviceResponse= new ServiceResponse();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
    	if($tokeServiceResponse->IsSuccess){
        	$serviceResponse = $this->securityDataProvider->SendOTPForMobile($serviceRequest->Data->Mobile,$tokeServiceResponse->Data->UserID);
    		$this->getSendmessage();
		}else{
			$serviceResponse = $tokeServiceResponse;
		}
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postOTPverified(){
        $serviceResponse= new ServiceResponse();
        $securityDataProvider = new SecurityDataProvider();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->securityDataProvider->OTPverified($serviceRequest->Data);
        if($serviceResponse->IsSuccess){
            $token = $this->GenerateToken($serviceResponse->Data->Email);
            $response=$this->ValidateToken($token,false,$serviceResponse->Data);
            $serviceResponse->Token=$token;
            $serviceResponse->IsSuccess = $response->IsSuccess;
        }
        $this->getSendmessage();
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postLogout()
    {
        $serviceResponse= new ServiceResponse();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        
        if($tokeServiceResponse->IsSuccess){
        	$securityDataProvider = new SecurityDataProvider();
	        $logout = $securityDataProvider->Logout($tokeServiceResponse->Data);
	        if($logout->IsSuccess){
	            $serviceResponse->IsSuccess= $this->RemoveToken($serviceRequest->Token);
	            $serviceResponse->Message=  trans('messages.LogoutSuccess');
	        }else{
	            $serviceResponse->Message=  trans('messages.ErrorOccured');
	        }
		}else{
			$serviceResponse = $tokeServiceResponse;
		}
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postForgot(){
        $serviceResponse = new ServiceResponse();
        $securityDataProvider = new SecurityDataProvider();
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->securityDataProvider->Forgot($serviceRequest->Data);
        $this->getSendmessage();
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function getSendmessage(){
    	$securityDataProvider = new SecurityDataProvider();
    	$securityDataProvider->SendMessage();
	}
	public function getSendmail(){
		$baseDataProvider = new \DataProviders\BaseDataProvider();
		$baseDataProvider->Sendmailqueue();	
	}
	
	public function postCheckMobileVerification(){
	    $serviceResponse = new ServiceResponse();
	    $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
			$serviceResponse = $this->securityDataProvider->CheckMobileVerification($CUser->Data->UserID);
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postCheckUserPlan(){
	    $serviceResponse = new ServiceResponse();
	    $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
			$serviceResponse = $this->securityDataProvider->CheckUserPlan($CUser->Data->UserID);
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postSaveIOSNotificationON(){

        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        if($CUser->IsSuccess){
            $serviceResponse = $this->securityDataProvider->postSaveIsIosNotificationON($serviceRequest->Data);
        }else{
            $serviceResponse = $CUser;
        }

        return $this->GetJsonResponse($serviceResponse);
    }

	
}