<?php

use DataProviders\UserDataProvider;
use DataProviders\IUserDataProvider;
use DataProviders\SecurityDataProvider;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;
use \Infrastructure\Common;
use \Infrastructure\Constants;

class MobileUserController extends BaseController {

	protected $userDataProvider;
	function __construct(){
//		$this->userDataProvider = $userDataProvider;
	}
	
    public function postViewchat(){
        $serviceResponse = new ServiceResponse();
        $userDataProvider = new UserDataProvider();
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
        	$timeStamp = property_exists($serviceRequest->Data,"TimeStamp")?$serviceRequest->Data->TimeStamp:0;
        	$isAdmin = $CUser->Data->RoleID == Constants::$RoleAdmin;
			$serviceResponse = $userDataProvider->ViewChat($isAdmin?$serviceRequest->Data->ToUserID:$CUser->Data->UserID, $isAdmin, $timeStamp);
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postSendchat(){
	    
	    $serviceResponse = new ServiceResponse();
        $userDataProvider = new UserDataProvider();
        	
	    $userImage=Input::file('UserImage')?Input::file('UserImage'):'';
        $userData = Request::input('UserData');
        
        $serviceRequest = $this->GetObjectFromJsonRequest(json_decode($userData));
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
        	$serviceRequest->Data->FromUserID = $CUser->Data->UserID;
        	$serviceResponse = $userDataProvider->SendChat($serviceRequest->Data, $userImage, $CUser->Data);
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postGetprofile(){
        $userDataProvider = new UserDataProvider();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
            $serviceResponse=$userDataProvider->GetProfile($user);
        }else{
            $serviceResponse = $user;
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postSaveprofile(){
        $userDataProvider = new UserDataProvider();

        if(Input::file('UserImage'))
            $userimage=Input::file('UserImage');
        else
            $userimage='';

        $serviceRequest=Input::all();
        $serviceRequest=$this->GetObjectFromJsonRequest(json_decode($serviceRequest['UserData']));
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
			$serviceResponse = $userDataProvider->SaveProfile($serviceRequest->Data,$userimage,$user->Data->UserID);
		}else{
			$serviceResponse = $user;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeletechat(){
	    
	    $serviceResponse = new ServiceResponse();
        $userDataProvider = new UserDataProvider();
		
		$serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
        	$serviceRequest->Data->FromUserID = $CUser->Data->UserID;
        	$serviceResponse = $userDataProvider->DeleteChat($serviceRequest->Data->ChatID, $CUser->Data->UserID);
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postUserlistchat(){
	    
	    $serviceResponse = new ServiceResponse();
        $userDataProvider = new UserDataProvider();
		
		$serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $CUser = $this->GetSessionUser($serviceRequest->Token);
        
        if($CUser->IsSuccess){
        	if($CUser->Data->RoleID == Constants::$RoleAdmin){
				$serviceResponse = $userDataProvider->UserListChat($serviceRequest->Data, $CUser->Data->UserID);
			}else{
				$serviceResponse->Message = trans("messages.UnauthorizeAction");
			}
		}else{
			$serviceResponse = $CUser;
		}
        
        return $this->GetJsonResponse($serviceResponse);
    }
   
   public function postLikeadd(){
   		$serviceResponse = new ServiceResponse();
        $userDataProvider = new UserDataProvider();
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse = $userDataProvider->Likeadd($serviceRequest->Data);        	
		}else{
			$serviceResponse = $user;
		}
        return $this->GetJsonResponse($serviceResponse);
   }
   
   	public function postSavemobile(){
        $userDataProvider = new UserDataProvider();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse = $userDataProvider->SaveMobile($serviceRequest->Data,$user);
		}else{
			$serviceResponse = $user;
		}
        $this->getSendmessage();
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postSavepassword(){
        $userDataProvider = new UserDataProvider();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $user = $this->GetSessionUser($serviceRequest->Token);
        if($user->IsSuccess){
        	$serviceResponse = $userDataProvider->SavePassword($serviceRequest->Data,$user);
		}else{
			$serviceResponse = $user;
		}
        $this->getSendmessage();
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function getSendmessage(){
    	$securityDataProvider = new SecurityDataProvider();
    	$securityDataProvider->SendMessage();
	}
	public function postErrorlog(){
		$userDataProvider = new UserDataProvider();
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $userDataProvider->SaveErrorlog($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);		
	}
}