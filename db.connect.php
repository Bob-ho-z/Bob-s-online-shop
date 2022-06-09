<?php
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL);
error_reporting(-1);

function inventory_db(){
    $db = new PDO('sqlite:../Inventory.db');
    $db->query('PRAGMA foreign_keys = ON;');
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $db;
}

function db_fetch_all_categories(){
    global $db;
    $db = inventory_db();
    $q = $db->prepare('SELECT * FROM categories;');
    if($q->execute())
    {
        return $q->fetchAll();
    }
}

function db_fetch_all_products(){
    global $db;
    $db = inventory_db();
    $q = $db->prepare('SELECT * FROM products;');
    if($q->execute())
    {
        return $q->fetchAll();
    }
}

function db_category_insert(){
    global $db;
    $db = inventory_db();

    if(!preg_match('/^\w+$/', $_POST['name']))
        throw new Exception("invalid-category-name");
    
    
    $q = $db->prepare("SELECT max(catid) FROM categories;");
    $q->execute();
    
    $catid = $q->fetchColumn() + 1;

    $sql="INSERT INTO categories (catid, name) VALUES (?,?);";
    $q=$db->prepare($sql);
    $q->bindParam(1,$catid);
    $name=$_POST["name"];
    $name = htmlspecialchars($name);

    $q->bindParam(2,$name);
    $q->execute();
    header("Location: admin_panel.php");
    exit();
    



}

function db_product_insert(){
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w+\s]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^\d+\.?\d*$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match('/^[\w\s ]+$/', $_POST['description']))
        throw new Exception("invalid-text");

    if ($_FILES["image"]["error"] == 0
        && ($_FILES["image"]["type"] == "image/jpeg" || $_FILES["image"]["type"] == "image/jpg" || $_FILES["image"]["type"] == "image/png" || $_FILES["image"]["type"] == "image/gif")
        && (mime_content_type($_FILES["image"]["tmp_name"]) == "image/jpeg" || mime_content_type($_FILES["image"]["tmp_name"]) == "image/jpg" || mime_content_type($_FILES["image"]["tmp_name"]) == "image/png" || mime_content_type($_FILES["image"]["tmp_name"]) == "image/gif")
        && $_FILES["image"]["size"] <= 10485760) {
        


        $catid = $_POST["catid"];
        $catid = filter_var($catid,FILTER_SANITIZE_NUMBER_INT);
        if(!filter_var($catid,FILTER_VALIDATE_INT))
            throw new Exception("invalid-catid");

        $pq = $db->prepare("SELECT name FROM categories WHERE catid=?;");
        $pq->bindParam(1,$catid);
        $pq->execute();

        $category = $pq->fetchColumn();

        $name = $_POST["name"];
        $price = $_POST["price"];
        $desc = $_POST["description"];
        
        $name = htmlspecialchars($name);
        $desc = htmlspecialchars($desc);
        $price = filter_var($price,FILTER_SANITIZE_NUMBER_FLOAT);
        if(!filter_var($catid,FILTER_VALIDATE_FLOAT))
            throw new Exception("invalid-price");



        $sql="INSERT INTO products (catid, name, price, description) VALUES (?, ?, ?, ?);";
        $q = $db->prepare($sql);
        $q->bindParam(1, $catid);
        $q->bindParam(2, $name);
        $q->bindParam(3, $price);
        $q->bindParam(4, $desc);
        $q->execute();
        $lastId = $db->lastInsertId();

        $insertName = str_replace(' ','',$name);
        preg_replace('/[^a-zA-Z\d]/','',$insertName);

        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if($_FILES["image"]["type"] == "image/jpeg")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/". $category . "/".$insertName . ".jpeg")) {
                // redirect back to original page; you may comment it during debug
                echo "success";
                header('Location: admin_panel.php');
                exit();
            }
        
        else if($_FILES["image"]["type"] == "image/jpg")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName . ".jpg")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin_panel.php');
                exit();
            }

        else if($_FILES["image"]["type"] == "image/png")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName  . ".png")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin_panel.php');
                exit();
            }

        else if($_FILES["image"]["type"] == "image/gif")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName  . ".gif")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin_panel.php');
                exit();
            }
    }

    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    echo"wrong";
    exit();
}


function db_category_update(){
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w+\s]+$/', $_POST['newName']))
        throw new Exception("invalid-new-name");

    

    $newName = $_POST["newName"];
    $newName = htmlspecialchars($newName);
    $catid = $_POST["catid"];
    $catid = filter_var($catid,FILTER_SANITIZE_NUMBER_INT);
    if(!filter_var($catid,FILTER_VALIDATE_INT))
        throw new Exception("invalid-catid");
    $q=$db->prepare("UPDATE categories SET name=? WHERE catid=?;");
    $q->bindParam(1,$newName);
    $q->bindParam(2,$catid);
    $q->execute();
    header("Location: admin_panel.php");
    exit();

    
    
}



function db_product_update(){
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid'];
    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    if (!preg_match('/^[\w+\s]+$/', $_POST['name']))
        throw new Exception("invalid-name");
    if (!preg_match('/^\d+\.?\d*$/', $_POST['price']))
        throw new Exception("invalid-price");
    if (!preg_match('/^[\w\s ]+$/', $_POST['description']))
        throw new Exception("invalid-text");




    if ($_FILES["image"]["error"] == 0
        && ($_FILES["image"]["type"] == "image/jpeg" || $_FILES["image"]["type"] == "image/jpg" || $_FILES["image"]["type"] == "image/png" || $_FILES["image"]["type"] == "image/gif")
        && (mime_content_type($_FILES["image"]["tmp_name"]) == "image/jpeg" || mime_content_type($_FILES["image"]["tmp_name"]) == "image/jpg" || mime_content_type($_FILES["image"]["tmp_name"]) == "image/png" || mime_content_type($_FILES["image"]["tmp_name"]) == "image/gif")
        && $_FILES["image"]["size"] <= 10485760) {
        

        
        $pid = $_POST["pid"];
        $pid = filter_var($pid,FILTER_SANITIZE_NUMBER_INT);
        if(!filter_var($pid,FILTER_VALIDATE_INT))
            throw new Exception("invalid-pid");
        $catid = $_POST["catid"];
        $catid = filter_var($catid,FILTER_SANITIZE_NUMBER_INT);
        if(!filter_var($catid,FILTER_VALIDATE_INT))
            throw new Exception("invalid-catid");

        $pq = $db->prepare("SELECT name FROM categories WHERE catid=?;");
        $pq->bindParam(1,$catid);
        $pq->execute();

        $category = $pq->fetchColumn();
        
        $name = $_POST["name"];
        $price = $_POST["price"];
        $desc = $_POST["description"];

        $name = htmlspecialchars($name);
        $desc = htmlspecialchars($desc);
        $price = filter_var($price,FILTER_SANITIZE_NUMBER_FLOAT);
        if(!filter_var($catid,FILTER_VALIDATE_FLOAT))
            throw new Exception("invalid-price");

        $sql="UPDATE products SET catid=?,name=?,price=?,description=? WHERE pid=?;";
        $q = $db->prepare($sql);
        $q->bindParam(1, $catid);
        $q->bindParam(2, $name);
        $q->bindParam(3, $price);
        $q->bindParam(4, $desc);
        $q->bindParam(5,$pid);
        $q->execute();
        $lastId = $db->lastInsertId();


        $insertName = str_replace(' ','',$name);
        preg_replace('/[^a-zA-Z\d]/','',$insertName);

        // Note: Take care of the permission of destination folder (hints: current user is apache)
        if($_FILES["image"]["type"] == "image/jpeg")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName  . ".jpeg")) {
                // redirect back to original page; you may comment it during debug
                echo "success";
                header('Location: admin_panel.php');
                exit();
            }
        
        else if($_FILES["image"]["type"] == "image/jpg")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName  . ".jpg")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin_panel.php');
                exit();
            }

        else if($_FILES["image"]["type"] == "image/png")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName  . ".png")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin_panel.php');
                exit();
            }

        else if($_FILES["image"]["type"] == "image/gif")
            if (move_uploaded_file($_FILES["image"]["tmp_name"], "/var/www/html/images/" . $category . "/".$insertName  . ".gif")) {
                // redirect back to original page; you may comment it during debug
                header('Location: admin_panel.php');
                exit();
            }
    }

    header('Content-Type: text/html; charset=utf-8');
    echo 'Invalid file detected. <br/><a href="javascript:history.back();">Back to admin panel.</a>';
    echo"wrong";
    exit();
}



function db_category_delete(){
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['catid']))
        throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];

    $catid = $_POST["catid"];
    $catid = filter_var($catid,FILTER_SANITIZE_NUMBER_INT);
    if(!filter_var($catid,FILTER_VALIDATE_INT))
        throw new Exception("invalid-catid");
    $q=$db->prepare("DELETE FROM categories WHERE catid=?;");
    $q->bindParam(1,$catid);
    $q->execute();
    header("Location: admin_panel.php");
    exit();

}

function db_product_delete(){
 
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid'];

    $pid = $_POST["pid"];
    $pid = filter_var($pid,FILTER_SANITIZE_NUMBER_INT);
    if(!filter_var($pid,FILTER_VALIDATE_INT))
        throw new Exception("invalid-pid");
    $q=$db->prepare("DELETE FROM products WHERE pid=?;");
    $q->bindParam(1,$pid);
    $q->execute();
    header("Location: admin_panel.php");
    exit();
}

function db_user_product()
{
    global $db;
    $db = inventory_db();
    $pid = $_POST["pid"];
    $_POST['pid'] = (int) $_POST['pid'];
    $pid = filter_var($pid,FILTER_SANITIZE_NUMBER_INT);
    
    error_log(print_r($pid,true));

    if(!filter_var($pid,FILTER_VALIDATE_INT))
        throw new Exception("invalid-pid-1");
    if (!preg_match('/^\d*$/', $_POST['pid']))
        throw new Exception("invalid-pid-2");
    
    $q = $db->prepare("SELECT name,price FROM products WHERE pid=?;");
    $q->bindParam(1,$pid);
    $q->execute();
    return $q->fetchAll();


}


function db_fetch_all_products_for_one_catid()
{
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['catid']))
    throw new Exception("invalid-catid");
    $_POST['catid'] = (int) $_POST['catid'];
    $catid = $_POST["catid"];

    $catid = filter_var($catid,FILTER_SANITIZE_NUMBER_INT);
    if(!filter_var($catid,FILTER_VALIDATE_INT))
        throw new Exception("invalid-catid");
    $q = $db->prepare("SELECT pid,catid,name,price,description FROM products WHERE catid=?;");
    $q->bindParam(1,$catid);
    $q->execute();
    return $q->fetchAll();

}

function db_fetch_detail_for_one_product()
{
    global $db;
    $db = inventory_db();

    if (!preg_match('/^\d*$/', $_POST['pid']))
    throw new Exception("invalid-pid");
    $_POST['pid'] = (int) $_POST['pid'];
    $pid = $_POST["pid"];
    $pid = filter_var($pid,FILTER_SANITIZE_NUMBER_INT);
    if(!filter_var($pid,FILTER_VALIDATE_INT))
        throw new Exception("invalid-pid");

    $q = $db->prepare("SELECT pid,catid,name,price,description FROM products WHERE pid=?;");
    $q->bindParam(1,$pid);
    $q->execute();
    return $q->fetchAll();
}



// This function is used to add new user to user table.
/*
function db_add_new_user()
{
    global $db;
    $db = inventory_db();
    $q = $db->prepare("SELECT max(userid) FROM users;");
    $q->execute();
    
    /*if( !($q->fetchColumn()))
    {
        $userid = 1;
    }
    else{
        $userid = $q->fetchColumn()  + 1;
    }*/
    /*
    $userid = 1;


    $email = $_POST['Email'];
    $salt = $_POST['Salt'];
    $password = hash_hmac('sha256',$_POST['Password'],$salt);
    $admin_flag = $_POST['Admin_flag'];


    $sql = "INSERT INTO users (userid,email,salt,password,admin_flag) VALUES (?,?,?,?,?);";
    $q=$db->prepare($sql);
    $q->bindParam(1,$userid);
    $q->bindParam(2,$email);
    $q->bindParam(3,$password);
    $q->bindParam(4,$admin_flag);
    $q->execute();
    header("Location: addNewUser.php");
    exit();



}
*/



function db_generateDigest()
{
    global $db;
    $db = inventory_db();
    /*$q = $db->prepare('SELECT * FROM categories;');
    
    
    if($q->execute())
    {
        $return = $q->fetchAll();
        $return[count($return)]['TestKey'] = "TestValue";
        return $return;
    }*/

    $data = [];
    $salt = bin2hex(random_bytes(32));
    $lastInsertId = $db->lastInsertId();
    $invoice = sprintf("%016d", $lastInsertId) . substr(bin2hex(random_bytes(32)), 0, 16);
    $totalPrice = 0;

    array_push(
        $data,
        'HKD',
        'adminForTest@cuhk.edu.hk',
        $salt
    );

    $index = $_POST['indexForData'];
    for($i=0;$i<$index;$i++)
    {
      $pidKey = 'pid_';
      strval($i);
      $pidKey .= $i;
      //error_log(print_r($pidKey,true));
      $pid = $_POST[$pidKey];
      error_log(print_r($pid,true));
      $quaKey = "qua_";
      $quaKey .= $i;
      $qua = $_POST[$quaKey];
      $q = $db->prepare("SELECT name FROM products WHERE pid=?;");
      $q->bindParam(1,$pid);
      $q->execute();
      //error_log(print_r($q->fetchAll(),true));
      //$result = $q->fetchAll();
      $name = $q->fetchAll()[0]['name'];
      //$price = $q->fetchAll()[0]['price'];

      $q = $db->prepare("SELECT price FROM products WHERE pid=?;");
      $q->bindParam(1,$pid);
      $q->execute();
      //error_log(print_r($q->fetchAll(),true));
      //$result = $q->fetchAll();
      $price = $q->fetchAll()[0]['price'];

      //error_log(print_r($q->fetchColumn(1),true));
      $totalPrice = $totalPrice + intval($qua) * floatval($price);

      array_push(
          $data,
          $name,
          $qua,
          $price
      );

    }
    array_push(
        $data,
        $totalPrice
    );
    $digest = hash("sha256", implode(";", $data));
    error_log(print_r($data,true));

    $userName = "Guest";
    if(!empty($_SESSION['auth']))
        $userName =  $_SESSION['auth']['em'];
    if(!empty($_COOKIE['auth']))
    {
        $t = json_decode(stripslashes($_COOKIE['auth']),true);
        $userName = $t['em'];

    }

    $sql = "INSERT INTO orders (invoice, digest, salt, username) VALUES (?,?,?,?);";
    $q = $db->prepare($sql);
    $q->bindParam(1,$invoice);
    $q->bindParam(2,$digest);
    $q->bindParam(3,$salt);
    $q->bindParam(4,$userName);
    $q->execute();

    $returnArray = array();
    $returnArray['custom_id'] = $lastInsertId;
    $returnArray['invoice_id'] = $invoice;

    return $returnArray;



}


// function check_txn_id($txn_id)
// {
//     global $db;
//     $db = inventory_db();
//     $q = $db->prepare('SELECT * FROM orders WHERE txn_id=?;');
//     $q->bindParam(1,$txn_id);
//     if($result = $q->execute())
//     {
//         if(!$result)
//         {
//             return true;
//         }
//         else
//             return false;
//     }
// }


function db_fetch_all_orders()
{
    global $db;
    $db = inventory_db();
    $q = $db->prepare('SELECT * FROM orders;');
    if($q->execute())
    {
        return $q->fetchAll();
    }
}

function db_order_display()
{
    //echo "Test for orders display";
    //return;

    $invoice = $_POST['invoice'];
    $invoice = strval($invoice);
    //return $invoice;
    global $db;
    $db = inventory_db();
    $q = $db->prepare("SELECT * FROM orders WHERE invoice=?;");
    $q->bindParam(1,$invoice);
    if($q->execute())
    {
        header(" Location: index.php");
        exit();
        //return $q->fetchAll();
    }
}


function showOrdersForUser($name)
{
    global $db;
    $db = inventory_db();
    $name = strval($name);
    $message = "Test from php 1 ";
    //$message .= $name;
    //return $message;
    if(strcmp("Guest",$name) == 0)
        return false;
    else
    {
        $q = $db->prepare("SELECT * FROM orders WHERE userName=? LIMIT 5;");
        $q->bindParam(1,$name);
        if($q->execute())
        {

            //return "Test from php";
            return $q->fetchAll();
        }
    }
    
}

?>