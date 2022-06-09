<?php
    $salt = openssl_random_pseudo_bytes(16);
    echo "salt is ";
    echo $salt;
    echo"<br/>";
    $password = "normal";
    echo "before password is ";
    echo $password;
    echo "<br/>";
    $hashedPassword = hash_hmac('sha256', $password,$salt);
    echo "passwod is ";
    echo  $hashedPassword;
    echo "<br/>";

?>