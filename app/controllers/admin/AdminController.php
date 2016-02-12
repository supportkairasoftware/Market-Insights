<?php
use DataProviders\IAdminDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;

class AdminController  extends BaseController
{
    function __construct(IAdminDataProvider  $adminDataProvider){
    $this->AdminDataProvider = $adminDataProvider;
    }

    /* Dev_kr region Start */
	public function getDashboard(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.dashboard');
	}
    public function postDashboard(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->Dashboard($serviceRequest->Data);
             foreach($serviceResponse->Data->LastTenUser as  $users){
                 $userID =Constants::$QueryStringUSerID."=".$users->UserID;
                 $users->EncryptUserID =Common::getEncryptDecryptID('encrypt', $userID);
             }
        return $this->GetJsonResponse($serviceResponse);
    }

	public function getUserList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.userlist');
    }
    public function postUserList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->getUserList($serviceRequest->Data,Auth::user()->UserID);

        if(count($serviceResponse->Data->UserListArray)>0){

            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;

            foreach($serviceResponse->Data->UserListArray as $users){
                $userID =Constants::$QueryStringUSerID."=".$users->UserID;
                $users->EncryptUserID =Common::getEncryptDecryptID('encrypt', $userID);
                $users->DisplayName = $users->FirstName." ".$users->LastName;
                $users->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getEditUser($encryptedUserID = 0)
    {
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');

        if($encryptedUserID){
            $decryptGroupID = Common::getEncryptDecryptValue('decrypt',$encryptedUserID);
            $userID =  Common::getExplodeValue($decryptGroupID,Constants::$QueryStringUSerID);
        }else{
            $userID = Auth::user()->UserID;
        }
        $serviceResponse = $this->AdminDataProvider->getUserDetails($userID);
        return View::make('admin.edituser', (array)$serviceResponse->Data);
    }
    public function postSaveUser(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->SaveUser($serviceRequest->Data,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }
    public function getCallList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        $serviceResponse = $this->AdminDataProvider->getCallListForSearch();
        return View::make('admin.calllist',(array)$serviceResponse->Data);
    }
    public function postCallList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->getCallList($serviceRequest->Data);
        if(count($serviceResponse->Data->CallListArray)>0){
            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->CallListArray as $calls){
                /*if($calls->CreatedDate != '0000-00-00'){
                    $calls->CreatedDate= date(Constants::$DefaultDisplayDateFormat,strtotime($calls->CreatedDate));
                }*/
                if($calls->IsOpen == Constants::$Value_True){
                    $calls->IsOpen = 'Open';
                }else{
                    $calls->IsOpen = 'Closed';
                }
                $calls->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postSearchScript(){
        $serviceResponse = $this->AdminDataProvider->SearchScript(Input::get());

        if(!empty($serviceResponse->Data)){
            $ScriptHtml = '<ul id="user-list">';
            foreach($serviceResponse->Data->ScriptListArray as $segmentScript){
                $Script = $segmentScript->Script;
                $ScriptHtml .= '<li onClick="selectScript(';
                $ScriptHtml .= "'".$Script."'";
              /*  $ScriptHtml .= $segmentScript->UserID;*/
                $ScriptHtml .= ')">'.$Script.'</li>';
            }
            $ScriptHtml .= '<ul>';
        }
        $serviceResponse->Data->ScriptListWithHTML = $ScriptHtml;

        return $this->GetJsonResponse($serviceResponse);
    }

    public function getUserPaymentList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        $serviceResponse = $this->AdminDataProvider->getPaymentForSearch();

        return View::make('admin.paymentlist',(array)$serviceResponse->Data);
    }
    public function postUserPaymentList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->getUserPaymentList($serviceRequest->Data);

        if(count($serviceResponse->Data->UserPaymentListArray)>0){
            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;

            foreach($serviceResponse->Data->UserPaymentListArray as $payments){
                if($payments->StartDate != '0000-00-00'){
                    $payments->StartDate= date(Constants::$SortDisplayDateFormat,strtotime($payments->StartDate));
                }
                if($payments->EndDate != '0000-00-00'){
                    $payments->EndDate= date(Constants::$SortDisplayDateFormat,strtotime($payments->EndDate));
                }
                if($payments->IsActive == Constants::$Value_True){
                    $payments->IsActive = 'Active';
                }else{
                    $payments->IsActive = 'InActive';
                }
                if($payments->IsTrial == Constants::$Value_True){
                    $payments->IsTrial = 'Trial';
                }else{
                    $payments->IsTrial = 'Paid';
                }
                $payments->DisplayName = $payments->FirstName." ".$payments->LastName;
                $payments->Address = $payments->City;
                $payments->Address .= isset($payments->State)?", ".$payments->State:'';
                $payments->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }



    /*Dev_kr region End */

    /*Dev_RB region Start */
    public function postUpdateuser(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->UpdateUser($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getNotificationList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.notificationlist');
    }
    public function postNotificationList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->getNotificationList($serviceRequest->Data,Auth::user()->UserID);
        if(count($serviceResponse->Data->NotificationListArray)>0){
            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->NotificationListArray as $notification){
                $userID =Constants::$QueryStringUSerID."=".$notification->UserID;
                $notification->EncryptUserID =Common::getEncryptDecryptID('encrypt', $userID);
                $notification->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getSMSList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.smslist');
    }

    public function postSMSList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->getSMSList($serviceRequest->Data,Auth::user()->UserID);
        if(count($serviceResponse->Data->SMSListArray)>0){
            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->SMSListArray as $sms){
                $sms->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getAddSetting($encryptedSettingID = 0){
        $isEditMode = false;
        if($encryptedSettingID){
            $isEditMode = true;
        }

        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');

        if($isEditMode){
            $decryptSettingID = Common::getEncryptDecryptValue('decrypt',$encryptedSettingID);
            $settingID =  Common::getExplodeValue($decryptSettingID,Constants::$QueryStringSettingID);
        }else{
            $settingID=0;
        }
        $serviceResponse = $this->AdminDataProvider->getSettingDetails($settingID);
        return View::make('admin.addsetting',(array)$serviceResponse->Data);
    }
    public function postSaveSetting(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->SaveSetting($serviceRequest->Data,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getAddPayment($encryptedPaymentHistoryID = 0){
       /* $isEditMode = false;
        if($encryptedPaymentHistoryID){
            $isEditMode = true;
        }*/

        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');

      /*  if($isEditMode){
            $decryptPaymentHistoryID = Common::getEncryptDecryptValue('decrypt',$encryptedPaymentHistoryID);
            $paymentHistoryID =  Common::getExplodeValue($decryptPaymentHistoryID,Constants::$QueryStringPaymentHistoryID);
        }else{*/
        $paymentHistoryID=0;
        $serviceResponse = $this->AdminDataProvider->getPaymentDetails($paymentHistoryID);
        if($serviceResponse->Data->PaymentModel->UserListArray)
            foreach($serviceResponse->Data->PaymentModel->UserListArray as $users){
                $users->DisplayName = $users->FirstName." ".$users->LastName;
            }
        return View::make('admin.addpayment',(array)$serviceResponse->Data);
    }
    public function postSavePayment(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->SavePayment($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }
    public function getUserDeviceList(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.userdevicelist');
    }
    public function postUserDeviceList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->getUserDeviceList($serviceRequest->Data,Auth::user()->UserID);

        if(count($serviceResponse->Data->UserDeviceListArray)>0){

            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->UserDeviceListArray as $users){
                $users->DisplayName = $users->FirstName." ".$users->LastName;
                $users->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postDeleteUserDevice(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->DeleteUserDevice($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeleteuser(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->DeleteUser($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }


    /*Dev_RB region End */
    
    public function postScriptlisturl(){
		$serviceResponse = $this->AdminDataProvider->AllScriptDetails();
		return json_encode($serviceResponse->Data);
	}
	public function postUserlisturl(){
		$serviceResponse = $this->AdminDataProvider->AllUserList();
		return json_encode($serviceResponse->Data);
	}
	
	public function getErrorlog(){
		$serviceResponse = $this->AdminDataProvider->GetErrorLog();
		return View::make('error.view',(array)$serviceResponse->Data);
	}
	
	public function getSavecall(){
		$serviceResponse = $this->AdminDataProvider->SaveCall();
		return View::make('admin.addcall',(array)$serviceResponse->Data);
	}
	public function postSavecalldata(){
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
		$serviceResponse = $this->AdminDataProvider->SaveCallData($serviceRequest->Data);
		return $this->GetJsonResponse($serviceResponse);
	}
	
	function postDeleteUserPayment(){
		$serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->AdminDataProvider->DeleteUserPayment($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
	}
	
	public function postDeleteCall(){
		$serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $ServiceResponse = $this->AdminDataProvider->DeleteCall($serviceRequest->Data);
        return $this->GetJsonResponse($ServiceResponse);
	}
}