<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;

use \ViewModels\ServiceResponse;
use \ViewModels\SearchValueModel;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \Crypt;
use \Mail;
use \vwGroupUserEntity;
use \GroupEntity;
use \GroupUserEntity;
use \vwUserGroupListEntity;
use \UserEntity;
use \DB;

class GroupDataProvider extends BaseDataProvider implements IGroupDataProvider {

    /*Dev_kr Region Start*/
    public function getGroupList($groupModel){
		
        $response = new ServiceResponse();
        $model= new stdClass();
        $userGroupEntity= new vwGroupUserEntity();

        $sortIndex ='CreatedDate';
        $sortDirection=Constants::$SortIndexDESC;

        $pageIndex = $groupModel->PageIndex;
        $pageSizeCount = $groupModel->PageSize;
        if(!empty($groupModel->SortIndex)){
            $sortIndex=$groupModel->SortIndex;
            $sortDirection=$groupModel->SortDirection;
        }
		
		$customWhere = "'1'='1' AND IsDeleted = 0";
        if(isset($groupModel->SearchParams)){

            if(isset($groupModel->SearchParams["textKeyWord"])){
                $textKeyWord = $groupModel->SearchParams["textKeyWord"];
                $customWhere .= "and (GroupName like "."'%".addslashes(trim($textKeyWord))."%')";
            }
            
            if(!empty($groupModel->SearchParams["IsActive"]) && $groupModel->SearchParams["IsActive"] != Constants::$ALL){
                if($groupModel->SearchParams["IsActive"] == 'Enabled') {
                    $customWhere .= "  AND IsEnable = 1";
                }
                else{
                    $customWhere .= "  AND IsEnable = 0";
                }
            }
        }
        
        /*$searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);*/

        $groupList = $this->GetEntityWithPaging($userGroupEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $groupList->CurrentPage;
        $model->TotalPages = $groupList->TotalPages;
        $model->TotalItems = $groupList->TotalItems;
        $model->ItemsPerPage = $groupList->ItemsPerPage;
        $model->GroupListArray = $groupList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    public function getGroupDetails($groupID){
        $response = new ServiceResponse();
        $data = new stdClass();
        $groupEntity = new GroupEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="GroupID";
        $searchValueData->Value=$groupID;
        array_push($searchParams, $searchValueData);

        /*$searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);*/


        $GroupDetail=$this->GetEntity($groupEntity,$searchParams);

        $data->GroupModel = $GroupDetail;
        $response->Data = $data;
        return $response;
    }
    public function SaveGroup($groupModel,$loginUserID)
    {
        $response = new ServiceResponse();
        $isEditMode=$groupModel->GroupID>0;
        $groupEntity = new GroupEntity();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name ="GroupName";
        $searchValueData->Value = Common::GetDataWithTrim($groupModel->GroupName);
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        if ($isEditMode) {
            $customWhere = "GroupID NOT IN ($groupModel->GroupID)";
        } else {
            $customWhere = "";
        }

        $checkUniqueEmail = $this->GetEntityCount($groupEntity, $searchParams, "", "", $customWhere);
        if ($checkUniqueEmail == 0) {
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            if ($isEditMode) {
                $groupEntity = $this->GetEntityForUpdateByPrimaryKey($groupEntity, $groupModel->GroupID);
            }
            $groupEntity->GroupName= Common::GetDataWithTrim($groupModel->GroupName);
            $groupEntity->IsEnable=Constants::$IsEnableValue;
            if(!$isEditMode) {
                $groupEntity->CreatedDate = $dateTime;
                $groupEntity->CreatedBy = $loginUserID;
            }
            $groupEntity->ModifiedDate=$dateTime;
            $groupEntity->ModifiedBy = $loginUserID;

            if($this->SaveEntity($groupEntity)){
                $response->IsSuccess = true;
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
            if(!$isEditMode){
                $response->Message = trans('messages.GroupAddedSuccess');
            }else{
                $response->Message = trans('messages.GroupUpdateSuccess');
            }
        }
        else {
            $response->Message ="'". Common::GetDataWithTrim($groupModel->GroupName)."' ".trans('messages.GroupAlreadyExist');
        }
        return $response;
    }
    public function DeleteUserGroup($groupID)
    {
        $response = new ServiceResponse();
        $groupUserEntity = new GroupUserEntity();
        $this->DeleteEntity($groupUserEntity,$groupID);
        $response->IsSuccess = true;
        $response->Message = trans('messages.UserGroupDeletedSuccess');

        return $response;
    }
    
    public function DeleteGroup($groupID)
    {
        $response = new ServiceResponse();
        $groupEntity = GroupEntity::find($groupID->scalar);
        
        if($groupEntity){
		        if($groupEntity->delete()){
				DB::delete("delete from usergroups where GroupID = ".$groupID->scalar);
				$response->IsSuccess = true;
        		$response->Message = trans('messages.GroupDeletedSuccess');
			}else{
				$response->Message = trans('messages.ErrorOccured');
			}
		}else{
			$response->Message = trans('messages.ErrorOccured');
		}
		
        return $response;
    }
    
    public function getUserGroupList($userGroupModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $userGroupEntity= new vwUserGroupListEntity();
        $sortIndex='';
        $sortDirection='';
        $pageIndex = $userGroupModel->PageIndex;
        $pageSizeCount = $userGroupModel->PageSize;
        if(!empty($userGroupModel->SortIndex)){
            $sortIndex=$userGroupModel->SortIndex;
            $sortDirection=$userGroupModel->SortDirection;
        }

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        $customWhere = "";
        if(isset($userGroupModel->SearchParams)){
            if(isset($userGroupModel->SearchParams["textKeyWord"])){
                $textKeyWord = $userGroupModel->SearchParams["textKeyWord"];
                $customWhere .= "(Mobile like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Email = '".addslashes(trim($textKeyWord))."' or  ". "Name like "."'%".addslashes(trim($textKeyWord))."%')";
            }
            if(!empty($userGroupModel->SearchParams["Group"])){
                if(isset($textKeyWord)) {
                    $customWhere .= " AND ";
                }
                $group = $userGroupModel->SearchParams["Group"];
                $customWhere .= "GroupID = '".$group."'";
            }
        }
        $groupList = $this->GetEntityWithPaging($userGroupEntity,$searchParams,$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);


        $model->CurrentPage = $groupList->CurrentPage;
        $model->TotalPages = $groupList->TotalPages;
        $model->TotalItems = $groupList->TotalItems;
        $model->ItemsPerPage = $groupList->ItemsPerPage;
        $model->UserGroupListArray = $groupList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getGroupListForSearch(){
        $response = new ServiceResponse();

        $model= new stdClass();
        $GroupUserModel= new stdClass();

        $GroupEntity= new GroupEntity();
        $UserEntity= new UserEntity();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        $groupList = $this->GetEntityList($GroupEntity,$searchParams,Constants::$GroupListSortIndex,Constants::$SortIndexASC);

        $userList = $this->GetEntityList($UserEntity,$searchParams,Constants::$UserListSortIndex,Constants::$SortIndexASC);

        $GroupUserModel->GroupListArray = $groupList;
        $GroupUserModel->UserListArray = $userList;

        $model->UserGroupModel=$GroupUserModel;

        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function SaveUserGroup($groupModel,$loginUserID)
    {
        $response = new ServiceResponse();
        $userGroupEntity = new GroupUserEntity();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "UserID";
        $searchValueData->Value = $groupModel->UserID;
        array_push($searchParams, $searchValueData);

        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "GroupID";
        $searchValueData->Value = $groupModel->GroupID;
        array_push($searchParams, $searchValueData);

        $groupCount = $this->GetEntityCount($userGroupEntity,$searchParams);
        if($groupCount> 0){
            $response->IsSuccess = false;
            $response->Message = trans('messages.AlreadyInThisGroup');
        }else{

            $dateTime = date(Constants::$DefaultDateTimeFormat);
            $userGroupEntity->GroupID = $groupModel->GroupID;
            $userGroupEntity->UserID = $groupModel->UserID;
            $userGroupEntity->IsEnable=Constants::$IsEnableValue;
            $userGroupEntity->CreatedDate = $dateTime;
            $userGroupEntity->ModifiedDate=$dateTime;

            if($this->SaveEntity($userGroupEntity)){
                $response->IsSuccess = true;
                $response->Message = trans('messages.UserGroupAddedSuccess');
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
        }
        return $response;
    }
    public function SearchUser($searchModel,$loginUserID){

        $response = new ServiceResponse();
        $model= new stdClass();
        $userEntity= new UserEntity();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        $customWhere = "";
        if(isset($searchModel['keyword'])){
            $textKeyWord = $searchModel["keyword"];
            $customWhere .= "(Mobile like "."'%".trim($textKeyWord)."%'"." or "."Email = '".trim($textKeyWord)."' or  "." FirstName like "."'%".trim($textKeyWord)."%'"." or ". "LastName like "."'%".trim($textKeyWord)."%')";
        }

        $userList = $this->GetEntityList($userEntity,$searchParams,Constants::$UserListSortIndex,Constants::$SortIndexASC,$customWhere);
        $model->UserListArray=$userList;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    /*Dev_kr Region End*/
    /*Dev_RB Region Start*/
    public function EnableGroup($groupModel){
        $response = new ServiceResponse();

        $searchUserProjectsParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "GroupID";
        $searchValueData->Value = $groupModel->GroupID;
        array_push($searchUserProjectsParams, $searchValueData);

        $getGroupData = $this->GetEntityForUpdateByFilter(new GroupEntity(), $searchUserProjectsParams);

        if ($getGroupData) {
            $getGroupData->IsEnable = $groupModel->IsEnable;

            $response->Data = $this->SaveEntity($getGroupData);

            if($groupModel->IsEnable == Constants::$IsEnableValue){
                $response->Message = trans('messages.GroupEnabled');
            }else{
                $response->Message = trans('messages.GroupDisabled');
            }
        }
        if ($response)
            $response->IsSuccess = true;
        else {
            $response->IsSuccess = false;
            $response->Message = trans('messages.ErrorOccured');
        }
        return $response;
    }
    /*Dev_RB Region End*/}