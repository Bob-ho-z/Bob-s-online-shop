<?php




// function reGenerateDigest()
// {
// 	global $db;
//     $db = inventory_db();
// 	$data = [];

// 	$q = $db->prepare('SELECT salt FROM orders WHERE invoice=?;');
//     $q->bindParam(1,$invoice_id);
// 	$q->execute();
// 	$salt = $q->fetchColumn();

// 	$lastInsertId = $custom_id;

// }
//
// I feel there are never too many comments in code.  Maybe it"s just me.
//
// Tom Donnelly, January 2016. covtom@gmail.com crazytom.com
// ZHOU Jiuqin, April 2022. bloaryth@gmai.com bloaryth.com
//
// ------------------------------------------------------------------------------------
// IPN is PayPal"s way of securely and reliably informing your website that you"ve had a transaction.
//
// If you want to offer instant access to digital downloads after payment, then you need to be informed as soon as the payment 
// has completed so that you don"t keep your customer waiting when they automatically return to your site.  (You do have 
// Auto-Return set on in your PayPal Seller Preferences - right?   
//  
// "But if I have Auto Return to my "success.php" page and Payment Data Transfer enabled in Seller Preferences, why do I need IPN?" I hear 
// you yell.  Good point. With these switched on, your client is automatically returned to your site after payment and you get confirmation 
// of the transaction.  But wait, there"s more...  
//
// There are two main reasons to use IPN:
//
// 1. Auto-Return uses GET not POST to give you this data and it"s easy to spoof
// 2. After completing payment on PayPal, your client may elect not to return to your website, or their connection breaks or a giant worm falls on their head
//
// So a sure-fire way of getting a reliable transaction confirmation in "real-time" from PayPal is via IPN.  In fact after purchase, PayPal
// issues a message saying "you will be returned .. in 10 seconds automatically".  There is a "go back now" button, but it"s er.. "sluggish" to give
// time for the IPN to complete before returning the customer to your site.
//
// Paypal IPN calls a program (URI)on your site (which I have called listener.php) with an array of variables about the transaction as POST data.
// All we have to do is acknowledge the notification with an HTTP 200 response, extract the variables they send to us (to record our own confirmation
//  of the transaction) and return the same data back to PayPal via HTTP with the text "cmd=_notify-validate" added in front of the data they sent.
//
// This last bit is to check that PayPal was the sender of the IPN.  PayPal checks that this is data that it sent to us If we get a good response to that, 
// then it"s authentic.
//
// How hard can it be?

//
// STEP 1 - be polite and acknowledge PayPal"s notification
//

header("HTTP/1.1 200 OK");

//
// STEP 2 - create the response we need to send back to PayPal for them to confirm that it"s legit
//

$resp = "cmd=_notify-validate";
foreach ($_POST as $parm => $var) {
	$var = urlencode(stripslashes($var));
	$resp .= "&$parm=$var";
}

// STEP 3 - Extract the data PayPal IPN has sent us, into local variables 

//$item_name        = $_POST["item_name"];
//$item_number      = $_POST["item_number"];
$payment_status   = $_POST["payment_status"];
$payment_amount   = $_POST["mc_gross"];
$payment_currency = $_POST["mc_currency"];
$txn_id           = $_POST["txn_id"];
$txn_type		  = $_POST["txn_type"];
$receiver_email   = $_POST["receiver_email"];
$payer_email      = $_POST["payer_email"];
$invoice          = $_POST["invoice"];
$custom_id	 	      = $_POST["custom"];

// Right.. we"ve pre-pended "cmd=_notify-validate" to the same data that PayPal sent us (I"ve just shown some of the data PayPal gives us. A complete list
// is on their developer site.  Now we need to send it back to PayPal via HTTP.  To do that, we create a file with the right HTTP headers followed by 
// the data block we just createdand then send the whole bally lot back to PayPal using fsockopen
//error_log(print_r("Inital",true));
error_log(print_r($_POST,true));

// check txn_id and txn_type


$db = new PDO('sqlite:../Inventory.db');
$db->query('PRAGMA foreign_keys = ON;');
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$q = $db->prepare('SELECT * FROM orders WHERE txn_id=?;');
$q->bindParam(1,$txn_id);
if($q->execute())
{
	$result = $q->fetchAll();
    if(!$result)
    {
        $check_txn_id = true;
    }
    else
        $check_txn_id =  false;
}

if($txn_type !="cart" || $check_txn_id == false)
{
	exit();
	//error_log(print_r("txn_type not = cart or this txn eist alread",true));
}
//error_log(print_r("Second",true));
//error_log(print_r($_POST,true));
//global $db;
//$db = inventory_db();
$data = [];

// error_log(print_r("invoice is",true));
// error_log(print_r($invoice,true));

$q = $db->prepare('SELECT salt FROM orders WHERE invoice=?;');
$q->bindParam(1,$invoice);
$q->execute();
$salt = $q->fetchColumn();
// error_log(print_r("salt is",true));
// error_log(print_r($salt,true));
$lastInsertId = $custom_id;
$totalPrice = 0;
array_push(
    $data,
    'HKD',
    'adminForTest@cuhk.edu.hk',
    $salt
);
$index = $_POST['num_cart_items'];
// error_log(print_r("index is",true));
// error_log(print_r($index,true));

for($i=1;$i<=$index;$i++)
{
  $itemNameKey = 'item_name';
  $indexFor = strval($i);
  $itemNameKey .= $indexFor;
  //error_log(print_r($pidKey,true));
  $itemName = $_POST[$itemNameKey];
//   error_log(print_r($itemName,true));
  $quantityKey = "quantity";

  $quantityKey .= $indexFor;
  //error_log(print_r("This is quantity key",true));
  //error_log(print_r($quantityKey,true));
  $quantity = $_POST[$quantityKey];
  $quantity = intval($quantity);
//   error_log(print_r("This is quantity ",true));
//   error_log(print_r($quantity,true));
  $priceKey = 'mc_gross_';

  $priceKey .= $indexFor;
  $price = $_POST[$priceKey];
  $price = floatval($price);


  $totalPrice = $totalPrice + $price;

  $priceForOne = $price / $quantity;
//   error_log(print_r("This is price ",true));
//   error_log(print_r($priceForOne,true));

  array_push(
	  $data,
	  $itemName,
	  $quantity,
	  $priceForOne
  );

}

array_push(
	$data,
	$totalPrice
);

// error_log(print_r("This is total price ",true));
// error_log(print_r($totalPrice,true));
$digest = hash("sha256", implode(";", $data));

// error_log(print_r("This is calculate digest  ",true));
// error_log(print_r($digest,true));
//global $db;
//$db = inventory_db();

//error_log(print_r("Third",true));
//error_log(print_r($_POST,true));

$q = $db->prepare('SELECT digest FROM orders WHERE invoice=?;');
$q->bindParam(1,$invoice);
$q->execute();
$DBdigest = $q->fetchColumn();

// error_log(print_r("This is DB digest ",true));
// error_log(print_r($DBdigest,true));

if($digest != $DBdigest)
	exit();
else{
//global $db;
//$db = inventory_db();
$q = $db->prepare('UPDATE orders SET txn_id=?, payment_status=? WHERE invoice=?;');
$q->bindParam(1,$txn_id);
$q->bindParam(2,$payment_status);
$q->bindParam(3,$invoice);
$q->execute();
}

//error_log(print_r("Four",true));
//error_log(print_r($_POST,true));

// STEP 4 - Get the HTTP header into a variable and send back the data we received so that PayPal can confirm it"s genuine

$httphead = "POST /cgi-bin/webscr HTTP/1.1\r\n";
$httphead .= "Content-Length: " . strlen($resp) . "\r\n";
$httphead .= "Content-Type: application/x-www-form-urlencoded\r\n";
$httphead .= "Host: www.sandbox.paypal.com\r\n";
$httphead .= "Connection: close\r\n\r\n";

// Now create a ="file handle" for writing to a URL to paypal.com on Port 443 (the IPN port)

$errno = "";
$errstr = "";

$fh = fsockopen("ssl://ipnpb.sandbox.paypal.com", 443, $errno, $errstr, 30);


// STEP 5 - Nearly done.  Now send the data back to PayPal so it can tell us if the IPN notification was genuine

if (!$fh) {

	// Uh oh. This means that we have not been able to get thru to the PayPal server.  It"s an HTTP failure.
	// You need to handle this here according to your preferred business logic.  An email, a log message, a trip to the pub..
}

// Connection opened, so spit back the response and get PayPal"s view whether it was an authentic notification		   

else {
	fputs($fh, $httphead . $resp);
	while (!feof($fh)) {
		$readresp = fgets($fh, 1024);
		if (strcmp(trim($readresp), "VERIFIED") == 0) {

			error_log("VERIFIED");

			// Hurrah. Payment notification was both genuine and verified

			// 	Now this is where we record a record such that when our client gets returned to our success.php page (which might be momentarily
			// (remember, PayPal tries to stall users for 10 seconds after purchase so the IPN gets through first) or much later, we can see if the
			// 	payment completed; and if it did, we can release the download.  You can go about this synchronisation between listener.php
			// 	and success.php in many different ways.  How you do it mostly depends on your need for security; but here is one way I do it:

			// 	When the client initiates the purchase by clicking the "buy" button, I write a new "unconfirmed" payment record in my Payments
			// 	table; this includes all the details of what they wish to purchase and their session-ID.  I then pass the record "id" of this pending entry in the CUSTOM
			// 	parameter to PayPal when it processes my site visitor tranaction.

			// 	After PayPal processes the transation, it doesn"t return the client to your site immediately; it conveniently stalls them for around
			// 	10 seconds, during which it quickly calls your listener program (this program) to give it the good news.  I then extract the record_id
			// 	that was inserted in the Payments table database that was created just before the client was sent to PayPal, but now I know that
			// 	the payment is VERIFIED, so I can update the record in the PAYMENTS table from "Pending" to "Completed".

			// 	When (or if) the user returns to my "Auto Return" success.php page, I query the database for all "Completed" transactions with the
			// 	same Session_id, read the digital products that they have purchased and then release them as downloadable links in
			// 	success.php.

			// 	Yes, session_id is not totally reliable, but you could use cookies, or you could use a comprehensive user
			// 	registration, logon & password retrieval system that would give you the degree of "lock down" you require.  Your choice.

		} else if (strcmp(trim($readresp), "INVALID") == 0) {

			error_log("INVALID");

			// Man alive!  A hacking attempt?
		}
	}
	fclose($fh);
}

//
//
// STEP 6 - Pour yourself a cold one.
//
//

?>