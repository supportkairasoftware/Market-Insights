<?php
namespace DataProviders;

Interface IGroupDataProvider {
    public function getGroupList($groupID);
    public function getGroupDetails($groupID);
    public function SaveGroup($groupModel,$loginUserID);
    public function DeleteUserGroup($groupID);
    public function EnableGroup($groupModel);
    public function getGroupListForSearch();
    public function SaveUserGroup($groupModel,$loginUserID);
    public function DeleteGroup($groupID);

}