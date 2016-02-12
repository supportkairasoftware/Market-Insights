<?php namespace DataProviders;
use Illuminate\Support\Facades\DB;
use \ViewModels\PageModel;
use Aws\S3\S3Client;
use \stdClass;
use \Config;
use \Infrastructure\Constants;
use \Infrastructure\Common;
use \ViewModels\SearchValueModel;
use \ReflectionClass;
use File;
use \Mail;

class BaseDataProvider
{
    public function GetReflectionClass($item){
        $reflectionClass = get_class($item);
        return new ReflectionClass($reflectionClass);
    }

    public function GetTableNameFromReflectionClass($item){
        $class = $this->GetReflectionClass($item);
        return $class->getProperty('table')->getValue($item);
    }

    private function GetFilterStringCommon($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = "", $IsMultiSort = false, $sortArray = null, $IsForCount = false, $filedName = "", $isSelectColumnsGiven=false, $selectColumnsCSV = ""){
        $class = $this->GetReflectionClass($item);
        $tableName = $class->getProperty('table')->getValue($item);
        $modelPropTypes = $class->getProperty('Model_Types')->getValue($item);
        $select = ($IsForCount ? "SELECT COUNT(*) AS cnt $filedName FROM " :  ($isSelectColumnsGiven ? "SELECT ".$selectColumnsCSV." FROM "  : "SELECT * FROM ")) . $tableName;

        $where = "";

        if (!empty($searchParams) && count($searchParams) > 0) {
            $where .= " WHERE 1=1";
            foreach ($searchParams as &$val) {
                $Value = addslashes($val->Value);
                $Name = $val->Name;
                $CheckStartWith = $val->CheckStartWith;
                $propertyType = trim($modelPropTypes[$Name]);

                if ($propertyType == 'string') {
                    if (!empty($CheckStartWith) && $CheckStartWith != 1)
                        $where .= " AND " . $Name . " LIKE '" . $Value . "%'";
                    else if (!empty($CheckStartWith) && $CheckStartWith)
                        $where .= " AND " . $Name . " LIKE '" . $Value . "'";
                    else
                        $where .= " AND " . $Name . " LIKE '%" . $Value . "%'";


                } else if ($propertyType == 'bool') {
                    $where .= " AND " . $Name . "=" . $Value;
                } else if ($propertyType == 'int' || $propertyType == 'long') {
                    $where .= " AND " . $Name . "=" . $Value;
                } else if ($propertyType == 'DateTime') {

                }
            }
        }

        $where .= $customWhere != "" ? empty($where) ? " WHERE (" . $customWhere . ")" : " AND (" . $customWhere . ")" : "";
        if ($CustomGroup)
            $where .= " GROUP BY  " . $CustomGroup;

        if (!$IsMultiSort && $sortIndex != "")
            $where .= " ORDER BY " . $sortIndex . " " . $sortDirection;
        else if ($IsMultiSort && !empty($sortArray) && count($sortArray) > 0) {
            $where .= " ORDER BY ";
            foreach ($sortArray as &$val) {
                $where .= $val->Index . " " . $val->Direction . ",";
            }
            $where = rtrim($where, ',');
        }
        $where = str_replace("1=1 AND", "", $where);
        $where = str_replace("1=1", "", $where);
        return $select . $where;
    }

    private function GetFilterString($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        return $this->GetFilterStringCommon($item, $searchParams, $sortIndex, $sortDirection, $customWhere, $CustomGroup);
    }

    private function GetFilterStringSelectColumn($item, $searchParams, $selectColumnsCSV, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        return $this->GetFilterStringCommon($item, $searchParams, $sortIndex, $sortDirection, $customWhere, $CustomGroup, false, null, false, "", true, $selectColumnsCSV);
    }

    private function GetFilterStringForMultiSort($item, $searchParams, $sortArray, $customWhere = "", $CustomGroup = ""){
        return $this->GetFilterStringCommon($item, $searchParams, "", "", $customWhere, $CustomGroup, true, $sortArray);
    }

    private function GetFilterStringForCount($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        return $this->GetFilterStringCommon($item, $searchParams, "", "", $customWhere, $CustomGroup, false, null, true);
    }

    private function GetFilterCountByFilename($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = "", $IsMultiSort = false, $sortArray = null, $IsForCount = false, $filedName = ""){
        return $this->GetFilterStringCommon($item, $searchParams, "", "", $customWhere, $CustomGroup, false, null, true, $filedName);
    }

    private function GetSPString($spname, $searchParamsArray){
        $sp = "CALL " . $spname . "(";

        for ($x = 0; $x < count($searchParamsArray); $x++) {
            $sp = $sp . " '" . $searchParamsArray[$x] . "',";
        }

        $sp = rtrim($sp, ',');
        return $sp . ")";
    }

    public function SaveEntity($item){
        $item->save();
        return $item;
    }

    public function RunQueryStatement($queryString, $queryType){
        switch ($queryType) {
            case Constants::$QueryType_Select:
                return DB::select($queryString);
                break;
            case Constants::$QueryType_Update:
                return DB::update($queryString);
                break;
            case Constants::$QueryType_Insert:
                return DB::insert($queryString);
                break;
            case Constants::$QueryType_Delete:
                return DB::delete($queryString);
                break;
        }
    }

    public function DeleteEntity($item, $primaryKeyValue){
        $class = $this->GetReflectionClass($item);
        $tableName = $class->getProperty('table')->getValue($item);
        $primaryKeyName = $class->getProperty('primaryKey')->getValue($item);
        DB::table($tableName)->where($primaryKeyName, $primaryKeyValue)->delete();
    }

    public function CustomDeleteEntity($item, $FieldKeyName, $FieldKeyValue){
        $tableName = $this->GetTableNameFromReflectionClass($item);
        DB::table($tableName)->where($FieldKeyName, $FieldKeyValue)->delete();
    }

    public function CustomUpdateEntity($item, $FieldKeyName, $FieldKeyValue, $UpdateValueArray){
        $tableName = $this->GetTableNameFromReflectionClass($item);
        DB::table($tableName)->where($FieldKeyName, $FieldKeyValue)->update($UpdateValueArray);
    }

    public function CustomMultiUpdateEntity($item, $FieldKeyName, $FieldKeyValueArray, $UpdateValueArray){
        $tableName = $this->GetTableNameFromReflectionClass($item);
        DB::table($tableName)->whereIn($FieldKeyName, $FieldKeyValueArray)->update($UpdateValueArray);
    }

    public function CustomMultiUpdateEntityWithMultipleFieldsSearch($item, $searchParams, $UpdateValueArray){
        $tableName = $this->GetTableNameFromReflectionClass($item);
        if (!empty($searchParams) && count($searchParams) > 0) {
            $db = DB::table($tableName);
            foreach ($searchParams as &$val) {
                $FieldKeyValue = $val->Value;
                $FieldKeyName = $val->Name;
                $db = $db->where($FieldKeyName, $FieldKeyValue);
            }
            return $db->update($UpdateValueArray);
        } else {
            return false;
        }
    }

    public function GetEntityForUpdateByPrimaryKey($item, $primaryKeyValue){
        $reflectionClass = get_class($item);
        return $reflectionClass::find($primaryKeyValue);
    }

    public function GetEntityForUpdateByFilter($item, $searchParams){
        $reflectionClass = get_class($item);
        $isFirstTimeSet = false;
        if (!empty($searchParams) && count($searchParams) > 0) {
            foreach ($searchParams as &$val) {
                $Value = $val->Value;
                $Name = $val->Name;
                if ($isFirstTimeSet)
                    $reflectionClass = $reflectionClass->where($Name, $Value);
                else {
                    $reflectionClass = $reflectionClass::where($Name, $Value);
                    $isFirstTimeSet = true;
                }
            }
        }
        return $reflectionClass->first();
    }

    public function GetEntity($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = ""){
        $selectQuery = $this->GetFilterString($item, $searchParams, $sortIndex, $sortDirection, $customWhere);
        $result = DB::select($selectQuery . " LIMIT 0,1");
        if (!empty($result))
            return DB::select($selectQuery . " LIMIT 0,1")[0];

        return null;
    }

    public function GetEntityList($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        $selectQuery = $this->GetFilterString($item, $searchParams, $sortIndex, $sortDirection, $customWhere, $CustomGroup);
        return DB::select($selectQuery);
    }

    public function GetEntityListWithFetchColumns($item, $searchParams,$selectColumnsCSV, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        $selectQuery = $this->GetFilterStringSelectColumn($item, $searchParams, $selectColumnsCSV, $sortIndex, $sortDirection, $customWhere, $CustomGroup);
        return DB::select($selectQuery);
    }

    public function GetEntityListWithMultiSort($item, $searchParams, $SortArray, $customWhere = ""){
        $selectQuery = $this->GetFilterStringForMultiSort($item, $searchParams, $SortArray, $customWhere);
        return DB::select($selectQuery);
    }

    public function GetEntityListBySP($spname, $searchParamArray = null){
        $spString = GetSPString($spname, $searchParamArray);
        return DB::select($spString);
    }

    public function GetPageInStoredProcResultSet($item, $pageIndex, $pageSize, $count, $itemsList){
        $pageModel = new PageModel();
        $pageModel->CurrentPage = $pageIndex;
        $pageModel->TotalItems = $count;
        $pageModel->TotalPages = $count / $pageSize;
        $pageModel->ItemsPerPage = $pageSize;

        if (($pageModel->TotalItems % $pageSize) != 0)
            $pageModel->TotalPages = $pageModel->TotalPages + 1;

        $pageModel->Items = $itemsList;
        return $pageModel;
    }

    public function RunQueryStatementWithPagination($selectQuery, $pageIndex, $pageSizeCount, $sortIndex = "", $sortDirection = ""){
        $selectQry = explode("FROM", $selectQuery, 2);

        if ($sortIndex != "" && $sortDirection != "")
            $selectQuery .= " ORDER BY " . $sortIndex . " " . $sortDirection;

        $totalRecords = 0;
        if ($pageSizeCount != Constants::$AllRecords) {
            $selectQueryWithPaging = $selectQuery . " LIMIT " . ($pageIndex - 1) * $pageSizeCount . "," . $pageSizeCount . "";
            $countSelectQuery = str_replace($selectQry[0], "SELECT Count(*) as TotalItems ", $selectQuery);
            $totalRecords = DB::select($countSelectQuery)[0]->TotalItems;
        } else {
            $selectQueryWithPaging = $selectQuery;
        }

        $items = DB::select($selectQueryWithPaging);

        if ($pageSizeCount == Constants::$AllRecords)
            $totalRecords = count($items);

        $pageModel = new PageModel();
        $pageModel->CurrentPage = $pageIndex;
        $pageModel->TotalItems = $totalRecords;
        $pageModel->ItemsPerPage = $pageSizeCount;
        $pageModel->TotalPages = ceil($pageModel->TotalItems / $pageModel->ItemsPerPage);
        $pageModel->Items = $items;
        return $pageModel;
    }

    public function GetEntityWithPaging($item, $searchParams, $pageIndex, $pageSizeCount, $sortIndex = "", $sortDirection = "", $customWhere = ""){
        $selectQuery = $this->GetFilterString($item, $searchParams, $sortIndex, $sortDirection, $customWhere);
        $countSelectQuery = str_replace("*", "Count(*) as TotalItems", $selectQuery);
        $selectQueryWithPaging = $selectQuery . " LIMIT " . ($pageIndex - 1) * $pageSizeCount . "," . $pageSizeCount . "";

        $pageModel = new PageModel();
        $pageModel->CurrentPage = $pageIndex;
        $pageModel->TotalItems = DB::select($countSelectQuery)[0]->TotalItems;//$selectQuery->count();
        $pageModel->ItemsPerPage = $pageSizeCount;
        $pageModel->TotalPages = ceil($pageModel->TotalItems / $pageModel->ItemsPerPage);
        $pageModel->Items = DB::select($selectQueryWithPaging);
        return $pageModel;
    }

    public function GetEntityListWithMultiSortWithPaging($item, $searchParams, $pageIndex, $pageSizeCount, $SortArray, $customWhere = "", $CustomGroup = ""){
        $selectQuery = $this->GetFilterStringForMultiSort($item, $searchParams, $SortArray, $customWhere, $CustomGroup);
        $countSelectQuery = str_replace("*", "Count(*) as TotalItems", $selectQuery);
        $queryData = DB::select($countSelectQuery);
        if ($queryData)
            $totalItems = $queryData[0]->TotalItems;
        else
            $totalItems = 0;
        if ($pageSizeCount == Constants::$AllRecords) {
            $pageSizeCount = $totalItems > 0 ? $totalItems : Constants::$DefaultPageSize;
        }

        $selectQueryWithPaging = $selectQuery . " LIMIT " . ($pageIndex - 1) * $pageSizeCount . "," . $pageSizeCount . "";

        $pageModel = new PageModel();
        $pageModel->CurrentPage = $pageIndex;
        $pageModel->TotalItems = $totalItems;
        $pageModel->ItemsPerPage = $pageSizeCount;
        $pageModel->TotalPages = ceil($pageModel->TotalItems / $pageModel->ItemsPerPage);

        $pageModel->Items = DB::select($selectQueryWithPaging);
        return $pageModel;
    }

    public function GetEntityWithPagingDistinctCount($item, $searchParams, $pageIndex, $pageSizeCount, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        $selectQuery = $this->GetFilterString($item, $searchParams, $sortIndex, $sortDirection, $customWhere, $CustomGroup);
        $countSelectQuery = str_replace("*", "COUNT(*) TotalItems FROM (SELECT " . $CustomGroup, $selectQuery) . ') AS a';
        $selectQueryWithPaging = $selectQuery . " LIMIT " . ($pageIndex - 1) * $pageSizeCount . "," . $pageSizeCount . "";

        $pageModel = new PageModel();
        $pageModel->CurrentPage = $pageIndex;
        $pageModel->TotalItems = DB::select($countSelectQuery)[0]->TotalItems;//$selectQuery->count();
        $pageModel->ItemsPerPage = $pageSizeCount;
        $pageModel->TotalPages = ceil($pageModel->TotalItems / $pageModel->ItemsPerPage);
        $pageModel->Items = DB::select($selectQueryWithPaging);

        return $pageModel;
    }

    public function GetEntityListWithDistinctCount($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = ""){
        $selectQuery = $this->GetFilterString($item, $searchParams, $sortIndex, $sortDirection, $customWhere, $CustomGroup);
        $countSelectQuery = str_replace("*", "* FROM (SELECT " . $CustomGroup, $selectQuery) . ') AS a';
        return DB::select($selectQuery);
    }

    public function FileUploadSettings($type = '', $projectid = 0, $userid){
        $Model = new stdClass();
        $final_folder = '';
        $projectidentifier = '';
        static $bucket;
        static $accesskey;
        static $secret;
        static $url;
        $sucess_action = Constants::$AWSuccessAction;
        /* to upload file as private */
        //$acl=Constants::$AWSAcl_Private;
        /* to upload file as public */
        $acl = Constants::$AWSAcl_Public;

        if ($projectid > 0) {
            $dbdata = DB::table('projects')->where('ProjectID', '=', $projectid)->first();
            $projectidentifier = $dbdata->Identifier;
            $final_folder = Config::get('app.awsprojectfolder') . '/' . $projectidentifier . '/';
        }

        $dbdata = DB::table('settings')->first();

        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;
        $url = $dbdata->AWSUrl;

        switch ($type) {
            case Constants::$AWSRequestType_Profile:
                $final_folder = Config::get('app.awsprofilefolder');
                break;
            case Constants::$AWSRequestType_Photos:
                $final_folder .= Config::get('app.awsproject_photos');
                break;
            case Constants::$AWSRequestType_Minutes:
                $final_folder .= Config::get('app.awsproject_minutes');
                break;
            case Constants::$AWSRequestType_Companydocs:
                $final_folder .= Config::get('app.awsproject_companydocs');
                break;
            case Constants::$AWSRequestType_Plans:
                $final_folder .= Config::get('app.awsproject_plans');
                break;
            case Constants::$AWSRequestType_Specs:
                $final_folder .= Config::get('app.awsproject_specs');
                break;
            case Constants::$AWSRequestType_Submittals:
                $final_folder .= Config::get('app.awsproject_submittals');
                break;
            case Constants::$AWSRequestType_Events:
                $final_folder .= Config::get('app.awsproject_events');
                break;
            case Constants::$AWSRequestType_Rfis:
                $final_folder .= Config::get('app.awsproject_rfis');
                break;
            default:
                $final_folder = Config::get('app.awsprofilefolder');
                break;
        }

        $policy = json_encode(array(
            'expiration' => date('Y-m-d\TG:i:s\Z', strtotime('+50 hours')),
            'conditions' => array(
                array(
                    'bucket' => $bucket
                ),
                array(
                    'acl' => $acl
                ),
                array(
                    'starts-with',
                    '$key',
                    ''
                ),
                array(
                    'success_action_status' => $sucess_action
                )
            )
        ));

        $base64Policy = base64_encode($policy);
        $signature = base64_encode(hash_hmac("sha1", $base64Policy, $secret, $raw_output = true));

        $Model->url = $url . $bucket . '/';
        $Model->accesskey = $accesskey;
        $Model->secret = $secret;
        $Model->acl = $acl;
        $Model->success_action = $sucess_action;
        $Model->base64Policy = $base64Policy;
        $Model->signature = $signature;
        $Model->folder = $final_folder;
        $Model->enc_userid = $userid;

        return $Model;
    }

    /*
     *
     * This function returns signed url generated by aws sdk to download file from aws.
     * $key=path of file on aws stored in db.
     *
     */

    public function Awsdownloadfile($key){
        $Model = new stdClass();
        static $bucket;
        static $url;

        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $url = $dbdata->AWSUrl;
        $url = $dbdata->AWSUrl;
        $Model->signedUrl = $url . $bucket . '/' . $key;
        return $Model;
    }

    public function Awsdownloadprivatefile($key){
        $Model = new stdClass();
        static $bucket;
        static $accesskey;
        static $secret;
        static $url;
        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;
        $url = $dbdata->AWSUrl;
        $client = S3Client::factory(array(
            'key' => $accesskey,
            'secret' => $secret
        ));

        header('content-type:application/json');

        $searchParams = array();
        $searchValueData = new SearchValueModel();
        $searchValueData->Name = "FilePath";
        $searchValueData->Value = $key;
        array_push($searchParams, $searchValueData);

        //$DateDifference = $this->GetDateDifferenceForCacheExpiryDate($CacheExpiryDate,$fileEntityNewObject,$primaryKeyValue);
        $DateDifference = Constants::$CacheExpirySeconds;
        $Model->signedUrl = $client->getObjectUrl($bucket, $key, '+' . $DateDifference . ' seconds');
        return $Model;
    }

    public function Awsdownloadsinglefile($key, $filename = '', $userid, $downloadTokenValue = ""){
        try {

            $signedurl = $this->Awsdownloadfile($key);
            $url = $signedurl->signedUrl;
            $file = explode('/', $key);
            $rfile = fopen($url, 'r');
            $tempfile = $file[count($file) - 1];

            $tempfolder = public_path() . DIRECTORY_SEPARATOR . 'temp';
            $tempfolder = str_replace('\\', '/', $tempfolder);
            $tempfolder = str_replace('/', DIRECTORY_SEPARATOR, $tempfolder);

            is_dir($tempfolder) || mkdir($tempfolder);

            $tempUserFolder = $tempfolder . DIRECTORY_SEPARATOR . $this->GenerateFolderName($userid);
            is_dir($tempUserFolder) || mkdir($tempUserFolder);

            $tempfile = $tempUserFolder . DIRECTORY_SEPARATOR . basename($tempfile);

            $lfile = fopen($tempfile, 'w');
            while (!feof($rfile))
                fwrite($lfile, fread($rfile, 4095), 4095);

            fclose($rfile);
            fclose($lfile);
            $fullPath = $tempfile;

            if (!$filename)
                $filename = basename($fullPath);

            setcookie("fileDownloadToken", $downloadTokenValue, time() + 3600, "/");
            setcookie("userSingleFileDownloadToken", $downloadTokenValue, time() + 3600, "/");
            setcookie("DownloadSingleRFI", $downloadTokenValue, time() + 3600, "/");
            setcookie("DownloadPlansRFI", $downloadTokenValue, time() + 3600, "/");
            setcookie("DownloadSpecsRFI", $downloadTokenValue, time() + 3600, "/");
            setcookie("DownloadSpecsSubmittal", $downloadTokenValue, time() + 3600, "/");
            setcookie("DownloadSubmittalFiles", $downloadTokenValue, time() + 3600, "/");

            $fileDetail = pathinfo($filename);
            $filedata = file_get_contents($fullPath); // Read the file's contents

            header('Content-Description: File Transfer; charset=UTF-8');

            $contentType = Common::GetFileContentType($filename);

            header('Content-Type: ' . $contentType . '');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($fullPath));
            ob_clean();
            flush();
            readfile($fullPath);
            $parentDir = dirname($fullPath);

            unlink($fullPath);
            is_dir($parentDir) && rmdir($parentDir);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    /*
     *
     * This function will open download popup to download zip file for user.
     * $files=array of keys(path of file on aws stored in db)
     * $userid = id of logged in user to generate zip file.
     *
     */
    //public function Awsdownloadzipfile($files,$userid, $ProjectIdentifier,$fileEntityNewObject = null)
    public function Awsdownloadzipfile($files, $userid, $ProjectIdentifier){
        try {
            $Model = new stdClass();
            $temparray = array();
            $zipper = new \Chumper\Zipper\Zipper;
            static $bucket;
            static $accesskey;
            static $secret;

            $dbdata = DB::table('settings')->first();
            $bucket = $dbdata->AWSBucketName;
            $accesskey = $dbdata->APIKey;
            $secret = $dbdata->AWSSecretKey;
            $client = S3Client::factory(array(
                'key' => $accesskey,
                'secret' => $secret
            ));

            header('content-type:application/json');
            $filepath = $ProjectIdentifier . '-' . date(Constants::$FileNameDateTimeFormat) . '.zip';

            $tempfolder = public_path() . DIRECTORY_SEPARATOR . 'temp';
            $tempfolder = str_replace('\\', '/', $tempfolder);
            $tempfolder = str_replace('/', DIRECTORY_SEPARATOR, $tempfolder);
            is_dir($tempfolder) || mkdir($tempfolder);
            $tempUserFolder = $tempfolder . DIRECTORY_SEPARATOR . $this->GenerateFolderName($userid);
            is_dir($tempUserFolder) || mkdir($tempUserFolder);
            $fullPath = $tempUserFolder . DIRECTORY_SEPARATOR . $filepath;
            $zip = $zipper->make($fullPath);
            $tempfilearray = array();

            $this->GetFilesArrayWithUniqueFileName($files);

            foreach ($files as $file) {
                $DateDifference = Constants::$CacheExpirySeconds;
                $signedUrl = $client->getObjectUrl($bucket, $file['FilePath'], '+ ' . $DateDifference . ' seconds');
                $url = $signedUrl;
                $rfile = fopen($url, 'r');
                $tempUserFolders = $tempUserFolder;
                $tempfile = $tempUserFolders . DIRECTORY_SEPARATOR . $file['FileName'];
                $lfile = fopen($tempfile, 'w');

                while (!feof($rfile))
                    fwrite($lfile, fread($rfile, 4095), 4095);
                fclose($rfile);
                fclose($lfile);
                !empty($file['FolderName']) ? $zip->folder($file['FolderName'])->add($tempfile) : $zip->add($tempfile);
                array_push($tempfilearray, $tempfile);
            }

            $rdata = array('fullpath' => $fullPath, 'files' => $tempfilearray, 'fileUrl' => url($filepath));
            return $rdata;
        } catch (Exception $e) {
            return false;
        }
    }

    public function downloadfile($data, $downloadTokenValue = ""){
        if (file_exists($data['fullpath'])) {
            try {
                setcookie("userSingleFileDownloadToken", $downloadTokenValue, time() + 3600, "/");
                setcookie("fileDownloadToken", $downloadTokenValue, time() + 3600, "/");
                setcookie("MultiFileDownloadToken", $downloadTokenValue, time() + 3600, "/");
                setcookie("searchMultiFileDownloadToken", $downloadTokenValue, time() + 3600, "/");
                setcookie("MultiFolderDownloadToken", $downloadTokenValue, time() + 3600, "/");
                setcookie("userSingleFolderDownloadToken", $downloadTokenValue, time() + 3600, "/");

                header('Content-Description: File Transfer; charset=UTF-8');
                header('Content-Type: application/zip');
                header('Content-Disposition: attachment; filename="' . basename($data['fullpath']) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($data['fullpath']));
                ob_clean();
                flush();
                readfile($data['fullpath']);
                $parentDir = dirname($data['fullpath']);
                unlink($data['fullpath']);
                foreach ($data['files'] as $file)
                    unlink($file);

                is_dir($parentDir) && rmdir($parentDir);

                return true;
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     *
     * This function extract the zip file and then unlink it and will return path of extracted folder.
     * $file = file path of zip file
     * $userid = id of logged in user to generate extract folder,which needs to be unlinked after processing.
     *
     */
    public function Extractzipfile($file, $userid){
        $path = false;
        try {
            $zipper = new \Chumper\Zipper\Zipper;

            $tempfolder = public_path() . DIRECTORY_SEPARATOR . 'temp';
            $tempfolder = str_replace('\\', '/', $tempfolder);
            $tempfolder = str_replace('/', DIRECTORY_SEPARATOR, $tempfolder);

            is_dir($tempfolder) || mkdir($tempfolder);

            $extractedfolderName = $this->GenerateFolderName($userid);

            $tempUserFolder = $tempfolder . DIRECTORY_SEPARATOR . $extractedfolderName;
            is_dir($tempUserFolder) || mkdir($tempUserFolder);

            $path = $tempUserFolder;

            $zipper->make($file)->extractTo($tempUserFolder);
        } catch (Exception $e) {
            $path = false;
        }

        return $path;
    }

    /*
     *
     * This function generates unique file name to upload on aws.
     * $name = file name which need to be regenerated
     * $userid = ID of user who is logged in, used to generate hash value used for file name.
     *
     */
    public function GenerateFileName($name, $userid){
        $ext = explode('.', $name);
        $t = explode(" ", microtime());
        $milliseconds = substr((string)$t[0], 2, 3);
        $str = $milliseconds + $userid;
        $hash = substr(md5($str), 0, 8);
        $date = date("d-m-Y h:i:s", time());
        $dateparts = explode(" ", $date);
        $date = explode('-', $dateparts[0]);
        $time = explode(':', $dateparts[1]);
        $name = $hash . '-' . $time[2] . $time[1] . $time[0] . '-' . $date[1] . $date[0] . $date[2] . '.' . $ext[count($ext) - 1];
        return $name;
    }

    /*
     *
     * This function generates unique folder name to download folder.
     * $name = file name which need to be regenerated
     * $userid = ID of user who is logged in, used to generate hash value used for file name.
     *
     */
    public function GenerateFolderName($userid){
        $t = explode(" ", microtime());
        $milliseconds = substr((string)$t[0], 2, 3);
        $str = $milliseconds + $userid;
        $hash = substr(md5($str), 0, 8);
        $date = date("d-m-Y h:i:s", time());
        $dateparts = explode(" ", $date);
        $date = explode('-', $dateparts[0]);
        $time = explode(':', $dateparts[1]);
        return $hash . '-' . $time[2] . $time[1] . $time[0] . '-' . $date[1] . $date[0] . $date[2];
    }

    /*
     * (server side upload)
     * this function will upload given file on aws using aws sdk and return location on aws.
     * $type= type of request like user-profile-image,project-photos etc.....all types are defined in constants.
     * $projectid=In case of user profile upload $projectid = 0 otherwise ID of project for which upload request is made.
     * $userid = ID of user who is logged in, used to generate hash value used for file name.
     * $path = path of file needs to be uploaded.
     *
     */
    public function FileUpload($type = '', $projectid = 0, $userid, $path){
        $Model = new stdClass();
        $final_folder = '';
        $projectidentifier = '';
        $url = '';
        static $bucket;
        static $accesskey;
        static $secret;
        static $url;
        $sucess_action = Constants::$AWSuccessAction;
        $acl = Constants::$AWSAcl_Public;

        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;

        $client = S3Client::factory(array(
            'key' => $accesskey,
            'secret' => $secret
        ));

        if ($projectid > 0) {
            $dbdata = DB::table('projects')->where('ProjectID', '=', $projectid)->first();

            $projectidentifier = $dbdata->Identifier;
            $final_folder = Config::get('app.awsprojectfolder') . '/' . $projectidentifier . '/';
        }

        switch ($type) {
            case Constants::$AWSRequestType_Profile:
                $final_folder = Config::get('app.awsprofilefolder');
                break;
            case Constants::$AWSRequestType_Photos:
                $final_folder .= Config::get('app.awsproject_photos');
                break;
            case Constants::$AWSRequestType_Minutes:
                $final_folder .= Config::get('app.awsproject_minutes');
                break;
            case Constants::$AWSRequestType_Companydocs:
                $final_folder .= Config::get('app.awsproject_companydocs');
                break;
            case Constants::$AWSRequestType_Plans:
                $final_folder .= Config::get('app.awsproject_plans');
                break;
            case Constants::$AWSRequestType_Specs:
                $final_folder .= Config::get('app.awsproject_specs');
                break;
            case Constants::$AWSRequestType_Submittals:
                $final_folder .= Config::get('app.awsproject_submittals');
                break;
            case Constants::$AWSRequestType_Events:
                $final_folder .= Config::get('app.awsproject_events');
                break;
            case Constants::$AWSRequestType_Rfis:
                $final_folder .= Config::get('app.awsproject_rfis');
                break;
            default:
                $final_folder = Config::get('app.awsprofilefolder');
                break;
        }

        $filepath = explode('/', $path);

        $fielname = $filepath[count($filepath) - 1];
        $new_filename = $this->GenerateFileName($fielname, $userid);
        $key = $final_folder . '/' . $new_filename;
        $filesize = filesize($path);
        $ext = pathinfo($path, PATHINFO_EXTENSION);

        $filedata = file_get_contents($path);
        try {
            $result = $client->putObject(array(
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => fopen($path, 'r'),
                'ACL' => $acl,
                "Cache-Control" => "max-age=86400000, must-revalidate",
            ));

            $url = $key;
            unlink($path);
        } catch (S3Exception $e) {
            echo $e->getMessage() . "\n";
        }
        $filedata = new stdClass();
        $filedata->url = $url;
        $filedata->size = $filesize;
        $filedata->extension = $ext;
        return $filedata;
    }

    /*
     *
     * This function delets file on aws using aws sdk.
     * $key = path of file on aws stored in db.
     *
     */
    public function Awsdeletefile($key){
        $Model = new stdClass();
        static $bucket;
        static $accesskey;
        static $secret;
        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;

        $client = S3Client::factory(array(
            'key' => $accesskey,
            'secret' => $secret
        ));

        $Model->result = $client->deleteObject(array('Bucket' => $bucket, 'Key' => $key));
        return $Model;
    }

    public function AwsChangeAcl(){
        static $bucket;
        static $accesskey;
        static $secret;
        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;

        $client = S3Client::factory(array(
            'key' => $accesskey,
            'secret' => $secret
        ));

        $client->putBucketPolicy(array('Bucket' => $bucket, 'Policy' => '{
              "Version":"2012-10-17",
              "Statement":[
                {
                  "Sid":"AddPerm",
                  "Effect":"Allow",
                  "Principal": "*",
                  "Action":["s3:GetObject"],
                  "Resource":["arn:aws:s3:::' . $bucket . '/*"]
                }
              ]
            }'));
        print_r($client->getBucketAcl(array('Bucket' => $bucket)));
    }


    public function Awsuploadzipfile($file, $userid){
        $zipper = new \Chumper\Zipper\Zipper;
        $path = public_path() . '/assets/' . $userid;
        if (!file_exists($path)) {
            mkdir($path, 0744);
        }
        $zipper->make($file)->extractTo($path);

        $Model = new stdClass();
        static $bucket;
        static $accesskey;
        static $secret;
        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;

        $client = S3Client::factory(array(
            'key' => $accesskey,
            'secret' => $secret
        ));

        $returndata = $this->ScanDirectory($client, $path, $bucket, $userid);

        return $returndata;
    }

    public function ScanDirectory($path, $type, $projectid, $userid){
        $Model = new stdClass();
        static $bucket;
        static $accesskey;
        static $secret;
        $dbdata = DB::table('settings')->first();
        $bucket = $dbdata->AWSBucketName;
        $accesskey = $dbdata->APIKey;
        $secret = $dbdata->AWSSecretKey;

        $client = S3Client::factory(array(
            'key' => $accesskey,
            'secret' => $secret
        ));

        $returndata = array();
        $results = scandir($path);

        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;

            if (is_dir($path . '/' . $result)) {
                $this->ScanDirectory($path . '/' . $result, $type, $projectid, $userid);
            } else if (is_file($path . '/' . $result)) {
                $url = $this->FileUpload($type, $projectid, $userid, $path . '/' . $result);
                $filearray = array('filepath' => $url, 'originalname' => $result);
                array_push($returndata, $filearray);
            }
        }
        rmdir($path);
        return count($returndata) > 0 ? $returndata : false;
    }

    public function ValidateZip($path){
        $results = scandir($path);
        foreach ($results as $result) {
            if ($result === '.' or $result === '..') continue;
            $file = $path . DIRECTORY_SEPARATOR . $result;
            if (is_file($file)) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);
                $name = pathinfo($file, PATHINFO_FILENAME);

                $IsHidden = substr($name, 0, 1);

                if ($IsHidden == '.') {
                    unlink($file);
                } else if (!in_array(strtolower($ext), Constants::$AllowedExtensions))
                    unlink($file);
            } else {
                File::deleteDirectory($path);
                return false;
            }
        }

        return true;
    }


    public function GetEntityCount($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = ""){
        $selectQuery = $this->GetFilterStringForCount($item, $searchParams, $sortIndex, $sortDirection, $customWhere);
        $result = DB::select($selectQuery . " LIMIT 0,1");
        return intval($result[0]->cnt);
    }

    public function File_NewName($filename, $RequestEntity, $searchFileData){
        if ($pos = strrpos($filename, '.')) {
            $name = substr($filename, 0, $pos);
            $ext = substr($filename, $pos);
        } else {
            $name = $filename;
        }

        $newname = $filename;
        $counter = 0;
        $checkUniqueFile = $this->GetEntityCount($RequestEntity, $searchFileData);
        "SELECT
                IF(COUNT(*) > 0,
                CONCAT('einstein_car_reservations_2','_',(COUNT(*)+1),'.png'), 'einstein_car_reservations_2.png') AS filerename
                FROM projectfiles
                WHERE projectfolderid = 42 AND
                            Filename REGEXP '^einstein_car_reservations_2(_[[:digit:]]+)?.png$'";
        while ($checkUniqueFile > 0) {
            $newname = $name . '_' . $counter . $ext;
            $counter++;
        }

        return $newname;
    }

    public function GetEntityCountByFieldName($item, $searchParams, $sortIndex = "", $sortDirection = "", $customWhere = "", $CustomGroup = "", $filedName = ""){
        $selectQuery = $this->GetFilterCountByFilename($item, $searchParams, $sortIndex, $sortDirection, $customWhere, $CustomGroup, "", "", true, $filedName);
        return DB::select($selectQuery);
    }

    /* Dev_as Region Start */

    public function GetFilesArrayWithUniqueFileName(&$files){
        $fileArrayWithCount = array();
        foreach ($files as &$file) {
            $folderName = (empty($file['FolderName']) ? "" : $file['FolderName']);
            $key = '"' . $folderName . $file['FileName'] . '"';
            if (array_key_exists($key, $fileArrayWithCount)) {
                $fileName = $file['FileName'];
                $extension = "." . pathinfo($fileName, PATHINFO_EXTENSION);
                $fileNameWithOutExtension = substr($fileName, 0, - strlen($extension));//rtrim($fileName, $extension);
                do {
                    $newFileName = $fileNameWithOutExtension . "_" . $fileArrayWithCount[$key] . $extension;
                    $fileArrayWithCount[$key]++;
                } while (sizeof(array_filter($files, function ($keyValue) use ($newFileName, $folderName) {
                        return $keyValue['FileName'] == $newFileName && (empty($keyValue['FolderName']) || $keyValue['FolderName'] == $folderName) && empty($keyValue['IsUpdated']);
                    })) > 0);
                $file['FileName'] = $newFileName;
                $file['IsUpdated'] = 1;
            } else {
                $fileArrayWithCount[$key] = 1;
                $file['IsUpdated'] = 1;
            }
        }
    }

    /* Dev_as Region End */

    public function Sendmailqueue(){
        $allmail=DB::table('emails')->where('IsSent',Constants::$Value_False)->get();
        if($allmail){
            foreach($allmail as $mail) {
                //Common::SendEmail($mail->TemplateName, array($data), $mail->Subject, $mail->ToAddress,'');
                //Constants::SendEmail($mail->TemplateName, array($data), $mail->Subject, $mail->ToAddress,'');
                try
                {
                    $bodydata=unserialize($mail->Data);
                    $dataModel=new StdClass();
                    $dataModel->Subject=$mail->Subject;
                    $dataModel->ToEmail=$mail->ToAddress;
                    $dataModel->ToEmailName=$mail->ToAddress;
                    $array=(array)$dataModel;

                    Mail::queue($mail->TemplateName, $bodydata, function($message) use ($array)
                    {
                        $message->to($array['ToEmail'],$array['ToEmailName'])->subject($array['Subject']);
                    });


                }
                catch (Exception $e)
                {

                }
                DB::update("Update emails set IsSent=".Constants::$Value_True." where EmailID=".$mail->EmailID );
            }
        }
    }
}
