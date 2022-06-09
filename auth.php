<?php


    /*function inventory_db(){
        $db = new PDO('sqlite:../Inventory.db');
        $db->query('PRAGMA foreign_keys = ON;');
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    }*/

    function auth(){
        if(!empty($_SESSION['auth']))
            return $_SESSION['auth']['em'];
        if(!empty($_COOKIE['auth'])){
            //echo $_COOKIE['s4210'];
            if($t = json_decode(stripslashes($_COOKIE['auth']),true)){
                if(time() > $t['exp'])
                    return false; // to expire the user
                global $db;
                $db = inventory_db();
                $q = $db->prepare('SELECT * FROM users WHERE email = ?;');
                $q->bindParam(1,$t['em']);
                $q->execute();
                if($r=$q->fetchAll()){
                    $realk=hash_hmac('sha256',$t['exp'].$r[0]['password'],$r[0]['salt']);
                    if($realk == $t['k']) {
                        $_SESSION['auth'] = $t;
                        return $t['em'];
                    }
                }
            }
        }
        return false;
    }
?>
