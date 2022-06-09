<?php 
ini_set('display_errors',1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

// Report all PHP errors
error_reporting(-1);
include_once('db.connect.php');
include_once('auth.php');
include_once('CSRF.php');
//session_start();
if(auth() == false )
{
	//header("Location: login.php");
	//exit();

	if(($_REQUEST['action'] != 'fetch_all_categories') && ($_REQUEST['action'] != 'fetch_all_products') && ($_REQUEST['action'] != 'user_product') && ($_REQUEST['action'] != 'fetch_all_products_for_one_catid') &&  ($_REQUEST['action'] != 'fetch_detail_for_one_product') && ($_REQUEST['action'] != 'generateDigest') )
	{
		header("Location: login.php");
		exit();
	}
	
}
else 
{
	$cookie = json_decode(stripslashes($_COOKIE['auth']),true);
	if($cookie['admin'] != 1)
	{
		if(($_REQUEST['action'] != 'fetch_all_categories') && ($_REQUEST['action'] != 'fetch_all_products') && ($_REQUEST['action'] != 'user_product') && ($_REQUEST['action'] != 'fetch_all_products_for_one_catid') &&  ($_REQUEST['action'] != 'fetch_detail_for_one_product') && ($_REQUEST['action'] != 'generateDigest') )
		{
			header("Location: login.php");
			exit();
		}
	}
}



header('Content-Type: application/json');

error_log(print_r($_POST,true));

if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}


if(!empty($_POST['nonce']))
{
	csrf_verifyNonce($_REQUEST['action'], $_POST['nonce']);

}


try{

	if (($returnVal = call_user_func('db_' . $_REQUEST['action'])) === false) {
		if ($db && $db->errorCode())
			error_log(print_r($db->errorInfo(), true));
		echo json_encode(array('failed'=>'1'));
	}
	echo 'while(1);' . json_encode(array('success' => $returnVal));
} 
catch(PDOException $e) {
	error_log($e->getMessage());
	echo json_encode(array('failed'=>'error-db'));
} 
catch(Exception $e) {
	echo 'while(1);' . json_encode(array('failed' => $e->getMessage()));
}

?>