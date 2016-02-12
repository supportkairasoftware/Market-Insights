<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use \ViewModels\ServiceResponse;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \ViewModels\SearchValueModel;
use \UserEntity;
use \UserNotificationEntity;
use \NotificationEntity;
use \vwUserListEntity;
use \RoleEntity;
use \GroupEntity;
use \PlanEntity;
use \ResultEntity;
use \UserRoleEntity;
use \SegmentEntity;
use \vwCallListEntity;
use \SegmentScriptEntity;
use \vwUserPaymentEntity;
use \PaymentEntity;
use \vwNotificationListEntity;
use \vwSMSListEntity;
use \SettingEntity;
use \PaymentPlansHistoryEntity;
use \vwUserDevicesEntity;
use \UserDevicesEntity;
use CallEntity;
use \UserNewsEntity;

class AdminDataProvider extends BaseDataProvider implements IAdminDataProvider {

    public function getUserList($userModel,$logedUserID){

        $response = new ServiceResponse();
        $model= new stdClass();
        $userEntity= new vwUserListEntity();

        $sortIndex ='CreatedDate';
        $sortDirection=Constants::$SortIndexDESC;

        $pageIndex = $userModel->PageIndex;
        $pageSizeCount = $userModel->PageSize;
        if(!empty($userModel->SortIndex)){
            $sortIndex=$userModel->SortIndex;
            $sortDirection=$userModel->SortDirection;
        }

       $customWhere = "'1'='1' AND IsDeleted = 0";

        if(isset($userModel->SearchParams)) {
            if (isset($userModel->SearchParams["Name"])) {
                $firstName = $userModel->SearchParams["Name"];
                $customWhere .= " AND Name like "."'%".addslashes(trim($firstName))."%'";
            }
            if(!empty($userModel->SearchParams["Email"])){
                if($userModel->SearchParams["Email"]) {
                    $email = $userModel->SearchParams["Email"];
                    $customWhere .= "  AND Email like "."'%".addslashes(trim($email))."%'  ";
                }
            }
            if(!empty($userModel->SearchParams["City"])){
                if($userModel->SearchParams["City"]) {
                    $city = $userModel->SearchParams["City"];
                    $customWhere .= "  AND City like "."'%".addslashes(trim($city))."%'  ";
                }
            }
            if(!empty($userModel->SearchParams["State"])){
                if($userModel->SearchParams["State"]) {
                    $state = $userModel->SearchParams["State"];
                    $customWhere .= "  AND State like "."'%".addslashes(trim($state))."%'  ";
                }
            }
            if(!empty($userModel->SearchParams["Mobile"])){
                if($userModel->SearchParams["Mobile"]) {
                    $mobile = $userModel->SearchParams["Mobile"];
                    $customWhere .= "  AND Mobile like "."'%".addslashes(trim($mobile))."%'  ";
                }
            }
            if(!empty($userModel->SearchParams["PlanName"])){
                if($userModel->SearchParams["PlanName"]) {
                    $planName = $userModel->SearchParams["PlanName"];
                    $customWhere .= "  AND PlanName like "."'".$planName."'  ";
                }
            }
            if(!empty($userModel->SearchParams["GroupID"])){
                if($userModel->SearchParams["GroupID"]) {
                    $groupID = $userModel->SearchParams["GroupID"];
                    $customWhere .= " AND GroupIDs like _utf8 "."'%,".trim($groupID).",%' COLLATE utf8_general_ci ";
                }
            }

            if(!empty($userModel->SearchParams["RoleID"])){
                if($userModel->SearchParams["RoleID"]) {
                    $roleID = $userModel->SearchParams["RoleID"];
                    $customWhere .= " AND RoleID like _utf8 "."'%,".trim($roleID).",%' COLLATE utf8_general_ci ";
                }
            }
            
            if(!empty($userModel->SearchParams["FromDate"]) && !empty($userModel->SearchParams["ToDate"])){
                $fromName = $userModel->SearchParams["FromDate"];
                $toName = $userModel->SearchParams["ToDate"];
                $customWhere .= "  AND DATE(CreatedDate) BETWEEN '".$fromName."' AND '".$toName."'";
            }else if(!empty($userModel->SearchParams["FromDate"])){
                $FromDate = $userModel->SearchParams["FromDate"];
                $customWhere .= " AND DATE(CreatedDate) >=  '".$FromDate."'";
            }else if(!empty($userModel->SearchParams["ToDate"])){
                $ToDate = $userModel->SearchParams["ToDate"];
                $customWhere .= " AND DATE(CreatedDate) <=  '".$ToDate."'";
            }
        }
		
        $userList = $this->GetEntityWithPaging($userEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $roleEntity = new RoleEntity();
        $roleList = $this->GetEntityList($roleEntity,"");

        $planEntity = new PlanEntity();
        $planList = $this->GetEntityList($planEntity,"");

        $groupEntity = new GroupEntity();
        $groupList = $this->GetEntityList($groupEntity,"");

        $model->CurrentPage = $userList->CurrentPage;
        $model->TotalPages = $userList->TotalPages;
        $model->TotalItems = $userList->TotalItems;
        $model->ItemsPerPage = $userList->ItemsPerPage;
        $model->UserListArray = $userList->Items;
        $model->RoleListArray = $roleList;
        $model->PlanListArray = $planList;
        $model->GroupListArray = $groupList;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getUserDetails($userID){
        $response = new ServiceResponse();
        $data = new stdClass();
        $userEntity = new vwUserListEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="UserID";
        $searchValueData->Value=$userID;
        array_push($searchParams, $searchValueData);

        /*$searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);*/

        $UserDetail = $this->GetEntity($userEntity,$searchParams);
        $UserDetail->RoleID = str_replace(",","",$UserDetail->RoleID);

        $roleEntity = new RoleEntity();
        $roleList = $this->GetEntityList($roleEntity,"");

        $data->UserModel = $UserDetail;
        $data->UserModel->RoleListArray = $roleList;
        $response->Data = $data;

        return $response;
    }
    public function SaveUser($userModel,$loginUserID)
    {
        $response = new ServiceResponse();
        $userEntity = new UserEntity();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name ="Email";
        $searchValueData->Value = Common::GetDataWithTrim($userModel->Email);
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name ="Mobile";
        $searchValueData->Value = Common::GetDataWithTrim($userModel->Mobile);
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        $customWhere = "UserID NOT IN ($userModel->UserID)";
        $checkUniqueEmail = $this->GetEntityCount($userEntity, $searchParams, "", "", $customWhere);
        if ($checkUniqueEmail == 0) {
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            $userEntity = $this->GetEntityForUpdateByPrimaryKey($userEntity, $userModel->UserID);

            $userEntity->FirstName= Common::GetDataWithTrim($userModel->FirstName);
            $userEntity->LastName= Common::GetDataWithTrim($userModel->LastName);
            $userEntity->Mobile= Common::GetDataWithTrim($userModel->Mobile);
            $userEntity->Email= Common::GetDataWithTrim($userModel->Email);
            $userEntity->IsVerified= property_exists($userModel,'IsVerified')?$userModel->IsVerified:$userEntity->IsVerified;

            if (!empty($userModel->City))
                $userEntity->City= Common::GetDataWithTrim($userModel->City);
            if (!empty($userModel->State))
                $userEntity->State= Common::GetDataWithTrim($userModel->State);

            if (!empty($userModel->TempPassword))
                $userEntity->Password= md5($userModel->TempPassword);

            $userEntity->IsEnable=Constants::$IsEnableValue;
            $userEntity->ModifiedDate=$dateTime;


            $searchParams = array();
            $searchValueData = new SearchValueModel();
            $searchValueData->Name ="UserID";
            $searchValueData->Value = Common::GetDataWithTrim($userModel->UserID);
            array_push($searchParams, $searchValueData);

            $RoleEntity = $this->GetEntityForUpdateByFilter(new UserRoleEntity(),$searchParams);
            $RoleEntity->RoleID= Common::GetDataWithTrim($userModel->RoleID);
            $this->SaveEntity($RoleEntity);

            if($this->SaveEntity($userEntity)){
                $response->IsSuccess = true;
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
            if($loginUserID == $userModel->UserID){
                $response->Message = trans('messages.ProfileUpdateSuccess');
            }else{
                $response->Message = trans('messages.UserUpdateSuccess');
            }
        }
        else {
            $response->Message ="'". Common::GetDataWithTrim($userModel->Email)."'"."  OR  "."' $userModel->Mobile' ".trans('messages.UserAlreadyExist');
        }
        return $response;
    }

    public function UpdateUser($userModel){
        $response = new ServiceResponse();

        $searchUserProjectsParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "UserID";
        $searchValueData->Value = $userModel->UserID;
        array_push($searchUserProjectsParams, $searchValueData);

        $getUserData = $this->GetEntityForUpdateByFilter(new UserEntity(), $searchUserProjectsParams);

        if ($getUserData) {
            $getUserData->IsEnable = $userModel->IsEnable;

            $response->Data = $this->SaveEntity($getUserData);

            if($userModel->IsEnable == Constants::$IsEnableValue){
                $response->Message = trans('messages.UserEnabled');
            }else{
                $response->Message = trans('messages.UserDisabled');
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

    public function getCallListForSearch(){
        $response = new ServiceResponse();

        $model= new stdClass();
        $callListModel= new stdClass();

        $SegmentEntity= new SegmentEntity();
        $ResultEntity= new ResultEntity();

        $segmentList = $this->GetEntityList($SegmentEntity,"",Constants::$SegmentListSortIndex,Constants::$SortIndexASC);

        $resultList = $this->GetEntityList($ResultEntity,"",Constants::$ResultListSortIndex,Constants::$SortIndexASC);

        $callListModel->SegmentListArray = $segmentList;
        $callListModel->ResultListArray = $resultList;
        $model->CallModel=$callListModel;

        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getCallList($callModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $CallListEntity= new vwCallListEntity();
        $sortIndex='CreatedDate';
        $sortDirection='Desc';

        $pageIndex = $callModel->PageIndex;
        $pageSizeCount = $callModel->PageSize;
        if(!empty($callModel->SortIndex)){
            $sortIndex=$callModel->SortIndex;
            $sortDirection=$callModel->SortDirection;
        }

        //$customWhere = "";
        $customWhere = "'1'='1'";
        if(isset($callModel->SearchParams)){

            if (isset($callModel->SearchParams["Script"])) {
                $Script = $callModel->SearchParams["Script"];
                $customWhere .= " AND Script like "."'%".addslashes($Script)."%'";
            }
            if(!empty($callModel->SearchParams["Action"])){
                if($callModel->SearchParams["Action"]) {
                    $Action = $callModel->SearchParams["Action"];
                    $customWhere .= "  AND Action like "."'%".trim($Action)."%'  ";
                }
            }
            if(!empty($callModel->SearchParams["IsOpen"])){
                if($callModel->SearchParams["IsOpen"] == 'Open') {
                    $customWhere .= "  AND IsOpen = '1'";
                }
                else{
                    $customWhere .= "  AND IsOpen = '0'";
                }
            }
            if(!empty($callModel->SearchParams["SegmentID"])){
                if($callModel->SearchParams["SegmentID"]) {
                    $SegmentID = $callModel->SearchParams["SegmentID"];
                    $customWhere .= "  AND SegmentID = ".$SegmentID;
                }
            }
            if(!empty($callModel->SearchParams["ResultID"])){
                if($callModel->SearchParams["ResultID"]) {
                    $ResultID = $callModel->SearchParams["ResultID"];
                    $customWhere .= "  AND ResultID = ".trim($ResultID);
                }
            }
            if((!empty($callModel->SearchParams["FromDate"])) && (isset($callModel->SearchParams["ToDate"]))){
                if($callModel->SearchParams["FromDate"]) {
                    $fromName = $callModel->SearchParams["FromDate"];
                    $toName = $callModel->SearchParams["ToDate"];
                    $customWhere .= "  AND CreatedDate BETWEEN '".$fromName."' AND '".$toName."'";
                }
            }else if(!empty($callModel->SearchParams["FromDate"])){
                if($callModel->SearchParams["FromDate"]) {
                    $FromDate = $callModel->SearchParams["FromDate"];
                    $customWhere .= "AND CreatedDate =  '".$FromDate."'";
                }
            }
        }
        $callList = $this->GetEntityWithPaging($CallListEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);
        if($callList && count($callList->Items)>0){
			foreach($callList->Items as $action){
				$action->Action = Constants::$CallActionsENUM[$action->Action];
                $action->T1 = round($action->T1,Constants::$DecimalValue);
                $action->T2 = round($action->T2,Constants::$DecimalValue);
                $action->SL = round($action->SL,Constants::$DecimalValue);
			}
		}
        $model->CurrentPage = $callList->CurrentPage;
        $model->TotalPages = $callList->TotalPages;
        $model->TotalItems = $callList->TotalItems;
        $model->ItemsPerPage = $callList->ItemsPerPage;
        $model->CallListArray = $callList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;

    }
    public function SearchScript($searchModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $segmentScriptEntity= new SegmentScriptEntity();

        $customWhere = "";
        if(isset($searchModel['script'])){
            $textKeyWord = $searchModel["script"];
            $customWhere .= "(Script like "."'%".trim($textKeyWord)."%')";
        }

        $userList = $this->GetEntityList($segmentScriptEntity,"",Constants::$ScriptListSortIndex,Constants::$SortIndexASC,$customWhere);
        $model->ScriptListArray=$userList;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getPaymentForSearch(){
        $response = new ServiceResponse();

        $model= new stdClass();
        $PaymentListModel= new stdClass();
        $paymentEntity= new PaymentEntity();
        $planEntity = new PlanEntity();

        $refNoList = $this->GetEntityList($paymentEntity,"",Constants::$RefNoListSortIndex,Constants::$SortIndexASC);

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        $planList = $this->GetEntityList($planEntity,$searchParams,Constants::$PlanListSortIndex,Constants::$SortIndexASC);

        $PaymentListModel->RefNoListArray = $refNoList;
        $PaymentListModel->PlanListArray = $planList;
        $model->PaymentModel=$PaymentListModel;

        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getUserPaymentList($paymentModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $userPaymentEntity= new vwUserPaymentEntity();
        $sortIndex='StartDate';
        $sortDirection=Constants::$SortIndexDESC;

        $pageIndex = $paymentModel->PageIndex;
        $pageSizeCount = $paymentModel->PageSize;
        if(!empty($paymentModel->SortIndex)){
            $sortIndex=$paymentModel->SortIndex;
            $sortDirection=$paymentModel->SortDirection;
        }

        //$customWhere = "";
        $customWhere = "'1'='1'";
        if(isset($paymentModel->SearchParams)){

            if(isset($paymentModel->SearchParams["textKeyWord"])){
                $textKeyWord = $paymentModel->SearchParams["textKeyWord"];
                $customWhere .= "and (Mobile like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Email = '".addslashes(trim($textKeyWord))."' or ". "Name like "."'%".addslashes(trim($textKeyWord))."%')";
            }
            if(!empty($paymentModel->SearchParams["PlanName"])){
                if($paymentModel->SearchParams["PlanName"]) {
                    $PlanName = $paymentModel->SearchParams["PlanName"];
                    $customWhere .= "  AND PlanName like "."'".addslashes(trim($PlanName))."'  ";
                }
            }
            if(!empty($paymentModel->SearchParams["ReferenceNo"])){
                if($paymentModel->SearchParams["ReferenceNo"]) {
                    $ReferenceNo = $paymentModel->SearchParams["ReferenceNo"];
                    $customWhere .= "  AND ReferenceNo like "."'%".addslashes(trim($ReferenceNo))."%'  ";
                }
            }
            if(!empty($paymentModel->SearchParams["IsActive"])){
                if($paymentModel->SearchParams["IsActive"] == 'Active') {
                    $customWhere .= "  AND IsActive = 1";
                }
                else{
                    $customWhere .= "  AND IsActive = 0";
                }
            }
        }
        $PaymentList = $this->GetEntityWithPaging($userPaymentEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $PaymentList->CurrentPage;
        $model->TotalPages = $PaymentList->TotalPages;
        $model->TotalItems = $PaymentList->TotalItems;
        $model->ItemsPerPage = $PaymentList->ItemsPerPage;
        $model->UserPaymentListArray = $PaymentList->Items;
       /* print_r($model->UserPaymentListArray);
        exit;*/
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;

    }


    /*Dev_RB Region Start*/
    public function getNotificationList($notificationModel,$logedUserID){
        $response = new ServiceResponse();
        $model= new stdClass();

        $notificationEntity = new vwNotificationListEntity();

        $sortIndex ='SentDate';
        $sortDirection=Constants::$SortIndexDESC;

        $pageIndex = $notificationModel->PageIndex;
        $pageSizeCount = $notificationModel->PageSize;
        if(!empty($notificationModel->SortIndex)){
            $sortIndex=$notificationModel->SortIndex;
            $sortDirection=$notificationModel->SortDirection;
        }

        $customWhere = "'1'='1' ";

	    if(isset($notificationModel->SearchParams)){
	            if(isset($notificationModel->SearchParams["textKeyWord"])){
	                $textKeyWord = $notificationModel->SearchParams["textKeyWord"];
	                //$customWhere .= " AND (  City like "."'%".trim($textKeyWord)."%'"." or "."State like "."'%".trim($textKeyWord)."%'"." or "."Mobile like "."'%".trim($textKeyWord)."%'"." or "."Email = '".trim($textKeyWord)."' or  "." FirstName like "."'%".trim($textKeyWord)."%'"." or ". "LastName like "."'%".trim($textKeyWord)."%')";
                    $customWhere .= " AND (  City like "."'%".addslashes(trim($textKeyWord))."%'"." or "."State like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Mobile like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Email = '".addslashes(trim($textKeyWord))."' or  "." FirstName like "."'%".addslashes(trim($textKeyWord))."%'"." or ". "LastName like "."'%".addslashes(trim($textKeyWord))."%'"."or ". "Name like "."'%".addslashes(trim($textKeyWord))."%')";
	            }
	            if(isset($notificationModel->SearchParams["Action"])){
	                $s = $notificationModel->SearchParams["Action"];
	                $customWhere .= " AND IsSent = ".$s;
	            }

                if(!empty($notificationModel->SearchParams["startDate"]) && !empty($notificationModel->SearchParams["endDate"])){
	                $startDate = $notificationModel->SearchParams["startDate"];
	                $ToDate = $notificationModel->SearchParams["endDate"];
	                $customWhere .= " AND DATE(SentDate) BETWEEN '".$startDate."' AND '".$ToDate."'";
	            }else if(!empty($notificationModel->SearchParams["startDate"])){
	                $startDate = $notificationModel->SearchParams["startDate"];
	                $customWhere .= " AND DATE(SentDate) >=  '".$startDate."'";
	            }else if(!empty($notificationModel->SearchParams["endDate"])){
	                $ToDate = $notificationModel->SearchParams["endDate"];
	                $customWhere .= " AND DATE(SentDate) <=  '".$ToDate."'";
	            }
        }

        $notificationList = $this->GetEntityWithPaging($notificationEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $notificationList->CurrentPage;
        $model->TotalPages = $notificationList->TotalPages;
        $model->TotalItems = $notificationList->TotalItems;
        $model->ItemsPerPage = $notificationList->ItemsPerPage;
        $model->NotificationListArray = $notificationList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getSMSList($smsModel,$logedUserID){

        $response = new ServiceResponse();
        $model= new stdClass();

        $smsEntity = new vwSMSListEntity();

        $sortIndex ='SentDate';
        $sortDirection=Constants::$SortIndexDESC;

        $pageIndex = $smsModel->PageIndex;
        $pageSizeCount = $smsModel->PageSize;
        if(!empty($smsModel->SortIndex)){
            $sortIndex = $smsModel->SortIndex;
            $sortDirection = $smsModel->SortDirection;
        }


        $customWhere = "'1'='1' ";

        if(isset($smsModel->SearchParams)){
            if(isset($smsModel->SearchParams["textKeyWord"])){
                $textKeyWord = $smsModel->SearchParams["textKeyWord"];
                $customWhere .= " AND (  City like "."'%".addslashes(trim($textKeyWord))."%'"." or "."State like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Mobile like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Email = '".addslashes(trim($textKeyWord))."' or  "." FirstName like "."'%".addslashes(trim($textKeyWord))."%'"." or ". "LastName like "."'%".addslashes(trim($textKeyWord))."%'"."or ". "Name like "."'%".addslashes(trim($textKeyWord))."%')";
            }
            if(isset($smsModel->SearchParams["Action"])){
                $s = $smsModel->SearchParams["Action"];
                $customWhere .= " AND IsSent = ".$s;
            }

            if((!empty($smsModel->SearchParams["startDate"])) && (isset($smsModel->SearchParams["endDate"]))){
                if($smsModel->SearchParams["startDate"]) {
                    $from = $smsModel->SearchParams["startDate"];
                    $to = $smsModel->SearchParams["endDate"];
                    $customWhere .= " AND SentDate BETWEEN '".$from."' AND '".$to."'";
                }
            }else if(!empty($smsModel->SearchParams["startDate"])){
                if($smsModel->SearchParams["startDate"]) {
                    $FromDate = $smsModel->SearchParams["startDate"];
                    $customWhere .= " AND SentDate LIKE '".$FromDate."%'";
                }
            }
        }

        $smsList = $this->GetEntityWithPaging($smsEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $smsList->CurrentPage;
        $model->TotalPages = $smsList->TotalPages;
        $model->TotalItems = $smsList->TotalItems;
        $model->ItemsPerPage = $smsList->ItemsPerPage;
        $model->SMSListArray = $smsList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    public function getSettingDetails($settingID){
        $response = new ServiceResponse();
        $data = new stdClass();

        $settingEntity = new SettingEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name ="SettingID";
        $searchValueData->Value = $settingID;
        array_push($searchParams, $searchValueData);

        $settingDetail = $this->GetEntity($settingEntity,$searchParams);

        $data->SettingModel = $settingDetail;
        $response->Data = $data;
        return $response;
    }

    public function SaveSetting($settingModel){

        $response = new ServiceResponse();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "SettingID";
        $searchValueData->Value = $settingModel->SettingID;
        array_push($searchParams, $searchValueData);

        $settingEntity = $this->GetEntityForUpdateByFilter(new SettingEntity(), $searchParams);

        if ($settingEntity) {
            $settingEntity->SMSUrl = Common::GetDataWithTrim($settingModel->SMSUrl);
            $settingEntity->SMSUserName = Common::GetDataWithTrim($settingModel->SMSUserName);
            $settingEntity->SMSPassword = $settingModel->SMSPassword;
            $settingEntity->SMSTemplates = Common::GetDataWithTrim($settingModel->SMSTemplates);

            $settingEntity->AccountName = Common::GetDataWithTrim($settingModel->AccountName);
            $settingEntity->AccountNumber = Common::GetDataWithTrim($settingModel->AccountNumber);
            $settingEntity->BranchName = Common::GetDataWithTrim($settingModel->BranchName);
            $settingEntity->IFSCCode = Common::GetDataWithTrim($settingModel->IFSCCode);
            $settingEntity->SenderID = Common::GetDataWithTrim($settingModel->SenderID);

            $response->Data = $this->SaveEntity($settingEntity);
            $response->IsSuccess = true;
            $response->Message = trans('messages.Setting');
        } else {
            $response->IsSuccess = false;
            $response->Message = trans('messages.ErrorOccured');
        }
        return $response;
    }


    public function getPaymentDetails($paymentHistoryID){

        $response = new ServiceResponse();
        $model = new stdClass();

        $PaymentModel = new stdClass();
        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name ="PaymentHistoryID";
        $searchValueData->Value = $paymentHistoryID;
        array_push($searchParams, $searchValueData);

        $paymentDetail = $this->GetEntity(new PaymentPlansHistoryEntity(),$searchParams);

        $searchUserParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name ="IsEnable";
        $searchValueData->Value = Constants::$Value_True;
        array_push($searchUserParams, $searchValueData);
        $userList = $this->GetEntityList(new UserEntity(),$searchUserParams);
        $planList = $this->GetEntityList(new PlanEntity(),$searchUserParams);
        if($planList)
            foreach($planList as $plan)
            {
                $discount = ($plan->Amount * $plan->Discount) / 100;
                $subscriptionAmount = $plan->Amount - $discount;
                $plan->SubscriptionAmount = $subscriptionAmount;
            }


        $PaymentModel->UserListArray = $userList;
        $PaymentModel->PlanListArray = $planList;
        $PaymentModel->PaymentModel = $paymentDetail;
        $model->PaymentModel = $PaymentModel;
        $response->Data = $model;

        return $response;
    }

    public function SavePayment($paymentModel){
        $response = new ServiceResponse();

        $paymentEntity = new PaymentPlansHistoryEntity();

        $validator = Validator::make((array)$paymentModel, $paymentEntity::$AddPayment_rules );
        $validator->setAttributeNames($paymentEntity::$niceNameArray);
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name ="PlanID";
        $searchValueData->Value = $paymentModel->PlanID;
        array_push($searchParams, $searchValueData);

        $planDetails = $this->GetEntity(new PlanEntity(),$searchParams);

        $dateTime = date(Constants::$DefaultDateTimeFormat);
        $planName = $planDetails->PlanName;
        $discount = ($planDetails->Amount * $planDetails->Discount) / 100;
        $amount = $planDetails->Amount - $discount;
        $noOfDays = ($planDetails->NoOfDays) -1 ;
        $startDate = $paymentModel->StartDate;
        $trial = $planDetails->IsTrial;
        $endDate = date(Constants::$DefaultDateTimeFormat, strtotime($startDate."+".$noOfDays." days"));

        if($planDetails){
            $paymentEntity->UserID = $paymentModel->UserID;
            $paymentEntity->SubscriptionAmount = $paymentModel->SubscriptionAmount;
            $paymentEntity->Amount = $amount;
            $paymentEntity->PlanName = Common::GetDataWithTrim($planName);
            $paymentEntity->NoOfDays = $planDetails->NoOfDays;
            $paymentEntity->StartDate = $startDate;
            $paymentEntity->EndDate = $endDate;
            $paymentEntity->PaymentDate = $dateTime;
            $paymentEntity->IsTrial = $trial;
            
            $dateTime = date("Y-m-d");
            if(strtotime($startDate)<=strtotime($dateTime)){
				$paymentEntity->IsActive = Constants::$Value_True;
			}else{
				$paymentEntity->IsActive = Constants::$Value_False;
			}
            
            $lastPlan = DB::table('paymentplanshistory')->where("UserID", $paymentModel->UserID)->orderBy("EndDate","desc")->first();
            if(empty($lastPlan) || ($lastPlan && strtotime($lastPlan->EndDate)<strtotime($startDate))){
            	$response->Data = $this->SaveEntity($paymentEntity);
	            $response->IsSuccess = true;
	            $response->Message = trans('messages.addpayment');
	            
	            if($paymentEntity->IsActive){
					$userNotificationEntity = new UserNotificationEntity();
			    	$userNotificationEntity->Description = trans("messages.PlanActivated", array('startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentEntity->EndDate))));
			    	$userNotificationEntity->UserID = $paymentEntity->UserID;
			    	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
			    	$userNotificationEntity->save();
			    	
			    	
			    	$notificationEntity = new NotificationEntity();
					$notificationEntity->UserID = $paymentEntity->UserID;
					$notificationEntity->NotificationType = Constants::$NotificationType['General'];
					$notificationEntity->Message = trans("messages.PlanActivated", array('startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentEntity->EndDate))));
					$notificationEntity->Key = (int)Constants::$PaidGroupID;
					$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
					$notificationEntity->save();
				}
            }else{
				$response->Message = "Your Last Plan will end on ".date(Constants::$DefaultDisplayDateFormat,strtotime($lastPlan->EndDate)).", Please select future date than this";
			}
        }
         else {
            $response->IsSuccess = false;
            $response->Message = trans('messages.ErrorOccured');
        }
        return $response;
    }

    public function Dashboard(){
        $response = new ServiceResponse();
        $model= new stdClass();

        $totalUser = UserEntity::where("IsDeleted",0)->count();//$this->GetEntityCount(new UserEntity(),"");

        $totalEarning = DB::select("SELECT SUM(pph.SubscriptionAmount) AS Amount, YEAR(PaymentDate) AS `Year` FROM paymentplanshistory pph where IsTrial = 0 GROUP BY YEAR(PaymentDate)");//PaymentPlansHistoryEntity::all()->sum("SubscriptionAmount");

        $totalPaidUsers = PaymentPlansHistoryEntity::where("IsTrial",0)->where("isActive",1)->count();

        $totalTrialUsers = PaymentPlansHistoryEntity::where("IsTrial",1)->where("isActive",1)->count();

        $lastTenUser = $this->RunQueryStatement("SELECT * FROM users ORDER BY CreatedDate DESC LIMIT 10",Constants::$QueryType_Select);

        $lastTenPayment = $this->RunQueryStatement("SELECT u.UserID,u.FirstName,u.LastName,u.City ,ph.IsActive,u.Mobile , ph.Amount ,ph.SubscriptionAmount,ph.PaymentDate FROM paymentplanshistory ph
        LEFT JOIN users u ON u.UserID = ph.UserID
        WHERE  ph.IsTrial = 0
        ORDER BY PaymentDate DESC LIMIT 10",Constants::$QueryType_Select);

		if($totalEarning && count($totalEarning)>0){
			foreach($totalEarning as $perYear){
				$perYear->Amount = Common::moneyFormatIndia((int)$perYear->Amount);
			}
		}else{
			$totalEarning = array();
		}
			
        //$totalEarning = Common::moneyFormatIndia((int)$totalEarning);
        $model->TotalUsers = $totalUser;
        $model->TotalEarning = $totalEarning;
        $model->TotalPaidUsers = $totalPaidUsers;
        $model->TotalTrialUsers = $totalTrialUsers;
        $model->LastTenUser = $lastTenUser;
        $model->LastTenPayment = $lastTenPayment;

        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }
    public function getUserDeviceList($userModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $userEntity= new vwUserDevicesEntity();

        $sortIndex ='UserDeviceID';
        $sortDirection = Constants::$SortIndexDESC;

        $pageIndex = $userModel->PageIndex;
        $pageSizeCount = $userModel->PageSize;
        if(!empty($userModel->SortIndex)){
            $sortIndex=$userModel->SortIndex;
            $sortDirection=$userModel->SortDirection;
        }

        $customWhere = "'1'='1'";

        if(isset($userModel->SearchParams)){
            if(isset($userModel->SearchParams["textKeyWord"])){
                $textKeyWord = $userModel->SearchParams["textKeyWord"];
                $customWhere .= " AND ( City like "."'%".addslashes(trim($textKeyWord))."%'"." or "."DeviceID like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Mobile like "."'%".addslashes(trim($textKeyWord))."%'"." or "."Email = '".addslashes(trim($textKeyWord))."' or  "." FirstName like "."'%".addslashes(trim($textKeyWord))."%'"." or ". "LastName like "."'%".addslashes(trim($textKeyWord))."%'"." or ". "Name like "."'%".addslashes(trim($textKeyWord))."%')";

            }
        }

        $userdeviceList = $this->GetEntityWithPaging($userEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $userdeviceList->CurrentPage;
        $model->TotalPages = $userdeviceList->TotalPages;
        $model->TotalItems = $userdeviceList->TotalItems;
        $model->ItemsPerPage = $userdeviceList->ItemsPerPage;
        $model->UserDeviceListArray = $userdeviceList->Items;

        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    public function DeleteUserDevice($userDeviceID){
        $response = new ServiceResponse();
        $this->DeleteEntity(new UserDevicesEntity(),$userDeviceID->scalar);

        $response->IsSuccess = true;
        $response->Message = trans('messages.UserDeviceDelete');
        return $response;
    }
    
    public function DeleteUser($userID){
        //print_r($userID);exit;

        $response = new ServiceResponse();
        $userEntity = UserEntity::find($userID->scalar);

		if($userEntity->delete()){
            /* remove User Device, news,notifications,role */
            DB::delete("delete from userdevices where UserID = ".$userID->scalar);
            DB::delete("delete from usernotifications where UserID = ".$userID->scalar);
            DB::delete("delete from userroles where UserID = ".$userID->scalar);
            DB::delete("delete from usernews where UserID = ".$userID->scalar);

            $response->IsSuccess = true;
        	$response->Message = trans('messages.UserDeleted');	
		}else{
			$response->Message = trans('messages.ErrorOccured');	
		}
        
        return $response;
    }

    /*Dev_RB Region End*/
    
    /*All Script List TypeAhead*/
    public function AllScriptDetails(){
    	$response = new ServiceResponse();
		$paymentDetail = DB::table('scripts')->select('ScriptID as id','Script as name')->where('IsEnable',Constants::$Value_True)->get();
		$response->IsSuccess = true;
		$response->Data=$paymentDetail;
		return $response;
	}
	
	/*All User List TypeAhead*/
	public function AllUserList(){
    	$response = new ServiceResponse();
		$paymentDetail = DB::table('users')->select('UserID as id', DB::raw('CONCAT(FirstName, " ", LastName, " (",IFNULL(Mobile,"")," - ",IFNULL(City,""),")") AS name'))->where('IsEnable',Constants::$Value_True)->get();
		$response->IsSuccess = true;
		$response->Data=$paymentDetail;
		return $response;
	}
	
	public function SaveCall(){
		$response= new ServiceResponse();
		$model = new stdClass();
		$scriptList=DB::table('scripts')->select('scripts.*','lu_segments.SegmentName', DB::raw('CONCAT(Script, "-", SegmentName) AS ScriptFull'))->leftJoin('lu_segments','lu_segments.SegmentID','=','scripts.SegmentID')->where('IsEnable',Constants::$Value_True)->get();
		$model->ScriptList=$scriptList;
		$response->Data=$model;
		return $response;
	}
	
	public function SaveCallData($callModel){
		$date = date(Constants::$DefaultDisplayDateTimeFormat);
		$response=new ServiceResponse();
		$callEntity= new CallEntity;
		$callEntity->ScriptID=$callModel->ScriptID;
		$callEntity->Action=$callModel->Action;
		$callEntity->InitiatingPrice=$callModel->InitiatingPrice;
		$callEntity->T1=property_exists($callModel,'T1')?round($callModel->T1,Constants::$DecimalValue):'';
		$callEntity->T2=property_exists($callModel,'T2')?round($callModel->T2,Constants::$DecimalValue):'';
		$callEntity->SL=property_exists($callModel,'SL')?round($callModel->SL,Constants::$DecimalValue):'';
		$resultDes='';
		foreach($callModel->ResultDescription as $ResultID){
			switch ($ResultID) {
			    case Constants::$T1Call:
			    	$date=property_exists($callModel,'t1date')?date_format(date_create($callModel->t1date),Constants::$DefaultDisplayDateTimeFormat):$date;
			        $resultDescription="T1 Achieved, $date ";
			        $callEntity->ResultID = $ResultID;
			        break;
			    case Constants::$T2Call:
			 	   	$date=property_exists($callModel,'t2date')?date_format(date_create($callModel->t2date),Constants::$DefaultDisplayDateTimeFormat):$date;
			        $resultDescription="T2 Achieved, $date ";
			        $callEntity->ResultID = $ResultID;
			        $callEntity->IsOpen = Constants::$Value_False;
			        break;
			    case Constants::$SLCall:
			  		$date=property_exists($callModel,'sldate')?date_format(date_create($callModel->sldate),Constants::$DefaultDisplayDateTimeFormat):$date;
			        $resultDescription="SL Hit, $date ";
			        $callEntity->ResultID = $ResultID;
			        $callEntity->IsOpen = Constants::$Value_False;
			        break;
			    case Constants::$ParcelBooked:
			    	$date=property_exists($callModel,'partialdate')?date_format(date_create($callModel->partialdate),Constants::$DefaultDisplayDateTimeFormat):$date;
			    	$resutvalue=property_exists($callModel,'PartialValue')?$callModel->PartialValue:'';
			        $resultDescription="Partial booked at $resutvalue , $date";
			        //$calldetails->IsOpen = Constants::$Value_False;
			        break;
			    case Constants::$CallClosed:
			    	$date=property_exists($callModel,'closedate')?date_format(date_create($callModel->closedate),Constants::$DefaultDisplayDateTimeFormat):$date;
			    	$resutvalue=property_exists($callModel,'CloseValue')?$callModel->CloseValue:'';
			        $resultDescription="Call closed at $resutvalue , $date";
			        $callEntity->IsOpen = Constants::$Value_False;
			        break;
			    default:
			    	echo "not update call details";
			        break;
			}
			
			//$callEntity->ResultDescription .= empty($resultDes)? $resultDescription: '|'.$resultDescription;
			$callEntity->ResultDescription .= empty($resultDes)? $resultDescription: "\r\n$resultDescription";	
			$resultDes=$resultDescription;
			$callEntity->ResultID=$ResultID;
		}
		
		$callEntity->CreatedDate=date_format(date_create($callModel->CreatedDate),Constants::$DefaultDateTimeFormat);
		//$callEntity->IsOpen=Constants::$Value_False;
		
		if($callEntity->save()){
			$response->IsSuccess=true;
			$response->Message="Call Added Successfully";
		}
		else{
			$response->IsSuccess=false;
			$response->Message="Try again";
		}
		
		return $response;
	}
	public function GetErrorLog(){
		$response = new ServiceResponse();
		$Model= new stdClass();
		$ProjectModel= new stdClass();
		$ProjectModel->Error=DB::table('errorlog')->orderBy('ID',Constants::$SortIndexDESC)->get();
		$Model->ProjectModel=$ProjectModel;
		$response->Data = $Model;
		return $response;
	}
	
	public function DeleteUserPayment($paymenyID){
        $response = new ServiceResponse();
        $paymentPlansHistoryEntity = PaymentPlansHistoryEntity::find($paymenyID->scalar);
        if($paymentPlansHistoryEntity){
			if($paymentPlansHistoryEntity->delete()){
				$response->IsSuccess = true;
	        	$response->Message = trans('messages.UserPaymentDeleted');	
			}else{
				$response->Message = trans('messages.ErrorOccured');	
			}
		}else{
			$response->Message = trans('messages.ErrorOccured');
		}
        
        return $response;
    }
	
	public function DeleteCall($callModel){
		$response =  new ServiceResponse();
		try{
			DB::delete("delete from usercalls where CallID = ".$callModel->scalar);
			DB::delete("delete from calls where CallID = ".$callModel->scalar);
			$response->IsSuccess = TRUE;
			$response->Message = trans("messages.CallDeletedSuccessfully");
		}catch(Exception $e){
			$response->Message = trans("messages.ErrorOccured");	
		}
		
		return $response;
	}
}