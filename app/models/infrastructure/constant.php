<?php
namespace Infrastructure;
use Illuminate\Mail\Mailer;

class Constants
{

    public static $DefaultDateTimeFormat= 'Y-m-d H:i:s';
    public static $CallDateTimeFormat='h:i:s A d-M-Y';
    public static $defaultTimeZone = 'Asia/Kolkata';//'America/Los_Angeles';
    public static $databaseDefaultTimeZone = 'GMT';
    public static $DefaultDisplayDateFormat= 'd-m-Y';
    public static $DefaultDisplayDateTimeFormat= 'h:i A d/m/Y';
    public static $DefaultDisplayDateTimeFormatSQL= '%h:%i %p %d/%m/%Y';
    public static $SortDisplayDateFormat= 'd/m/y'; //used for payment palan notifications
    public static $CheckStartWith = 1;
    public static $Value_True = 1;
    public static $Value_False = 0;
    public static $QueryType_Update = "UPDATE";
    public static $QueryType_Select = "SELECT";
    public static $QueryType_Insert = "INSERT";
    public static $QueryType_Delete = "DELETE";
	public static $ALL="All";

    /*Role Constant */
    public static $RoleAdmin=1;
    public static $RoleClient=2;
    public static $RoleCustomer = 2;
    public static $RoleSupportStaff=3;

    public static $SortIndexDESC = 'desc';
    public static $SortIndexASC = 'ASC';


    public static $AdminLogin = "Administrator Login";
    public static $SupportStaffLogin = "Support Staff Login";

    public static $AdminRoleName = "Administrator";
    public static $SupportStaffRoleName = "SupportStaff";

    public static $AdminDashboard = "dashboard";
    public static $SupportStaffDashboard = "dashboard";
    public static $AllRecords = -1;

    /*Role Constant */

    public static $defaultSortIndex='CreatedDate';
    public static $IsEnableValue = 1;
    public static $IsDisableValue = 0;
    public static $maxCharcterToDisplayDefault = 30;
    public static $maxCharcterToDisplayDefaultForMobile = 12;

    /*Group section start*/
    public static $QueryStringGroupID = "GroupID";
    public static $GroupListSortIndex = "GroupName";
    /*Group section start*/

	/*Email section start*/
	public static $LinkExpireDays_ForgotEmail=2;
	public static $Email_ForgotPasswordBody = 'emails.forgot';
	/*Email section end*/

    /*plan section Start */
    public static $QueryStringPlanID = "PlanID";
    public static $PlanListSortIndex = "PlanName";
    /*plan section end */

    /*user section Start */
    public static $QueryStringUSerID = "UserID";
    public static $UserListSortIndex = "FirstName";
    
    /*user section end*/

    /*Fundamental section Start */
    public static $QueryStringFundamentalID = "FundamentalID";
    public static $FundamentalListSortIndex = "Title";
    /*Fundamental section end*/

    /*Analyst section Start */
    public static $QueryStringAnalystID = "AnalystID";
    public static $AnalystListSortIndex = "Title";
    /*Analyst section end*/
    
    /*Script section Start */
    public static $QueryStringScriptID = "ScriptID";
    //public static $AnalystListSortIndex = "Title";
    /*Script section end*/

    /*Result & Segment */
    public static $ResultListSortIndex = "ResultName";
    public static $SegmentListSortIndex = "SegmentName";
    public static $ScriptListSortIndex = 'Script';
    public static $RefNoListSortIndex = 'ReferenceNo';
    /*Result & Segment */

    /*Setting*/
    public static $QueryStringSettingID = "SettingID";
    /*Setting*/

    /*Payment*/
    public static $QueryStringPaymentHistoryID = "PaymentHistoryID";
    /*Payment*/

    /* --------------------------------------------- */
	public static $CacheExpirationTime = 20;
	public static $Status_Active = 1;
	public static $Status_InActive = 0;

	public static $EmailSetting_SMTP = 'smtp.gmail.com';
	public static $EmailSetting_SMTP_PORT = 587;
	public static $EmailSetting_FROM_ADDRESS = 'mailer@kairasoftware.com';
	public static $Email_ForgotPasswordSubject = 'Password Reset';

	 public static $Formats_12HrTimeFormat = 'g:i a';
	 public static $Formats_12HrTimeFormat_2 = 'g:ia';
	 public static $Formats_Add15Min = '+15 minutes';

	 public static $RandomJobNameStartLetter = 'M5E';

    public static $DefaultErrorHeader = "Default Error";
    public static $ServerErrorHeader="Server Error";
    public static $ServerErrorMessage="<p>Sorry, it looks as though something broke in our system.<br/>If you continue to experience technical difficulties with the page you're trying to reach, please let us know!</p>";
    public static $NotFoundHeader="Page Not Found";
    public static $NotFoundCodeMsg="404 Not Found";
    public static $NotFoundErrorMessage="Sorry, the page you're looking for isn't here.";
    public static $ForbiddenHeader="Forbidden";
    public static $ForbiddenErrorMessage="<p>You do not have permission to retrieve the URL or link you requested.<br/>Please inform the administrator of how you got here, if you think this was a mistake.</p>";
    public static $ForbiddenCodeMsg="403 Access Denied";
    public static $CommonErrorCodeMsg="Something's wrong";


    public static $messageArray = array(
		'required' => 'asdasd',//trans('messages.PropertyRequired'),
		'min' => 'fghfgh',//trans('messages.PropertyMin'),
		'max' => 'fghfghfgh'//trans('messages.PropertyMax')
	);
	
	public static $CallActionsENUM = array(
		'1'=>'Buy',
		'2'=>'Sell',
	);

	public static $baseUploadPath = '../app/upload/';
	public static $baseDownLoadPath = '/app/upload/';
	public static $Path_ProfileImages='images/profile/';
	public static $Path_NewsImages='images/news/';
    public static $Path_AnalystImages='images/analyst/';
    public static $Path_FundamentalImages='images/fundamentals/';
    public static $Path_ScriptImages='images/script/';
	public static $Path_ChatImages = 'images/chat/';
	public static $tempPath = 'temp/';
	public static $corePath = 'core/';
    public static $Path_FundamentalPDF ='documents/fundamentals/';
    public static $CurrencySymbol='Rs.';


	public static function SendEmail($bodyTempate, $bodyData,$subject, $toEmail,$toEmailName)
	{
		$dataModel=new StdClass();
		$dataModel->Subject=$subject;
		$dataModel->ToEmail=$toEmail;
		$dataModel->ToEmailName=$ToEmailName;
		$array=(array)$dataModel;
		Mail::queue($bodyTempate, array("token"=>$encrypted), function($message) use ($array)
		 {
			 $message->to($array['ToEmail'],$array['ToEmailName'])->subject($array['Subject']);
		 });
	}
	
	/**
	* **************Push notification events****************
	*/
	public static $EventType_NewChatReceived = 'newchatreceived';
	/**
	* **************Push notification events end****************
	*/
	
	public static $DeviceChanged = 103;
	public static $MobileNotVerified = 104;
	public static $MaxAllowedDeviceChange=5;
	
	/* Group */

	public static $AllGroupID="10004";
	public static $AllGroupName="All";
	
	public static $FreeGroupID="10001";
	public static $FreeGroupName="Free";
	
	public static $TrialGroupID="10002";
	public static $TrialGroupName="Trial";
	
	public static $PaidGroupID="10003";
	public static $PaidGroupName="Paid";
	
	public static $NewsFeedType = array(
		"NEWS" => "NEWS",
		"ANALYTICS" => "ANALYTICS",
		"FUNDAMENTALS" => "FUNDAMENTALS"
	);
	
	public static $NotificationType = array(
		"General" => "General",
		"Analyst" => "Analyst",
		"Fundamental" => "Fundamental",
		"Equity" => "Equity",
		"Future" => "Future",
		"Commodity" => "Commodity",
		"BTST" => "BTST",
		"Currency" => "Currency",
		"Chat" => "Chat"
	);
	
	public static $SegmentType = array(
		"1" => "Equity",
		"2" => "Future",
		"3" => "Commodity",
		"4" => "BTST"
	);
	
	/**
	* Call result
	*/
	public static $T1Call="1";
	public static $T2Call="2";
	public static $SLCall="3";
	public static $CallClosed="6";
	public static $CallHidden = "5";
	public static $ParcelBooked = "4";

    public static $DecimalValue = "2";

    /*Expiration Plan Messages */

    public static $UserNewsActivateTrailID="1"; // 147 - Live
    public static $UserNewsExpireTrailID="2"; // 158 - Live

}
