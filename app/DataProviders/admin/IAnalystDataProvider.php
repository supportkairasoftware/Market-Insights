<?php
namespace DataProviders;

Interface IAnalystDataProvider {
    public function getAnalystList($analystModel);
    public function EnableAnalyst($analystModel);
    public function getAnalystDetails($analystID);
    public function SaveAnalyst($analystModel,$loginUserID);
    public function Allanalyst($model);
    public function AnalystDetails($model);
    public function DeleteAnalyst($model);
}