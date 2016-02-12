<?php
use DataProviders\IScriptDataProvider;
use Illuminate\Support\Facades\Input;
use \Infrastructure\Common;
use \Infrastructure\Constants;
use \ViewModels\SessionHelper;
use ViewModels\ServiceRequest;
use ViewModels\ServiceResponse;

class ScriptController  extends BaseController
{
    function __construct(IScriptDataProvider  $scriptDataProvider){
        $this->DataProvider = $scriptDataProvider;
    }
    
    public function getScriptList(){
    	if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        $segments = $this->DataProvider->GetSegments();
        return View::make('admin.scriptlist', array('segments'=>($segments && count($segments)>0)?$segments:[]));
    }

    public function postScriptList(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->getScriptList($serviceRequest->Data);
        if(count($serviceResponse->Data->ScriptListArray)>0){
            $index = ($serviceRequest->Data->PageSize * ($serviceRequest->Data->PageIndex-1))+1;
            foreach($serviceResponse->Data->ScriptListArray as $script){
                $scriptID = Constants::$QueryStringScriptID."=".$script->ScriptID;
                $script->EncryptScriptID=Common::getEncryptDecryptID('encrypt', $scriptID);
                $script->Index = $index++;
            }
        }
        return $this->GetJsonResponse($serviceResponse);
    }

    public function postEnablescript(){
        $serviceRequest=$this->GetObjectFromJsonRequest(Input::json()->all());
        $serviceResponse = $this->DataProvider->EnableScript($serviceRequest->Data);
        return $this->GetJsonResponse($serviceResponse);
    }

    public function getAddScript($encryptedScriptID = 0){
        $isEditMode = false;
        if($encryptedScriptID){
            $isEditMode = true;
        }
        if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
        if($isEditMode){
            $decryptScriptID = Common::getEncryptDecryptValue('decrypt',$encryptedScriptID);
            $scriptID =  Common::getExplodeValue($decryptScriptID,Constants::$QueryStringScriptID);
        }else{
            $scriptID = 0;
        }

        $serviceResponse = $this->DataProvider->getScriptDetails($scriptID);
        return View::make('admin.addscript',(array)$serviceResponse->Data);
    }

    public function postSaveScript(){
    	$serviceRequest = Input::all();
        $serviceResponse = $this->DataProvider->SaveScript($serviceRequest,Auth::user()->UserID);
        return $this->GetJsonResponse($serviceResponse);
    }
    
    public function postDeleteScript(){
    	if (SessionHelper::getRoleID() != Constants::$RoleAdmin)
            return Redirect::to('unauthorize');
    	else{
			$serviceRequest = Input::all();
	        $serviceResponse = $this->DataProvider->DeleteScript($serviceRequest['ScriptID']);
	        return $this->GetJsonResponse($serviceResponse);		
		}
	}
    
}