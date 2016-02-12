<?php
use DataProviders\IPaymentDataProvider;
use DataProviders\SecurityDataProvider;
use \Infrastructure\Constants;
use ViewModels\ServiceResponse;

class PaymentController extends BaseController  {

	function __construct(IPaymentDataProvider  $paymentDataProvider){
        $this->DataProvider = $paymentDataProvider;
    }
       
    public function getPayment($planID,$token){
    	$serviceResponse = new ServiceResponse();
    	$user = $this->GetSessionUser($token);
    	
    	if($user->IsSuccess){
    		$userDetail=UserEntity::where('UserID',$user->Data->UserID)->first();
        	if($userDetail->IsVerified){
        		$serviceResponse=$this->DataProvider->PaymentDetails($planID);
	        	if($serviceResponse->IsSuccess){
					$amount=$serviceResponse->Data->PaidAmount;
	    		
			    	$data = array(
					    'amount'  => $amount,
					    'fullname' => ($user->Data->FirstName).' '.$user->Data->LastName,
					    'email'=>$user->Data->Email,
					    'mobile'=>$user->Data->Mobile,
					    'productInfo'=>'This is the product info',
					    'planID'=>$planID,
					    'userID'=>$user->Data->UserID,
					);
					
					return View::make('payment.detail')->with($data);
				}     
        	}else{
				$serviceResponse->IsSuccess = true;
		        $serviceResponse->Data = $userDetail;
		        $serviceResponse->ErrorCode=Constants::$MobileNotVerified;
		        $serviceResponse->Message = trans("messages.MobileNotEnabled");
			}
		}else{
			$serviceResponse = $user;
			return $this->GetJsonResponse($serviceResponse);
		}
		
    }
    
    public function postSuccess(){
    	$serviceRequest = Input::all();
    	$serviceResponse=$this->DataProvider->SavePayuPayment($serviceRequest);
    	if($serviceResponse->IsSuccess){
			$securityDataProvider = new SecurityDataProvider();
    		$securityDataProvider->SendMessage();
		}
    	
    	
    	$data = array(
		    'status'  => $serviceRequest['status'],
		    'txnid'=>$serviceResponse->Data->ReferenceNo,
		    'amount'=>$serviceResponse->Data->SubscriptionAmount
		);
			
		return View::make('payment.success')->with($data);
    }
    
    public function postError(){
    	$serviceRequest = Input::all();
    	$serviceResponse=$this->DataProvider->FailPayuPayment($serviceRequest);
    	
    	$securityDataProvider = new SecurityDataProvider();
    	$securityDataProvider->SendMessage();
    	
    	$data = array(
		    'status'  => $serviceRequest['status'],
		    'txnid'=>$serviceRequest['txnid']
		);
		
        return View::make('payment.error')->with($data);
    }
}