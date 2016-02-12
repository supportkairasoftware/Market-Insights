<?php 
namespace DataProviders;

Interface IUserDataProvider {

    public function SendChat($chatModel, $userImage, $userRole);
	public function ViewChat($currentUserID, $isAdmin, $timeStamp);
	public function DeleteChat($chatID, $currentUserID);
	public function UserListChat($seachModel, $UserID);
    public function GetProfile($user);
    public function SaveProfile($userdetails,$userimage,$user);
}
