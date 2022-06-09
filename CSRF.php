<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    function csrf_getNonce($action)
    {
        //Generate a nonce
        $nonce = mt_rand() . mt_rand();

        // Save the nonce to seesion
        if(!isset($_SESSION['csrf_nonce']))
            $_SESSION['csrf_nonce'] = array();
        $_SESSION['csrf_nonce'][$action] = $nonce;

        return $nonce;

    }

    // Check the nonce of the form
    function csrf_verifyNonce($action,$receivedNonce)
    {
        // assume the $_REQUEST['action'] already validated
        if(isset($receivedNonce) && $_SESSION['csrf_nonce'][$action] == $receivedNonce)
        {
            if($_SESSION['auth']==null)
            {
                unset($_SESSION['csrf_nonce'][$action]);
            }
            return true;
        }
        throw new Exception('csrf-attack');
    }
?>