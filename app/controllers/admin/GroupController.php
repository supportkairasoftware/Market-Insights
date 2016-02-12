<?php
use DataProviders\IGroupDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class GroupController  extends BaseController
{
    function __construct(IGroupDataProvider  $GroupDataProvider){
        $this->GroupDataProvider = $GroupDataProvider;
    }
    /* Dev_kr region Start */
	public function getGroupList()
	{
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        return View::make('admin.grouplist');
	}
    public function postgrouplist(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->getGroupList($serviceRequest->Data);

        if(count($serviceResponse->Data->GroupListArray)>0){

            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;

            foreach($serviceResponse->Data->GroupListArray as $groups){
                $groupID =Constants::$QueryStringGroupID."=".$groups->GroupID;
                $groups->EncryptGroupID=Common::getEncryptDecryptID('encrypt', $groupID);
                $groups->DisplayName = Common::GetSubString($groups->GroupName);
                $groups->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function getAddGroup($encryptedGroupID = 0)
    {
        $isEditMode = false;
        if($encryptedGroupID){
            $isEditMode = true;
        }
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        if($isEditMode){
            $decryptGroupID = Common::getEncryptDecryptValue('decrypt',$encryptedGroupID);
            $groupID =  Common::getExplodeValue($decryptGroupID,Constants::$QueryStringGroupID);
        }else{
            $groupID=0;
        }
        $serviceResponse = $this->GroupDataProvider->getGroupDetails($groupID);
        return View::make('admin.addgroup',(array)$serviceResponse->Data);
    }
    public function postSaveGroup(){

        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->SaveGroup($serviceRequest->Data,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postDeleteUserGroup()
    {
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->DeleteUserGroup($serviceRequest->Data->UserGroupID);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeleteGroup()
    {
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->DeleteGroup($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function getAddUserGroup($encryptedGroupIDUserID = 0)
    {
        $isEditMode = false;
        if($encryptedGroupIDUserID){
            $isEditMode = true;
        }
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        if($isEditMode){
            $decryptGroupID = Common::getEncryptDecryptValue('decrypt',$encryptedGroupIDUserID);
            $groupID =  Common::getExplodeValue($decryptGroupID,Constants::$QueryStringGroupID);
        }else{
            $groupID=0;
        }
        $serviceResponse = $this->GroupDataProvider->getGroupDetails($groupID);
        return View::make('admin.groupuser',(array)$serviceResponse->Data);
    }
    public function getUserGroup(){
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');

        $serviceResponse = $this->GroupDataProvider->getGroupListForSearch();

        if(!empty($serviceResponse->Data)){
            foreach($serviceResponse->Data->UserGroupModel->UserListArray as $users){
                $userID =Constants::$QueryStringUSerID."=".$users->UserID;
                $users->EncryptUserID=Common::getEncryptDecryptID('encrypt', $userID);
                $users->UserName = $users->FirstName." ".$users->LastName." - ".$users->Mobile;
            }
            //print_r($serviceResponse->Data->UserGroupModel->UserListArray);exit;
            foreach($serviceResponse->Data->UserGroupModel->GroupListArray as $groups){
                $groupID =Constants::$QueryStringGroupID."=".$groups->GroupID;
                $groups->EncryptGroupID=Common::getEncryptDecryptID('encrypt', $groupID);
            }
        }
        return View::make('admin.usergrouplist',(array)$serviceResponse->Data);
    }

    public function postUserGroup(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->getUserGroupList($serviceRequest->Data);

        if(count($serviceResponse->Data->UserGroupListArray)>0){

            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;

            foreach($serviceResponse->Data->UserGroupListArray as $groups){
                $groupID =Constants::$QueryStringGroupID."=".$groups->GroupID;
                $groups->EncryptGroupID=Common::getEncryptDecryptID('encrypt', $groupID);

                $userID =Constants::$QueryStringUSerID."=".$groups->UserID;
                $groups->EncryptUserID=Common::getEncryptDecryptID('encrypt', $userID);
                $groups->UserName = $groups->FirstName."  ".$groups->LastName;
                $groups->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function postSaveUserGroup(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->SaveUserGroup($serviceRequest->Data,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postSearchUser(){
        $serviceResponse = $this->GroupDataProvider->SearchUser(Input::get(),Auth::User()->UserID);

        if(!empty($serviceResponse->Data)){
            $userName = '<ul id="user-list">';
            foreach($serviceResponse->Data->UserListArray as $users){
                $User = $users->FirstName.' '.$users->LastName.' - '.$users->Mobile;
                /*$userName .= '<li onClick="selectUser('.$users->UserID.')">'.$users->FirstName.' '.$users->LastName.' '.$users->Mobile.'</li>';*/
                $userName .= '<li onClick="selectUser(';
                $userName .= "'".$User."',";
                $userName .= $users->UserID;
                $userName .= ')">'.$User.'</li>';
            }
            $userName .= '<ul>';
        }
        $serviceResponse->Data->UserListWithHTML = $userName;

        return $this->GetJsonResponse($serviceResponse);
    }
    /*Dev_kr region End */

    /*Dev_RB region Start*/
    public function postEnableGroup(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->GroupDataProvider->EnableGroup($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }
    /*Dev_RB region End*/
}