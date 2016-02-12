<?php
namespace DataProviders;

Interface IFundamentalDataProvider {
    public function getFundamentalList($fundamentalModel);
    public function getFundamentalDetails($fundamentalID);
    public function SaveFundamental($fundamentalModel,$loginUserID);
    public function EnableFundamental($fundamentalModel);
    public function AllFundamentalDetails($model);
    public function FundatmentalDetails($model);
    public function AllFundamentals($model);
    public function DeleteFundamental($model);
}