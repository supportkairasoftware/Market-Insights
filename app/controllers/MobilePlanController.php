<?php

use DataProviders\IPlanDataProvider;
use DataProviders\SecurityDataProvider;

class MobilePlanController  extends BaseController
{
    function __construct(IPlanDataProvider  $PlanDataProvider){
        $this->PlanDataProvider = $PlanDataProvider;
    }

    public function postAllplanlist(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse = $this->PlanDataProvider->Allplanlist($serviceRequest->Data);
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
	}

    public function postUsertrialcheck(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
            $serviceResponse = $this->PlanDataProvider->UserTrialPlan($tokeServiceResponse);
        }else{
            $serviceResponse = $tokeServiceResponse;
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postActivatetrial(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
            $serviceResponse = $this->PlanDataProvider->ActivateTrial($serviceRequest->Data,$tokeServiceResponse);
        }else{
            $serviceResponse = $tokeServiceResponse;
        }

        return $this->GetJsonResponse($serviceResponse);
    }
    public function postPaymenthistory(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
            $serviceResponse = $this->PlanDataProvider->PaymentHistory($serviceRequest->Data,$tokeServiceResponse);
        }else{
            $serviceResponse = $tokeServiceResponse;
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function getClosehistoryplan(){
		$serviceResponse = $this->PlanDataProvider->CloseHistoryPlan();
        return $this->GetJsonResponse($serviceResponse);
	}
	
	public function getNotifyuserforplanexpire(){
		$serviceResponse = $this->PlanDataProvider->NotifyUserForPlanExpire();
        return $this->GetJsonResponse($serviceResponse);
	}
	
	public function postAddnews(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
            $serviceResponse = $this->PlanDataProvider->Addnews($tokeServiceResponse);
        }else{
            $serviceResponse = $tokeServiceResponse;
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    
   public function postSavenews(){
		if(Input::file('NewsImage'))
            $newsImage=Input::file('NewsImage');
        else
            $newsImage='';

        $serviceRequest=Input::all();
        $token=json_decode($serviceRequest['NewsData']);
        $tokeServiceResponse=$this->GetSessionUser($token->Token);
        $serviceRequest=$this->GetObjectFromJsonRequest(json_decode($serviceRequest['NewsData']));
        if($tokeServiceResponse->IsSuccess){
            $serviceResponse = $this->PlanDataProvider->SaveNews($serviceRequest->Data,$newsImage,$tokeServiceResponse);
        }else{
            $serviceResponse = $tokeServiceResponse;
        }
        return $this->GetJsonResponse($serviceResponse);
   }
   
   public function postSavepayment(){
   		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
        	$serviceResponse = $this->PlanDataProvider->SavePayment($serviceRequest->Data,$tokeServiceResponse);    
        }else{
            $serviceResponse = $tokeServiceResponse;
        }
        $this->getSendmessage();
        return $this->GetJsonResponse($serviceResponse);
        
   }
   
   	public function getSendmessage(){
    	$securityDataProvider = new SecurityDataProvider();
    	$securityDataProvider->SendMessage();
	}
	
	public function postNewslist(){
	    
	    $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
			$serviceResponse = $this->PlanDataProvider->GetNewsList($serviceRequest->Data, $CUser->Data->UserID);
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }
   
}