<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use \ViewModels\ServiceResponse;
use \ViewModels\SearchValueModel;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \Crypt;
use \Mail;
use UserEntity;
use \FundamentalEntity;
use NotificationEntity;


class FundamentalDataProvider extends BaseDataProvider implements IFundamentalDataProvider {

    /*Dev_kr Region Start*/
    public function getFundamentalList($fundamentalModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $fundamentalEntity= new FundamentalEntity();

        $sortIndex= 'ModifiedDate';
        $sortDirection= Constants::$SortIndexDESC;

        $pageIndex = $fundamentalModel->PageIndex;
        $pageSizeCount = $fundamentalModel->PageSize;
        if(!empty($fundamentalModel->SortIndex)){
            $sortIndex=$fundamentalModel->SortIndex;
            $sortDirection=$fundamentalModel->SortDirection;
        }

        $searchParams = array();
        
        $customWhere = "'1'='1'";

        if(isset($fundamentalModel->SearchParams)) {
            if (isset($fundamentalModel->SearchParams["textKeyWord"])) {
                $title = $fundamentalModel->SearchParams["textKeyWord"];
                $customWhere .= " AND Title like "."'%".addslashes(trim($title))."%'";
            }
            
            if (isset($fundamentalModel->SearchParams["Status"])) {
            	$searchValueData = new SearchValueModel();
		        $searchValueData->Name = "IsEnable";
		        
            	$firstName = $fundamentalModel->SearchParams["Status"];
            	if ($fundamentalModel->SearchParams["Status"] === 1) {
            		$searchValueData->Value = 1;
            		array_push($searchParams, $searchValueData);
        		}else if($fundamentalModel->SearchParams["Status"] === 10) {
        			$searchValueData->Value = 0;
        			array_push($searchParams, $searchValueData);
        		}
            }
        }

        $fundamentalList = $this->GetEntityWithPaging($fundamentalEntity,$searchParams,$pageIndex,$pageSizeCount,$sortIndex,$sortDirection, $customWhere);

        $model->CurrentPage = $fundamentalList->CurrentPage;
        $model->TotalPages = $fundamentalList->TotalPages;
        $model->TotalItems = $fundamentalList->TotalItems;
        $model->ItemsPerPage = $fundamentalList->ItemsPerPage;
        $model->FundamentalListArray = $fundamentalList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    public function getFundamentalDetails($fundamentalID){
        $response = new ServiceResponse();
        $data = new stdClass();
        $fundamentalEntity = new FundamentalEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="FundamentalID";
        $searchValueData->Value=$fundamentalID;
        array_push($searchParams, $searchValueData);

        $FundamentalDetail=$this->GetEntity($fundamentalEntity,$searchParams);
        if($FundamentalDetail){
            if($FundamentalDetail->Image){
                   $FundamentalDetail->Image=asset(Constants::$Path_FundamentalImages.$FundamentalDetail->FundamentalID.'/'.rawurlencode($FundamentalDetail->Image));
            }
        }
        $data->FundamentalModel = $FundamentalDetail;
        $response->Data = $data;
        return $response;
    }
    public function EnableFundamental($fundamentalModel){
        $response = new ServiceResponse();

        $searchUserProjectsParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "FundamentalID";
        $searchValueData->Value = $fundamentalModel->FundamentalID;
        array_push($searchUserProjectsParams, $searchValueData);

        $getFundamentalData = $this->GetEntityForUpdateByFilter( new FundamentalEntity(), $searchUserProjectsParams);
        $messages = array(
            'required' => trans('messages.PropertyRequired'),
            'min' => trans('messages.PropertyMin')
        );

        $fundamentalEntity = new FundamentalEntity();
		$validator = Validator::make((array)$getFundamentalData->getOriginal(), $fundamentalEntity::$Publish_rules, $messages);
        $validator->setAttributeNames($fundamentalEntity::$niceNameArray);
        
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        if ($getFundamentalData) {
            $getFundamentalData->IsEnable = $fundamentalModel->IsEnable;
			$getFundamentalData->CreatedDate = date(Constants::$DefaultDateTimeFormat);
            $response->Data = $this->SaveEntity($getFundamentalData);

            if($fundamentalModel->IsEnable == Constants::$IsEnableValue){
            	$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
            	if(count($users)>0){
					foreach($users as $userID){
						$notificationEntity = new NotificationEntity();
						$notificationEntity->UserID = $userID;
						$notificationEntity->NotificationType = Constants::$NotificationType['Fundamental'];
						$notificationEntity->Message = $getFundamentalData->Title; //trans("messages.NewFundamentalMessageReceivedPush");
						$notificationEntity->ImageUrl = ($getFundamentalData->Image)?(Constants::$Path_FundamentalImages.$getFundamentalData->FundamentalID.'/'.rawurlencode($getFundamentalData->Image)):'';
						$notificationEntity->Key = $fundamentalModel->FundamentalID;
						$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
						$notificationEntity->save();
					}
				}
                $response->Message = trans('messages.FundamentalEnabled');
            }else{
                $response->Message = trans('messages.FundamentalDisabled');
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
    public function SaveFundamental($fundamentalModel,$loginUserID){
        $response = new ServiceResponse();
        $messages = array(
            'required' => trans('messages.PropertyRequired'),
            'min' => trans('messages.PropertyMin')
        );

        $isEditMode = $fundamentalModel['FundamentalID']> 0;
        $fundamentalEntity = new FundamentalEntity();

        $validator = Validator::make((array)$fundamentalModel, $isEditMode ? $fundamentalEntity::$Add_rules : $fundamentalEntity::$Add_rules, $messages);
        $validator->setAttributeNames($fundamentalEntity::$niceNameArray);
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name ="Title";
        $searchValueData->Value = Common::GetDataWithTrim($fundamentalModel['Title']);
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        if ($isEditMode) {
            $customWhere = "FundamentalID NOT IN ($fundamentalModel[FundamentalID])";
            
        } else {
            $customWhere = "";
        }

        $checkUnique = $this->GetEntityCount($fundamentalEntity, $searchParams, "", "", $customWhere);
        if ($checkUnique == 0) {
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            $fundamentalEntity->Title = Common::GetDataWithTrim($fundamentalModel['Title']);
            if ($isEditMode) {
                $fundamentalEntity = $this->GetEntityForUpdateByPrimaryKey($fundamentalEntity, $fundamentalModel['FundamentalID']);
                if($fundamentalEntity->IsEnable == Constants::$Value_True){
					$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
	            	if(count($users)>0){
						foreach($users as $userID){
							$notificationEntity = new NotificationEntity();
							$notificationEntity->UserID = $userID;
							$notificationEntity->NotificationType = Constants::$NotificationType['Fundamental'];
							$notificationEntity->Message = 'Correction - '.$fundamentalEntity->Title; //trans("messages.NewFundamentalMessageReceivedPush");
							$notificationEntity->ImageUrl = ($fundamentalEntity->Image)?(Constants::$Path_FundamentalImages.$fundamentalEntity->FundamentalID.'/'.rawurlencode($fundamentalEntity->Image)):'';
							$notificationEntity->Key = $fundamentalEntity->FundamentalID;
							$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
							$notificationEntity->save();
						}
					}
				}
            }
            
            $fundamentalEntity->Description = $fundamentalModel['Description'];

            if(!$isEditMode) {
                $fundamentalEntity->CreatedDate = $dateTime;
            }
            $fundamentalEntity->ModifiedDate = $dateTime;

            if($analystdetails = $this->SaveEntity($fundamentalEntity)){
                $response->IsSuccess = true;
                if($analystdetails){
                    if($fundamentalModel['Image']){
                        if (!is_dir(public_path(Constants::$Path_FundamentalImages .$fundamentalEntity->FundamentalID))) {
                            mkdir(public_path(Constants::$Path_FundamentalImages .$fundamentalEntity->FundamentalID), 0755);
                        }
                        else {
                            $path = public_path(Constants::$Path_FundamentalImages.$fundamentalEntity->FundamentalID.'/');
                            foreach (glob($path . "*.*") as $file) {
                                unlink($file);
                            }
                        }
                        $destinationPath = public_path(Constants::$Path_FundamentalImages .$fundamentalEntity->FundamentalID);
                        $fileName = $fundamentalModel['Image']->getClientOriginalName();
                        $success = $fundamentalModel['Image']->move($destinationPath, $fileName);

                        if ($success) {
                            $fundamentalEntity->Image = $fileName;
                            $fundamentalEntity->save();
                        }
                    }
                    if($fundamentalModel['PDF']){
                        if (!is_dir(public_path(Constants::$Path_FundamentalPDF .$fundamentalEntity->FundamentalID))) {
                            mkdir(public_path(Constants::$Path_FundamentalPDF .$fundamentalEntity->FundamentalID), 0755);
                        }
                        else {
                            $path = public_path(Constants::$Path_FundamentalPDF.$fundamentalEntity->FundamentalID.'/');
                            foreach (glob($path . "*.*") as $file) {
                                unlink($file);
                            }
                        }
                        $destinationPath = public_path(Constants::$Path_FundamentalPDF .$fundamentalEntity->FundamentalID);
                        $fileName = $fundamentalModel['PDF']->getClientOriginalName();
                        $success = $fundamentalModel['PDF']->move($destinationPath, $fileName);

                        if ($success) {
                            $fundamentalEntity->PDF = $fileName;
                            $fundamentalEntity->save();
                        }
                    }
                }
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
            if(!$isEditMode){
                $response->Message = trans('messages.FundamentalAddedSuccess');
            }else{
                $response->Message = trans('messages.FundamentalUpdateSuccess');
            }
        }
        else {
            $response->Message ="'". Common::GetDataWithTrim($fundamentalEntity['Title'])."' ".trans('messages.FundamentalAlreadyExist');
        }
        return $response;
    }
    
    /*Dev_RB Region End*/

    /*Mobile Service Start*/
    public function AllFundamentals($model){
        $response= new ServiceResponse();
        $LastIndex = 0;
		$PageSize = 10;
		if(!empty($model->SearchText)){
			$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
			$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			$allFundamentals = DB::table('fundamentals')->where("IsEnable",1)->where('Title','LIKE', '%'.$model->SearchText.'%')
			->orWhere('Description','LIKE', '%'.$model->SearchText.'%')
			->orWhere('Image','LIKE', '%'.$model->SearchText.'%')
			->orWhere('Likes','LIKE', '%'.$model->SearchText.'%')
			->orderBy('CreatedDate',Constants::$SortIndexDESC)
			->skip($LastIndex)
			->take($PageSize)->get();
		}else{
			$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
			$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			$allFundamentals = DB::table('fundamentals')->where("IsEnable",1)->orderBy('CreatedDate',Constants::$SortIndexDESC)->skip($LastIndex)->take($PageSize)->get();
		}
				
        if($allFundamentals){
            foreach($allFundamentals as $fundamentalImage){
                if($fundamentalImage->Image){
                    $fundamentalImage->Image=asset(Constants::$Path_FundamentalImages.$fundamentalImage->FundamentalID.'/'.rawurlencode($fundamentalImage->Image));
                }
                if($fundamentalImage->PDF){
                    $fundamentalImage->PDF=asset(Constants::$Path_FundamentalPDF.$fundamentalImage->FundamentalID.'/'.$fundamentalImage->PDF);
                }
                $fundamentalImage->CreatedDate = date(Constants::$DefaultDisplayDateTimeFormat, strtotime($fundamentalImage->CreatedDate));
                $fundamentalImage->ModifiedDate = date(Constants::$DefaultDisplayDateTimeFormat, strtotime($fundamentalImage->ModifiedDate));
            }
            $response->Data=array("FundamentalData"=>$allFundamentals);
        }
        else{
        	$response->Data=array("FundamentalData"=>[]);
            $response->Message=trans("messages.NoFundamentalsFound");
        }
        $response->IsSuccess=true;
        return $response;
    }

    public function FundatmentalDetails($model){
        $response= new ServiceResponse();
        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "FundamentalID";
        $searchValueData->Value = $model->FundamentalID;
        array_push($searchParams, $searchValueData);

        $fundamentalDetails=$this->GetEntity(new FundamentalEntity(),$searchParams);
        if($fundamentalDetails){
            if($fundamentalDetails->Image){
                $fundamentalDetails->Image=asset(Constants::$Path_FundamentalImages.$fundamentalDetails->FundamentalID.'/'.rawurlencode($fundamentalDetails->Image));
            }
            if($fundamentalDetails->PDF){
                $fundamentalDetails->PDF=asset(Constants::$Path_FundamentalPDF.$fundamentalDetails->FundamentalID.'/'.$fundamentalDetails->PDF);
            }
            $response->Data=$fundamentalDetails;
        }
        $response->IsSuccess=true;
        return $response;
    }
    
    public function AllFundamentalDetails($model){
        $response= new ServiceResponse();
        
        $allFundamentals=FundamentalEntity::where('FundamentalID', '<=', $model->FirstID)->orderBy('FundamentalID',Constants::$SortIndexDESC)->where("IsEnable",1)->get();
        if($allFundamentals){
            foreach($allFundamentals as $fundamentalImage){
                if($fundamentalImage->Image){
                    $fundamentalImage->Image=asset(Constants::$Path_FundamentalImages.$fundamentalImage->FundamentalID.'/'.rawurlencode($fundamentalImage->Image));
                }
                if($fundamentalImage->PDF){
                    $fundamentalImage->PDF=asset(Constants::$Path_FundamentalPDF.$fundamentalImage->FundamentalID.'/'.rawurlencode($fundamentalImage->PDF));
                }
            }
            $response->Data=array("FundamentalData"=>$allFundamentals);
        }
        $response->IsSuccess=true;
        return $response;
    }
    
    public function DeleteFundamental($model){
		$response = new ServiceResponse();
        $fundamentalEntity = FundamentalEntity::find($model->scalar);
        if($fundamentalEntity){
			if($fundamentalEntity->delete()){
				$response->IsSuccess = true;
	        	$response->Message = trans('messages.FundamentalDeleted');	
			}else{
				$response->Message = trans('messages.ErrorOccured');	
			}
		}else{
			$response->Message=trans("messages.NoFundamentalsFound");
		}
		
        
        return $response;
	}
    /*Mobile Service End*/

}

