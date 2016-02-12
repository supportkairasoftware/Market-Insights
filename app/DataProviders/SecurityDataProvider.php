<?php
namespace DataProviders;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use \ViewModels\SessionHelper;
use Illuminate\Support\Facades\Session;

use \UserEntity;
use \MessageEntity;
use \UserRoleEntity;
use \vwUserRoleEntity;
use \vwLoginEntity;
use \ViewModels\ServiceResponse;
use \ViewModels\SearchValueModel;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \stdClass;
use \DateTime;
use \DateInterval;
use \Crypt;
use \Mail;
use \Authentication;
use \UserHistoryEntity;
use \PaymentPlansHistoryEntity;
use EmailEntity;
use \NotificationEntity;
use Illuminate\Support\Facades\URL;
use UserDevicesEntity;
use SettingEntity;
use UserNotificationEntity;

class SecurityDataProvider extends BaseDataProvider implements ISecurityDataProvider {

    public function Call(){
        return 12;
    }


    public static function GetGoogleCloudMessage($otp){
        //$token=$email;
        return array(
//            /"Token"=>$token,
            "OTP"=>$otp
        );
    }

    public function Signup($signupModel,$userimage)
    {
        $response = new ServiceResponse();
        $userEntity = new UserEntity();

        $isEditMode = $signupModel->UserID > 0;
        $dateTime = date(Constants::$DefaultDateTimeFormat);

        /* Check for duplicate Email */
        $searchEmailParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "Email";
        $searchValueData->Value = $signupModel->Email;
        $searchValueData->CheckStartWith = Constants::$CheckStartWith;
        array_push($searchEmailParams, $searchValueData);

        if($isEditMode)
            $customWhere = "UserID NOT IN ($signupModel->UserID)";
        else
            $customWhere = "";

        $checkUniqueEmail = $this->GetEntityCount(new UserEntity(), $searchEmailParams, "", "", $customWhere);

        if ($checkUniqueEmail>0) {
            $response->IsSuccess = false;
            $response->Message = trans('messages.EmailAlreadyRegistered');
        } else {
            /* Check for duplicate Mobile Number */
            $searchParams = array();
            $searchValueData = new SearchValueModel();
            $searchValueData->Name = "Mobile";
            $searchValueData->Value = $signupModel->Mobile;
            $searchValueData->CheckStartWith = Constants::$CheckStartWith;
            array_push($searchParams, $searchValueData);

            if ($isEditMode)
                $customWhere = "UserID NOT IN ($signupModel->UserID)";
            else
                $customWhere = "";

            $checkUniqueMobile = $this->GetEntityCount(new UserEntity(), $searchParams, "", "", $customWhere);
            
            if ($checkUniqueMobile > 0) {
                $response->IsSuccess = false;
                $response->Message = trans('messages.MobileAlreadyRegistered');
            } else {
                if ($isEditMode) {
                    $userEntity = $this->GetEntityForUpdateByPrimaryKey(new UserEntity(), $signupModel->UserID);
                    $userEntity->ModifiedDate = $dateTime;
                } else {
                    $userEntity->CreatedDate = $dateTime;
                    $userEntity->ModifiedDate = $dateTime;
                }
                
                $userEntity->FirstName = $signupModel->FirstName;
                $userEntity->LastName = $signupModel->LastName;
                $userEntity->Email = $signupModel->Email;
                $userEntity->Password = $signupModel->Password;
                $userEntity->State = $signupModel->State;
                $userEntity->City = $signupModel->City;
                $userEntity->IsAndroid =property_exists($signupModel,'IsAndroid')?Constants::$Value_True:Constants::$Value_False;
                $userEntity->IsEnable = Constants::$Value_True;
                $userEntity->IsSocial = property_exists($signupModel,'IsSocial')?$signupModel->IsSocial:Constants::$Value_False;
                $userEntity->Mobile = $signupModel->Mobile;
                $userEntity->DeviceID = property_exists($signupModel,'DeviceID')?$signupModel->DeviceID:Constants::$Value_False;
                $otp = rand(100000, 999999);
                $userEntity->OTP=$otp;

                if($userEntity->save()) {
                    if ($userimage) {
                        if (!is_dir(public_path(Constants::$Path_ProfileImages .$userEntity->UserID))) {
                            mkdir(public_path(Constants::$Path_ProfileImages .$userEntity->UserID), 0755);
                        }
                        else {
                            $path = public_path(Constants::$Path_ProfileImages.$userEntity->UserID.'/');
                            // Loop over all of the files in the folder
                            foreach (glob($path . "*.*") as $file) {
                                unlink($file); // Delete each file through the loop
                            }
                        }
                        $destinationPath = public_path(Constants::$Path_ProfileImages .$userEntity->UserID);
                        $fileName = $userimage->getClientOriginalName();
                        $success = $userimage->move($destinationPath, $fileName);

                        if ($success) {
                            $userEntity->UserImageUrl = $fileName;
                            $userEntity->save();
                        }
                    }
                    
					if (!$isEditMode) {
						
						DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate)
SELECT ".$userEntity->UserID.",NewsID, CreatedDate FROM news WHERE GroupID LIKE '%10001;%' OR GroupID LIKE '%10004;%' ORDER BY CreatedDate DESC LIMIT 30");

                        /* Staging*/	DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate) values(?,".Constants::$UserNewsActivateTrailID.",?)",array($userEntity->UserID, date(Constants::$DefaultDateTimeFormat)));
                     // LIVE  DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate) values(?,147,?)",array($userEntity->UserID, date(Constants::$DefaultDateTimeFormat)));
						DB::insert("INSERT INTO usercalls (UserID, CallID)
SELECT ".$userEntity->UserID.",CallID FROM calls WHERE IsHidden = 0 AND (GroupID LIKE '%10001;%' OR GroupID LIKE '%10004;%') ORDER BY CreatedDate DESC LIMIT 60");

						$userNotificationEntity = new UserNotificationEntity();
						$userNotificationEntity->Description = trans("messages.WelcomeMessage");
						$userNotificationEntity->UserID = $userEntity->UserID;
						$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
						$userNotificationEntity->save();
						
						$userRoleEntity = new UserRoleEntity();
	                    $userRoleEntity->UserID = $userEntity->UserID;
	                    $userRoleEntity->RoleID = Constants::$RoleClient;
	                    $userRoleEntity->save();
	                    
	                    $userDeviceEntity=new UserDevicesEntity();
						$userDeviceEntity->UserID=$userEntity->UserID;
						$userDeviceEntity->DeviceID=$signupModel->DeviceID;
						$userDeviceEntity->save();
						
						$messageEntity = new MessageEntity();
	                    $messageEntity->Mobile = $signupModel->Mobile;
	                    $messageEntity->Message = trans("messages.SendOTPMessage", array('otp'=>$userEntity->OTP));
	                    $messageEntity->save();
	                    
	                    $userEntity->RoleID = Constants::$RoleClient;
					}

		        	$response->Message = $isEditMode?trans('messages.UserUpdateSuccess'):trans('messages.UserCreationSuccess');
                    if($userEntity->UserImageUrl){
                        	$userEntity->UserImageUrl=asset(Constants::$Path_ProfileImages.$userEntity->UserID.'/'.rawurlencode($userEntity->UserImageUrl));
                    }
                    $response->Data = $userEntity;
                    $response->IsSuccess = true;
                }else{
					$serviceResponse->Message = trans("message.ErrorOccured");
				}
            }
        }
        return $response;
    }

    public function postAuthenticate($loginModel)
    {
        $response = new ServiceResponse();
        $dateTime = date(Constants::$DefaultDateTimeFormat);
        $loginModel->RoleID = property_exists($loginModel, "RoleID") && $loginModel->RoleID?$loginModel->RoleID:2;

        if(property_exists($loginModel,'IsSocial') && $loginModel->IsSocial== Constants::$Value_True){ //Social login

            $userEntity = UserEntity::where("Email", $loginModel->Email)->first();

        	if($userEntity){ //check for user is exists

        		if($userEntity->IsVerified && $userEntity->IsEnable){ //check for user is otp verified
        			//get last device from userdevice        			
        			$userRoles=DB::table('userroles')->where('UserID',$userEntity->UserID)->first();
                	if($userRoles->RoleID != Constants::$RoleAdmin){
						$userdevices=UserDevicesEntity::where('UserID',$userEntity->UserID)->orderBy('UserDeviceID',Constants::$SortIndexDESC)->first();
						if(count($userdevices)>0 && $userdevices->DeviceID != $loginModel->DeviceID){ //check last device is same as device in request
	        			$deviceChangedCount=DB::table('userdevices')->where('UserID',$userEntity->UserID)->count();
	        				if(property_exists($loginModel,'IsDeviceChange') && $loginModel->IsDeviceChange){
								if($deviceChangedCount < Constants::$MaxAllowedDeviceChange){ //check for user total device
									$userDeviceEntity=new UserDevicesEntity();
									$userDeviceEntity->UserID=$userEntity->UserID;
									$userDeviceEntity->DeviceID=$loginModel->DeviceID;
									$userDeviceEntity->save();
								}else{
									$response->Message = trans('messages.MaxDeviceChangedExceed');//you have exceeded
				                    return $response;
								}
									
							}else{
								$response->Data=$userEntity;
								//notify user about their device change
			                    $response->Message = trans("messages.NotifyUserForDeviceChanged",array('CountChanged'=>$deviceChangedCount));
			                    $response->ErrorCode = Constants::$DeviceChanged;
			                    return $response;
							}
						}
					}
        		
					$checkUserRole = vwUserRoleEntity::where('UserID',$userEntity->UserID)->where('RoleID',$loginModel->RoleID)->first();
	                if($checkUserRole){
	                	$userEntity->DeviceUDID = property_exists($loginModel,'DeviceUDID')?$loginModel->DeviceUDID:'';
                    	$userEntity->save();
                        if($userEntity->UserImageUrl){
                           		$userEntity->UserImageUrl= asset(Constants::$Path_ProfileImages.$userEntity->UserID.'/'.rawurlencode($userEntity->UserImageUrl));
                        }

                        $paymentplanshistory=DB::select("SELECT *,TIMESTAMPDIFF(DAY, StartDate, NOW()) as daytime FROM paymentplanshistory WHERE UserID='".$userEntity->UserID."' AND IsTrial='".Constants::$Value_True."' AND IsActive='".Constants::$Value_True."' ");

                        if($paymentplanshistory){
                            $day=($paymentplanshistory[0]->NoOfDays)-($paymentplanshistory[0]->daytime);
                            $userEntity->IsTrialActive=true;
                            $userEntity->TimePeriod=$day;
                        }
                        else{
                            $userEntity->IsTrialActive=false;
                            $userEntity->TimePeriod=0;
                        }
	                    $response->Data = $userEntity;
	                    $response->Data->RoleID = $checkUserRole->RoleID;
	                    $response->Data->RoleName = $checkUserRole->RoleName;
	                    $response->IsSuccess = true;
	                    $response->Message = trans('messages.LoginSuccess');
	                }else{
	                    $response->IsSuccess = false;
	                    $response->Message = trans('messages.RolePermissionDenied');
	                }
				}else if(property_exists($loginModel,"Mobile") && $loginModel->Mobile){ //send otp again
					$userEntity->FirstName = $loginModel->FirstName;
	                $userEntity->LastName = $loginModel->LastName;
	                $userEntity->Email = $loginModel->Email;
	                $userEntity->IsAndroid = property_exists($loginModel, 'IsAndroid') ? Constants::$Value_True : Constants::$Value_False;
	                $userEntity->IsEnable = Constants::$Value_True;
	                $userEntity->IsSocial = Constants::$Value_True;
	                $userEntity->GoogleID =property_exists($loginModel,'GoogleID')?$loginModel->GoogleID:'';
	                $userEntity->FbID =property_exists($loginModel,'FbID')?$loginModel->FbID:'';
	                $userEntity->DeviceID =property_exists($loginModel,'DeviceID')?$loginModel->DeviceID:'';
                    $userEntity->DeviceUDID = property_exists($loginModel,'DeviceUDID')?$loginModel->DeviceUDID:'';
                    $userEntity->OTP = rand(100000, 999999);
                    $userEntity->Mobile = $loginModel->Mobile;

                    $userEntity->save();

	                $userRoleEntity = new UserRoleEntity();
	                $userRoleEntity->UserID = $userEntity->UserID;
	                $userRoleEntity->RoleID = Constants::$RoleClient;
	                $userRoleEntity->save();
	                
	                $userDeviceEntity=new UserDevicesEntity();
					$userDeviceEntity->UserID=$userEntity->UserID;
					$userDeviceEntity->DeviceID=$loginModel->DeviceID;
					$userDeviceEntity->save();
	                
	                /*$userNotificationEntity = new UserNotificationEntity();
		        	$userNotificationEntity->Description = trans("messages.WelcomeMessage");
		        	$userNotificationEntity->UserID = $userEntity->UserID;
		        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
		        	$userNotificationEntity->save();*/

                    $messageEntity = new MessageEntity();
                    $messageEntity->Mobile = $loginModel->Mobile;
                    $messageEntity->Message = trans("messages.SendOTPMessage", array('otp'=>$userEntity->OTP));
                    $messageEntity->save();

                    $response->Message = trans('messages.UserNotVerifed');
                    if($userEntity->UserImageUrl){
                        	$userEntity->UserImageUrl=asset(Constants::$Path_ProfileImages.$userEntity->UserID.'/'.rawurlencode($userEntity->UserImageUrl));
                    }
                    $paymentplanshistory=DB::select("SELECT *,TIMESTAMPDIFF(DAY, StartDate, NOW()) as daytime FROM paymentplanshistory WHERE UserID='".$userEntity->UserID."' AND IsTrial='".Constants::$Value_True."' AND IsActive='".Constants::$Value_True."' ");

                    if($paymentplanshistory){
                        $day=($paymentplanshistory[0]->NoOfDays)-($paymentplanshistory[0]->daytime);
                        $userEntity->IsTrialActive=true;
                        $userEntity->TimePeriod=$day;
                    }
                    else{
                        $userEntity->IsTrialActive=false;
                        $userEntity->TimePeriod=0;
                    }
                    $response->Data = $userEntity;
	                $response->IsSuccess = true;
	                
	                $checkUserRole = vwUserRoleEntity::where('UserID',$userEntity->UserID)->first();
	                if($checkUserRole){
	                	$response->Data->RoleID = $checkUserRole->RoleID;
	                    $response->Data->RoleName = $checkUserRole->RoleName;
	                }else{
	                    $response->IsSuccess = false;
	                    $response->Message = trans('messages.RolePermissionDenied');
	                }
				}else if(!($userEntity->IsEnable)) {
					$response->Message = trans('messages.UserNotEnabled');
					$loginModel->IsVerified = FALSE;
					$response->Data = $loginModel;
				}
				else{
					$response->Message = trans('messages.UserNotVerifed');
					$response->IsSuccess = true;
					$loginModel->IsVerified = FALSE;
					$response->Data = $loginModel;
				}				
			}else if(property_exists($loginModel,"Mobile") && $loginModel->Mobile){ //create new user and send otp
              
                $CheckForDuplicateUser=DB::table('users')->where("Email",$loginModel->Email)->orWhere("Mobile",$loginModel->Mobile)->first();

				if($CheckForDuplicateUser && $loginModel->Email == $CheckForDuplicateUser->Email){
					$response->Message = trans('messages.EmailAlreadyRegistered');
	                $response->IsSuccess = false;
	                return $response;
				}else if($CheckForDuplicateUser && $loginModel->Mobile == $CheckForDuplicateUser->Mobile){
					$response->Message = trans('messages.MobileAlreadyRegistered');
	                $response->IsSuccess = false;
	                return $response;
				}
				$userEntity = new UserEntity();
				$userEntity->FirstName = $loginModel->FirstName;
                $userEntity->LastName = $loginModel->LastName;
                $userEntity->Email = $loginModel->Email;
                $userEntity->IsAndroid = property_exists($loginModel, 'IsAndroid') ? Constants::$Value_True : Constants::$Value_False;
                $userEntity->IsEnable = Constants::$Value_True;
                $userEntity->IsSocial = Constants::$Value_True;
                $userEntity->GoogleID =property_exists($loginModel,'GoogleID')?$loginModel->GoogleID:'';
                $userEntity->FbID =property_exists($loginModel,'FbID')?$loginModel->FbID:'';
                $userEntity->DeviceID =property_exists($loginModel,'DeviceID')?$loginModel->DeviceID:'';
                $userEntity->DeviceUDID =property_exists($loginModel,'DeviceUDID')?$loginModel->DeviceUDID:'';
                $userEntity->CreatedDate=$dateTime;
                $userEntity->ModifiedDate=$dateTime;
                $userEntity->OTP = rand(100000, 999999);
                $userEntity->Mobile = $loginModel->Mobile;
                $result=$userEntity->save();
                
                
                /*Profile Image save*/
                if($result){
					
	                if(property_exists($loginModel,'isGplusLogin') && $loginModel->isGplusLogin){
	                	/*Google Download Image*/
						$id='gmail';
						$url = $loginModel->UserImageUrl;
						if (!is_dir(public_path(Constants::$Path_ProfileImages .$userEntity->UserID))) {
	                            mkdir(public_path(Constants::$Path_ProfileImages .$userEntity->UserID), 0755);
	                    }
	                    else {
	                        $path = public_path(Constants::$Path_ProfileImages.$userEntity->UserID.'/');
	                        // Loop over all of the files in the folder
	                        foreach (glob($path . "*.*") as $file) {
	                            unlink($file); // Delete each file through the loop
	                        }
	                    }
	                    $data = file_get_contents($url);
						$fp = fopen("img$id.jpg","wb");
						fwrite($fp, $data);
						$filename="img$id.jpg";
						$destinationPath = public_path(Constants::$Path_ProfileImages .$userEntity->UserID."/image.jpg");
	                    $success = copy($filename, $destinationPath);
	                    unlink($filename);
		
					}
					else if(property_exists($loginModel,'isFbLogin') && $loginModel->isFbLogin){
						/*Facebook Download Image*/
						$id=$loginModel->FbID;
						$url = $loginModel->UserImageUrl;
						
						if (!is_dir(public_path(Constants::$Path_ProfileImages .$userEntity->UserID))) {
	                            mkdir(public_path(Constants::$Path_ProfileImages .$userEntity->UserID), 0755);
	                    }
	                    else {
	                        $path = public_path(Constants::$Path_ProfileImages.$userEntity->UserID.'/');
	                        // Loop over all of the files in the folder
	                        foreach (glob($path . "*.*") as $file) {
	                            unlink($file); // Delete each file through the loop
	                        }
	                    }
	                    $data = file_get_contents($url);
						$fp = fopen("img$id.jpg","wb");
						fwrite($fp, $data);
						$filename="img$id.jpg";
						$destinationPath = public_path(Constants::$Path_ProfileImages .$userEntity->UserID."/image.jpg");
	                    $success = copy($filename, $destinationPath);
	                    unlink($filename);
					}
					$userEntity->UserImageUrl = "image.jpg";
					$userEntity->save();
				}

                $userRoleEntity = new UserRoleEntity();
                $userRoleEntity->UserID = $userEntity->UserID;
                $userRoleEntity->RoleID = Constants::$RoleClient;
                $userRoleEntity->save();
                
                $userDeviceEntity=new UserDevicesEntity();
				$userDeviceEntity->UserID=$userEntity->UserID;
				$userDeviceEntity->DeviceID=$loginModel->DeviceID;
				$userDeviceEntity->save();
                
				DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate)
SELECT ".$userEntity->UserID.",NewsID,CreatedDate FROM news WHERE GroupID LIKE '%10001;%' OR GroupID LIKE '%10004;%' ORDER BY CreatedDate DESC LIMIT 30");

			/* Staging*/ DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate) values(?,".Constants::$UserNewsActivateTrailID.",?)",array($userEntity->UserID, date(Constants::$DefaultDateTimeFormat)));
               // LIVE -  DB::insert("INSERT INTO usernews (UserID, NewsID, CreatedDate) values(?,147,?)",array($userEntity->UserID, date(Constants::$DefaultDateTimeFormat)));

				DB::insert("INSERT INTO usercalls (UserID, CallID)
SELECT ".$userEntity->UserID.",CallID FROM calls WHERE IsHidden = 0 AND (GroupID LIKE '%10001;%' OR GroupID LIKE '%10004;%') ORDER BY CreatedDate DESC LIMIT 30");
                                
				$userNotificationEntity = new UserNotificationEntity();
	        	$userNotificationEntity->Description = trans("messages.WelcomeMessage");
	        	$userNotificationEntity->UserID = $userEntity->UserID;
	        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
	        	$userNotificationEntity->save();

                $messageEntity = new MessageEntity();
                $messageEntity->Mobile = $loginModel->Mobile;
                $messageEntity->Message = trans("messages.SendOTPMessage", array('otp'=>$userEntity->OTP));
                $messageEntity->save();

                $response->Message = trans('messages.UserNotVerifed');

                if($userEntity->UserImageUrl){
                    	$userEntity->UserImageUrl=asset(Constants::$Path_ProfileImages.$userEntity->UserID.'/'.rawurlencode($userEntity->UserImageUrl));
                }
                $paymentplanshistory=DB::select("SELECT *,TIMESTAMPDIFF(DAY, StartDate, NOW()) as daytime FROM paymentplanshistory WHERE UserID='".$userEntity->UserID."' AND IsTrial='".Constants::$Value_True."' AND IsActive='".Constants::$Value_True."' ");

                if($paymentplanshistory){
                    $day=($paymentplanshistory[0]->NoOfDays)-($paymentplanshistory[0]->daytime);
                    $userEntity->IsTrialActive=true;
                    $userEntity->TimePeriod=$day;
                }
                else{
                    $userEntity->IsTrialActive=false;
                    $userEntity->TimePeriod=0;
                }
                $response->Data = $userEntity;
                $response->IsSuccess = true;

                $checkUserRole = vwUserRoleEntity::where('UserID',$userEntity->UserID)->first();
                if($checkUserRole){
                	$response->Data->RoleID = $checkUserRole->RoleID;
                    $response->Data->RoleName = $checkUserRole->RoleName;
                }else{
                    $response->IsSuccess = false;
                    $response->Message = trans('messages.RolePermissionDenied');
                }
			}else{
				$response->Message = trans('messages.UserNotVerifed');
				//$response->ErrorCode = Constants::$MobileNotVerified;
				$response->IsSuccess = true;
				$loginModel->IsVerified = FALSE;
				$response->Data = $loginModel;
			}
        }else{ //Manual login
       		$userEntity = UserEntity::where("Email", $loginModel->Email)->where("Password", $loginModel->Password)->first();
       		if(!$userEntity){
				$userEntity = UserEntity::where("Email", $loginModel->Email)->where("Password", md5($loginModel->Password))->first();
			}
            if($userEntity) {
                if($userEntity->IsVerified && $userEntity->IsEnable){
                	$userRoles=DB::table('userroles')->where('UserID',$userEntity->UserID)->first();
                	if($userRoles->RoleID!=Constants::$RoleAdmin){
						$userdevices=UserDevicesEntity::where('UserID',$userEntity->UserID)->orderBy('UserDeviceID',Constants::$SortIndexDESC)->first();
						if(count($userdevices)>0 && $userdevices->DeviceID != $loginModel->DeviceID){ //check last device is same as device in request
	        			$deviceChangedCount=DB::table('userdevices')->where('UserID',$userEntity->UserID)->count();
	        				if(property_exists($loginModel,'IsDeviceChange') && $loginModel->IsDeviceChange){
								if($deviceChangedCount < Constants::$MaxAllowedDeviceChange){ //check for user total device
									$userDeviceEntity=new UserDevicesEntity();
									$userDeviceEntity->UserID=$userEntity->UserID;
									$userDeviceEntity->DeviceID=$loginModel->DeviceID;
									$userDeviceEntity->save();
								}else{
									$response->Message = trans('messages.MaxDeviceChangedExceed');//you have exceeded
				                    return $response;
								}
									
							}else{
								$response->Data=$userEntity;
								//notify user about their device change
								if($deviceChangedCount >= Constants::$MaxAllowedDeviceChange){ //check for user total device
									$response->Message = trans('messages.MaxDeviceChangedExceed');//you have exceeded
								}else{
									$response->Message = trans("messages.NotifyUserForDeviceChanged",array('CountChanged'=>$deviceChangedCount));
			                    	$response->ErrorCode = Constants::$DeviceChanged;	
								}
			                    
			                    return $response;
							}
						}
					}
                	
                    //$checkUserRole = vwUserRoleEntity::where('UserID',$userEntity->UserID)->where('RoleID',$loginModel->RoleID)->first();
                    $checkUserRole = vwUserRoleEntity::where('UserID',$userEntity->UserID)->first();
                    if($checkUserRole){
                    	$userEntity->DeviceUDID = property_exists($loginModel,'DeviceUDID')?$loginModel->DeviceUDID:'';
                        $userEntity->IsAndroid =property_exists($loginModel,'IsAndroid')?Constants::$Value_True:Constants::$Value_False;
                    	$userEntity->save();
                        if($userEntity->UserImageUrl){
                            	$userEntity->UserImageUrl=asset(Constants::$Path_ProfileImages.$userEntity->UserID.'/'.rawurlencode($userEntity->UserImageUrl));
                        }
                        $paymentplanshistory=DB::select("SELECT *,TIMESTAMPDIFF(DAY, StartDate, NOW()) as daytime FROM paymentplanshistory WHERE UserID='".$userEntity->UserID."' AND IsTrial='".Constants::$Value_True."' AND IsActive='".Constants::$Value_True."' ");

                        if($paymentplanshistory){
                            $day=($paymentplanshistory[0]->NoOfDays)-($paymentplanshistory[0]->daytime);
                            $userEntity->IsTrialActive=true;
                            $userEntity->TimePeriod=$day;
                        }
                        else{
                            $userEntity->IsTrialActive=false;
                            $userEntity->TimePeriod=0;
                        }
                        $response->Data = $userEntity;
                        $response->Data->RoleID = $checkUserRole->RoleID;
                        $response->Data->RoleName = $checkUserRole->RoleName;
                        $response->IsSuccess = true;
                        $response->Message = trans('messages.LoginSuccess');
                    }else{

                        $response->IsSuccess = false;
                        $response->Message = trans('messages.RolePermissionDenied');
                    }
                }
                else if(!($userEntity->IsEnable)) {
					$response->Message = trans('messages.UserNotEnabled');
					$response->Data = $userEntity;
				}
				else{
					if(property_exists($loginModel,"Mobile") && $loginModel->Mobile){
						
						$checkMobile=DB::table('users')->where('Mobile',$loginModel->Mobile)->where('UserID','!=',$userEntity->UserID)->first();
						if($checkMobile){
							$response->IsSuccess=FALSE;
							$response->Message=trans("messages.MobileAlreadyRegistered");
							return $response;
						}
						else{
							$otp = rand(100000, 999999);
							$userEntity->OTP = rand(100000, 999999);
	                    
		                    if($userEntity->save()){
								$messageEntity = new MessageEntity();
			                    $messageEntity->Mobile = $loginModel->Mobile;
			                    $messageEntity->Message = trans("messages.SendOTPMessage", array('otp'=>$userEntity->OTP));
			                    $messageEntity->save();	
							}
						}
					}
					
					$response->Message = trans('messages.UserNotVerifed');
					$response->IsSuccess = true;
					$response->Data = $userEntity;
					
					$checkUserRole = vwUserRoleEntity::where('UserID',$userEntity->UserID)->first();
	                if($checkUserRole){
	                	$response->Data->RoleID = $checkUserRole->RoleID;
	                    $response->Data->RoleName = $checkUserRole->RoleName;
	                }else{
	                    $response->IsSuccess = false;
	                    $response->Message = trans('messages.RolePermissionDenied');
	                }	
				}
            }else{
                $response->Message = trans('messages.LoginFail');
            }
        }
        return $response;
    }

    public function OTPverified($otpmodel){
        $response= new ServiceResponse();

        $userentity = UserEntity::where("UserID", $otpmodel->UserID)->where("OTP", $otpmodel->OTP)->first();

        if($userentity){
        	$userentity->Mobile =  property_exists($otpmodel,'Mobile')?$otpmodel->Mobile:$userentity->Mobile;
            $userentity->IsVerified = Constants::$Value_True;
            $userentity->save();

            if($userentity->UserImageUrl){
                	$userentity->UserImageUrl=asset(Constants::$Path_ProfileImages.$userentity->UserID.'/'.rawurlencode($userentity->UserImageUrl));
            }

            $paymentplanshistory=DB::select("SELECT *,TIMESTAMPDIFF(DAY, StartDate, NOW()) as daytime FROM paymentplanshistory WHERE UserID='".$userentity->UserID."' AND IsTrial='".Constants::$Value_True."' AND IsActive='".Constants::$Value_True."' ");

            if($paymentplanshistory){
                $day=($paymentplanshistory[0]->NoOfDays)-($paymentplanshistory[0]->daytime);
                $userentity->IsTrialActive=true;
                $userentity->TimePeriod=$day;
            }
            else{
                $userentity->IsTrialActive=false;
                $userentity->TimePeriod=0;
            }
            $response->Data=$userentity;
            $response->IsSuccess = true;
            $response->Message = trans('messages.OTPverified');
        }
        else{
            $response->Data="";
            $response->IsSuccess = false;
            $response->Message = trans('messages.OTPincorrect');
        }
        return $response;
    }

    public function Forgot($forgotmodel){
        $response = new ServiceResponse();
        
		$searchPasswordParams = array();
		$mobile=true;
		if(property_exists($forgotmodel,'Email') && $forgotmodel->Email){
			$searchValueData = new SearchValueModel();
	        $searchValueData->Name = "Email";
	        $searchValueData->Value = $forgotmodel->Email;
	        array_push($searchPasswordParams, $searchValueData);
	        $checkUser= $this->GetEntity(new UserEntity(),$searchPasswordParams);
	   		$mobile = !$checkUser;
	    }
	    
	    if(property_exists($forgotmodel,'Mobile') && $forgotmodel->Mobile && $mobile){
	        $searchValueData = new SearchValueModel();
	        $searchValueData->Name = "Mobile";
	        $searchValueData->Value = $forgotmodel->Mobile;
	        array_push($searchPasswordParams, $searchValueData);
	        $checkUser= $this->GetEntity(new UserEntity(),$searchPasswordParams);
		}
		
        if($checkUser){
            
            if($checkUser->IsVerified) {
                
                $randomPassword = $checkUser->Password;
                $messageEntity = new MessageEntity();
                $messageEntity->Mobile = $checkUser->Mobile;
                $messageEntity->Message = trans("messages.ForgotPasswordMessage", array("pass"=>$randomPassword));
                $messageEntity->save();
                
                $now =new DateTime("now");
                $now->add(new DateInterval('P'.Constants::$LinkExpireDays_ForgotEmail.'D'));
                $token=$checkUser->Email."|".$now->format('Y-m-d H:i:s');
                $encrypted = Crypt::encrypt($token);

                $emailentity=new EmailEntity();
                $emailentity->TemplateName=Constants::$Email_ForgotPasswordBody;
                $emailentity->Data= serialize(array( 'token' => $encrypted,'name'=>'Member','password'=>$checkUser->Password));
                $emailentity->Subject= "Forgot Password";
                $emailentity->ToAddress=$checkUser->Email;
                $emailentity->save();
                
//	            $message=file_get_contents("http://smsc.a4add.com/api/smsapi.aspx?username=naresh123&password=naresh123&from=SMCLUB&to=".$userverified->Mobile."&message=".urlencode("Dear Customer, Your new Password is ").$randomPassword.urlencode(" please login with this password and update your password.")."");
	            /*$UserEntity = $this->GetEntityForUpdateByPrimaryKey(new UserEntity(),$userverified->UserID);

	            $UserEntity->Password = md5($randomPassword);
	            $UserEntity->save();*/
	            $response->Data=$checkUser;
	            $response->IsSuccess = true;
                $response->Message = trans('messages.ForgotPassword');
                return $response;
            }
            else{
                $response->IsSuccess = false;
                $response->Message = trans('messages.UserNotVerifed');
            }
        }else{
            $response->IsSuccess = false;
            $response->Message =(property_exists($forgotmodel,'Email') && $forgotmodel->Email)?trans('messages.EmailNotRegistered'):trans('messages.MobileNotRegistered');
        }
        return $response;
    }

    public function Logout($userModel){
        $response = new ServiceResponse();
		$userEntity = UserEntity::find($userModel->UserID);
        if($userEntity){
            $userEntity->DeviceUDID = '';
            if($userEntity->save()){
                $response->IsSuccess = true;
            }
        }
        $response->IsSuccess=true;
        return $response;
    }
	
	public function WebLogout($model){
	    $response = new ServiceResponse();
		$dateTime = date(Constants::$DefaultDateTimeFormat);
		$userHistoryEntity=UserHistoryEntity::where('UserID',$model->UserID)->orderBy('UserHistoryID',Constants::$SortIndexDESC)->first();
		$userHistoryEntity->LogoutTime=$dateTime;
		$userHistoryEntity->save();
        Auth::logout();
        SessionHelper::SessionFlush();
        $response->IsSuccess=true;
        return $response;
    }


    /* dev_kr Section Start */
    public function AuthenticateUser($loginModel){
        $response = new ServiceResponse();
        $authModel=new StdClass();

        $messages = array(
            'required' => trans('messages.PropertyRequired')
        );

        $validator = Validator::make((array)$loginModel, array('Email'=>'required|email','Password'=>'required'),$messages);
        $validator->setAttributeNames(UserEntity::$niceNameArray);
        if ($validator->fails()){
            $response->Message = Common::getValidationMessagesFormat($validator->messages());
            return $response;
        }

        $hashedPassword = $loginModel->Password;//md5($loginModel->Password);

        $searchParams=Array();
        $searchValueData=new SearchValueModel();
        $searchValueData->Name="Email";
        $searchValueData->Value=$loginModel->Email ;
        $searchValueData->CheckStartWith=Constants::$CheckStartWith;
        array_push($searchParams, $searchValueData);

        $searchValueData=new SearchValueModel();
        $searchValueData->Name="Password";
        $searchValueData->Value=$hashedPassword ;
        array_push($searchParams, $searchValueData);

        $searchValueData=new SearchValueModel();
        $searchValueData->Name="IsEnable";
        $searchValueData->Value= Constants::$IsEnableValue ;
        array_push($searchParams, $searchValueData);

        $loggedUserResults=DB::table('users')->where('Email',$loginModel->Email)->where('Password',$loginModel->Password)->where('IsEnable',Constants::$IsEnableValue)->first();
        if(!$loggedUserResults){
            $loggedUserResults=DB::table('users')->where('Email',$loginModel->Email)->where('Password',md5($loginModel->Password))->where('IsEnable',Constants::$IsEnableValue)->first();
        }

        if(is_null($loggedUserResults))
        {
            $response->Message = trans('messages.InvalidUserNamePassword');
        }else
        {
            $user = $this->GetEntityForUpdateByPrimaryKey(new UserEntity(),$loggedUserResults->UserID);

            $searchParams = array();
            $searchValueData=new SearchValueModel();
            $searchValueData->Name="UserID";
            $searchValueData->Value=$loggedUserResults->UserID;
            array_push($searchParams, $searchValueData);
            $role = $this->GetEntity(new vwUserRoleEntity(),$searchParams);

			if($role->RoleID != Constants::$Value_True){
				$response->Message=trans("messages.UnauthorizeAction");
				return $response;
			}
			
			$userHistoryEntity = new UserHistoryEntity();
            $dateTime = date(Constants::$DefaultDateTimeFormat);
            $userHistoryEntity->LoginTime=$dateTime;
            $userHistoryEntity->LogoutTime = '0000-00-00 00:00:00';
            $userHistoryEntity->UserID = $loggedUserResults->UserID;
            $this->SaveEntity($userHistoryEntity);
			
            Auth::login($user);

            $authModel->userdeatil = $user;
            $authModel->userdeatil->RoleID = $role->RoleID;
            $authModel->userdeatil->RoleName = $role->RoleName;

            $authModel->getUserRoleID = $role->RoleID;
            $getRole = Common::GetLoginRoleText($authModel->getUserRoleID);
            $authModel->redirectURL =$getRole->redirectURL;
			
            $response->Data=$authModel;
            $response->Message = trans('messages.LoginSuccess');
			$response->IsSuccess=true;
        }
        return $response;
    }
    /*dev_kr section end */
    
    public function SendOTPForMobile($mobile){
    	$response = new ServiceResponse();
		$messageEntityList = MessageEntity::where("IsSent",false)->where("Mobile",$mobile)->get();
		if($messageEntityList && count($messageEntityList)>0){
			$smsSetting=SettingEntity::first();
			foreach($messageEntityList as $messageEntity){
				$message=file_get_contents(vsprintf($smsSetting->SMSUrl, array($messageEntity->Mobile, urlencode($messageEntity->Message))));
				if($message){
					$messageEntity->IsSent = 1;
					$messageEntity->SentDate = date(Constants::$DefaultDateTimeFormat);
					$messageEntity->save();	
				}				
			}
		}
		$response->IsSuccess = TRUE;
		return $response;
	}
	
	public function SendMessage(){
		$smsSetting=SettingEntity::first();
		
		$messageEntityList = MessageEntity::where("IsSent",false)->get();
		if($messageEntityList && count($messageEntityList)>0){
			foreach($messageEntityList as $messageEntity){
				
				$message=file_get_contents(vsprintf($smsSetting->SMSUrl, array($messageEntity->Mobile, urlencode($messageEntity->Message))));
				
				if($message){
					$messageEntity->IsSent = 1;
					$messageEntity->SentDate = date(Constants::$DefaultDateTimeFormat);
					$messageEntity->save();	
				}
			}
		}
	}
	
	public function SendGCMFromNotification(){
		DB::statement("SET SESSION group_concat_max_len = 1000000;");
		//$notifications = DB::select("SELECT GROUP_CONCAT(users.DeviceUDID) AS Devices, GROUP_CONCAT(NotificationID) AS NotificationIDs, n.NotificationID, NotificationType, Message, `Key`, ImageUrl, IsPast FROM notifications n INNER JOIN users ON users.UserID = n.UserID AND users.DeviceUDID IS NOT NULL AND users.DeviceUDID != '' WHERE issent = 0 GROUP BY `NotificationType`, `Key`, `Message`");
		
		$notifications = DB::select("SELECT GROUP_CONCAT(users.DeviceUDID) AS Devices, GROUP_CONCAT(NotificationID) AS NotificationIDs, n.NotificationID, NotificationType,Message, `Key`, ImageUrl, IsPast FROM notifications n LEFT JOIN users ON users.UserID = n.UserID and users.IsAndroid = 1 WHERE issent = 0
AND IFNULL(users.DeviceUDID,'') != '' GROUP BY `NotificationType`, `Key`, `Message`");
		
		foreach($notifications as $notification){
			if($notification && $notification->Devices) {
				$AlldeviceUdIDs = explode(',',$notification->Devices);
				$AllnotificationIDs = explode(',',$notification->NotificationIDs);
				
				if($AlldeviceUdIDs && is_array($AlldeviceUdIDs) && count($AlldeviceUdIDs)>0){
					$listOfdeviceUdIDs = array_chunk($AlldeviceUdIDs, 20);
					$listOfnotificationIDs = array_chunk($AllnotificationIDs, 20);
					$total = DB::update("update notifications set IsSent = 2 where NotificationID IN (".$notification->NotificationIDs.") and IsSent = 0");
					foreach($listOfdeviceUdIDs as $num => $deviceUdIDs){
						if($deviceUdIDs && is_array($deviceUdIDs) && count($deviceUdIDs)>0){
							$notificationResponse = Common::SendGoogleCloudMessage($deviceUdIDs,Common::GetGoogleCloudMessage($notification->Message,$notification->NotificationType,$notification->Key, $notification->ImageUrl, $notification->IsPast));

							if($notificationResponse){
								$results = json_decode($notificationResponse)->results;
								if($results && count($results)>0){
									foreach($results as $key => $result){
										if(!property_exists($result,'error')){
											$notificationEntity = NotificationEntity::where("NotificationID", $listOfnotificationIDs[$num][$key])->update(array(
												"IsSent" => 1,
												"SentDate" => date(Constants::$DefaultDateTimeFormat)
											));
										}else{
											$notificationEntity = NotificationEntity::where("NotificationID", $listOfnotificationIDs[$num][$key])->update(array(
												"IsSent" => 3,
												"SentDate" => date(Constants::$DefaultDateTimeFormat)
											));
										}
									}	
								}else{
									$notificationEntity = DB::update("update notifications set IsSent = 0 where NotificationID IN (".implode(',', $listOfnotificationIDs[$num]).") and IsSent = 2");	
								}	
							}else{
								$notificationEntity = DB::update("update notifications set IsSent = 0 where NotificationID IN (".implode(',', $listOfnotificationIDs[$num]).") and IsSent = 2");
							}
						}
					}
				}
			}
		}


        $this->SendAPNsFromNotification();
	}
	
	public function CheckMobileVerification($userID){
		$response = new ServiceResponse();
		$user = UserEntity::find($userID);
		
		if($user->IsVerified){
			$response->IsSuccess = TRUE;
		}else{
			$response->Message = trans("messages.MobileNotEnabled");
		}
		
		return $response;
	}
	
	public function CheckUserPlan($userID){
		$response = new ServiceResponse();
		$data = new stdClass;
		$activePlan = PaymentPlansHistoryEntity::where('UserID',$userID)->where('IsActive',1)->first();;
		if($activePlan && $activePlan->IsTrial){
			$data->IsTrial = TRUE;
			$data->IsPaid = FALSE;
			$data->IsFree = FALSE;
			$now = strtotime(date('Y-m-d')); // or your date as well
			$endDate = strtotime(date('Y-m-d',strtotime($activePlan->EndDate)));
			$datediff = $endDate-$now;
			$data->NoOfDaysLeft = floor($datediff/(60*60*24));
		}else if($activePlan){
			$data->IsTrial = FALSE;
			$data->IsPaid = TRUE;
			$data->IsFree = FALSE;
			$data->NoOfDaysLeft = 0;
		}else{
			$data->IsTrial = FALSE;
			$data->IsPaid = FALSE;
			$data->IsFree = TRUE;
			$data->NoOfDaysLeft = 0;
		}
		
		$response->IsSuccess = TRUE;
		$response->Data = $data;
		
		return $response;
	}

    public function SendAPNsFromNotification(){
        $notifications=DB::select("SELECT u.DeviceUDID AS Devices,u.IsIOSGeneralOn,
                                    u.IsIOSAnalystOn,u.IsIOSFundamentalOn,u.IsIOSEquityOn,u.IsIOSFutureOn,u.IsIOSCommodityOn,u.IsIOSBTSTOn,u.IsIOSChatOn,
                                    n.NotificationID, NotificationType,Message, `Key`, ImageUrl, IsPast
                                    FROM notifications n LEFT JOIN users u ON u.UserID = n.UserID and u.IsAndroid = 0 AND u.DeviceUDID IS NOT NULL WHERE issent = 0 AND IFNULL(u.DeviceUDID,'') != ''");
        if ($notifications) {
            foreach($notifications as $notification){
                $notifications=DB::select("Update notifications SET IsSent=2 where NotificationID=?",array($notification->NotificationID));
                if ($notification && $notification->Devices) {
                    if ($notification->IsIOSGeneralOn &&  $notification->NotificationType == Constants::$NotificationType['General'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSAnalystOn &&  $notification->NotificationType == Constants::$NotificationType['Analyst'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSFundamentalOn &&  $notification->NotificationType == Constants::$NotificationType['Fundamental'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSEquityOn &&  $notification->NotificationType == Constants::$NotificationType['Equity'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSFutureOn &&  $notification->NotificationType == Constants::$NotificationType['Future'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSCommodityOn &&  $notification->NotificationType == Constants::$NotificationType['Commodity'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSBTSTOn &&  $notification->NotificationType == Constants::$NotificationType['BTST'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    else if($notification->IsIOSChatOn &&  $notification->NotificationType == Constants::$NotificationType['Chat'] ){
                        $notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));
                    }
                    /*else{
                        $notificationEntity = DB::table('notifications')->where("NotificationID", $notification->NotificationID)->update(array("IsSent" => 1,"SentDate" => date(Constants::$DefaultDateTimeFormat)));
                    }*/

                    //$notificationResponse = Common::SendIOSCloudMessage($notification->Devices, Common::GetIOSCloudMessage($notification->Message, $notification->NotificationType, $notification->Key, $notification->NotificationID, $notification->ImageUrl, $notification->IsPast));

                    if ($notificationResponse) {
                        $notificationEntity = DB::table('notifications')->where("NotificationID", $notification->NotificationID)->update(array("IsSent" => 1,"SentDate" => date(Constants::$DefaultDateTimeFormat)));
                    } else {
                        $notificationEntity = DB::table('notifications')->where("NotificationID", $notification->NotificationID)->update(array("IsSent" => 3,"SentDate" => date(Constants::$DefaultDateTimeFormat)));
                    }
                }
                $notifications=DB::select("Update notifications SET IsSent=0 where NotificationID=? and IsSent=2",array($notification->NotificationID));
            }
        }
    }

    public function postSaveIsIosNotificationON($notificationModel){

        $response =  new ServiceResponse();

        $userEntity = UserEntity::find($notificationModel->UserID);
        if($userEntity){
            $userEntity->IsIOSGeneralOn =property_exists($notificationModel,'IsIOSGeneralOn')?$notificationModel->IsIOSGeneralOn:$userEntity->IsIOSGeneralOn;
            $userEntity->IsIOSAnalystOn =property_exists($notificationModel,'IsIOSAnalystOn')?$notificationModel->IsIOSAnalystOn:$userEntity->IsIOSAnalystOn;
            $userEntity->IsIOSFundamentalOn =property_exists($notificationModel,'IsIOSFundamentalOn')?$notificationModel->IsIOSFundamentalOn:$userEntity->IsIOSFundamentalOn;
            $userEntity->IsIOSEquityOn =property_exists($notificationModel,'IsIOSEquityOn')?$notificationModel->IsIOSEquityOn:$userEntity->IsIOSEquityOn;
            $userEntity->IsIOSFutureOn =property_exists($notificationModel,'IsIOSFutureOn')?$notificationModel->IsIOSFutureOn:$userEntity->IsIOSFutureOn;
            $userEntity->IsIOSCommodityOn =property_exists($notificationModel,'IsIOSCommodityOn')?$notificationModel->IsIOSCommodityOn:$userEntity->IsIOSCommodityOn;
            $userEntity->IsIOSBTSTOn =property_exists($notificationModel,'IsIOSBTSTOn')?$notificationModel->IsIOSBTSTOn:$userEntity->IsIOSBTSTOn;
            $userEntity->IsIOSChatOn =property_exists($notificationModel,'IsIOSChatOn')?$notificationModel->IsIOSChatOn:$userEntity->IsIOSChatOn;

            if($userEntity->save()){
                $response->Data = $userEntity;
                $response->IsSuccess = true;
            }else{
                $response->IsSuccess = false;
            }
        }
        return $response;
    }
}