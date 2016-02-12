<?php
namespace DataProviders;

Interface ICallDataProvider {
    public function GetSegmets();
    public function GetLookupForCall();
    public function GetScriptLookupForCall();
    public function GetCurrentCalls($segmentModel,$user);
    public function GetHistoryCalls($segmentModel,$user);
    public function SaveCall($callModel);
    public function UpdateCall($callModel);
    public function Allcalllist($callModel);
    public function HideCall($callID);
    public function CallResultUpdate($callresult);
}