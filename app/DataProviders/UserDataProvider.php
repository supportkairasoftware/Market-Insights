<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

use \ViewModels\ServiceResponse;
use \UserEntity;
use \Infrastructure\Constants;
use \MessageEntity;
use \NotificationEntity;
use \ChatEntity;
use \UserRoleEntity;
use \AnalystEntity;
use \FundamentalEntity;
use ErrorLogEntity;

class UserDataProvider extends BaseDataProvider implements IUserDataProvider {
	
	public function SendChat($chatModel, $file, $currentUser){
		
		$serviceResponse = new ServiceResponse();
		$chatEntity = new ChatEntity();
		
		if(!empty($file)){
			$path = $file->getRealPath();
			$fileName = $file->getClientOriginalName();
			$extension = $file->getClientOriginalExtension();
			$newFileName = md5($fileName.time()).'.'.$extension;
			$size = $file->getSize();
			
			//is_dir(base_path(Constants::$Path_ChatImages)) || mkdir(base_path(Constants::$Path_ChatImages));
			
			$success = $file->move(public_path(Constants::$Path_ChatImages), $newFileName);
			if($success){
				$chatEntity->ImageName = $fileName;
				$chatEntity->ImageGUID = $newFileName;
			}else{
				$serviceResponse->IsSuccess = FALSE;
				$serviceResponse->Message = trans("message.ErrorOccured");
				return $serviceResponse;
			}
		}
		
		if($currentUser->RoleID != Constants::$RoleAdmin){
			$pastChat = DB::table('chats')->whereRaw("FromUserID = ".$chatModel->FromUserID." or ToUserID = ".$chatModel->FromUserID,array())->orderBy("Date","Desc")->first();
			if($pastChat){
				$toUserID = ($pastChat->FromUserID == $chatModel->FromUserID)?$pastChat->ToUserID:$pastChat->FromUserID;
				$chatModel->ToUserID = $toUserID;
			}else{
				$chatModel->ToUserID = DB::table('allowedchatadmin')->where('IsDefault',1)->pluck('AdminID')||1;	
			}
		}
		
		$chatEntity->Message = $chatModel->Message;
		$chatEntity->FromUserID = $chatModel->FromUserID;
		$chatEntity->ToUserID = $chatModel->ToUserID;
		$chatEntity->Date = date(Constants::$DefaultDateTimeFormat);
		$validator = Validator::make((array)$chatModel, ChatEntity::$sendChat_rules);
		$validator->setAttributeNames(ChatEntity::$niceNameArray);
				
		if ($validator->fails()){
			$serviceResponse->ErrorMessages = $validator->messages();
			return $serviceResponse;
		}else{
			if($chatEntity->save())	{
				$serviceResponse->IsSuccess = TRUE;
				
				$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $chatEntity->ToUserID;
				$notificationEntity->NotificationType = Constants::$NotificationType['Chat'];
				$notificationEntity->Message = trans("messages.NewChatMessageReceivedPush", array("name"=>$currentUser->FirstName.' '.$currentUser->LastName,"message"=>$chatEntity->Message?$chatEntity->Message:"Image"));
				$notificationEntity->ImageUrl = ($chatEntity->ImageGUID)?(Constants::$Path_ChatImages.rawurlencode($chatEntity->ImageGUID)):'';
				$notificationEntity->Key = $chatEntity->FromUserID;
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->save();
				
			}else{
				$serviceResponse->IsSuccess = FALSE;
				$serviceResponse->Message = trans("message.ErrorOccured");
			}
		}
		return $serviceResponse;
	}
	
	public function ViewChat($currentUserID, $isAdmin, $timeStamp){
		$serviceResponse = new ServiceResponse();
		$result = DB::select('call getChatList(? , ?, ?, ?, ?)',array($currentUserID, asset(Constants::$Path_ChatImages), Constants::$DefaultDisplayDateTimeFormatSQL, $isAdmin?1:0, $timeStamp));
		
		if($result && count($result)>0){
			$lastChat = $result[count($result)-1];
			if($isAdmin){
				$lastChatWith = ($currentUserID == $lastChat->FromUserID)?$lastChat->FromUserID:$lastChat->ToUserID;	
			}else{
				$lastChatWith = ($currentUserID == $lastChat->FromUserID)?$lastChat->ToUserID:$lastChat->FromUserID;
			}
			
			$userEntity = UserEntity::where("UserID",$lastChatWith)->first();
			$name = !empty($userEntity)?$userEntity->FirstName . ' ' . $userEntity->LastName:'';
			$chatImageUrl = !empty($userEntity) && !empty($userEntity->UserImageUrl)?asset(Constants::$Path_ProfileImages.$userEntity->UserID.'/'.rawurlencode($userEntity->UserImageUrl)):'';
			$serviceResponse->Data = array("ChatList"=>$result, "ChatName"=>$name,"ChatImageUrl"=>$chatImageUrl);
			$serviceResponse->IsSuccess = TRUE;
		}else{
			$serviceResponse->Data = array("ChatList"=>array(), "ChatName"=>"","ChatImageUrl"=>"");
			$serviceResponse->Message = trans("messages.NoRecordFound");
			$serviceResponse->IsSuccess = TRUE;
		}
		
		return $serviceResponse;
	}
	
	public function DeleteChat($chatID, $currentUserID){
		$serviceResponse = new ServiceResponse();
		$chatEntity = ChatEntity::where("ChatID", $chatID)->first();
		
		if($chatEntity && $chatEntity->FromUserID == $currentUserID){
			$chatEntity->IsDeletedBySender = 1;
			$chatEntity->save();
			$serviceResponse->IsSuccess = TRUE;
			$serviceResponse->Message = trans("messages.ChatDeletedSuccessfully");
		}else if($chatEntity && $chatEntity->ToUserID == $currentUserID){
			$chatEntity->IsDeletedByReceiver = 1;
			$chatEntity->save();
			$serviceResponse->IsSuccess = TRUE;
			$serviceResponse->Message = trans("messages.ChatDeletedSuccessfully");
		}else{
			$serviceResponse->Message = trans("messages.UnauthorizeAction");
		}
		
		return $serviceResponse;
	}
	
	public function UserListChat($seachModel, $UserID){
		$serviceResponse = new ServiceResponse();
		$LastIndex = 0;
		$PageSize = 10;
		
		if(!empty($seachModel->SearchText)){
			$LastIndex = !empty($seachModel->LastID)?$seachModel->LastID:$LastIndex;
			$PageSize = !empty($seachModel->PageSize)?$seachModel->PageSize:$PageSize;
			$result = DB::select('call searchUserListChat(? , ?, ?, ?, ?)',array($seachModel->SearchText, asset(Constants::$Path_ProfileImages), Constants::$DefaultDisplayDateTimeFormatSQL, $LastIndex, $PageSize));
		}else{
			$LastIndex = !empty($seachModel->LastID)?$seachModel->LastID:$LastIndex;
			$PageSize = !empty($seachModel->PageSize)?$seachModel->PageSize:$PageSize;
			$result = DB::select('call getUserListChat(? , ?, ?, ?, ?)',array($UserID, asset(Constants::$Path_ProfileImages), Constants::$DefaultDisplayDateTimeFormatSQL, $LastIndex, $PageSize));
		}
		
		if($result && count($result)>0){
			$serviceResponse->Data = array("UserList"=>$result);
			$serviceResponse->IsSuccess = TRUE;
		}else{
			$serviceResponse->IsSuccess = TRUE;
			$serviceResponse->Data = array("UserList"=>[]);
			$serviceResponse->Message = trans("messages.NoRecordFound");
		}
		
		return $serviceResponse;
	}

	public function GetProfile($user){
		$serviceResponse = new ServiceResponse();
		$userdetails=UserEntity::find($user->Data->UserID);
		if(!is_null($userdetails->UserImageUrl)){
			$userdetails->UserImageUrl=asset(Constants::$Path_ProfileImages.$userdetails->UserID.'/'.rawurlencode($userdetails->UserImageUrl));
		}
		$serviceResponse->Data=$userdetails;
		$serviceResponse->IsSuccess=true;
		return $serviceResponse;
	}
	public function SaveProfile($userdetails,$userimage,$user){
		$serviceResponse = new ServiceResponse();
		$dateTime = date(Constants::$DefaultDateTimeFormat);

		$userentity=UserEntity::find($user);
		$userentity->FirstName=$userdetails->FirstName;
		$userentity->LastName=$userdetails->LastName;
		$userentity->Email=$userentity->Email;
		$userentity->Mobile=$userentity->Mobile;
		$userentity->State=$userdetails->State;
		$userentity->City=$userdetails->City;
		$userentity->FbID=$userdetails->FbID;
		$userentity->ModifiedDate=$dateTime;
		$userentity->save();
		$userResult = $userentity->save();

		if($userResult) {
			if ($userimage) {
				if (!is_dir(public_path(Constants::$Path_ProfileImages .$userentity->UserID))) {
					mkdir(public_path(Constants::$Path_ProfileImages .$userentity->UserID), 0755);
				}
				else {
					$path = public_path(Constants::$Path_ProfileImages.$userentity->UserID.'/');
					// Loop over all of the files in the folder
					foreach (glob($path . "*.*") as $file) {
						unlink($file); // Delete each file through the loop
					}
				}
				$destinationPath = public_path(Constants::$Path_ProfileImages .$userentity->UserID);
				$fileName = $userimage->getClientOriginalName();
				$success = $userimage->move($destinationPath, $fileName);

				if ($success) {
					$userentity->UserImageUrl = $fileName;
					$userentity->save();
				}
			}
			$serviceResponse->IsSuccess=true;
			$serviceResponse->Message=trans("messages.AccountUpdateSuccess");
		}else{
			$serviceResponse->Message=trans("messages.ErrorOccured");	
		}
		
		if($userentity->UserImageUrl){
				$userentity->UserImageUrl=asset(Constants::$Path_ProfileImages.$userentity->UserID.'/'.rawurlencode($userentity->UserImageUrl));
		}
		$serviceResponse->Data=$userentity;
		return $serviceResponse;
	}
	public function Likeadd($likemodel){
		$serviceResponse = new ServiceResponse();
		if($likemodel->Type =='Analyst'){
			$result=AnalystEntity::find($likemodel->ID);
			if($result){
				$result->Likes=($result->Likes)+1;
				$result->save();	
				$serviceResponse->Data=$result;
				$serviceResponse->IsSuccess=true;
			}else{
				$serviceResponse->IsSuccess=false;
				$serviceResponse->Message=trans("messages.ErrorOccured");
			}
		}
		else{
			$result=FundamentalEntity::find($likemodel->ID);
			if($result){
				$result->Likes=($result->Likes)+1;
				$result->save();
				$serviceResponse->Data=$result;
				$serviceResponse->IsSuccess=true;
			}else{
				$serviceResponse->IsSuccess=false;
				$serviceResponse->Message=trans("messages.ErrorOccured");
			}
		}
		return $serviceResponse;
	}
	
	public function SaveMobile($model,$user){
		$serviceResponse= new ServiceResponse();
		$checkMobile=DB::table('users')->where('Mobile',$model->Mobile)->where('UserID','!=',$user->Data->UserID)->first();
		if($checkMobile){
			$serviceResponse->IsSuccess=FALSE;
			$serviceResponse->Message=trans("messages.MobileAlreadyRegistered");
		}
		else{
			$otp = rand(100000, 999999);
			
			$userEntity=UserEntity::find($user->Data->UserID);
			$userEntity->OTP = $otp;
			$result=$userEntity->save();
			
			$messageEntity = new MessageEntity();
            $messageEntity->Mobile = $model->Mobile;
            $messageEntity->Message = trans("messages.SendOTPMessage", array('otp'=>$userEntity->OTP));
            $messageEntity->save();
            
            $serviceResponse->Data=array("UserID"=>$userEntity->UserID);
            $serviceResponse->IsSuccess=true;
		}
		return $serviceResponse;
	}
	public function SavePassword($model,$user){
		$serviceResponse= new ServiceResponse();
		$userEntity=UserEntity::where('UserID',$user->Data->UserID)->where('Password',$model->currentPassword)->first();
		if($userEntity){
			$userEntity->Password=$model->newPassword;
			$result=$userEntity->save();
			if($result){
				$serviceResponse->IsSuccess=true;
            	$serviceResponse->Message=trans("messages.PasswordUpdateSuccess");	
			}else{
				$serviceResponse->IsSuccess=false;
            	$serviceResponse->Message=trans("messages.ErrorOccured");	
			}
		}
		else{
			$serviceResponse->IsSuccess=false;
            $serviceResponse->Message=trans("messages.PasswordNotMatchwithusername");
		}
		return $serviceResponse;
	}
	
	public function SaveErrorlog($model){
		$serviceResponse= new ServiceResponse();
		$errorLogEntity= new ErrorLogEntity;
		$errorLogEntity->Description=$model->description;
		$errorLogEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
		$errorLogEntity->Type=$model->device_type;
		if($errorLogEntity->Save()){
			$serviceResponse->IsSuccess=true;	
		}
		return $serviceResponse;
	}
}