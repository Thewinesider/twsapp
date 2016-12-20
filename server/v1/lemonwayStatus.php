<?php
require_once 'dbHandler.php';
require_once 'lwConnect.php';
require_once 'JWT.php';
require_once '../lemonway/Autoloader.php';
require_once '../lemonway/Models/Operation.php';

//Money-in by card with 3D Secure, using Atos/BNP card form in indirect mode
if (isset($_POST) && sizeof($_POST) > 0){
    //notification from Lemon Way's server. Will not work if you're testing using a local return URL
    foreach ($_POST as $key => $value) {
        // Write to server error log for example purpose
        error_log('<br/>POST '.$key.' : '.$value.'');
    }
    if (isset($_POST['response_transactionId'])){
        //call GetMoneyInTransDetails to retrieve payment status, and proceed depending on result.
        $merchantToken = '';
        $moneyInID = $_POST['response_transactionId'];
        $isFromGET = false;
        include './finalize.php';
    }
} else if (isset ($_GET) && sizeof($_GET) > 0){
    //user browser is returning from payment
    if (isset($_GET['response_wkToken'])){
        //call GetMoneyInTransDetails to retrieve payment status, and proceed depending on result.
        $merchantToken = $_GET['response_wkToken'];
        $moneyInID = '';
        $isFromGET = true;
        include './finalize.php';
    }
}

?>