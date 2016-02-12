<?php 
namespace DataProviders;

Interface IPaymentDataProvider {
	public function PaymentDetails($planID);
	public function SavePayuPayment($paymentModel);
	public function FailPayuPayment($paymentModel);
}
