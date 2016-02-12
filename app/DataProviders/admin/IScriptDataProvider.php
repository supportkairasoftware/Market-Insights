<?php
namespace DataProviders;

Interface IScriptDataProvider {
    public function getScriptList($scriptModel);
    public function EnableScript($scriptModel);
    public function getScriptDetails($scriptID);
    public function SaveScript($scriptModel,$user);
    public function GetSegments();
    public function DeleteScript($scriptID);
}