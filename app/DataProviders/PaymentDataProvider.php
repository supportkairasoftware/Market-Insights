<?php
namespace DataProviders;

use ViewModels\ServiceResponse;
use Illuminate\Support\Facades\DB;
use \Infrastructure\Constants;

use PlanEntity;
use PaymentPlansHistoryEntity;
use MessageEntity;
use UserNotificationEntity;
use NotificationEntity;

class PaymentDataProvider extends BaseDataProvider implements IPaymentDataProvider {
	
	public function PaymentDetails($planID) {
		$response= new ServiceResponse;
		$planDetails=PlanEntity::find($planID);
		if($planDetails){
			$discount = ($planDetails->Amount * $planDetails->Discount) / 100;
        	$planDetails->PaidAmount = $planDetails->Amount - $discount;
        	$response->Data=$planDetails;
			$response->IsSuccess=true;
		}else{
			$response->IsSuccess=FALSE;
			$response->Message = trans('messages.ErrorOccured');
		}
		
		return $response;
	}
	
	public function SavePayuPayment($paymentModel){
		$response= new ServiceResponse;
		$paymentModel=(object)$paymentModel;
		
		$dateTime = date(Constants::$DefaultDateTimeFormat);
		
		$planList = DB::table('paymentplans')->where('PlanID', $paymentModel->udf1)->first();
		$user = DB::table('users')->where('UserID',$paymentModel->udf2)->first();
		
		$lastPlan = DB::table('paymentplanshistory')->where("UserID", $user->UserID)->orderBy("EndDate","desc")->first();
        $paymentpalnshistoryEntity = new PaymentPlansHistoryEntity();
        if(empty($lastPlan) || strtotime($lastPlan->EndDate)<strtotime($dateTime)){
        	$startTime = $dateTime;
        	$paymentpalnshistoryEntity->IsActive = Constants::$Value_True;
        	
        }else{
			$startTime = date(Constants::$DefaultDateTimeFormat, strtotime("+1 days", strtotime($lastPlan->EndDate)));
		}
		
		$endTime = date(Constants::$DefaultDateTimeFormat, strtotime("+".$planList->NoOfDays." days",strtotime($startTime)));
		
		
		
		$paymentpalnshistoryEntity->UserID = $user->UserID;
        $paymentpalnshistoryEntity->Amount = $planList->Amount;
        $paymentpalnshistoryEntity->SubscriptionAmount = $paymentModel->amount;
        $paymentpalnshistoryEntity->ReferenceNo = $paymentModel->txnid;
        $paymentpalnshistoryEntity->StartDate = $startTime;
        $paymentpalnshistoryEntity->EndDate = $endTime;
        $paymentpalnshistoryEntity->PaymentDate = $dateTime;
        $paymentpalnshistoryEntity->PlanName = $planList->PlanName;
        $paymentpalnshistoryEntity->NoOfDays = $planList->NoOfDays;
        $paymentpalnshistoryEntity->IsTrial = $planList->IsTrial;
        
        if($paymentpalnshistoryEntity->save()){
        	
        	$allowadmin=DB::table('allowedchatadmin')->where('IsDefault',1)->first();
        	$useradmin=DB::table('users')->where('UserID',(property_exists($allowadmin,"AdminID") && $allowadmin->AdminID>0)? $allowadmin->AdminID:1)->first();
        	
        	/**
			* Send message to admin and customer
			*/
        	
        	/*For Admin*/
        	$messageEntity = new MessageEntity();
            $messageEntity->Mobile = $useradmin->Mobile;
            $messageEntity->Message = trans("messages.SuccessPayment", array('amount'=>$paymentpalnshistoryEntity->SubscriptionAmount,'startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
            $messageEntity->save();
            
            /*For Customer*/
            $messageEntity = new MessageEntity();
            $messageEntity->Mobile = $paymentModel->phone;
            $messageEntity->Message = trans("messages.SuccessPayment", array('amount'=>$paymentpalnshistoryEntity->SubscriptionAmount,'startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
            $messageEntity->save();
            
            /**
			* Send UserNotification/news to admin and customer
			*/
			
			/*For Admin*/
			$userNotificationEntity = new UserNotificationEntity();
        	$userNotificationEntity->Description = trans("messages.SuccessPayment", array('amount'=>$paymentpalnshistoryEntity->SubscriptionAmount,'startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));;
        	$userNotificationEntity->UserID = $useradmin->UserID;
        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
        	$userNotificationEntity->save();
        	
        	/*For Customer*/
        	$userNotificationEntity = new UserNotificationEntity();
        	$userNotificationEntity->Description = trans("messages.SuccessPayment", array('amount'=>$paymentpalnshistoryEntity->SubscriptionAmount,'startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
        	$userNotificationEntity->UserID = $user->UserID;
        	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
        	$userNotificationEntity->save();
        	
        	 /**
			* Send GCM Notification/news to admin and customer
			*/
			
        	/*For Admin*/
        	$notificationEntity = new NotificationEntity();
			$notificationEntity->UserID = $useradmin->UserID;
			$notificationEntity->NotificationType = Constants::$NotificationType['General'];
			$notificationEntity->Message = trans("messages.SuccessPayment", array('amount'=>$paymentpalnshistoryEntity->SubscriptionAmount,'startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
			$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
			$notificationEntity->save();
        			        	
			/*For Customer*/
			$notificationEntity = new NotificationEntity();
			$notificationEntity->UserID = $user->UserID;
			$notificationEntity->NotificationType = Constants::$NotificationType['General'];
			$notificationEntity->Message = trans("messages.SuccessPayment", array('amount'=>$paymentpalnshistoryEntity->SubscriptionAmount,'startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
			$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
			$notificationEntity->save();
			
			
			/* For customer about plan activate */
			if($paymentpalnshistoryEntity->IsActive){
				$userNotificationEntity = new UserNotificationEntity();
		    	$userNotificationEntity->Description = trans("messages.PlanActivated", array('startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
		    	$userNotificationEntity->UserID = $paymentpalnshistoryEntity->UserID;
		    	$userNotificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
		    	$userNotificationEntity->save();
		    	
		    	
		    	$notificationEntity = new NotificationEntity();
				$notificationEntity->UserID = $paymentpalnshistoryEntity->UserID;
				$notificationEntity->NotificationType = Constants::$NotificationType['General'];
				$notificationEntity->Message = trans("messages.PlanActivated", array('startDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->StartDate)),'endDate'=>date(Constants::$SortDisplayDateFormat,strtotime($paymentpalnshistoryEntity->EndDate))));
				$notificationEntity->Key = (int)Constants::$PaidGroupID;
				$notificationEntity->CreatedDate = date(Constants::$DefaultDateTimeFormat);
				$notificationEntity->save();
			}
			
			$response->Data=$paymentpalnshistoryEntity;
			$response->IsSuccess=true;
		}
		
		return $response;
	}
	
	public function FailPayuPayment($paymentModel){
		$response= new ServiceResponse;
		$paymentModel=(object)$paymentModel;
		
		$allowadmin=DB::table('allowedchatadmin')->where('IsDefault',1)->first();
		$useradmin=DB::table('users')->where('UserID',(property_exists($allowadmin,"AdminID") && $allowadmin->AdminID>0)? $allowadmin->AdminID:1)->first();
		
		$messageEntity = new MessageEntity();
        $messageEntity->Mobile = $useradmin->Mobile;
        $messageEntity->Message = trans("messages.FailedPayment", array('fullname'=>$paymentModel->firstname,'mobile'=>$paymentModel->phone,'txnid'=>$paymentModel->txnid));
        $messageEntity->save();
        
		return $response;
	}
	
}