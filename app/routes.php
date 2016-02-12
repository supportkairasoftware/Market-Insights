<?php
use \Infrastructure\Constants;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//Mobile Controller
Route::controller('mobileplan', 'MobilePlanController');
Route::controller('call', 'CallController');
Route::controller('analyst', 'MobileAnalystController');
Route::controller('fundamental', 'MobileFundamentalController');
Route::controller('payment', 'PaymentController');
Route::controller('admin', 'AdminController');

//Login Controller Routes
Route::get('/', 'SecurityController@getlogin');
Route::post('signup', array('uses' => 'MobileSecurityController@postsignup'));
Route::post('sendotpformobile', array('uses' => 'MobileSecurityController@postSendotpformobile'));
Route::get('sendmessage', array('uses' => 'MobileSecurityController@getsendmessage'));
Route::get('sendmail', array('uses' => 'MobileSecurityController@getSendmail'));
Route::get('sendnotifications', array('uses' => 'SecurityController@getSendnotifications'));
Route::get('sendiosnotifications', array('uses' => 'SecurityController@getSendIOSNotifications'));
Route::post('otpverified', array('uses' => 'MobileSecurityController@postotpverified'));
Route::post('authenticate', array('uses' => 'MobileSecurityController@postauthenticate'));
Route::post('forgot', array('uses' => 'MobileSecurityController@postforgot'));
Route::post('logout', array('uses' => 'MobileSecurityController@postLogout'));
Route::post('checkuserplan', array('uses' => 'MobileSecurityController@postCheckUserPlan'));
Route::post('checkmobileverification', array('uses' => 'MobileSecurityController@postCheckMobileVerification'));
Route::post('adminauthenticate', array('uses' => 'SecurityController@postauthenticate'));
Route::get('adminlogout', array('uses' => 'SecurityController@getlogout'));
Route::post('notificationonoff',array('uses' => 'MobileSecurityController@postSaveIOSNotificationON'));
/* Admin Panel */
/*Route::group(array('before' => 'auth:'.Constants::$RoleAdmin), function() {*/

Route::get('unauthorize', array('uses' => 'SecurityController@getunauthorized'));
Route::get('dashboard', array('uses' => 'AdminController@getDashboard'));
Route::post('getdashboard', array('uses' => 'AdminController@postDashboard'));
Route::get('calllist', array('uses' => 'AdminController@getCallList'));
Route::post('deletecall', array('uses' => 'AdminController@postDeleteCall'));
Route::post('getcalllist', array('uses' => 'AdminController@postCallList'));
Route::post('searchscript/', array('uses' => 'AdminController@postSearchScript'));

Route::get('userpaymentlist', array('uses' => 'AdminController@getUserPaymentList'));
Route::post('getuserpaymentlist', array('uses' => 'AdminController@postUserPaymentList'));
Route::post('deleteuserpayment', array('uses' => 'AdminController@postDeleteUserPayment'));

/*group */
Route::get('grouplist', array('uses' => 'GroupController@getGroupList'));
Route::post('getgrouplist', array('uses' => 'GroupController@postgrouplist'));
Route::get('addgroup/{GroupID?}', array('uses' => 'GroupController@getAddGroup'));
Route::post('savegroup', array('uses' => 'GroupController@postSaveGroup'));
Route::post('deletegroup', array('uses' => 'GroupController@postDeleteUserGroup'));
Route::post('dltgroup', array('uses' => 'GroupController@postDeleteGroup'));
Route::get('addusergroup/{UserGroupID?}', array('uses' => 'GroupController@getAddUserGroup'));
Route::post('updateuser', array('uses' => 'AdminController@postUpdateuser'));
Route::get('usergrouplist/', array('uses' => 'GroupController@getUserGroup'));
Route::post('getusergrouplist/', array('uses' => 'GroupController@postUserGroup'));
Route::post('enablegroup', array('uses' => 'GroupController@postEnableGroup'));
Route::post('saveUserGroup', array('uses' => 'GroupController@postSaveUserGroup'));
Route::post('searchuser/', array('uses' => 'GroupController@postSearchUser'));

/*group */

/*plans */
Route::post('getplanlist', array('uses' => 'PlanController@postplanlist'));
Route::get('addplan/{PlanID?}', array('uses' => 'PlanController@getAddPlan'));
Route::post('saveplan', array('uses' => 'PlanController@postSavePlan'));
Route::get('planlist', array('uses' => 'PlanController@getPlanList'));
Route::post('getplanlist', array('uses' => 'PlanController@postplanlist'));
Route::post('updateplan', array('uses' => 'PlanController@postUpdateplan'));
Route::post('updatetrial', array('uses' => 'PlanController@postUpdateptrial'));
Route::post('deleteplan', array('uses' => 'PlanController@postDeleteplan'));
/*plans */

/*user */
Route::get('userlist', array('uses' => 'AdminController@getUserList'));
Route::post('postuserlist', array('uses' => 'AdminController@postUserList'));
Route::get('edituser/{UserID?}', array('uses' => 'AdminController@getEditUSer'));
Route::post('saveuser', array('uses' => 'AdminController@postSaveUser'));

Route::controller('mobileuser', 'MobileUserController');
/*user */

/*Fundamental */
Route::get('fundamentallist', array('uses' => 'FundamentalController@getFundamentalList'));
Route::post('getfundamentallistlist', array('uses' => 'FundamentalController@postFundamentalList'));
Route::get('addfundamental/{FundamentalID?}', array('uses' => 'FundamentalController@getAddFundamental'));
Route::post('savefundamental', array('uses' => 'FundamentalController@postSaveFundamental'));
Route::post('deletefundamental', array('uses' => 'FundamentalController@postDeleteFundamental'));
Route::post('enablefundamental', array('uses' => 'FundamentalController@postEnableFundamental'));
Route::post('deletefundamental', array('uses' => 'FundamentalController@postDeletefundamental'));
/*Fundamental */

/*Analyst */
Route::get('analystlist', array('uses' => 'AnalystController@getAnalystList'));
Route::post('getanalystlist', array('uses' => 'AnalystController@postAnalystList'));
Route::post('enableanalyst', array('uses' => 'AnalystController@postEnableanalyst'));
Route::get('addanalyst/{AnalystID?}', array('uses' => 'AnalystController@getAddAnalyst'));
Route::post('saveanalyst', array('uses' => 'AnalystController@postSaveAnalyst'));
Route::post('saveanalystimage', array('uses' => 'AnalystController@postSaveimages'));
Route::post('deleteanalyst', array('uses' => 'AnalystController@postDeleteanalyst'));
//Route::post('removeanalystimage', array('uses' => 'AnalystController@postRemoveimage'));

/*Analyst */

/*Notification */
Route::get('notificationlist', array('uses' => 'AdminController@getNotificationList'));
Route::post('postnotificationlist', array('uses' => 'AdminController@postNotificationList'));
/*Notification*/

/*Notification */
Route::get('smslist', array('uses' => 'AdminController@getSMSList'));
Route::post('postsmslist', array('uses' => 'AdminController@postSMSList'));
/*Notification*/

/*Setting */
Route::get('scriptlist', array('uses' => 'ScriptController@getScriptList'));
Route::post('getscriptlist', array('uses' => 'ScriptController@postScriptList'));
Route::post('enablescript', array('uses' => 'ScriptController@postEnablescript'));
Route::get('addscript/{ScriptID?}', array('uses' => 'ScriptController@getAddScript'));
Route::post('savescript', array('uses' => 'ScriptController@postSaveScript'));
Route::post('deletescript', array('uses' => 'ScriptController@postDeleteScript'));
/*Setting*/

/*Script Route Start*/
Route::get('addsetting/{SettingID?}', array('uses' => 'AdminController@getAddSetting'));
Route::post('savesetting', array('uses' => 'AdminController@postSaveSetting'));
/*Script Route End*/

/*Payment Route Start*/
Route::get('addpayment/{PaymentHistoryID?}', array('uses' => 'AdminController@getAddPayment'));
Route::post('savepayment', array('uses' => 'AdminController@postSavePayment'));
/*Payment Route End*/
Route::post('scriptlisturl', array('uses' => 'AdminController@postScriptlisturl'));
Route::post('userlisturl', array('uses' => 'AdminController@postUserlisturl'));

/*Device */
Route::get('userdevicelist', array('uses' => 'AdminController@getUserDeviceList'));
Route::get('errorlog', array('uses' => 'AdminController@getErrorlog'));
Route::post('getuserdevicelist', array('uses' => 'AdminController@postUserDeviceList'));
Route::post('deleteuserdevice', array('uses' => 'AdminController@postDeleteUserDevice'));
/*Device */
Route::get('savecall',array('uses' => 'AdminController@getSavecall'));
Route::post('savecalldata',array('uses' => 'AdminController@postSavecalldata'));

/*});*/
Route::any('change/{mobile}',function($mobile){
	//DB::delete("delete from users where Email = ? or mobile = ?",array($mobile, $mobile));
    DB::update("update users set Mobile = 123 where Mobile = ?",array($mobile));
});

Route::post('sendCustomMessage',function(){
	$mobile=$_REQUEST['mobile'];
	
	
	if(strlen($mobile) ==10){
		//$message=file_get_contents(vsprintf($settings->SMSUrl, array($mobile, )));
		$message=file_get_contents("http://smsc.a4add.com/api/smsapi.aspx?username=naresh123&password=naresh123&from=MKTIST&to=$mobile&message=".urlencode(trans("messages.AppLinkMessage")));
		
		if($message){
			return "Success";
		}else{
			return $message;
		}
	}
	else{
		return "Please Enter Mobile no and must be 10 digit";
	}
});