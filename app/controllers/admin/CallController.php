<?php
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use DataProviders\CallDataProvider;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class CallController  extends BaseController
{
		
    function __construct(){
        /*$serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
    	$CUser = $this->GetSessionUser($serviceRequest->Token);
    	if(!$CUser->IsSuccess){
    		$CUser = $CUser->Data;
    	}else{
			return $this->GetJsonResponse($CUser);
		}*/
    }
    
    public function postSegments(){
		$CallDataProvider = new CallDataProvider();
		$ServiceResponse = new ServiceResponse();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
            $ServiceResponse = $CallDataProvider->GetSegmets();
        }else{
            $ServiceResponse = $tokeServiceResponse;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
    
    public function postGetlookups(){
    	$CallDataProvider = new CallDataProvider();
		$ServiceResponse = new ServiceResponse();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        //$tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        //if($tokeServiceResponse->IsSuccess){
            $ServiceResponse = $CallDataProvider->GetLookupForCall();
        //}else{
          //  $ServiceResponse = $tokeServiceResponse;
        //}
		return $this->GetJsonResponse($ServiceResponse);
    }
    
    public function postGetscriptlookups(){
    	$CallDataProvider = new CallDataProvider();
		$ServiceResponse = new ServiceResponse();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        //$tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        //if($tokeServiceResponse->IsSuccess){
            $ServiceResponse = $CallDataProvider->GetScriptLookupForCall();
        //}else{
          //  $ServiceResponse = $tokeServiceResponse;
        //}
		return $this->GetJsonResponse($ServiceResponse);
    }
    
    public function postGetcurrentcalls(){
		$CallDataProvider = new CallDataProvider();
		$ServiceResponse = new ServiceResponse();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $tokeServiceResponse=$this->GetSessionUser($serviceRequest->Token);
        if($tokeServiceResponse->IsSuccess){
            $ServiceResponse = $CallDataProvider->GetCurrentCalls($serviceRequest->Data,$tokeServiceResponse->Data->UserID);
        }else{
            $ServiceResponse = $tokeServiceResponse;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
    public function postGethistorycalls(){
    	$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->GetHistoryCalls($serviceRequest->Data,$cUser->Data->UserID);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
    public function postEditcall(){    	
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->EditCall($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
    public function postSavecall(){    	
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->SaveCall($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
    public function postUpdatecall(){
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->UpdateCall($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
	public function postAllcalllist(){    	
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->Allcalllist($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
	public function postHidecall(){    	
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->HideCall($serviceRequest->Data->CallID);
        }else{
            $ServiceResponse = $cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
	public function postCallresultupdate(){    	
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->CallResultUpdate($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
	public function postGetallhistorycalllist(){
    	$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->GetAllHistoryCallList($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
	public function postAllcurrentcalllist(){    	
		$ServiceResponse = new ServiceResponse();
        $CallDataProvider = new CallDataProvider();
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $cUser=$this->GetSessionUser($serviceRequest->Token);
        
        if($cUser->IsSuccess){
            $ServiceResponse = $CallDataProvider->AllCurrentcalllist($serviceRequest->Data);
        }else{
            $ServiceResponse=$cUser;
        }
		return $this->GetJsonResponse($ServiceResponse);
	}
	
    /* Dev_RB region End */
}