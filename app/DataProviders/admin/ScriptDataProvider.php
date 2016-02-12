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
use ScriptEntity;
use \CallEntity;
use SegmentEntity;
use vwScriptEntity;


class ScriptDataProvider extends BaseDataProvider implements IScriptDataProvider{

    public function getScriptList($scriptModel){

        $response = new ServiceResponse();
        $model= new stdClass();
        $vwScriptEntity = new vwScriptEntity();
        $sortIndex='ScriptID';
        $sortDirection=Constants::$SortIndexDESC;
        $pageIndex = $scriptModel->PageIndex;
        $pageSizeCount = $scriptModel->PageSize;
        if(!empty($scriptModel->SortIndex)){
            $sortIndex=$scriptModel->SortIndex;
            $sortDirection=$scriptModel->SortDirection;
        }
        
        $customWhere = "'1'='1'";
        if(isset($scriptModel->SearchParams)){

            if(isset($scriptModel->SearchParams["textKeyWord"])){
                $textKeyWord = $scriptModel->SearchParams["textKeyWord"];
                $customWhere .= "and (SegmentName like "."'%".addslashes(trim($textKeyWord))."%')";
            }
            
            if(isset($scriptModel->SearchParams["ScriptName"])){
                $scriptName = $scriptModel->SearchParams["ScriptName"];
                $customWhere .= "and (Script like "."'%".addslashes(trim($scriptName))."%')";
            }
            
            if(!empty($scriptModel->SearchParams["IsActive"]) && $scriptModel->SearchParams["IsActive"] != Constants::$ALL){
                if($scriptModel->SearchParams["IsActive"] == 'Enabled') {
                    $customWhere .= "  AND IsEnable = 1";
                }
                else{
                    $customWhere .= "  AND IsEnable = 0";
                }
            }
        }
        
        $scriptList = $this->GetEntityWithPaging($vwScriptEntity,"",$pageIndex,$pageSizeCount,$sortIndex,$sortDirection,$customWhere);

        $model->CurrentPage = $scriptList->CurrentPage;
        $model->TotalPages = $scriptList->TotalPages;
        $model->TotalItems = $scriptList->TotalItems;
        $model->ItemsPerPage = $scriptList->ItemsPerPage;
        $model->ScriptListArray = $scriptList->Items;
        $response->Data=$model;
        $response->IsSuccess = true;
        return $response;
    }

   public function EnableScript($scriptModel)
   {
        $response = new ServiceResponse();
		
		$searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "ScriptID";
        $searchValueData->Value = $scriptModel->ScriptID;
        array_push($searchParams, $searchValueData);

        $getScriptData = $this->GetEntityForUpdateByFilter(new ScriptEntity(), $searchParams);

        if ($getScriptData) {
            $getScriptData->IsEnable = $scriptModel->IsEnable;

            $response->Data = $this->SaveEntity($getScriptData);

            if ($getScriptData->IsEnable == Constants::$IsEnableValue) {
                $response->Message = trans('messages.ScriptEnabled');
            } else {
                $response->Message = trans('messages.ScriptDisabled');
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
    
    public function getScriptDetails($scriptID){
        $response = new ServiceResponse();
        $data = new stdClass();
        $scriptEntity = new ScriptEntity();

        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="ScriptID";
        $searchValueData->Value=$scriptID;
        array_push($searchParams, $searchValueData);

        $scriptDetail = $this->GetEntity($scriptEntity,$searchParams);
        
        if($scriptDetail){
            if($scriptDetail->Image){
                $scriptDetail->Image = asset(Constants::$Path_ScriptImages.$scriptDetail->ScriptID.'/'.rawurlencode($scriptDetail->Image));
            }
        }
        
        $searchParams = array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="IsEnabled";
        $searchValueData->Value=Constants::$Value_True;
        array_push($searchParams, $searchValueData);
        
        $segmentdetails = $this->GetEntityList(new SegmentEntity(),$searchParams);
        
        $data->ScriptModel['ScriptDetails'] = $scriptDetail;
        $data->ScriptModel['SegmentList']=$segmentdetails;        
        $response->Data = $data;
        return $response;
    }

    public function SaveScript($scriptModel,$cUser){
    	$response = new ServiceResponse();
        
        $messages = array(
            'required' => trans('messages.PropertyRequired'),
            'min' => trans('messages.PropertyMin'),
            'max' => trans('messages.PropertyMax')
        );

        $isEditMode = ($scriptModel['ScriptID']) > 0;
        $scriptEntity= new ScriptEntity();
		$validator = Validator::make((array)$scriptModel, $scriptEntity::$Add_rules,$messages);
		$validator->setAttributeNames($scriptEntity::$niceNameArray);
        
        if ($validator->fails()) {
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

       
        $checkUnique = ScriptEntity::where("Script",trim($scriptModel['Script']))->where("SegmentID",$scriptModel['SegmentID'])->where('ScriptID','!=',$scriptModel['ScriptID'])->first();
        if ($checkUnique == null) {
            
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            if ($isEditMode) {
                $scriptEntity = $this->GetEntityForUpdateByPrimaryKey($scriptEntity, $scriptModel['ScriptID']);
            }
            $scriptEntity->Script = Common::GetDataWithTrim($scriptModel['Script']);
            $scriptEntity->SegmentID=$scriptModel['SegmentID'];

            if($scriptDetails = $this->SaveEntity($scriptEntity)){
            	if($scriptModel['Image']){
                    if (!is_dir(public_path(Constants::$Path_ScriptImages .$scriptEntity->ScriptID))) {
                        mkdir(public_path(Constants::$Path_ScriptImages .$scriptEntity->ScriptID), 0755);
                    }
                    else {
                        $path = public_path(Constants::$Path_ScriptImages.$scriptEntity->ScriptID.'/');
                        foreach (glob($path . "*.*") as $file) {
                            unlink($file);
                        }
                    }
                    $destinationPath = public_path(Constants::$Path_ScriptImages .$scriptEntity->ScriptID);
                    $fileName = $scriptModel['Image']->getClientOriginalName();
                    $success = $scriptModel['Image']->move($destinationPath, $fileName);

                    if ($success) {
                        $scriptEntity->Image = $fileName;
                        $scriptEntity->save();
                    }
                }
                $response->Message = !$isEditMode?trans('messages.ScriptAddedSuccess'):trans('messages.ScriptUpdateSuccess');
                $response->IsSuccess = true;
            }else{
                $response->Message = trans('messages.ErrorOccured');
            }
        }
        else {
            $response->Message ="'". Common::GetDataWithTrim($scriptModel['Script']). "' ".trans('messages.ScriptAlreadyExist');
        }
        return $response;
    }
    
    public function DeleteScript($scriptID){
    	$response = new ServiceResponse();
		$scriptEntity = ScriptEntity::find($scriptID);
		if($scriptEntity){
			$callEntity = CallEntity::where("ScriptID", $scriptEntity->ScriptID)->first();
			if(!$callEntity){
				if($scriptEntity->delete()){
					$response->IsSuccess = TRUE;
					$response->Message = trans("messages.ScriptDeleteSuccess");
				}else{
					$response->Message = trans("messages.ErrorOccured");
				}
			}else{
				$response->Message = trans("messages.ScriptAlreadyInUse");
			}
			$scriptEntity = ScriptEntity::find($scriptID);
		}else{
			$response->Message = trans("messages.ErrorOccured");
		}
		return $response;
	}
    
    public function GetSegments(){
    	return SegmentEntity::get();
    }
}