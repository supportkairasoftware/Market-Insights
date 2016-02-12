<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use IlluminateQueueClosure;

use \ViewModels\ServiceResponse;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \SegmentEntity;
use \vwCallListEntity;
use \CallEntity;
use \ScriptEntity;
use PaymentPlansHistoryEntity;
use UserEntity;
use UserCallsEntity;
use NotificationEntity;

class CallDataProvider extends BaseDataProvider implements ICallDataProvider{

    public function GetSegmets(){
    	$ServiceResponse =  new ServiceResponse();
    	$SegmentEntity = SegmentEntity::select('SegmentID','SegmentName')->where('IsEnabled', 1)->get();
    	$ServiceResponse->Data = array("SegmentList" => $SegmentEntity);
    	$ServiceResponse->IsSuccess = TRUE;
    	return $ServiceResponse;
    }
    
    public function GetLookupForCall(){
    	$ServiceResponse =  new ServiceResponse();
    	$SegmentsProcessed = array();
    	$ScriptsProcessed = array();
    	$ActionsProcessed = array();
    	
    	$Segments = SegmentEntity::where('IsEnabled', 1)->get();
		    	
    	foreach($Segments as $Segment){
    		$model = new stdClass();
    		$model->key = $Segment->SegmentID;
    		$model->value = $Segment->SegmentName;
    		$model->parentid = 0;
    		$SegmentsProcessed[] = $model;	
		}
    			
		foreach(Constants::$CallActionsENUM as $key=>$action){
    		$model = new stdClass();
    		$model->key = $key;
    		$model->value = $action;
    		$model->parentid = 0;
    		$ActionsProcessed[] = $model;	
		}
		
		$totalGroups=Common::CommonGroups();
		
		$ServiceResponse->Data = array("SegmentList" => $SegmentsProcessed, "ActionList"=>$ActionsProcessed,"GroupList"=>$totalGroups);
    	$ServiceResponse->IsSuccess = TRUE;
    	return $ServiceResponse;
    }
    
    public function GetScriptLookupForCall(){
    	$ServiceResponse =  new ServiceResponse();
    	$ScriptsProcessed = array();
    	
    	$Scripts = ScriptEntity::where('IsEnable', 1)->get();
    	
    	foreach($Scripts as $Script){
    		$model = new stdClass();
    		$model->key = $Script->ScriptID;
    		$model->value = $Script->Script;
    		$model->parentid = $Script->SegmentID+0;
    		$ScriptsProcessed[] = $model;	
		}
		
		$ServiceResponse->Data = array("ScriptList" => $ScriptsProcessed);
    	$ServiceResponse->IsSuccess = TRUE;
    	return $ServiceResponse;
    }
    
    public function GetCurrentCalls($model,$userID){
    	$checkUserrole=Common::UserRoles($userID);
    	$ServiceResponse =  new ServiceResponse();
    	$LastIndex = 0;
		$PageSize = 10;
		
		$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
		$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
		
		$where = '1=1';
    	if(!empty($model->SearchText)){
			$where = "(Script Like '%".$model->SearchText."%' OR InitiatingPrice Like '%".$model->SearchText."%' OR T1 Like '%".$model->SearchText."%' OR T2 Like '%".$model->SearchText."%' OR SL Like '%".$model->SearchText."%')";
		}
		
		if($checkUserrole->RoleID == Constants::$RoleAdmin){
			$vwCallListEntity = vwCallListEntity::orderBy('UpdatedDate',Constants::$SortIndexDESC)->where("SegmentID",$model->SegmentID)->where('IsOpen',Constants::$Value_True)->whereRaw($where)->skip($LastIndex)->take($PageSize)->get();
		}
    		
    	else{
    		$calIDs = DB::table('usercalls')->where('UserID',$userID)->lists('CallID');
    		if($calIDs && count($calIDs)>0){
	    		$vwCallListEntity = vwCallListEntity::orderBy('UpdatedDate',Constants::$SortIndexDESC)
	    		->where("SegmentID",$model->SegmentID)
	    		->where('IsOpen',Constants::$Value_True)
	    		->whereRaw($where)
	    		->whereIn('CallID', $calIDs)
	    		->skip($LastIndex)->take($PageSize)->get();
	    	}else{
				$vwCallListEntity = array();
			}
		}
    	
    	if(count($vwCallListEntity)>0){
			foreach($vwCallListEntity as $vwCallList){
				$vwCallList->T1=$vwCallList->T1.'';
				$vwCallList->T2=$vwCallList->T2.'';
				$vwCallList->SL=$vwCallList->SL.'';
				$vwCallList->InitiatingPrice=$vwCallList->InitiatingPrice.'';
				$vwCallList->ResultID=($vwCallList->ResultID)+0;
				$vwCallList->ResultName=$vwCallList->ResultName.'';
				$vwCallList->ResultDescription=$vwCallList->ResultDescription?$vwCallList->ResultDescription:'';
				$vwCallList->ScriptName=$vwCallList->Script.''.(!empty($vwCallList->Contract)?' ('.$vwCallList->Contract.')':'').(!empty($vwCallList->SubSection)?' ('.$vwCallList->SubSection.')':'');
				$vwCallList->Image=$vwCallList->Image?asset(Constants::$Path_ScriptImages.$vwCallList->ScriptID.'/'.rawurlencode($vwCallList->Image)):'';
				$vwCallList->CreatedDate=date_format(date_create($vwCallList->CreatedDate),Constants::$DefaultDisplayDateTimeFormat);
			}
		}
		
    	$ServiceResponse->Data = array("CallList" => $vwCallListEntity);
    	$ServiceResponse->IsSuccess = TRUE;
    	
    	return $ServiceResponse;	
    }
    
    public function GetHistoryCalls($model,$userID){
    	$checkUserrole=Common::UserRoles($userID);
    	$ServiceResponse =  new ServiceResponse();
    	$LastIndex = 0;
		$PageSize = 10;
		
		$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
		$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			
		$where = '1=1';
		
		if(!empty($model->SearchText)){
			$where = "( and Script Like '%".$model->SearchText."%' OR InitiatingPrice Like '%".$model->SearchText."%' OR T1 Like '%".$model->SearchText."%' OR T2 Like '%".$model->SearchText."%' OR SL Like '%".$model->SearchText."%')";
		}
		
		if($checkUserrole->RoleID == Constants::$RoleAdmin){
			$vwCallListEntity = vwCallListEntity::orderBy('UpdatedDate',Constants::$SortIndexDESC)->where("SegmentID",$model->SegmentID)->where('IsOpen',Constants::$Value_False)->whereRaw($where)->skip($LastIndex)->take($PageSize)->get();
		}
    		
    	else{
    		
    		$calIDs = DB::table('usercalls')->where('UserID',$userID)->lists('CallID');
    		if($calIDs && count($calIDs)>0){
	    		$vwCallListEntity = vwCallListEntity::orderBy('UpdatedDate',Constants::$SortIndexDESC)
	    		->where("SegmentID",$model->SegmentID)
	    		->where('IsOpen',Constants::$Value_False)
	    		->whereRaw($where)
	    		->whereIn('CallID', $calIDs)
	    		->skip($LastIndex)->take($PageSize)->get();
	    	}else{
				$vwCallListEntity = array();
			}
		}
    	
    	if(count($vwCallListEntity)>0){
			foreach($vwCallListEntity as $vwCallList){
				$vwCallList->T1=$vwCallList->T1.'';
				$vwCallList->T2=$vwCallList->T2.'';
				$vwCallList->SL=$vwCallList->SL.'';
				$vwCallList->InitiatingPrice=$vwCallList->InitiatingPrice.'';
				$vwCallList->ResultID=($vwCallList->ResultID)+0;
				$vwCallList->ResultName=$vwCallList->ResultName.'';
				$vwCallList->ResultDescription=$vwCallList->ResultDescription?$vwCallList->ResultDescription:'';
				$vwCallList->ScriptName=$vwCallList->Script.''.(!empty($vwCallList->Contract)?' ('.$vwCallList->Contract.')':'').(!empty($vwCallList->SubSection)?' ('.$vwCallList->SubSection.')':'');
				$vwCallList->Image=$vwCallList->Image?asset(Constants::$Path_ScriptImages.$vwCallList->ScriptID.'/'.rawurlencode($vwCallList->Image)):'';
				$vwCallList->CreatedDate=date_format(date_create($vwCallList->CreatedDate),Constants::$DefaultDisplayDateTimeFormat);
				
			}
		}
    	$ServiceResponse->Data = array("CallList" => $vwCallListEntity);
    	$ServiceResponse->IsSuccess = TRUE;
    	
    	return $ServiceResponse;	
    }
    
    public function SaveCall($callModel){
    	$ServiceResponse =  new ServiceResponse();
    	
    	if(property_exists($callModel, "CallID") && $callModel->CallID){
    		$isEditMode=TRUE;
			$CallEntity = CallEntity::find($callModel->CallID);
		}else{
			$isEditMode=false;
			$CallEntity = new CallEntity();	
			$CallEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
			$CallEntity->UpdatedDate = $CallEntity->CreatedDate;
		}
		
		if($CallEntity){
			
			$CallEntity->ScriptID = $callModel->ScriptID;
			$CallEntity->Action = $callModel->Action;
			$CallEntity->InitiatingPrice = $callModel->InitiatingPrice;
			$CallEntity->GroupID=property_exists($callModel,'GroupID') && !$isEditMode?serialize($callModel->GroupID):$CallEntity->GroupID;
            $CallEntity->T1 = round($callModel->T1,Constants::$DecimalValue);
            $CallEntity->T2 = round($callModel->T2,Constants::$DecimalValue);
            $CallEntity->SL = round($callModel->SL,Constants::$DecimalValue);
			$CallEntity->StrikePrice = $callModel->StrikePrice > 0?$callModel->StrikePrice:0;
			$CallEntity->ResultDescription =property_exists($callModel,'ResultDescription')? $callModel->ResultDescription:'';
			$CallEntity->Contract = property_exists($callModel,'Contract')? $callModel->Contract:'';
			$CallEntity->SubSection = property_exists($callModel,'SubSection')? $callModel->SubSection:'';
			
			if($CallEntity->save()){
				
				$SegmentName=DB::table('scripts')->select('lu_segments.SegmentID','scripts.Script')
				        ->leftJoin('lu_segments', 'scripts.SegmentID', '=', 'lu_segments.SegmentID')
				        ->where('scripts.ScriptID',$callModel->ScriptID)
				        ->first();
				
				/*Save Notification for all users*/
	            $userlist=array();
	            
	            if($isEditMode){
	            	$callModel->GroupID = unserialize($CallEntity->GroupID);
	            }
	            
		        $users=array();
			        if(in_array(Constants::$AllGroupID,$callModel->GroupID)){
						$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
					}
					else{
						foreach($callModel->GroupID as $value){
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
			        	if(!$isEditMode){
				        	$UserCallsEntity = new UserCallsEntity();
				        	$UserCallsEntity->CallID = $CallEntity->CallID;
				        	$UserCallsEntity->UserID = $userID;
				        	$UserCallsEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				        	$UserCallsEntity->save();
			        	}
			        	
						$notificationEntity = new NotificationEntity();
						$notificationEntity->UserID = $userID;
						$notificationEntity->NotificationType = Constants::$SegmentType[$SegmentName->SegmentID];
						$notificationEntity->Message = $isEditMode?trans("messages.UpdateCallsMessageReceivedPush",array('ScriptName' =>$SegmentName->Script,'Action'=>Constants::$CallActionsENUM[$CallEntity->Action],'initprice'=>$CallEntity->InitiatingPrice,'T1'=>$CallEntity->T1,'T2'=>$CallEntity->T2,'SL'=>$CallEntity->SL)):trans("messages.NewCallsMessageReceivedPush",array('ScriptName' =>$SegmentName->Script,'Action'=>Constants::$CallActionsENUM[$CallEntity->Action],'initprice'=>$CallEntity->InitiatingPrice,'T1'=>$CallEntity->T1,'T2'=>$CallEntity->T2,'SL'=>$CallEntity->SL));
						$notificationEntity->Key = $SegmentName->SegmentID;//$CallEntity->CallID;
						$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
						$notificationEntity->save();
					}
				
				$ServiceResponse->Message =$isEditMode?trans("messages.CallUpdateSuccess"):trans("messages.CallSaveSuccess");
				$ServiceResponse->IsSuccess = TRUE;
			}else{
				$ServiceResponse->Message = trans("messages.ErrorOccured");	
			}
		}else{
			$ServiceResponse->Message = trans("messages.ErrorOccured");
		}
    	
    	return $ServiceResponse;	
		
	}
    
    public function UpdateCall($callModel){
		$response =  new ServiceResponse();
    	
    	if(property_exists($callModel, "CallID") && $callModel->CallID){
			$messages = array(
	            'required' => trans('messages.PropertyRequired')
	        );
	        
	        $validator = Validator::make((array)$callModel, CallEntity::$updateCall_rules, $messages);
	        $validator->setAttributeNames(CallEntity::$niceNameArray);
	        
	        if ($validator->fails()) {
	            $response->Message = Common::getValidationMessagesFormat($validator->messages());
	            return $response;
			}else{
				$CallEntity = CallEntity::find($callModel->CallID);
				$CallEntity->ScriptID = $callModel->ScriptID;
				$CallEntity->Action = $callModel->Action;
				$CallEntity->InitiatingPrice = $callModel->InitiatingPrice;

				$CallEntity->T1 = round($callModel->T1,Constants::$DecimalValue);
				$CallEntity->T2 = round($callModel->T2,Constants::$DecimalValue);
				$CallEntity->SL = round($callModel->SL,Constants::$DecimalValue);
				$CallEntity->ResultID = property_exists($callModel,'ResultID')?$callModel->ResultID:'';
				$CallEntity->ResultDescription =property_exists($callModel,'ResultDescription')? $callModel->ResultDescription:'';
				
				if($CallEntity->save()){
					$response->Message = trans("messages.CallUpdateSuccess");
					$response->IsSuccess = TRUE;
				}else{
					$response->Message = trans("messages.ErrorOccured");	
				}
			}
			
		}else{
			$response->Message = trans("messages.ErrorOccured");	
		}
    	
    	return $response;
	}
	
	public function Allcalllist($callModel){
		$ServiceResponse =  new ServiceResponse();
		$LastIndex = !empty($callModel->LastID)?$callModel->LastID:'0';
		$PageSize = !empty($callModel->PageSize)?$callModel->PageSize:'10';
		
    	$CallEntity = CallEntity::select('scripts.SegmentID','scripts.Script as ScriptName','CreatedDate','scripts.Image','CallID','calls.ScriptID as ScriptID','Action','InitiatingPrice','T1','T2','SL','ResultDescription','ResultID')->leftJoin('scripts','calls.ScriptID','=','scripts.ScriptID')->orderBy('UpdatedDate',Constants::$SortIndexDESC)->where('IsOpen',Constants::$Value_True)->skip($LastIndex)->take($PageSize)->get();
    	
    	if(count($CallEntity)>0){
			foreach($CallEntity as $scriptImage){
				$scriptImage->Image=$scriptImage->Image?asset(Constants::$Path_ScriptImages.$scriptImage->ScriptID.'/'.rawurlencode($scriptImage->Image)):'';
				$scriptImage->T1 = round($scriptImage->T1,Constants::$DecimalValue).'';
				$scriptImage->T2 =round($scriptImage->T2,Constants::$DecimalValue).'';
				$scriptImage->SL =round($scriptImage->SL,Constants::$DecimalValue).'';
				$scriptImage->InitiatingPrice =$scriptImage->InitiatingPrice.'';
				$scriptImage->CreatedDate=date_format(date_create($scriptImage->CreatedDate),Constants::$DefaultDisplayDateTimeFormat);
				$scriptImage->ResultID=$scriptImage->ResultID.'';
				$scriptImage->ResultDescription=$scriptImage->ResultDescription.'';
				
			}
			$ServiceResponse->Data = array("CallList" => $CallEntity);
		}
    	
		else{
        	$ServiceResponse->Data=array("CallList"=>[]);
            $ServiceResponse->Message=trans("messages.NoCallFound");
        }    		
    	$ServiceResponse->IsSuccess = TRUE;
    	return $ServiceResponse;
	}
	
	public function HideCall($callID){
		$response =  new ServiceResponse();
		$calldetails = CallEntity::find($callID);
		if($calldetails){
			//$calldetails->IsHidden = Constants::$Value_True;
			//if($calldetails->save())
			if(DB::delete("delete from calls where CallID = $callID"))
			{
				$response->IsSuccess = TRUE;
				$response->Message = trans("messages.CallDeletedSuccessfully");
			}else{
				$response->Message = trans("messages.ErrorOccured");	
			}	
		}else{
			$response->Message = trans("messages.ErrorOccured");	
		}
		return $response;
	}
	
	public function CallResultUpdate($callresult){
		
		$ServiceResponse =  new ServiceResponse();
		$date = date(Constants::$DefaultDisplayDateTimeFormat);
		
		$calldetails=CallEntity::find($callresult->CallID);
		//$calldetails->ResultID = $callresult->ResultID;

		switch ($callresult->ResultID) {
		    case Constants::$T1Call:
		        $resultDescription="T1 Achieved, $date ";
		        $calldetails->ResultID = $callresult->ResultID;
		        break;
		    case Constants::$T2Call:
		        $resultDescription="T2 Achieved, $date ";
		        $calldetails->ResultID = $callresult->ResultID;
		        $calldetails->IsOpen = Constants::$Value_False;
		        break;
		    case Constants::$SLCall:
		        $resultDescription="SL Hit, $date ";
		        $calldetails->ResultID = $callresult->ResultID;
		        $calldetails->IsOpen = Constants::$Value_False;
		        break;
		    case Constants::$ParcelBooked:
		    	$resutvalue=$callresult->Value;
		        $resultDescription="Partial booked at $resutvalue , $date";
		        //$calldetails->IsOpen = Constants::$Value_False;
		        break;
		    case Constants::$CallClosed:
		    	$resutvalue=$callresult->Value;
		        $resultDescription="Call closed at $resutvalue , $date";
		        $calldetails->IsOpen = Constants::$Value_False;
		        break;
		    default:
		    	echo "not update call details";
		        break;
		}
		
		$ResultDescriptionOld = $calldetails->ResultDescription;
		$calldetails->ResultDescription = empty($ResultDescriptionOld)?$resultDescription:"$ResultDescriptionOld\r\n$resultDescription";		$calldetails->UpdatedDate = date(Constants::$DefaultDateTimeFormat);
		
		if($calldetails->save()){
			$callGroups=CallEntity::where('CallID',$callresult->CallID)->first();
			$groupIDs=unserialize($callGroups->GroupID);
			
			$SegmentName=DB::table('scripts')->select('lu_segments.SegmentID','scripts.Script')
				        ->leftJoin('lu_segments', 'scripts.SegmentID', '=', 'lu_segments.SegmentID')
				        ->where('scripts.ScriptID',$calldetails->ScriptID)
				        ->first();
					
			/*Save Notification for all users*/
	        $userlist=array();
	        $users=array();
	        if(in_array(Constants::$AllGroupID,$groupIDs)){
				$users=UserEntity::where('IsEnable',Constants::$Value_True)->lists('UserID');
			}
			else{
				foreach($groupIDs as $value){
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
	        
	        switch ($callresult->ResultID) {
			    case Constants::$T1Call:
			       $messages="T1 achieved for ".$SegmentName->Script." Wait for T2.";
			        break;
			    case Constants::$T2Call:
			       $messages="T2 achieved for ".$SegmentName->Script.". Kindly close call.";
			        break;
			    case Constants::$SLCall:
			       $messages="SL hit for ".$SegmentName->Script.". Kindly close call.";
			        break;
			    case Constants::$ParcelBooked:
			    	$resutvalue=$callresult->Value;
			    	$messages="Book partial profite for ".$SegmentName->Script." at .$resutvalue.";
			        break;
			    case Constants::$CallClosed:
			    	$resutvalue=$callresult->Value;
			    	$messages="Exit from ".$SegmentName->Script." at .$resutvalue. and close call.";
			        break;
			    default:
			    	echo "not update call details";
			        break;
			};
			
	        foreach($users as $userID){
	        	
				$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $userID;
				$notificationEntity->NotificationType = Constants::$SegmentType[$SegmentName->SegmentID];
				$notificationEntity->Message = $messages;
				$notificationEntity->Key = $SegmentName->SegmentID;//$calldetails->CallID;
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->IsPast = ($calldetails->IsOpen == Constants::$Value_False);
				$notificationEntity->save();
			}
		}
		
		$ServiceResponse->IsSuccess=true;
		$ServiceResponse->Message=trans("messages.CallUpdateSuccess");
		return $ServiceResponse;
	}
	
	public function EditCall($callModel){
		$response =  new ServiceResponse();
		$callDetail = CallEntity::select('scripts.SegmentID','scripts.Script as ScriptName','CreatedDate','scripts.Image','CallID','calls.ScriptID as ScriptID','Action','StrikePrice','InitiatingPrice','T1','T2','SL')->leftJoin('scripts','calls.ScriptID','=','scripts.ScriptID')->where('CallID',$callModel->CallID)->first();
		$callDetail->Image=$callDetail->Image?asset(Constants::$Path_ScriptImages.$callDetail->ScriptID.'/'.rawurlencode($callDetail->Image)):'';
		$callDetail->T1 = round($callDetail->T1,Constants::$DecimalValue).'';
		$callDetail->T2 = round($callDetail->T2,Constants::$DecimalValue).'';
		$callDetail->SL = round($callDetail->SL,Constants::$DecimalValue).'';
		$callDetail->InitiatingPrice =$callDetail->InitiatingPrice.'';
		$callDetail->StrikePrice = $callDetail->StrikePrice > 0?$callDetail->StrikePrice:0;
		$callDetail->CreatedDate=date_format(date_create($callDetail->CreatedDate),Constants::$DefaultDisplayDateTimeFormat);
		$response->Data=$callDetail;
		return $response;
	}
	
	public function GetAllHistoryCallList($model){
    	$ServiceResponse =  new ServiceResponse();
    	$LastIndex = 0;
		$PageSize = 10;
		
		$LastIndex = !empty($model->LastID)?$model->LastID:$LastIndex;
		$PageSize = !empty($model->PageSize)?$model->PageSize:$PageSize;
			
    	if(!empty($model->SearchText)){
			$where = "(Script Like '%".$model->SearchText."%' OR InitiatingPrice Like '%".$model->SearchText."%' OR T1 Like '%".$model->SearchText."%' OR T2 Like '%".$model->SearchText."%' OR SL Like '%".$model->SearchText."%')";
		}else{
			$where = '1=1';
		}
		
    	$vwCallListEntity = vwCallListEntity::where('IsOpen', 0)->whereRaw($where)->skip($LastIndex)->take($PageSize)->get();
    	
    	$ServiceResponse->Data = array("CallList" => $vwCallListEntity);
    	$ServiceResponse->IsSuccess = TRUE;
    	
    	return $ServiceResponse;	
    }
    
    public function AllCurrentcalllist($callModel){
		$ServiceResponse =  new ServiceResponse();
		$LastIndex = !empty($callModel->LastID)?$callModel->LastID:'0';
		$PageSize = !empty($callModel->PageSize)?$callModel->PageSize:'10';
		
    	$CallEntity = CallEntity::select('scripts.SegmentID','scripts.Script as ScriptName','CreatedDate','scripts.Image','CallID','calls.ScriptID as ScriptID','Action','InitiatingPrice','T1','T2','SL','ResultDescription','ResultID')->leftJoin('scripts','calls.ScriptID','=','scripts.ScriptID')->orderBy('UpdatedDate',Constants::$SortIndexDESC)->where('IsOpen',Constants::$Value_True)->skip($LastIndex)->take($PageSize)->get();
    	
    	if(count($CallEntity)>0){
			foreach($CallEntity as $scriptImage){
				$scriptImage->Image=$scriptImage->Image?asset(Constants::$Path_ScriptImages.$scriptImage->ScriptID.'/'.rawurlencode($scriptImage->Image)):'';
				$scriptImage->T1 = round($scriptImage->T1,Constants::$DecimalValue).'';
				$scriptImage->T2 =round($scriptImage->T2,Constants::$DecimalValue).'';
				$scriptImage->SL =round($scriptImage->SL,Constants::$DecimalValue).'';
				$scriptImage->InitiatingPrice =$scriptImage->InitiatingPrice.'';
				$scriptImage->CreatedDate=date_format(date_create($scriptImage->CreatedDate),Constants::$DefaultDisplayDateTimeFormat);
				$scriptImage->ResultID=$scriptImage->ResultID.'';
				$scriptImage->ResultDescription=$scriptImage->ResultDescription.'';
			}
			$ServiceResponse->Data = array("CallList" => $CallEntity);
		}
    	
		else{
        	$ServiceResponse->Data=array("CallList"=>[]);
            $ServiceResponse->Message=trans("messages.NoCallFound");
        }    		
    	$ServiceResponse->IsSuccess = TRUE;
    	return $ServiceResponse;
	}
}