<?php
namespace Infrastructure;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use \ViewModels\SessionHelper;
use \EmailEntity;
use \SettingEntity;
use \Mail;
use \stdClass;
use \URL;
use \Config;
use \ViewModels\SearchValueModel;
use \DataProviders\BaseDataProvider;
use PHPMailer;
use \Infrastructure\Constants;
use \ViewModels\SortModel;
use \Carbon\Carbon;
use UserEntity;
use PaymentPlansHistoryEntity;
use GroupEntity;
use GroupUserEntity;


class Common
{
    public static function SetIsCronJobRunning(&$maxFailedMailAttempt){
        $baseDataProvider=new BaseDataProvider();
        $settingEntity= $baseDataProvider->GetEntityForUpdateByPrimaryKey(new SettingEntity(),'1');
        $isCronJobRunning = $settingEntity->IsCronJobRunning;
        if($isCronJobRunning)
            return false;
        $settingEntity->IsCronJobRunning = true;
        $maxFailedMailAttempt = $settingEntity->maxFailedMailAttempt;
        $baseDataProvider->SaveEntity($settingEntity);
        return true;
    }

    public static function UnsetIsCronJobRunning() {
        $baseDataProvider=new BaseDataProvider();
        $settingEntity= $baseDataProvider->GetEntityForUpdateByPrimaryKey(new SettingEntity(),'1');
        $settingEntity->IsCronJobRunning=0;
        $baseDataProvider->SaveEntity($settingEntity);
    }

    public static function SetIsWorkFlowCronJobRunning(){
        $baseDataProvider=new BaseDataProvider();
        $settingEntity= $baseDataProvider->GetEntityForUpdateByPrimaryKey(new SettingEntity(),'1');
        $isCronWorkFlowJobRunning = $settingEntity->IsWorkFlowCronJobRunning;
        if($isCronWorkFlowJobRunning)
            return false;

        $settingEntity->IsWorkFlowCronJobRunning = true;
        $baseDataProvider->SaveEntity($settingEntity);

        return true;
    }

    public static function UnsetIsWorkFlowCronJobRunning(){
        $baseDataProvider=new BaseDataProvider();
        $settingEntity= $baseDataProvider->GetEntityForUpdateByPrimaryKey(new SettingEntity(),'1');
        $settingEntity->IsWorkFlowCronJobRunning=0;
        $baseDataProvider->SaveEntity($settingEntity);
    }

    public static function ELMAHDevMailsToSend($bodyTemplate, $bodyData,$dccExceptionSubject,$toEmail){
        $bodyData['logopath']=asset('assets/images/logo.png');
        $bodyData['bgimagepath']=asset('assets/images/bg.jpg');

        $mailTemplate = self::GenerateEmailBody($bodyTemplate, $bodyData);

        $mail = new PHPMailer();
        $mail->IsSMTP();  	  								 // telling the class to use SMTP
        $mail->SMTPDebug    = 0;
        $mail->IsHTML(true);
        $mail->SMTPAuth     =  true;
        $mail->Host     	=  Config::get('mail.host');//Mailer//Constants::$smtpserver ; 	 // SMTP server
        $mail->SMTPSecure   =  Config::get('mail.encryption');//Constants::$driver ;
        $mail->Username 	=  Config::get('mail.username');//Constants::$smtpuser ;        // SMTP username
        $mail->Password		=  Config::get('mail.password');//Constants::$smtppass ;        // SMTP password
        $mail->Port         =  Config::get('mail.port');//Constants::$smtpport ;
        $mail->From     	=  Config::get('mail.from.address');//Constants::$smtpfrom ;
        $mail->FromName 	=  Config::get('mail.from.name');//'DCC Live';

        $mail->Subject =  $dccExceptionSubject;
        $mail->Body    =  $mailTemplate;
        $mail->AddAddress($toEmail);
        if(!$mail->Send()){
            return false;
        }else{
            return true;
        }
    }

    public static function getNextMonthDate($start_date,$monthInterval){
        $newDate = date(Constants::$DefaultDateTimeFormat, strtotime($start_date . '+ '.$monthInterval.' months'));

        if(((intval(date("m", strtotime($start_date))) + $monthInterval) % 12) != (intval(date("m", strtotime($newDate))) % 12) ){
            $lastMonthDate = date(Constants::$DefaultDateTimeFormat, strtotime($start_date . '+ '.($monthInterval - 1).' months'));
            $newDate = date(Constants::$MonthEndDateTimeFormat, strtotime($lastMonthDate . '+5 days'));
        }
        return $newDate;
    }

    public static function MailsToSend($maxFailedMailAttempt){
        $searchParams = Array();
        $emailEntity = new EmailEntity();
        $baseDataProvider = new BaseDataProvider();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "IsSent";
        $searchValueData->Value = 0;
        array_push($searchParams, $searchValueData);
        $users = $baseDataProvider->GetEntityList($emailEntity, $searchParams);
        foreach ($users as $userObj) {
            $mail = new PHPMailer();
            $mail->IsSMTP();                                     // telling the class to use SMTP
            $mail->SMTPDebug = 0;
            $mail->IsHTML(true);
            $mail->SMTPAuth = true;
            $mail->Host = Config::get('mail.host');//Mailer//Constants::$smtpserver ; 	 // SMTP server
            $mail->SMTPSecure = Config::get('mail.encryption');//Constants::$driver ;
            $mail->Username = Config::get('mail.username');//Constants::$smtpuser ;        // SMTP username
            $mail->Password = Config::get('mail.password');//Constants::$smtppass ;        // SMTP password
            $mail->Port = Config::get('mail.port');//Constants::$smtpport ;
            $mail->From = Config::get('mail.from.address');//Constants::$smtpfrom ;
            $mail->FromName = Config::get('mail.from.name');//'DCC Live';

            if ($userObj->IsSent == 0 && $userObj->MailAttempt != $maxFailedMailAttempt) {
                $userObj->ToEmail = is_array($userObj->ToEmail) ? $userObj->ToEmail : explode(',', $userObj->ToEmail);

                $mail->Subject = $userObj->Subject;
                $mail->Body = $userObj->Body;
                foreach ($userObj->ToEmail as $email => $name) {
                    $mail->AddAddress($name);
                }

                if (!empty($userObj->ToCC)) {
                    $mail->AddCC($userObj->ToCC);
                }
                if (!$mail->Send()) {
                    $isSent = false;
                    $userObj->MailAttempt = $userObj->MailAttempt++;
                } else {
                    $isSent = true;
                }
                $emailEntity = $baseDataProvider->GetEntityForUpdateByPrimaryKey(new EmailEntity(), $userObj->EmailID);//EmailEntity::find($userObj->EmailID);
                $emailEntity->IsSent = $isSent;
                $emailEntity->MailAttempt = $userObj->MailAttempt;
                $baseDataProvider->SaveEntity($emailEntity);
            }
        }
    }

    public static function SendEmail($bodyTemplate, $bodyData,$subject, $toEmail,$toEmailName="",$toCCEmail=""){
        $baseDataProvider=new BaseDataProvider();
        $emailEntity = new EmailEntity();
        $emailEntity->Subject=$subject;
        $emailEntity->ToEmail=$toEmail;
        if(!empty($toCCEmail)){
            $emailEntity->ToCC=$toCCEmail;
        }
        $emailEntity->CreatedDate=date(Constants::$DefaultDateTimeFormat);
        $emailEntity->IsSent = 0;
        $emailEntity->MailAttempt = 1;

        $bodyData['logopath']=asset('assets/images/logo.png');
        $bodyData['bgimagepath']=asset('assets/images/bg.jpg');
        if(!empty($toEmailName)){
            $bodyData['FirstName'] = $toEmailName;
        }

        if(isset($bodyData['UserID'])){
            $emailEntity->CreatedBy=$bodyData['UserID'];
        }
        else{
            $emailEntity->CreatedBy=Auth::user()->UserID;
        }

        $mailTemplate = self::GenerateEmailBody($bodyTemplate, $bodyData);
        $emailEntity->Body=$mailTemplate;

        $baseDataProvider->SaveEntity($emailEntity);
    }

    public static function TestCronJob(){
        DB::table('users')->where('UserID',1)->update(array('CreatedDate'=> date(Constants::$DefaultDateTimeFormat)));
    }

    public static function CheckRolePermission($permissionArray){
        $roleID = SessionHelper::getRoleID();

        if(!empty($permissionArray) && !empty($roleID) && in_array($roleID, $permissionArray))
            return true;
        else
            return false;
    }

    public static function myRandom($no,$isNumeric=false, $chr = 'ACEFHJKMNPRTUVWXY4937',$str = "") {
        if($isNumeric)
            $chr="1234567890";
        $length = strlen($chr);
        while($no --) {
            $str .= $chr{mt_rand(0, $length- 1)};
        }
        return $str;
    }

    public static function random_password($no = 8) {
        $str = "";
        $chr = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        $length = strlen($chr);
        while($no --) {
            $str .= $chr{mt_rand(0, $length- 1)};
        }
        return $str;
    }

    public static function GetSettings() {
        $result= DB::table('settings')->first();
        return $result;
    }

    public static function GetValueFromString($stringValue) {
        if 	(is_numeric($stringValue))
            $stringValue = (int) $stringValue;
        return $stringValue;
    }

    public static function base64_to_file($base64_string,$fileExtension, $output_filePath) {
        if (!is_dir($output_filePath)) {
            mkdir($output_filePath, 0755, true);
        }

        $fileName=Common::myRandom(20).$fileExtension;
        $output_file=$output_filePath.$fileName;
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);

        return $fileName;
    }

    public static function encryptor($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        //pls set your unique hashing key
        $secret_key = 'test';
        $secret_iv = 'test123';

        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        //do the encyption given text/string/number
        if( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        }
        else if( $action == 'decrypt' ){
            //decrypt the given text/string/number
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

//TODO Encrypt - Decrypt Value need to change

    public static function getEncryptDecryptID($action,$propertyName){
        //    $decodedID = urldecode($propertyName);
        return urlencode(Common::encryptor($action, $propertyName));
    }

    public static function getEncryptDecryptValue($action,$propertyName){
        return urldecode(Common::encryptor($action, $propertyName));
    }

    public static function getExplodeValue($multiQueryString,$queryStringKey){
        if(str_contains($multiQueryString,'&'.$queryStringKey.'=') < 0){
            $first = explode('=',$multiQueryString);
            return $first[1];
        }
        if( starts_with($multiQueryString,$queryStringKey.'=') == 1 || str_contains($multiQueryString,'&'.$queryStringKey.'=') > 0) {
            $MultiQueryStringArray = explode('&', $multiQueryString);

            $first = current(array_filter($MultiQueryStringArray, function ($keyValue) use ($multiQueryString, $queryStringKey) {
                return str_contains($keyValue, $queryStringKey . '=') > 0;
            }));
            if(!empty($first))
                return explode('=',$first)[1];
        }
        return '0';
    }

    public static function GetCsvFromArrayProperty($array,$propertyName) {
        $csv = "";
        for( $i = 0; $i < count($array); $i++ ) {
            $csv .= '"' . str_replace('"', '""', $array[$i]->$propertyName) . '"';
            if( $i < count($array) - 1 ) $csv .= ",";
        }
        return $csv;
    }

    public static function GetCsvFromArrayPropertyWithSpaceAndQuote($array,$propertyName) {
        $csv = "";
        for($i = 0; $i < count($array); $i++ ) {
            if($i > 0)
                $csv .= ' ';

            $csv .= "'" . $array[$i]->$propertyName . "'";

            if( $i < count($array) - 1)
                $csv .= ",";
        }
        return $csv;
    }

    public static function GetPropertyArrayFromArray($array,$propertyName){
        return array_map(create_function('$o', 'return $o->'.$propertyName.';'), $array);
    }

    public static function GetCsvFormatUserStatus($array,$propertyName) {
        $csv = "";
        for( $i = 0; $i < count($array); $i++ ) {
            $csv .= '"' . str_replace('"', '""', $array[$i][$propertyName]) . '"';
            if( $i < count($array) - 1 ) $csv .= ",";
        }
        return $csv;
    }

    public static function GetCsvFormatFromArrayValues($array) {
        $csv = "";
        for( $i = 0; $i < count($array); $i++ ) {
            $csv .= '"' . str_replace('"', '""', $array[$i]) . '"';
            if( $i < count($array) - 1 ) $csv .= ",";
        }
        return $csv;
    }

    public static function getValidationMessagesFormat($validationMessage){
        $validationMessagesArray = "";
        foreach($validationMessage->toArray() as $key=>$value){
            $validationMessagesArray.= '<li>'.$value[0].'. '.'</li>';
        }
        return $validationMessagesArray;
    }

    public static function GetSearchArrayKey($filterWithArray,$fieldName,$fieldValue){/*----------function for searching array key from array list ------------------*/
        $key="";
        $updateArray = array_values(array_filter($filterWithArray,function($key) use ($fieldValue,$fieldName){
            return	$key->$fieldName == $fieldValue;
        }));

        if(!empty($updateArray)){
            $key = array_search($updateArray[0],$filterWithArray,false);
        }
        return $key;
    }

    public static function GetArrayFilterFromPropertyValue($filterWithArray,$fieldName,$fieldValue,$PropertyName){/*----------function for searching array key from array list ------------------*/
        $updateArray = array_values(array_filter($filterWithArray,function($key) use ($fieldName,$fieldValue){
            return	$key->$fieldName == $fieldValue;
        }));
        if(!empty($updateArray)){
            return $updateArray[0]->$PropertyName;
        }else{
            return $updateArray;
        }
    }

    public static  function GetNameWithRemoveSpace($propertyName) {
        return trim($propertyName);
    }

    public static function AddItemToSortArray(&$array,$index,$direction){
        $sortModel = new SortModel($index,$direction);
        array_push($array,$sortModel);
    }

    public static function formatSizeUnits($bytes){
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . 'GB';
        }
        elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . 'MB';
        }
        elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . 'KB';
        }
        elseif ($bytes > 1){
            $bytes = $bytes . 'B';
        }
        elseif ($bytes == 1){
            $bytes = $bytes . 'B';
        }
        else {
            $bytes = '0B';
        }

        return $bytes;
    }

    public static function ConvertGMTToPST($gmtTime, $outputDateFormat = 'Y-m-d h:i:s A', $timezoneRequired = null){
        $timezoneRequired = isset($timezoneRequired) ? $timezoneRequired : Constants::$defaultTimeZone;
        $system_timezone = date_default_timezone_get();

        date_default_timezone_set(Constants::$databaseDefaultTimeZone);
        $gmt = date("Y-m-d h:i:s A");

        $local_timezone = $timezoneRequired;
        date_default_timezone_set($local_timezone);
        $local = date("Y-m-d h:i:s A");

        date_default_timezone_set($system_timezone);
        $diff = (strtotime($local) - strtotime($gmt));

        $gmtTime = Carbon::parse($gmtTime);

        $date = Carbon::createFromFormat(Constants::$DefaultDateTimeFormat,$gmtTime,$timezoneRequired);

        $date = $date->modify("+$diff seconds");

        $datetime = date($outputDateFormat, strtotime($date));
        $result = (string)$datetime;
        return $result;
    }

    public static function ConvertPSTToGMT($pstTime,$outputDateFormat = 'Y-m-d H:i:s',$timezoneRequired= null){
        $timezone =  isset($timezoneRequired) ? $timezoneRequired :Constants::$defaultTimeZone;
        $timezoneGMT =  Constants::$databaseDefaultTimeZone;

        $system_timezone = date_default_timezone_get();


        date_default_timezone_set($timezone);
        $pst= date(Constants::$DefaultDateTimeFormat);

        $local_timezone = $timezoneGMT;
        date_default_timezone_set($local_timezone);
        $local = date("Y-m-d H:i:s");

        date_default_timezone_set($system_timezone);
        $diff = (strtotime($local) - strtotime($pst));

        $pstTime = Carbon::parse($pstTime);

        $date = Carbon::createFromFormat(Constants::$DefaultDateTimeFormat,$pstTime,$timezoneGMT);

        $date = $date->modify("+$diff seconds");

        $datetime = date($outputDateFormat, strtotime($date));
        $result = (string)$datetime;
        return $result;
    }

    public static function GetFileCacheExpiryDate($date){
        $cacheExpiryDate = date('Y-m-d h:i:s', strtotime($date . " + ". Constants::$CacheExpiryDays ." days"));
        return $cacheExpiryDate;
    }


    public static function GetAgeExcludingWeekEnds($startDate){
        $begin=strtotime($startDate);
        $end = strtotime(Common::ConvertGMTToPST(date(Constants::$DefaultDateTimeFormat)));
        if($begin>=$end){
            return 0;
        }else{

            $singleDaySeconds = 86400;
            $nextDay = strtotime(date("m/d/Y",strtotime("+1 days", $begin)));
            $allDaysSeconds = $nextDay-$begin;
            $weekendSeconds = date("N", $begin) > 5 ? $allDaysSeconds : 0;
            $begin = $nextDay;

            while($begin<=$end){
                $nextBegin = $begin + $singleDaySeconds;
                $diff = $nextBegin<=$end ? $singleDaySeconds : ($begin <= $end ? ($end - $begin) : 0);
                $allDaysSeconds += $diff;
                if (date("N", $begin) > 5)
                    $weekendSeconds += $diff;
                $begin = $nextBegin;
            };
            $workingDaysSeconds=$allDaysSeconds-$weekendSeconds;
            $working_days = (int)($workingDaysSeconds/$singleDaySeconds);

            return $working_days;
        }
    }

    public static function DownloadPDF($folderPath,$fileName){
        if (file_exists($folderPath.$fileName)) {
            header('Content-type: application/force-download');
            header('Content-Disposition: attachment; filename=' .$fileName);
            @readfile($folderPath.$fileName);
            @unlink($folderPath.$fileName);
        }else{
            return false;
        }
    }


    public  static  function moneyFormatIndia($num){
        $explrestunits = "" ;
        if(strlen($num)>3){
            $lastthree = substr($num, strlen($num)-3, strlen($num));
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
            $expunit = str_split($restunits, 2);
            for($i=0; $i<sizeof($expunit); $i++){
                // creates each of the 2's group and adds a comma to the end
                if($i==0)
                {
                    $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
                }else{
                    $explrestunits .= $expunit[$i].",";
                }
            }
            $thecash = $explrestunits.$lastthree;
        } else {
            $thecash = $num;
        }
        return $thecash; // writes the final format where $currency is the currency symbol.
    }

    public static function GetUUID(){
        $string = (string)Common::ConvertGMTToPST(date(Constants::$DefaultDateTimeFormat));
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
        $UUID =  preg_replace('/-+/', '', $string);
        return $UUID;
    }

    public static function GetTwoDigitsNumberFormat($number){
        $newNumber = sprintf("%02d",$number);
        return $newNumber;
    }

    public static function GetSubmittalStatusWiseCsvString($submittalStatus){
        switch ($submittalStatus) {
            case Constants::$SubmittalStatusPending:
                return "'".Constants::$SubmittalStatusPending."'" .","."'".Constants::$SubmittalStatusApprove."'" ."," . "'".Constants::$SubmittalStatusApproveAndResubmit."'"."," .  "'".Constants::$SubmittalStatusResubmit."'"."," .  "'".Constants::$SubmittalStatusApproveAsNoted."'" ;
                break;
            case Constants::$SubmittalStatusOpen:
                return "'".Constants::$SubmittalStatusOpen."'" .","."'".Constants::$SubmittalStatusApprove."'" ."," . "'".Constants::$SubmittalStatusApproveAndResubmit."'"."," .  "'".Constants::$SubmittalStatusResubmit."'"."," .  "'".Constants::$SubmittalStatusApproveAsNoted."'" ;
                break;
            case Constants::$SubmittalStatusApproveAndResubmit:
                return "'".Constants::$SubmittalStatusApproveAndResubmit."'"."," . "'".Constants::$SubmittalStatusResubmit."'";
                break;
            case Constants::$SubmittalStatusResubmit:
                return "'".Constants::$SubmittalStatusApproveAndResubmit."'"."," . "'".Constants::$SubmittalStatusResubmit."'";
                break;
            case Constants::$SubmittalStatusApproveAsNoted:
                return "'".Constants::$SubmittalStatusApprove."'" ."," . "'".Constants::$SubmittalStatusApproveAsNoted."'";
                break;
            case Constants::$SubmittalStatusApprove:
                return "'".Constants::$SubmittalStatusApprove."'";
                break;
        }
    }

    public static function GetWeek($dateString) {
        $week = new stdClass();
        $dt = strtotime($dateString);
        $week->StartDate = date('N', $dt)==1 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('last monday', $dt));
        $week->EndDate = date('N', $dt)==7 ? date('Y-m-d', $dt) : date('Y-m-d', strtotime('next sunday', $dt));
        return $week;
    }

    public static function GetArrayFilterValues($array,$fieldName,$fieldValue) {
        $values = array_values(array_filter($array,function($key) use ($fieldName,$fieldValue){
            return	$key->$fieldName == $fieldValue;
        }));
        return $values;
    }

    public static function GetArrayOfKeyValue(array $input,$columnKey,$indexKey=null){
        $array = array();
        foreach ($input as $value) {
            if ( ! isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( ! isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }

    public static function GetTimezoneStringWithColumnName($dateColumn){
        return $convertDateTimeZone = "CAST(CONVERT_TZ($dateColumn,'" . Constants::$databaseDefaultTimeZone . "','" . Constants::$defaultTimeZone . "') AS DATE)";
    }

    public static function SortArrayOfObjectsWithDateField($array,$fieldName,$order="ASC"){/*--Sorts multi dimensional array with date field in asc  order as default--*/
        usort($array, function($a, $b) use($order,$fieldName){

            $ad = date('Y-m-d H:i:s',strtotime($a->{$fieldName}));
            $bd = date('Y-m-d H:i:s',strtotime($b->{$fieldName}));
            if ($ad == $bd) {
                return 0;
            }
            if($order == Constants::$SortIndexDESC) {
                return $ad < $bd ? 1 : -1;
            }
            else{
                return $ad > $bd ? 1 : -1;
            }
        });
        return $array;
    }

    public static function GetNextFiveWorkingDays($timezone= null){
        $timezone =  isset($timezone) ? $timezone :Constants::$defaultTimeZone;
        $ymdDateFormat = Constants::$YmdDateFormat;
        $currentDate = date(Constants::$DefaultDateTimeFormat);
        $currentTimeZoneDate = self::ConvertGMTToPST($currentDate,$ymdDateFormat,$timezone);

        $nextFiveWorkingDays = array();
        for($i=0;$i<7;$i++){
            $newDate = date('Y-m-d', strtotime($currentTimeZoneDate. ' + '.$i.' days'));
            if(date("N", strtotime($newDate)) <= 5)
                array_push($nextFiveWorkingDays,$newDate);
        }
        return $nextFiveWorkingDays;
    }

    public static function GetFileContentType($filename) {
        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'csv' => 'text/csv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'dwg' => 'application',
            'dwgs' => 'application',

            // adobe
            'pdf' => 'content/application',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'docx' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $value = explode(".", $filename);
        $ext = strtolower(array_pop($value));   //Line 32
        $fileName = array_shift($value);  //Line 34
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }

    /**
     * @param $bodyTemplate
     * @param $bodyData
     * @param $matches
     * @return mixed|string
     */
    public static function GenerateEmailBody($bodyTemplate, $bodyData){
        $templateName = substr($bodyTemplate, strpos($bodyTemplate, ".") + 1);
        $mailTemplate = file_get_contents(URL::to('/') . '/local/app/views/emails/' . $templateName . '.blade.php', false);
        $pattern = '/\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/';
        preg_match_all($pattern, $mailTemplate, $matches);

        if (isset($matches[1])) {
            foreach ($matches[1] as $key => $value) {
                $mailTemplate = str_replace("$" . $value, $bodyData[$value], $mailTemplate);
            }
            return $mailTemplate;
        }
        return $mailTemplate;
    }
    public static function GetSubString($string,$maxCharlength = ""){
        if(!$maxCharlength){

            if(Common::isMobile()){
                $maxCharlength = Constants::$maxCharcterToDisplayDefaultForMobile;
            }else{
                $maxCharlength = Constants::$maxCharcterToDisplayDefault;
            }

        }
        if(isset($string) && strlen($string)>$maxCharlength){
            $string =  substr($string,0,$maxCharlength)."...";
        }
        return  $string;
    }
    public static function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    public static function CheckUserLogin()
    {
        $response = new ServiceResponse();
        if(SessionHelper::getRoleID() || Auth::check()) {
            $roleDetails = Common::GetLoginRoleText(SessionHelper::getRoleID());
            $response->IsSuccess= true;
            $response->Data= $roleDetails;
        }
        return $response;
    }

    public static function GetLoginRolesMenu()
    {
        $roles = new stdClass();
        $combineAdminRoleID = Constants::$QueryStringRoleID . "=" . Constants::$RoleAdmin;
        $encryptedAdminRoleID = Common::getEncryptDecryptValue("encrypt", $combineAdminRoleID);
        $roles->AdminRoleId = $encryptedAdminRoleID;

        $combineStaffRoleID = Constants::$QueryStringRoleID . "=" . Constants::$RoleSupportStaff;
        $encryptedStaffRoleID = Common::getEncryptDecryptValue("encrypt", $combineStaffRoleID);
        $roles->SupportStaffRoleId = $encryptedStaffRoleID;

        return $roles;

    }
    public static function GetLoginRoleText($roleID)
    {
        $data = new stdClass();
        switch($roleID){
            case Constants::$RoleAdmin:
                $data->roleText = Constants::$AdminLogin;
                $data->redirectURL= Constants::$AdminDashboard;
                $data->roleName= Constants::$AdminRoleName;
                break;
            case Constants::$RoleCustomer:
                $data->roleText = Constants::$SupportStaffLogin;
                $data->redirectURL= Constants::$SupportStaffDashboard;
                $data->roleName= Constants::$SupportStaffRoleName;
                break;
        }
        return $data;
    }
    public static function GetDataWithTrim($string)
    {
        return trim($string);
    }
    
    public static function SendGoogleCloudMessage($deviceTokens, $messageArray)  
	{  

		$AuthorizationKey = 'AIzaSyCL1ixEZ1EYg8sVAB-wUQMJlG9UJ5SuWfw';//'AIzaSyDWnFSPM9BEv-Mab-SdZ8VWK4motMd8hbI';//
								

		$url = 'https://android.googleapis.com/gcm/send';
		$headers = array('Authorization:key=' . $AuthorizationKey,'Content-Type: application/json');  
		$data = array(  
			'registration_ids' => $deviceTokens,  
			//'collapse_key' => $collapseKey,  
			//'data.title'=>'Title Goes Here',
			'data' => array('PushData'=>$messageArray));
	  
		$ch = curl_init();  
		curl_setopt($ch, CURLOPT_URL, $url);  
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
		curl_setopt($ch, CURLOPT_POST, true);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  
		  

		$response = curl_exec($ch); 
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  

		if (curl_errno($ch)) {  
			//return 'false';//probably you want to return false  
		}  

		if ($httpCode != 200) {  
			//return 'false';//probably you want to return false  
		}  

		curl_close($ch);  
		return $response;  
	}
	
	public static function GetGoogleCloudMessage($message,$notificationType,$key=0, $ImageUrl = "",$IsPast=0){
		
		$notificationTypeINT = 0;
		switch($notificationType){
			case 'General': 	$notificationTypeINT = 1;
								break;
			case 'Analyst': 	$notificationTypeINT = 2;
								break;
			case 'Fundamental': $notificationTypeINT = 3;
								break;
			case 'Equity': 		$notificationTypeINT = 4;
								break;
			case 'Future':		$notificationTypeINT = 5;
								break;
			case 'Commodity': 	$notificationTypeINT = 6;
								break;
			case 'BTST': 		$notificationTypeINT = 7;
								break;
			case 'Currency': 	$notificationTypeINT = 8;
								break;
			case 'Chat':		$notificationTypeINT = 9;
								break;
		}
		
		if(in_array($notificationTypeINT, array(4,5,6,7))){
			$value = rand(10, 99);
			$notificationID = $notificationTypeINT.$key+$value+0;
		}else{
			$notificationID = $notificationTypeINT.$key+0;	
		}
		
     	return array(
     		"ImageUrl"=>!empty($ImageUrl)?asset($ImageUrl):'',
	      	"Message"=>$message,
	      	"NotificationType"=>$notificationType,
			"NotificationID"=>$notificationID,
			"Key"=>$key,
			"IsPast"=>$IsPast
		);
    }
    
    public static function CommonGroups(){
    	$groupConlist=array();
    	
    	$groups=GroupEntity::select('GroupID','GroupName')->where('IsEnable',Constants::$Value_True)->get();
        
        if(count($groups)>0){
        	foreach($groups as $addcount){
	        	$countUserInGroups=GroupUserEntity::leftJoin('users','users.UserID','=','usergroups.UserID')->where('GroupID',$addcount->GroupID)->where("users.IsEnable",Constants::$Value_True)->count();
				$addcount['GroupName']=$addcount['GroupName'].' '."($countUserInGroups)";
			}
		}
		
		$allUser=UserEntity::where('IsEnable',Constants::$Value_True)->count();
		$groupConlist[]=array("GroupID"=>Constants::$AllGroupID,"GroupName"=>Constants::$AllGroupName.' '."($allUser)");
        $freeUser=DB::select("SELECT count(*) as count FROM users u WHERE u.UserID NOT IN (SELECT pph.UserID FROM paymentplanshistory pph where pph.IsActive = 1)  AND IsEnable=1");
		$freeUser=$freeUser['0']->count;
		
		$groupConlist[]=array("GroupID"=>Constants::$FreeGroupID,"GroupName"=>Constants::$FreeGroupName.' '."($freeUser)");
		$trialCount=PaymentPlansHistoryEntity::leftJoin('users','users.UserID','=','paymentplanshistory.UserID')->where("users.IsEnable",Constants::$Value_True)->where('IsTrial',Constants::$Value_True)->where('IsActive',Constants::$Value_True)->distinct('UserID')->count();
        $groupConlist[]=array("GroupID"=>Constants::$TrialGroupID,"GroupName"=>Constants::$TrialGroupName.' '."($trialCount)");
        $paidCount=PaymentPlansHistoryEntity::leftJoin('users','users.UserID','=','paymentplanshistory.UserID')->where("users.IsEnable",Constants::$Value_True)->where('IsTrial',Constants::$Value_False)->where('IsActive',Constants::$Value_True)->distinct('UserID')->count();
        $groupConlist[]=array("GroupID"=>Constants::$PaidGroupID,"GroupName"=>Constants::$PaidGroupName.' '."($paidCount)");
        
        return array_merge($groupConlist,$groups->toArray());;
        
	}
	
	public static function UserRoles($userID){
		$userRoles=DB::table('userroles')->where('UserID',$userID)->first();
		return $userRoles;
	}

    /*IOS Notification Function  */
    public static function SendIOSCloudMessage($deviceTokens,$message){
        $deviceToken=$deviceTokens;
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', 'MIPushFinal.pem');
        stream_context_set_option($ctx, 'ssl','passphrase', 'support2@');

        $fp = stream_socket_client(
            "ssl://gateway.sandbox.push.apple.com:2195" , $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

        $body['aps'] =$message;

        /*$body['aps'] = array(
             'alert' => 'komal',
             'badge' =>0,
             'sound' => 'default',
             'content-available' => 1,
         );*/

        // Encode the payload as JSON
        $payload = json_encode($body);
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        //echo $result;exit;
        if (!$result)
            echo 'Message not delivered' . PHP_EOL;
        else
            echo 'Message successfully delivered' . PHP_EOL;
        // Close the connection to the server
        fclose($fp);
        return $result;
    }

    public static function GetIOSCloudMessage($message,$notificationType,$key=0,$notificationID=0, $ImageUrl = "",$IsPast=0){

        $notificationTypeINT = 0;
        switch($notificationType){
            case 'General': 	$notificationTypeINT = 1;
                break;
            case 'Analyst': 	$notificationTypeINT = 2;
                break;
            case 'Fundamental': $notificationTypeINT = 3;
                break;
            case 'Equity': 		$notificationTypeINT = 4;
                break;
            case 'Future':		$notificationTypeINT = 5;
                break;
            case 'Commodity': 	$notificationTypeINT = 6;
                break;
            case 'BTST': 		$notificationTypeINT = 7;
                break;
            case 'Currency': 	$notificationTypeINT = 8;
                break;
            case 'Chat':		$notificationTypeINT = 9;
                break;
        }
/*      /* if(in_array($notificationTypeINT, array(4,5,6,7))){
            $value = rand(10, 99);
            $notificationID = $notificationTypeINT.$key+$value+0;
        }else{
            $notificationID = $notificationTypeINT.$key+0;
   }*/

        $uniqunotificationID = $notificationTypeINT.$key+0;

        return array(
            "ImageUrl"=>!empty($ImageUrl)?asset($ImageUrl):'',
            'alert' => $message,
            'badge' =>$uniqunotificationID,
            'sound' => 'default',
            'content-available' => 1,
            "NotificationType"=>$notificationType,
            "NotificationID"=>$notificationID,
            "Key"=>$key,
            "IsPast"=>$IsPast
        );
    }

    /*IOS Notification Function END  */

}