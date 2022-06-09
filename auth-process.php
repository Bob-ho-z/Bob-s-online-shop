<?php 
ini_set('display_errors',1);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

// Report all PHP errors
error_reporting(-1);
include_once('db.connect.php');
include_once('logout.php');
include_once('CSRF.php');
//include_once('user.php');

header('Content-Type: application/json');

if (empty($_REQUEST['action']) || !preg_match('/^\w+$/', $_REQUEST['action'])) {
	echo json_encode(array('failed'=>'undefined'));
	exit();
}

csrf_verifyNonce($_REQUEST['action'], $_POST['nonce']);

try{

	if (($returnVal = call_user_func('user_' . $_REQUEST['action'])) === false) {
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


/*function inventory_db(){
	$db = new PDO('sqlite:../Inventory.db');
	$db->query('PRAGMA foreign_keys = ON;');
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $db;
}*/

function user_login(){
	if (empty($_POST['Email']) || empty($_POST['Password'])
	|| !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['Email'])
	|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['Password']))

	{
		throw new Exception('Wrong Credentials');
		header("Location: login.php");
		exit();
	}

	

	// Implement the login logic here
	global $db;
	$db = inventory_db();

	$email = $_POST['Email'];
	$password = $_POST['Password'];

	$email = filter_var($email,FILTER_SANITIZE_EMAIL);
	if(!filter_var($email,FILTER_VALIDATE_EMAIL))
		throw new Exception('Wrong Credentials');
	$password = htmlspecialchars($password);

	$sql = "SELECT * FROM users WHERE email=?;";
	$q = $db->prepare($sql);
	$q->bindParam(1,$email);
	$q->execute();
	$result = $q->fetchAll();

	if(! $result || hash_hmac('sha256',$password,$result[0]['salt']) != $result[0]['password'])
	{
		// Display the alert box 
	   /* echo 
		'<script>alert("Welcome to Geeks for Geeks")
		window.location.href("login.php")
		</script>';*/
		header("Location: login.php");
		exit();
	}
	else 
	{
		$exp = time() + 3600 * 24 * 2; // 2 days
		// set  token here
		$token = array(
			'em'=> $result[0]['email'],
			'exp'=> $exp,
			'k'=>hash_hmac('sha256',$exp.$result[0]['password'], $result[0]['salt']),
			'admin'=>$result[0]['adminFlag']
		);
		// set cookie here for client
		setcookie('auth',json_encode($token),$exp,'','',true,true);
		// set seesion for server
		$_SESSION['auth'] = $token;
		session_regenerate_id();
		if($result[0]['adminFlag'] == 1)
		{
			
			header("Location: admin_panel.php");
			exit(); 
		}
		else if($result[0]['adminFlag'] == 0)
		{
			header("Location: index.php");
			exit(); 
		}
	}
	




}


/*function logout()
{
	setcookie("auth","",time()-3600);
	session_destroy();
	session_write_close();
}*/


function user_resetPassword()
{
	//user_login();

	if (empty($_POST['Email']) || empty($_POST['Password']) || empty($_POST['NewPassword'])
	|| !preg_match("/^[\w=+\-\/][\w='+\-\/\.]*@[\w\-]+(\.[\w\-]+)*(\.[\w]{2,6})$/", $_POST['Email'])
	|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['Password'])
	|| !preg_match("/^[\w@#$%\^\&\*\-]+$/", $_POST['NewPassword']))

	{
		throw new Exception('Wrong Credentials');
		header("Location: login.php");
		exit();
	}


	global $db;
	$db = inventory_db();

	$email = $_POST['Email'];
	$password = $_POST['Password'];
	$newPassword = $_POST['NewPassword'];

	$email = filter_var($email,FILTER_SANITIZE_EMAIL);
	if(!filter_var($email,FILTER_VALIDATE_EMAIL))
		throw new Exception('Wrong Credentials');
	$password = htmlspecialchars($password);
	$newPassword = htmlspecialchars($newPassword);


	// get salt value
	$sql = "SELECT * FROM users WHERE email=?;";
	$q = $db->prepare($sql);
	$q->bindParam(1,$email);
	$q->execute();
	$result = $q->fetchAll();

	if(! $result || hash_hmac('sha256',$password,$result[0]['salt']) != $result[0]['password'])
	{
		header("Location: login.php");
		exit();
		//echo $result;
		//echo(hash_hmac('sha256',$password,$result[0]['salt']) != $result[0]['password']);
	}
	else 
	{
		$exp = time() + 3600 * 24 * 2; // 2 days
		// set  token here
		$token = array(
			'em'=> $result[0]['email'],
			'exp'=> $exp,
			'k'=>hash_hmac('sha256',$exp.$result[0]['password'], $result[0]['salt']),
			'admin'=>$result[0]['adminFlag']
		);
		// set cookie here for client
		setcookie('auth',json_encode($token),$exp,'','',true,true);
		// set seesion for server
		$_SESSION['auth'] = $token;
		session_regenerate_id( );

	}

	$newHashPassword = hash_hmac('sha256',$newPassword,$result[0]['salt']);


	$sql = "UPDATE users SET password=? WHERE email=?;";
	$q = $db->prepare($sql);

	$q->bindParam(1,$newHashPassword);
	$q->bindParam(2,$email);
	$q->execute();

	logout();


}


?>