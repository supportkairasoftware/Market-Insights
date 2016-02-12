<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

use \ViewModels\ServiceResponse;
use \ViewModels\SearchValueModel;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \Crypt;
use \Mail;
use \AnalystEntity;
use UserEntity;
use NotificationEntity;
use Image;
use Intervention\Image\ImageManagerStatic ;
use File;

class AnalystDataProvider extends BaseDataProvider implements IAnalystDataProvider{

    /*Dev_RB Region Start*/
    public function getAnalystList($analystModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $analystEntity = new AnalystEntity();
        $sortIndex ='ModifiedDate';
        $sortDirection=Constants::$SortIndexDESC;
        $pageIndex = $analystModel->PageIndex;
        $pageSizeCount = $analystModel->PageSize;
        if(!empty($analystModel->SortIndex)){
            $sortIndex=$analystModel->SortIndex;
            $sortDirection=$analystModel->SortDirection;
        }

        $customWhere = "'1'='1'";
        if(isset($analystModel->SearchParams)){

            if(isset($analystModel->SearchParams["textKeyWord"])){
                $textKeyWord = $analystModel->SearchParams["textKeyWord"];
                $customWhere .= "and (Title like "."'%".addslashes(trim($textKeyWord))."%')";
            }
            
            if(!empty($analystModel->SearchParams["IsActive"]) && $analystModel->SearchParams["IsActive"] != Constants::$ALL){
                if($analystModel->SearchParams["IsActive"] == 'Published') {
                    $customWhere .= "  AND IsEnable = 1";
                }
                else{
                    $customWhere .= "  AND IsEnable = 0";
                }
            }
        }

        $analystList = $this->GetEntityWithPaging($analystEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $analystList->CurrentPage;
        $model->TotalPages = $analystList->TotalPages;
        $model->TotalItems = $analystList->TotalItems;
        $model->ItemsPerPage = $analystList->ItemsPerPage;
        $model->AnalystListArray = $analystList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

    public function EnableAnalyst($analystModel)
    {
        $response = new ServiceResponse();

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "AnalystID";
        $searchValueData->Value = $analystModel->AnalystID;
        array_push($searchParams, $searchValueData);

        $getAnalystData = $this->GetEntityForUpdateByFilter(new AnalystEntity(), $searchParams);
        
        $messages = array(
            'required' => trans('messages.PropertyRequired'),
            'min' => trans('messages.PropertyMin')
        );

        $analystEntity = new AnalystEntity();
		$validator = Validator::make((array)$getAnalystData->getOriginal(), $analystEntity::$Publish_rules, $messages);
        $validator->setAttributeNames($analystEntity::$niceNameArray);
        
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        if ($getAnalystData) {
            $getAnalystData->IsEnable = $analystModel->IsEnable;
			$getAnalystData->CreatedDate = date(Constants::$DefaultDateTimeFormat);
            $response->Data = $this->SaveEntity($getAnalystData);

            if ($getAnalystData->IsEnable == Constants::$IsEnableValue) {
            	$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
            	if(count($users)>0){
					foreach($users as $userID){
						$notificationEntity = new NotificationEntity();
						$notificationEntity->UserID = $userID;
						$notificationEntity->NotificationType = Constants::$NotificationType['Analyst'];
						$notificationEntity->Message = $analystModel->Title; //trans("messages.NewAnalystMessageReceivedPush");
						$notificationEntity->ImageUrl = ($getAnalystData->Image)?(Constants::$Path_AnalystImages.$getAnalystData->AnalystID.'/'.rawurlencode($getAnalystData->Image)):'';
						$notificationEntity->Key = $analystModel->AnalystID;
						$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
						$notificationEntity->save();
					}
				}
				
                $response->Message = trans('messages.AnalystEnabled');
            } else {
                $response->Message = trans('messages.AnalystDisabled');
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

    public function getAnalystDetails($analystID){
        $response = new ServiceResponse();
        $data = new stdClass();
        $analystEntity = new AnalystEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="AnalystID";
        $searchValueData->Value=$analystID;
        array_push($searchParams, $searchValueData);

        $AnalystDetail = $this->GetEntity($analystEntity,$searchParams);
        if($AnalystDetail){
            if($AnalystDetail->Image){
                $AnalystDetail->Image = asset(Constants::$Path_AnalystImages.$AnalystDetail->AnalystID.'/'.rawurlencode($AnalystDetail->Image));
            }
        }

        $data->AnalystModel = $AnalystDetail;
        $response->Data = $data;
        return $response;
    }

    /*public function RemoveAnalystImage($analystModel){
        $response = new ServiceResponse();
        $analystEntity = $this->GetEntityForUpdateByPrimaryKey(new AnalystEntity(),$analystModel->AnalystID);
        $analystEntity->Image= '';
        $result=$this->SaveEntity($analystEntity);
        if($result) {
            $response->Data=$analystEntity;
            $response->IsSuccess=true;
            $response->Message= trans('messages.AnalystImageRemove');
        } else {
            $response->Message= trans('messages.ErrorOccured');
        }
        return $response;
    }*/

    public function SaveAnalyst($analystModel,$loginUserID){
    	$response = new ServiceResponse();
        $messages = array(
            'required' => trans('messages.PropertyRequired'),
            'min' => trans('messages.PropertyMin')
        );

        $isEditMode = $analystModel['AnalystID']> 0;
        $analystEntity = new AnalystEntity();

        $validator = Validator::make((array)$analystModel, $isEditMode ? $analystEntity::$Add_rules : $analystEntity::$Add_rules, $messages);
        $validator->setAttributeNames($analystEntity::$niceNameArray);
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name ="Title";
        $searchValueData->Value = Common::GetDataWithTrim($analystModel['Title']);
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsEnable";
        $searchValueData->Value = Constants::$IsEnableValue;
        array_push($searchParams, $searchValueData);

        if ($isEditMode) {
            $customWhere = "AnalystID NOT IN ($analystModel[AnalystID])";
        } else {
            $customWhere = "";
        }

        $checkUnique = $this->GetEntityCount($analystEntity, $searchParams, "", "", $customWhere);
        if ($checkUnique == 0) {
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            
            if ($isEditMode) {
                $analystEntity = $this->GetEntityForUpdateByPrimaryKey($analystEntity, $analystModel['AnalystID']);
                if($analystEntity->IsEnable == Constants::$Value_True){
					$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
	            	if(count($users)>0){
						foreach($users as $userID){
							$notificationEntity = new NotificationEntity();
							$notificationEntity->UserID = $userID;
							$notificationEntity->NotificationType = Constants::$NotificationType['Analyst'];
							$notificationEntity->Message = 'Correction - '.$analystEntity->Title;
							$notificationEntity->ImageUrl = ($analystEntity->Image)?(Constants::$Path_AnalystImages.$analystEntity->AnalystID.'/'.rawurlencode($analystEntity->Image)):'';
							$notificationEntity->Key = $analystEntity->AnalystID;
							$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
							$notificationEntity->save();
						}
					}
				}
            }
            $analystEntity->Title = Common::GetDataWithTrim($analystModel['Title']);
            $analystEntity->Description = $analystModel['Description'];
            
            if(!$isEditMode) {
                $analystEntity->CreatedDate = $dateTime;
            }
            $analystEntity->ModifiedDate = $dateTime;

            if($analystdetails = $this->SaveEntity($analystEntity)){
                if($analystdetails){
                	$response->IsSuccess = true;
                    if($analystModel['Image']){
                        if (!is_dir(public_path(Constants::$Path_AnalystImages .$analystEntity->AnalystID))) {
                            mkdir(public_path(Constants::$Path_AnalystImages .$analystEntity->AnalystID), 0755);
                        }
                        else {
                            $path = public_path(Constants::$Path_AnalystImages.$analystEntity->AnalystID.'/');
                            foreach (glob($path . "*.*") as $file) {
                                unlink($file);
                            }
                        }
                        $destinationPath = public_path(Constants::$Path_AnalystImages .$analystEntity->AnalystID);
                       /* $fileName = $analystModel['Image']->getClientOriginalName();
                        $originalFileName = Input::file()['Image']->getClientOriginalName();
                        $resizeFile = $originalFileName->resize(120, 120);*/

                        $fileName = $analystModel['Image']->getClientOriginalName();
                        $success = $analystModel['Image']->move($destinationPath, $fileName);

                        if ($success) {
                            $analystEntity->Image = $fileName;
                            $analystEntity->save();
                        }
                    }
                }
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
            if(!$isEditMode){
                $response->Message = trans('messages.AnalystAddedSuccess');
            }else{
                $response->Message = trans('messages.AnalystUpdateSuccess');
            }
        }
        else {
            $response->Message ="'". Common::GetDataWithTrim($analystEntity['Title'])."' ".trans('messages.AnalystAlreadyExist');
        }
        return $response;
    }


    /*Dev_RB Region End*/

    /*Mobile Service Start*/
    public function Allanalyst($model){
        $response= new ServiceResponse();
        $LastIndex = 0;
		$PageSize = 10;
		
        if(!empty($model->SearchText)){
			$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
			$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			$allAnalyst = DB::table('analyst')->where("IsEnable",1)->where('Title','LIKE', '%'.$model->SearchText.'%')
			->orWhere('Description','LIKE', '%'.$model->SearchText.'%')
			->orWhere('Image','LIKE', '%'.$model->SearchText.'%')
			->orWhere('Likes','LIKE', '%'.$model->SearchText.'%')
			->orderBy('CreatedDate',Constants::$SortIndexDESC)
			->skip($LastIndex)
			->take($PageSize)->get();
		}else{
			$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
			$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			$allAnalyst = DB::table('analyst')->where("IsEnable",1)->orderBy('CreatedDate',Constants::$SortIndexDESC)->skip($LastIndex)->take($PageSize)->get();
		}
		
        if($allAnalyst){
            foreach($allAnalyst as $analystImage){
                if($analystImage->Image){
                    $analystImage->Image=asset(Constants::$Path_AnalystImages.$analystImage->AnalystID.'/'.rawurlencode($analystImage->Image));
                }
                $analystImage->CreatedDate = date(Constants::$DefaultDisplayDateTimeFormat, strtotime($analystImage->CreatedDate));
                $analystImage->ModifiedDate = date(Constants::$DefaultDisplayDateTimeFormat, strtotime($analystImage->ModifiedDate));
            }
            $response->Data=array("AnalystData"=>$allAnalyst);
        }
        else{
        	$response->Data=array("AnalystData"=>[]);
            $response->Message=trans("messages.NoAnalystFound");
        }
        $response->IsSuccess=true;
        return $response;
    }

    public function AnalystDetails($model){
        $response= new ServiceResponse();
        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "AnalystID";
        $searchValueData->Value = $model->AnalystID;
        array_push($searchParams, $searchValueData);

        $analystDetails=$this->GetEntity(new AnalystEntity(),$searchParams);
        if($analystDetails){
            if($analystDetails->Image){
                $analystDetails->Image=asset(Constants::$Path_AnalystImages.$analystDetails->AnalystID.'/'.rawurlencode($analystDetails->Image));
            }
            $response->Data=$analystDetails;
        }
        $response->IsSuccess=true;
        return $response;
    }
    
    public function AllAnalystDetails($model){
        $response= new ServiceResponse();
        
        $analystDetails=AnalystEntity::where('AnalystID', '<=', $model->FirstID)->orderBy('AnalystID',Constants::$SortIndexDESC)->where("IsEnable",1)->get();
        if($analystDetails){
            foreach($analystDetails as $analystDetailsimage){
            	$analystDetailsimage->Image=asset(Constants::$Path_AnalystImages.$analystDetailsimage->AnalystID.'/'.rawurlencode($analystDetailsimage->Image));
            }
            $response->Data=array("AnalystData"=>$analystDetails);
        }
        $response->IsSuccess=true;
        return $response;
    }
    
    public function DeleteAnalyst($model){
		$response = new ServiceResponse();
        $analystEntity = AnalystEntity::find($model->scalar);
        if($analystEntity){
			if($analystEntity->delete()){
				$response->IsSuccess = true;
	        	$response->Message = trans('messages.AnalystDeleted');	
			}else{
				$response->Message = trans('messages.ErrorOccured');	
			}
		}else{
			$response->Message=trans("messages.NoAnalystFound");
		}
		
        
        return $response;
	}
    /*Mobile Service End*/
}