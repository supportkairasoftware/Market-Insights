<?php namespace ViewModels;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;
use \stdClass;

class SessionHelper
{
	public static function setRoleID($RoleID){
    Session::put('RoleID',$RoleID);
}

    public static function getRoleID(){
        return 	Session::get('RoleID');
    }
    public static function setRoleName($RoleName){
        Session::put('RoleName',$RoleName);
    }

    public static function getRoleName(){
        return 	Session::get('RoleName');
    }
    public static function setUserName($UserName){
        Session::put('UserName',$UserName);
    }

    public static function getUserName(){
        return 	Session::get('UserName');
    }
	public static function setRedirectURL($RedirectURL){
		Session::put('RedirectURL',$RedirectURL);
	}
	
	public static function getRedirectURL(){
		return 	Session::get('RedirectURL');
	}
	
	public static function RemoveSessionForgetURL(){
		return  Session::forget('RedirectURL');
	}

    public static function SessionFlush(){
        Session::flush();
    }
}
?>