<?php
namespace DataProviders;
 
use Illuminate\Support\ServiceProvider;
 
class BaseServiceProvider extends ServiceProvider {
 
  public function register()
  {
    $app = $this->app;
	$app->bind('DataProviders\ISecurityDataProvider','DataProviders\SecurityDataProvider');
    $app->bind('DataProviders\IGroupDataProvider','DataProviders\GroupDataProvider');
    $app->bind('DataProviders\IPlanDataProvider','DataProviders\PlanDataProvider');
    $app->bind('DataProviders\IAdminDataProvider','DataProviders\AdminDataProvider');
    $app->bind('DataProviders\IFundamentalDataProvider','DataProviders\FundamentalDataProvider');
    $app->bind('DataProviders\IAnalystDataProvider','DataProviders\AnalystDataProvider');
    $app->bind('DataProviders\IScriptDataProvider','DataProviders\ScriptDataProvider');
    $app->bind('DataProviders\IPaymentDataProvider','DataProviders\PaymentDataProvider');
  }
}