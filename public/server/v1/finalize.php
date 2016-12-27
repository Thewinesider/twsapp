<?php
header( "refresh:5;url=../../" );
use LemonWay\Models\Operation;

$lemonway = lwConnect::getApiInstance();

/**
 *		Case : This follows the MoneyInWebInit case : your customer has returned to your website or you have received a POST notification from Lemon Way. You now need to find out how the payment went.
 *		Steps :
 *			- GetMoneyInTransDetails
 *		Note :
 *			- In this example, we print data, but keep in mind that if you arrive here from the Lemon Way notification (with POST data), printing will be pointless. You can log data in a file instead.
 *			- You have defined 3 return urls (success, error, cancel), but it is still recommended to make this call in order to verify the information and to make sure the POST or GET data were not maliciously modified.
 *			- If the status is SUCCESS (3) or ERROR (4), then it's a final status, it won't change.
 *			- If the status is still PENDING (0), then the status can still change : it means your customer has cancelled the payment or returned to your website, or something else that made them arrive on one of your return urls, without finishing their payment. The customer will still be able to go back to the payment form using the browser's back button, and make the payment. You should either :
 *				- just like Lemon Way, not change the payment status and still give your customer the opportunity to pay
 *				- decide to mark the payment as failed on your side, but keep in mind that this won't prevent Lemon Way from accepting the payment if the customer pays.
 */

$res = $lemonway->GetMoneyInTransDetails(array('transactionId'=>$moneyInID,
    'transactionComment'=>'',
    'transactionMerchantToken'=>$merchantToken));
if (isset($res->lwError)){
    print '<br/>Error, code '.$res->lwError->CODE.' : '.$res->lwError->MSG;
    return;
}
if (count($res->operations) != 1){
    print '<br/>Error, too many results : '.count($res->operations);
    //TODO : error to handle. Check if your merchant transactionMerchantToken is unique
    return;
} else {
    if ((string)$res->operations[0]->STATUS == Operation::STATUS_SUCCES){
        ob_start();
        $db = new DbHandler();
        $cookieLifetime = 365 * 24 * 60 * 60;
        $db->execQuery("UPDATE customers SET payment_type = 1 WHERE associated_to = " . $_COOKIE['uid']);
        setcookie("payment_is_set", 1, time() + $cookieLifetime);
?>
        <div style="text-align: center; font-size: 30px; border: 1px solid #eee; width: 1024px; margin:0 auto">
            Tutto ok! Hai completato la registrazione. Verrai reindirizzato su The Winesider entro <strong>5 secondi</strong>.
        </div>
<?php
        /* TODO: examples of things to do :
            -if $isFromGET = true, display a payment successful message
            -mark the payment as successful in your database
            -send a confirmation email if it wasn't already sent
        */
    } elseif ((string)$res->operations[0]->STATUS == Operation::STATUS_ERROR){
?>
        <div style="text-align: center; font-size: 30px; border: 1px solid #eee; width: 1024px; margin:0 auto">
            Non è stato possibile concludere l'operazione, devi inserire nuovamente i dati. Verrai reindirizzato su The Winesider entro <strong>5 secondi</strong>.
        </div>
<?php
        /* TODO: examples of things to do :
            -if $isFromGET = true, display a payment failed message
            -mark the payment as failed in your database
        */
    } elseif ((string)$res->operations[0]->STATUS == Operation::STATUS_WAITING_FINALISATION){
?>
        <div style="text-align: center; font-size: 30px; border: 1px solid #eee; width: 1024px; margin:0 auto">
            Hai annullato l'operazione, i dati non sono stati registrati. Dovrei inserirli nuovamente. Verrai reindirizzato su The Winesider entro <strong>5 secondi</strong>.
        </div>
<?php
        /* TODO: examples of things to do :
            -if $isFromGET = true, display a payment pending message. It is possible that the customer goes back to the Atos card payment form and decides to pay
        */
    }
}

?>