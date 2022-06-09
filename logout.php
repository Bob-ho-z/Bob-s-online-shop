<?php
    function logout()
    {
        setcookie("auth","",time()-3600);
        session_destroy();
        session_write_close();

        header("Location: login.php");
        exit();
    }
?>