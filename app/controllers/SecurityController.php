<?php
use DataProviders\ISecurityDataProvider;
use \ViewModels\SessionHelper;
use Illuminate\Support\Facades\Session;
use ViewModels\ServiceResponse;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
//use \Crypt;
//use \Mail;

class SecurityController extends BaseController  {

	function __construct(ISecurityDataProvider $securityDataProvider){
		$this->securityDataProvider = $securityDataProvider;
	}
       /* public function getLogin(){

            try {
                if(!Auth::check())
                    return View::make('security.login');
                else
                    return  Redirect::to('dashboard');
            }
            catch(Exception $e) {
                return json_encode($e);
            }
        }*/
    public function getUnauthorized(){
        if(!(SessionHelper::getRoleID()) && Auth::check()) {
            Auth::logout();

            return Redirect::to('/')->with('SessionExpired',trans('messages.SessionExpired'));
        }
        return View::make('errors.unauthorized');
    }

    public function getLogin(){
        if(!Auth::check()){
            return View::make('security.login',(array)Session::get('SessionExpired'));
        }
        else{
            return  Redirect::to('dashboard');
        }
    }
    public function postAuthenticate(){
        $serviceRequest = $this->GetObjectFromJsonRequest(Input::json()->all());
        /*$serviceResponse = $this->securityDataProvider->postAuthenticate($serviceRequest->Data);*/

        $serviceResponse = $this->securityDataProvider->AuthenticateUser($serviceRequest->Data);
        if (!empty($serviceResponse->Data)) {
            SessionHelper::setRoleID($serviceResponse->Data->userdeatil->RoleID);
            SessionHelper::setRoleName($serviceResponse->Data->userdeatil->RoleName);
            SessionHelper::setUserName($serviceResponse->Data->userdeatil->FirstName);
        }
        if($serviceResponse->IsSuccess){
            $userLoginChecked = Auth::User();
            if(!empty($userLoginChecked)) {
                $sessionCheckURL = SessionHelper::getRedirectURL();
                if (!empty($sessionCheckURL)) {
                    $serviceResponse->Data->redirectURL = $sessionCheckURL;
                } else {
                    $logInRoleData = Common::GetLoginRoleText($userLoginChecked->RoleID);
                    $serviceResponse->Data->redirectURL = URL::to('/'.$logInRoleData->redirectURL);
                }
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }
    public function getLogout(){
		$userLoginChecked = Auth::User();
        $serviceResponse = $this->securityDataProvider->WebLogout($userLoginChecked);
		return Redirect::to('');
    }
    
    public function getSendnotifications(){
        $this->securityDataProvider->SendGCMFromNotification();
    }
    public function getSendIOSNotifications(){
        $this->securityDataProvider->SendAPNsFromNotification();
    }


}