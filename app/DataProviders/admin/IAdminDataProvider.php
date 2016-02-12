<?php
namespace DataProviders;

Interface IAdminDataProvider {
    public function getUserList($usermodel,$logedUserID);
    public function getUserDetails($userID);
    public function SaveUser($userModel,$loginUserID);
    public function UpdateUser($userModel);
    public function getCallListForSearch();
    public function getCallList($callModel);
    public function SearchScript($searchModel);
    public function getPaymentForSearch();
    public function getUserPaymentList($PaymentModel);
    public function getNotificationList($notificationModel,$logedUserID);
    public function getSMSList($smsModel,$logedUserID);
    public function getSettingDetails($settingID);
    public function SaveSetting($settingModel);
    public function getPaymentDetails($paymentHistoryID);
    public function SavePayment($paymentModel);
    public function Dashboard();
    public function getUserDeviceList($userModel);
    public function DeleteUser($userID);
    public function DeleteUserPayment($paymenyID);
    public function DeleteCall($callModel);
}