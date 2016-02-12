<?php
/*$status=$_POST["status"];
$firstname=$_POST["firstname"];
$amount=$_POST["amount"];
$txnid=$_POST["txnid"];

$posted_hash=$_POST["hash"];
$key=$_POST["key"];
$productinfo=$_POST["productinfo"];
$email=$_POST["email"];
$salt = Config::get("payu.SALT");

	if (isset($_POST["additionalCharges"])) {
       $additionalCharges=$_POST["additionalCharges"];
        $retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
	}
	else {	  
		$retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
	}
	$hash = hash("sha512", $retHashSeq);*/
	
	echo "<h3>Sorry, We are unable to process your request.</h3>";
	echo "<h4>Please try again.</h4>";
	/*if ($hash != $posted_hash) {
		echo "Invalid Transaction. Please try again";
	}
	else {
		echo "<h3>Your order status is ". $status .".</h3>";
		echo "<h4>Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.</h4>";
	} */
?>
<!--Please enter your website homepagge URL -->

<?php /*<p><a href="<?php echo URL::to('/').'/payment/payment'; ?>"> Try Again</a></p>*/ ?>
