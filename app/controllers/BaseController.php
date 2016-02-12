<?php
use ViewModels\ServiceRequest;
use ViewModels\SessionHelper;
//use \URL;
use \Infrastructure\Common;
use \Infrastructure\Constants;
//use \stdClass;
use ViewModels\ServiceResponse;
use \DataProviders\BaseDataProvider;

class BaseController extends Controller {

    public $showProjectMenuData;
    private $Token = '';

    public function __construct()
    {
        $this->showProjectMenuData = new StdClass();
        $this->showProjectMenuData->ShowProjectMenu = true;
        $this->showProjectMenuData->EncProjectId = null;
        $this->DataProvider = new BaseDataProvider();
        $this->data = new stdClass();
        View::share('showProjectMenuData', $this->showProjectMenuData);
    }

    public function ValidateToken($token,$isValidating,$user){

        $serviceResponse=new ServiceResponse();
        Cache::forget($token);

        if($isValidating)
        {
            if(Cache::has($token))
            {

//			  Constants->CacheExpirationTime
                //Cache::add($token, $this.GetSessionUser($token), 6000);
                Cache::add($token, $user, 525600);
                Cache::add($user->id, $token, 525600);
                $serviceResponse->IsSuccess=true;
                $this->Token = $token;
            }
            $serviceResponse->Message = trans('messages.TokenIsNotValid');
            $serviceResponse->ErrorCode = "101";

        }
        else
        {
            //Cache::add($token, $user, Constants->CacheExpirationTime);
            Cache::add($token, $user,525600);
            $serviceResponse->Data = Cache::get($token);
            $serviceResponse->IsSuccess=true;
            $this->Token = $token;

        }
        return $serviceResponse;
    }

    public function GetJsonResponse($serviceResponse){
        if(is_object($serviceResponse->Data) && property_exists($serviceResponse->Data, "booleanType") && is_array($serviceResponse->Data->booleanType)){
            foreach($serviceResponse->Data->booleanType as $variable){
                $serviceResponse->Data[$variable] = $serviceResponse->Data[$variable] > 0 ?true:false;
            }
        }

        if(is_object($serviceResponse->Data) && property_exists($serviceResponse->Data, "nullable") && is_array($serviceResponse->Data->nullable)){
            foreach($serviceResponse->Data->nullable as $variable){
                $serviceResponse->Data[$variable] = !empty($serviceResponse->Data[$variable])?$serviceResponse->Data[$variable]:'';
            }
        }

		$serviceResponse->Token = $this->Token;
        $jsonResponse = Response::make(json_encode($serviceResponse), 200);
        $jsonResponse->header('Content-Type', 'application/json');
        $jsonResponse->header('Cache-Control', 'public, max-age=86400');
        return $jsonResponse;
    }

    public function GetObjectFromJsonRequest($jsonRequest){
        $serviceRequest = new ServiceRequest();
        $request= (object)$jsonRequest;
        $serviceRequest->Token= (!property_exists($request, 'Token') ? null : $request->Token);
        $serviceRequest->Data=(!property_exists($request, 'Data') ? null : is_array($request->Data)? (object)$request->Data : (is_string($request->Data)?json_decode($request->Data):(object)$request->Data));
        return $serviceRequest;
    }

    public function GenerateToken($userUniqueEmail){
        return md5($userUniqueEmail);
    }
    public function RemoveToken($token){
        Cache::forget($token);
        return true;
    }

    public function GetShowProjectMenuData(){
        return $this->showProjectMenuData;
    }

    public function SetShowProjectMenuData($projectMenuData){
        $this->showProjectMenuData->ShowProjectMenu = $projectMenuData->ShowProjectMenu;
        if($projectMenuData->ShowProjectMenu == true){
            $this->showProjectMenuData->EncProjectId = $projectMenuData->EncProjectId;
        }
    }

    public function ShareViewData(){
        View::share('showProjectMenuData', $this->showProjectMenuData);
    }

    public function IsAuthorized($requestSegment){
        if(SessionHelper::getRoleID() != Constants::$RoleDCC){
            $requestURLSegment =  Request::segment($requestSegment);

            if(!empty($requestURLSegment)){
                $decodeRequestURL = urldecode($requestURLSegment);
                $decryptProjectID = Common::getEncryptDecryptValue('decrypt',$decodeRequestURL);
                $projectID =  Common::getExplodeValue($decryptProjectID,Constants::$QueryStringProjectID);
                $propertyName = 'ProjectID';

                $userProjectIdArray = Common::GetPropertyArrayFromArray(SessionHelper::getUserProjectList(),$propertyName);

                if(!in_array($projectID,$userProjectIdArray))
                    return true;
                else
                    return false;
            }
            else
                return false;
        }
    }
    
    public function GetSessionUser($token){
		$serviceResponse=new ServiceResponse();
       	if(!is_null($token) && $token!='' && Cache::has($token))
		{ 
			$serviceResponse->IsSuccess=true;
			$serviceResponse->Data=Cache::get($token);
			$this->Token = $token;
		}
		else
		{
			$serviceResponse->Message="Session Expire";
			$serviceResponse->ErrorCode = 101;
		}

		return $serviceResponse;
   	}
}
