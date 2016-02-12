<?php 
namespace DataProviders;

Interface ISecurityDataProvider {

    public function Signup($userModel,$userimage);
    public function postAuthenticate($userModel);
    public function OTPverified($otpmodel);
    public function Forgot($forgotmodel);
    public function Logout($userModel);
    public function CheckMobileVerification($userID);
    public function CheckUserPlan($userID);
}
