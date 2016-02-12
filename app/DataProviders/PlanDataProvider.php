<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

use \ViewModels\ServiceResponse;
use \ViewModels\SearchValueModel;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \Crypt;
use \Mail;
use \Authentication;
use \PlanEntity;
use \PaymentPlansHistoryEntity;
use UserEntity;
use UserDevicesEntity;
use GroupEntity;
use NewsEntity;
use GroupUserEntity;
use NotificationEntity;
use MessageEntity;
use UserNewsEntity;
use UserNotificationEntity;

class PlanDataProvider extends BaseDataProvider implements IPlanDataProvider {

    public function getPlanDetails($planID){

        $response = new ServiceResponse();
        $data = new stdClass();
        $planEntity = new PlanEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="PlanID";
        $searchValueData->Value=$planID;
        array_push($searchParams, $searchValueData);

        /*$searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);*/

        $PlanDetail = $this->GetEntity($planEntity,$searchParams);

        $data->PlanModel = $PlanDetail;
        $response->Data = $data;
        return $response;
    }

    public function SavePlan($planModel,$loginUserID){
        $response = new ServiceResponse();

        $messages = array(
            'required' => trans('messages.PropertyRequired'),
            'min' => trans('messages.PropertyMin'),
            'max' => trans('messages.PropertyMax')
        );

        $isEditMode=$planModel->PlanID>0;
        $planEntity = new PlanEntity();

        $validator = Validator::make((array)$planModel, $isEditMode ? $planEntity::$Add_rules : $planEntity::$Add_rules, $messages);
        $validator->setAttributeNames($planEntity::$niceNameArray);
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name ="PlanName";
        $searchValueData->Value = Common::GetDataWithTrim($planModel->PlanName);
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        if ($isEditMode) {
            $customWhere = "PlanID NOT IN ($planModel->PlanID)";
        } else {
            $customWhere = "";
        }

        $checkUniquePlan = $this->GetEntityCount($planEntity, $searchParams, "", "", $customWhere);
        if ($checkUniquePlan == 0) {
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            if ($isEditMode) {
                $planEntity = $this->GetEntityForUpdateByPrimaryKey($planEntity, $planModel->PlanID);
            }
            $planEntity->PlanName = Common::GetDataWithTrim($planModel->PlanName);
            $planEntity->Amount = $planModel->Amount;

            if(isset($planModel->Discount))
                $planEntity->Discount = $planModel->Discount;

            $planEntity->NoOfDays = $planModel->NoOfDays;
            $planEntity->IsEnable = Constants::$IsEnableValue;

            if(!empty($planModel->IsTrial))
                $planEntity->IsTrial = $planModel->IsTrial;
            else
                $planEntity->IsTrial = Constants::$Value_False;

            if(!$isEditMode) {
                $planEntity->CreatedDate = $dateTime;
            }
            $planEntity->ModifiedDate=$dateTime;

            if($this->SaveEntity($planEntity)){
                $response->IsSuccess = true;
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
            if(!$isEditMode){
                $response->Message = trans('messages.PlanAddedSuccess');
            }else{
                $response->Message = trans('messages.PlanUpdateSuccess');
            }
        }
        else {
            $response->Message ="'". Common::GetDataWithTrim($planModel->PlanName)."' ".trans('messages.PlanAlreadyExist');
        }
        return $response;
    }


    public function getPlanList($planModel){

        $response = new ServiceResponse();
        $model= new stdClass();

        $planEntity = new PlanEntity();
        $sortIndex ='CreatedDate';
        $sortDirection=Constants::$SortIndexDESC;

        $pageIndex = $planModel->PageIndex;
        $pageSizeCount = $planModel->PageSize;
        if(!empty($planModel->SortIndex)){
            $sortIndex = $planModel->SortIndex;
            $sortDirection = $planModel->SortDirection;
        }

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsDeleted";
        $searchValueData->Value = 0;
        array_push($searchParams,$searchValueData);

        $planList = $this->GetEntityWithPaging($planEntity,$searchParams,$pageIndex,$pageSizeCount,$sortIndex,$sortDirection);

        $model->CurrentPage = $planList->CurrentPage;
        $model->TotalPages = $planList->TotalPages;
        $model->TotalItems = $planList->TotalItems;
        $model->ItemsPerPage = $planList->ItemsPerPage;
        $model->PlanListArray = $planList->Items;

        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    public function Updateplan($planModel){
        $response = new ServiceResponse();

        $searchUserProjectsParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "PlanID";
        $searchValueData->Value = $planModel->PlanID;
        array_push($searchUserProjectsParams, $searchValueData);

        $getPlanData = $this->GetEntityForUpdateByFilter(new PlanEntity(), $searchUserProjectsParams);

        if ($getPlanData) {
            $getPlanData->IsEnable = $planModel->IsEnable;

            $response->Data = $this->SaveEntity($getPlanData);

            if($planModel->IsEnable == Constants::$IsEnableValue){
                $response->Message = trans('messages.PlanEnabled');
            }else{
                $response->Message = trans('messages.PlanDisabled');
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

    public function UpdateTrial($trialModel){
        $response = new ServiceResponse();

        $searchUserProjectsParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "PlanID";
        $searchValueData->Value = $trialModel->PlanID;
        array_push($searchUserProjectsParams, $searchValueData);

        $getPlanData = $this->GetEntityForUpdateByFilter(new PlanEntity(), $searchUserProjectsParams);
        
        if ($getPlanData) {
	        if($trialModel->IsTrial == Constants::$IsEnableValue){
	        	$getCurrentTrial = PlanEntity::where("IsTrial",1)->first();
	        	if($getCurrentTrial && $getCurrentTrial->PlanID){
					$response->Message = trans('messages.TrialCanNotBeEnable');	
				}else{
					$getPlanData->IsTrial = $trialModel->IsTrial;
					$response->Data = $this->SaveEntity($getPlanData);
					$response->Message = trans('messages.TrialEnabled');	
					$response->IsSuccess = true;
				}
	        }else{
	        	$getPlanData->IsTrial = $trialModel->IsTrial;
	        	$response->Data = $this->SaveEntity($getPlanData);
	            $response->Message = trans('messages.TrialDisabled');
	            $response->IsSuccess = true;
	        }
	        
        }else{
            $response->IsSuccess = false;
            $response->Message = trans('messages.ErrorOccured');
        }
        
        return $response;

    }
    
    public function DeletePlan($planID){
        $response = new ServiceResponse();
        $planEntity = PlanEntity::find($planID->scalar);
		if($planEntity->delete()){
			$response->IsSuccess = true;
        	$response->Message = trans('messages.PlanDeleted');	
		}else{
			$response->Message = trans('messages.ErrorOccured');	
		}
        
        return $response;
    }

    /*Mobile Serivce Method Start*/
    public function Allplanlist($model){
        $response = new ServiceResponse();
        /*if($model->IsTrial){
			$planList = PlanEntity::where('IsEnable',Constants::$Value_True)->where('IsTrial',Constants::$Value_False)->get();
		}else{
			$planList = PlanEntity::where('IsEnable',Constants::$Value_True)->get();
		}*/
        $planList = PlanEntity::where('IsEnable',Constants::$Value_True)->where('IsTrial',Constants::$Value_False)->get();

        if($planList){
            foreach($planList as $planday){
                if(($planday->IsTrial)){
                    $planday->NoOfDays="".$planday->NoOfDays." Days";
                }
                /*if(($planday->NoOfDays) % 30 == Constants::$Value_False){
                    $planday->NoOfDays="".($planday->NoOfDays) / 30 ." Month";
                }
                else{
                    $planday->NoOfDays="".$planday->NoOfDays." Day";
                }*/
                $planday->Amount = $planday->Amount + 0;
                if($planday->Discount){
                    $planday->Discount= (int)$planday->Discount;
                }
                else{
                    $planday->Discount= 0;
                }
            }
        }
        $response->Data=array("PlanList"=>$planList);
        $response->IsSuccess = true;
        return $response;
    }
    public function UserTrialPlan($user){
        $response = new ServiceResponse();
        $paymentplanshistory=DB::select("SELECT *,TIMESTAMPDIFF(DAY, StartDate, NOW()) as daytime FROM paymentplanshistory WHERE UserID='".$user->Data->UserID."' AND IsTrial='".Constants::$Value_True."' AND IsActive='".Constants::$Value_True."' ");

        if($paymentplanshistory){
            $paymentplanshistory[0]->DayTime=($paymentplanshistory[0]->NoOfDays)-($paymentplanshistory[0]->daytime);
            $response->Data=$paymentplanshistory;
        }
        else{
            $planList=DB::table('paymentplans')->where('IsEnable',Constants::$Value_True)->where('IsTrial',Constants::$Value_True)->first();
            $response->Data=$planList;
        }

        $response->IsSuccess = true;
        return $response;
    }
    public function ActivateTrial($model,$user){
        $response = new ServiceResponse();
        $dateTime = date(Constants::$DefaultDateTimeFormat);
        $userDetail=UserEntity::where('UserID',$user->Data->UserID)->first();
        if($userDetail->IsVerified){			
	        $paymentplanshistory=DB::table('paymentplanshistory')->where('UserID',$user->Data->UserID)->where('IsTrial',Constants::$Value_True)->first();
	        $paymentplanshistoryCount =DB::table('paymentplanshistory')->where('UserID',$user->Data->UserID)->count();
	        if($paymentplanshistory){
	            if($paymentplanshistory->IsActive){
	                $response->Message=trans("messages.UserTrialAlreadyActivation");
	            }else{
	                $response->Message=trans("messages.TrialIsExpired");
	            }
	            $response->IsSuccess = false;
	        }else if ($paymentplanshistoryCount > 0){
				$response->Message=trans("messages.TrialIsExpired");
			}else {
	            $planList = DB::table('paymentplans')->where('IsEnable', Constants::$Value_True)->where('IsTrial', Constants::$Value_True)->first();
	            $endTime = date(Constants::$DefaultDateTimeFormat, strtotime("+".$planList->NoOfDays." days",time()));
	            $paymentpalnshistoryEntity = new PaymentPlansHistoryEntity();
	            $paymentpalnshistoryEntity->UserID = $user->Data->UserID;
	            $paymentpalnshistoryEntity->Amount = $planList->Amount;
	            $paymentpalnshistoryEntity->SubscriptionAmount = $planList->NoOfDays;
	            $paymentpalnshistoryEntity->ReferenceNo = "";
	            $paymentpalnshistoryEntity->StartDate = $dateTime;
	            $paymentpalnshistoryEntity->EndDate = $endTime;
	            $paymentpalnshistoryEntity->PlanName = $planList->PlanName;
                $paymentpalnshistoryEntity->PaymentDate = $dateTime;
	            $paymentpalnshistoryEntity->NoOfDays = $planList->NoOfDays;
	            $paymentpalnshistoryEntity->IsTrial = Constants::$Value_True;
	            $paymentpalnshistoryEntity->IsActive = Constants::$Value_True;
	            $success = $paymentpalnshistoryEntity->save();
	            $paymentResponse = array(
		            'Amount' => Constants::$CurrencySymbol.$paymentpalnshistoryEntity->Amount,
		            'SubscriptionAmount' => Constants::$CurrencySymbol.$paymentpalnshistoryEntity->SubscriptionAmount,
		            'ReferenceNo' => $paymentpalnshistoryEntity->ReferenceNo,
		            'StartDate' => date_format(date_create($paymentpalnshistoryEntity->StartDate), Constants::$DefaultDisplayDateTimeFormat),
		            'EndDate' => date_format(date_create($paymentpalnshistoryEntity->EndDate), Constants::$DefaultDisplayDateTimeFormat),
		            'PlanName' => $paymentpalnshistoryEntity->PlanName,
		            'NoOfDays' => $paymentpalnshistoryEntity->NoOfDays+0,
		            'PaymentDate' => date_format(date_create($paymentpalnshistoryEntity->PaymentDate), Constants::$DefaultDisplayDateTimeFormat),
		            'IsTrial' => $paymentpalnshistoryEntity->IsTrial+0
	            );
	            if ($success) {	            	
	            	$userDeviceEntity=new UserDevicesEntity();
					$userDeviceEntity->UserID=$user->Data->UserID;
					$userDeviceEntity->DeviceID=$model->DeviceID;
					$userDeviceEntity->save();
								
	                $response->IsSuccess = true;
	                $response->Data = $paymentResponse;
	                $response->Message = trans("messages.UserTrialActivation");
	                
	                /**
					* Send notification/news to admin and customer
					*/
					
					$userNotificationEntity = new UserNotificationEntity();
		        	$userNotificationEntity->Description = trans("messages.UserTrialActivation");
		        	$userNotificationEntity->UserID = $user->Data->UserID;
		        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
		        	$userNotificationEntity->save();
		        			        	
					$notificationEntity = new NotificationEntity();
					$notificationEntity->UserID = $user->Data->UserID;
					$notificationEntity->NotificationType = Constants::$NotificationType['General'];
					$notificationEntity->Message = trans("messages.UserTrialActivation");
					$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
					$notificationEntity->save();
					
					$users = DB::select("select * from users where userid in (select userid from userroles where roleid = 1)");
					if($users && count($users)>0){
						foreach($users as $userDetail){
							$message = trans("messages.UserTrialActivationToAdmin",array("info"=>$user->Data->FirstName." ".$user->Data->LastName." (".$user->Data->Email.", ".$user->Data->Mobile.")"));
							$userNotificationEntity = new UserNotificationEntity();
				        	$userNotificationEntity->Description = $message;
				        	$userNotificationEntity->UserID = $userDetail->UserID;
				        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				        	$userNotificationEntity->save();
							
							$notificationEntity = new NotificationEntity();
							$notificationEntity->UserID = $userDetail->UserID;
							$notificationEntity->NotificationType = Constants::$NotificationType['General'];
							$notificationEntity->Message = $message;
							$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
							$notificationEntity->save();
						}
					}
	                
	            } else {
	                $response->IsSuccess = false;
	                $response->Message = trans("messages.UserTrialNotActivation");
	            }
	        }
        }
        else{
			$response->IsSuccess = true;
	        $response->Data = $userDetail;
	        $response->ErrorCode=Constants::$MobileNotVerified;
	        $response->Message = trans("messages.MobileNotEnabled");
		}
        return $response;
    }
    public function PaymentHistory($model,$user){
        $response=new ServiceResponse();
        $LastIndex = 0;
		$PageSize = 10;
		
        if(!empty($model->SearchText)){
			$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
			$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			$paymentplanshistory = DB::table('paymentplanshistory')
			->where('UserID',$user->Data->UserID)
			->whereRaw("(Amount Like '%".$model->SearchText."%' OR SubscriptionAmount Like '%".$model->SearchText."%' OR ReferenceNo Like '%".$model->SearchText."%' OR PlanName Like '%".$model->SearchText."%' OR NoOfDays Like '%".$model->SearchText."%')")
			->skip($LastIndex)
			->take($PageSize)->get();
		}else{
			$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
			$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			$paymentplanshistory =DB::table('paymentplanshistory')->select('IsTrial','Amount','SubscriptionAmount','ReferenceNo','StartDate','EndDate','PlanName','NoOfDays','PaymentDate')->where('UserID',$user->Data->UserID)->skip($LastIndex)->take($PageSize)->get();
		}
	
        if($paymentplanshistory){
        	foreach($paymentplanshistory as $paymentplan){
				$paymentplan->Amount=$paymentplan->Amount;
				$paymentplan->SubscriptionAmount=$paymentplan->SubscriptionAmount;
				$paymentplan->StartDate=date_format(date_create($paymentplan->StartDate), Constants::$DefaultDisplayDateTimeFormat);
				$paymentplan->EndDate=date_format(date_create($paymentplan->EndDate), Constants::$DefaultDisplayDateTimeFormat);
				$paymentplan->PaymentDate=date_format(date_create($paymentplan->PaymentDate), Constants::$DefaultDisplayDateTimeFormat);
			}
            $response->Data=array("PaymentHistory"=>$paymentplanshistory);
        }
        else{
            $response->Message=trans("messages.NoHistoryFound");
            $response->Data=array("PaymentHistory"=>[]);
        }
        $response->IsSuccess=true;
        return $response;
    }
    
    public function CloseHistoryPlan(){
        $response=new ServiceResponse();
        
        $allplanList=DB::select("Select * from paymentplanshistory where 1 = IF(CURDATE() BETWEEN DATE_FORMAT(StartDate,'%y-%m-%d') AND DATE_FORMAT(EndDate,'%y-%m-%d'),1,0) and IsActive = 0");
        
        $allExpiredplanList=DB::select("Select * from paymentplanshistory where 0 = IF(CURDATE() BETWEEN DATE_FORMAT(StartDate,'%y-%m-%d') AND DATE_FORMAT(EndDate,'%y-%m-%d'),1,0) and IsActive = 1");
		
		if($allplanList>0){
			$allplan=DB::update("UPDATE paymentplanshistory SET IsActive= IF(CURDATE() BETWEEN DATE_FORMAT(StartDate,'%y-%m-%d') AND DATE_FORMAT(EndDate,'%y-%m-%d'),1,0)");
			if($allplan>0){
				$response->Message=$allplan." row(s) affected";
				$response->IsSuccess=true;
			}
			
			foreach($allExpiredplanList as $plan){
				if($plan->IsTrial){
					$message = trans("messages.TrialIsExpired");
                    /* Staging*/ 	DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate) values(?,".Constants::$UserNewsExpireTrailID.",?)",array($plan->UserID, date(Constants::$DefaultDateTimeFormat)));
                    // Live - DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate) values(?,158,?)",array($plan->UserID, date(Constants::$DefaultDateTimeFormat)));
				}else{
					$message = trans("messages.PlanExpired");
				}
				
				$userNotificationEntity = new UserNotificationEntity();
		    	$userNotificationEntity->Description = $message;
		    	$userNotificationEntity->UserID = $plan->UserID;
		    	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
		    	$userNotificationEntity->save();
		    	
		    	
		    	$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $plan->UserID;
				$notificationEntity->NotificationType = Constants::$NotificationType['General'];
				$notificationEntity->Message = $message;
				$notificationEntity->Key = (int)Constants::$FreeGroupID;
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->save();
			}
			
			foreach($allplanList as $plan){
				
				if($plan->IsTrial){
					$message = trans("messages.UserTrialActivation");
				}else{
					$message = trans("messages.PlanActivated", array('startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($plan->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($plan->EndDate))));
				}
				
				$userNotificationEntity = new UserNotificationEntity();
		    	$userNotificationEntity->Description = $message;
		    	$userNotificationEntity->UserID = $plan->UserID;
		    	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
		    	$userNotificationEntity->save();
		    	
		    	
		    	$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $plan->UserID;
				$notificationEntity->NotificationType = Constants::$NotificationType['General'];
				$notificationEntity->Message = $message;
				$notificationEntity->Key = $plan->IsTrial?0:(int)Constants::$PaidGroupID;
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->save();
			}
		}
        
        return $response;
    }
    
    public function Addnews($user){
        $response= new ServiceResponse();        
        
        $totalGroups=Common::CommonGroups();
		
		$response->Data=array("GroupList"=>$totalGroups);		
		$response->IsSuccess=TRUE;
		return $response;
    }
    
    public function SaveNews($newsmodel,$newsImage,$user){
		$response = new ServiceResponse();
        $newsEntity = new NewsEntity();
        
        $messages = array(
            'required' => trans('messages.PropertyRequired')
        );
        
        $validator = Validator::make((array)$newsmodel, $newsEntity::$Add_rules, $messages);
        $validator->setAttributeNames($newsEntity::$niceNameArray);
        
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            $response->IsSuccess = false;
            return $response;
        }
        
		$dateTime = date(Constants::$DefaultDateTimeFormat);

        $newsEntity->Description=$newsmodel->Description;
        $newsEntity->GroupID=serialize($newsmodel->GroupID);
        $newsEntity->CreatedDate=$dateTime;
        $newsEntity->ModifiedDate=$dateTime;
        $newsEntity->UserID=$user->Data->UserID;
        
        if($newsEntity->save()) {
            if ($newsImage) {
                if (!is_dir(public_path(Constants::$Path_NewsImages .$newsEntity->NewsID))) {
                    mkdir(public_path(Constants::$Path_NewsImages .$newsEntity->NewsID), 0755);
                }
                else {
                    $path = public_path(Constants::$Path_NewsImages.$newsEntity->NewsID.'/');
                    // Loop over all of the files in the folder
                    foreach (glob($path . "*.*") as $file) {
                        unlink($file); // Delete each file through the loop
                    }
                }
                $destinationPath = public_path(Constants::$Path_NewsImages .$newsEntity->NewsID);
                $fileName = $newsImage->getClientOriginalName();
                $success = $newsImage->move($destinationPath, $fileName);

                if ($success) {
                    $newsEntity->Image = $fileName;
                    $newsEntity->save();
                }
            }
            
            /*Save Notification for all users*/
            $userlist=array();
	        $users=array();
	        if(in_array(Constants::$AllGroupID,$newsmodel->GroupID)){
				$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
			}
			else{
				foreach($newsmodel->GroupID as $value){
					if($value == Constants::$TrialGroupID){
						$userlist[]=PaymentPlansHistoryEntity::where('IsTrial',Constants::$Value_True)->where('IsActive',Constants::$Value_True)->distinct('UserID')->lists('UserID');
					}
					else if($value == Constants::$FreeGroupID){
						$historyUser=DB::table('paymentplanshistory')->where('IsActive',Constants::$Value_True)->distinct('UserID')->lists('UserID');
						if(count($historyUser)>0){
							$freeUser=DB::table('users')->whereNotIn('UserID', $historyUser)->where('IsEnable',Constants::$Value_True)->lists('UserID');
							$userlist[]=$freeUser;	
						}		
					}
					else if($value == Constants::$PaidGroupID){
						$paidCount=PaymentPlansHistoryEntity::where('IsTrial',Constants::$Value_False)->where('IsActive',Constants::$Value_True)->distinct('UserID')->lists('UserID');
						$userlist[]=$paidCount;
					}
					else{
						$userlist[]=DB::table('usergroups')->where('GroupID',$value)->lists('UserID');
					}
				}
				
				$userlist[]=DB::table('users')->whereIn('userid',DB::table('userroles')->where('RoleID', Constants::$RoleAdmin)->lists('UserID'))->where('IsEnable',Constants::$Value_True)->lists('UserID');
				
				if(count($userlist)>1){
					foreach($userlist as $listuser){
						$users=array_unique(array_merge($users,$listuser));	
					}
				}
			}
	        
	        foreach($users as $userID){
	        	$UserNewsEntity = new UserNewsEntity();
	        	$UserNewsEntity->NewsID = $newsEntity->NewsID;
	        	$UserNewsEntity->UserID = $userID;
	        	$UserNewsEntity->save();
	        	
	        	
				$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $userID;
				$notificationEntity->NotificationType = Constants::$NotificationType['General'];
				$notificationEntity->Message = $newsEntity->Description; //trans("messages.NewNewsMessageReceivedPush");
				$notificationEntity->ImageUrl = ($newsEntity->Image)?(Constants::$Path_NewsImages.$newsEntity->NewsID.'/'.rawurlencode($newsEntity->Image)):'';
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->save();
			}
            
			$response->Data = $newsEntity;
			$response->Message =trans('messages.newsadded') ;
            $response->IsSuccess = true;
        }
        return $response;
	}
	
	public function GetNewsList($seachModel, $userID){
		$serviceResponse = new ServiceResponse();
		$LastIndex = 0;
		$PageSize = 10;
		
		
		$LastIndex = !empty($seachModel->LastID)?$seachModel->LastID:$LastIndex;
		$PageSize = !empty($seachModel->PageSize)?$seachModel->PageSize:$PageSize;
		$result = DB::select('call getNewsList(? , ?, ?, ?, ?)',array($userID, asset(Constants::$Path_NewsImages), Constants::$DefaultDisplayDateTimeFormatSQL, $LastIndex, $PageSize));
		
		
		if($result && count($result)>0){
			$serviceResponse->Data = array("NewsList"=>$result);
			$serviceResponse->IsSuccess = TRUE;
		}else{
			$serviceResponse->IsSuccess = TRUE;
			$serviceResponse->Data = array("NewsList"=>[]);
			$serviceResponse->Message = trans("messages.NoRecordFound");
		}
		
		return $serviceResponse;
	}
	
	public function SavePayment($paymentModel,$cUser){
		$response= new ServiceResponse();
		
		$dateTime = date(Constants::$DefaultDateTimeFormat);
		        
        $planList = DB::table('paymentplans')->where('PlanID', $paymentModel->PlanID)->first();
        
        $endTime = date(Constants::$DefaultDateTimeFormat, strtotime("+".$planList->NoOfDays." days",time()));
        
        $paymentpalnshistoryEntity = new PaymentPlansHistoryEntity();
        $paymentpalnshistoryEntity->UserID = $cUser->Data->UserID;
        $paymentpalnshistoryEntity->Amount = $paymentModel->Amount;
        $paymentpalnshistoryEntity->SubscriptionAmount = $planList->Amount;
        $paymentpalnshistoryEntity->ReferenceNo = $paymentModel->ReferenceNo;
        $paymentpalnshistoryEntity->StartDate = $dateTime;
        $paymentpalnshistoryEntity->PaymentDate = $dateTime;
        $paymentpalnshistoryEntity->EndDate = $endTime;
        $paymentpalnshistoryEntity->PlanName = $planList->PlanName;
        $paymentpalnshistoryEntity->NoOfDays = $planList->NoOfDays;
        $paymentpalnshistoryEntity->IsTrial = Constants::$Value_False;
        $paymentpalnshistoryEntity->IsActive = Constants::$Value_True;
        $paymentpalnshistoryEntity->save();
        if($paymentpalnshistoryEntity){
        	
        	$messageEntity = new MessageEntity();
            $messageEntity->Mobile = $cUser->Data->Mobile;
            $messageEntity->Message = trans("messages.Paymentsuccessfully" );
            $messageEntity->save();
            
            $notificationEntity = new NotificationEntity();
			$notificationEntity->UserID = $cUser->Data->UserID;
			$notificationEntity->NotificationType = Constants::$NotificationType['Currency'];
			$notificationEntity->Message = trans("messages.Paymentsuccessfully");
			$notificationEntity->Key = $paymentpalnshistoryEntity->PaymentHistoryID;
			$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
			$notificationEntity->save();
		            
        	//$users=DB::table('users')->select('users.UserID','Mobile','DeviceUDID')->leftJoin('userroles', 'users.UserID', '=', 'userroles.UserID')->where('userroles.RoleID',Constants::$RoleAdmin)->get();
			
			$allowadmin=DB::table('allowedchatadmin')->where('IsDefault',Constants::$Value_True)->first() || 1;
			$useradmin=DB::table('users')->where('UserID',$allowadmin->AdminID)->first();
        	
			$messageEntity = new MessageEntity();
			$messageEntity->Mobile = $useradmin->Mobile;
			$messageEntity->Message = trans("messages.PaymentMessage", array('FirstName'=>$cUser->Data->FirstName.' '.$cUser->Data->LastName,"Mobile"=>$cUser->Data->Mobile,"City"=>$cUser->Data->City,"Plan"=>$planList->PlanName));
			$messageEntity->save();	
			
			$notificationEntity = new NotificationEntity();
			$notificationEntity->UserID = $useradmin->UserID;
			$notificationEntity->NotificationType = Constants::$NotificationType['Currency'];
			$notificationEntity->Message = trans("messages.Paymentsuccessfully");
			$notificationEntity->Key = $paymentpalnshistoryEntity->PaymentHistoryID;
			$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
			$notificationEntity->save();
			
			/*if(count($users)>0){
	        	foreach($users as $message){
	        		$messageEntity = new MessageEntity();
		            $messageEntity->Mobile = $useradmin->Mobile;
		            $messageEntity->Message = trans("messages.PaymentMessage", array('FirstName'=>$cUser->Data->FirstName.' '.$cUser->Data->LastName,"Mobile"=>$cUser->Data->Mobile,"City"=>$cUser->Data->City,"Plan"=>$planList->PlanName));
		            $messageEntity->save();	
		            
		            $notificationEntity = new NotificationEntity();
					$notificationEntity->UserID = $message->UserID;
					$notificationEntity->NotificationType = Constants::$NotificationType['Currency'];
					$notificationEntity->Message = trans("messages.Paymentsuccessfully");
					$notificationEntity->Key = $paymentpalnshistoryEntity->PaymentHistoryID;
					$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
					$notificationEntity->save();
				}
			}*/
			
			$response->IsSuccess = true;	
			$response->Message=trans("messages.Paymentsuccessfully");
		}
		return $response;
	}
	
	public function NotifyUserForPlanExpire(){
        $response = new ServiceResponse();
        $Plans = DB::select("SELECT pph.UserID, DATEDIFF(pph.EndDate, CURRENT_DATE()) AS TimeLeft, pph.PlanName FROM paymentplanshistory pph LEFT JOIN paymentplanshistory pph2 ON pph.UserID = pph2.UserID AND DATEDIFF(pph2.StartDate, pph.EndDate)=1
WHERE pph.IsActive = 1 AND IFNULL(pph2.PaymentHistoryID,'') = '' AND DATEDIFF(pph.EndDate, CURRENT_DATE()) IN (0,3,5,7)");
		if($Plans && count($Plans)>0){
			foreach($Plans as $Plan){
				$message = Lang::choice("messages.PlanWillExpireInDays", $Plan->TimeLeft, array("plan"=>$Plan->PlanName,"day"=>$Plan->TimeLeft));
				$userNotificationEntity = new UserNotificationEntity();
	        	$userNotificationEntity->Description = $message;
	        	$userNotificationEntity->UserID = $Plan->UserID;
	        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
	        	$userNotificationEntity->save();
				
				$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $Plan->UserID;
				$notificationEntity->NotificationType = Constants::$NotificationType['General'];
				$notificationEntity->Message = $message;
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->save();
			}
			$response->IsSuccess=true;
		}
        return $response;
    }
    /*Mobile Serivce Method End*/

}