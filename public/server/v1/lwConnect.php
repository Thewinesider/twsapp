<?php
use LemonWay\LemonWayAPI;
use LemonWay\Models\Wallet;

class lwConnect {

    /**
     * DIRECTKIT_URL Used to set API DirectKit url
     * @var string
     */
    const DIRECTKIT_URL    = 'https://sandbox-api.lemonway.fr/mb/winesider/dev/directkitxml/service.asmx';
    //const DIRECTKIT_URL    = 'https://ws.lemonway.fr/mb/winesider/prod/directkitxml/service.asmx';

    /**
     * WEBKIT_URL Used to set API WebKit url
     * @var string
     */
    const WEBKIT_URL       = 'https://sandbox-webkit.lemonway.fr/winesider/dev/';
    //const WEBKIT_URL       = 'https://webkit.lemonway.fr/mb/winesider/prod/';

    /**
     * LOGIN Used to set API user login
     * @var string
     */
    const LOGIN            = 'society';
    //const LOGIN            = 'adminmb';

    /**
     * PASS Used to set API user password
     * @var string
     */
    const PASS             = '123456';
    //const PASS             = 'adminmb';

    const CSS             = 'https://www.lemonway.fr/mercanet_lw.css';

    /**
     * LANG Used to set API language
     * @var string
     */
    const LANG             = 'it';

    /**
     * DEBUG Used to switch API in debug mode
     * @var boolean
     */
    const DEBUG            = false;

    /**
     * api Lemon Way API
     * @var LemonWayAPI
     */
    public static $api;

    /**
     * Build the API if needed
     * @return LemonWayAPI
     */
    public static function getApiInstance(){
        if(self::$api == null){
            self::$api = new LemonWayAPI();

            self::$api->config->dkUrl = self::DIRECTKIT_URL;
            self::$api->config->wkUrl = self::WEBKIT_URL;
            self::$api->config->wlLogin = self::LOGIN;
            self::$api->config->wlPass = self::PASS;
            self::$api->config->css = self::CSS;
            self::$api->config->lang = self::LANG;
            self::$api->config->isDebugEnabled = self::DEBUG;
        }
        return self::$api;
    }

    /**
    *   Get error
    *
    */
    public static function errorException($code) {
        $error = "";
        switch ($code) {
            case 152:
                $error = "Il Wallet esiste già.";
                break;
            case 153:
                $error = "Il Wallet o l'IBAN esistono già.";
                break;
            case 221:
                $error = "Formato IBAN non valido.";
                break;
            case 242:
                $error = "Formato SWIFT/BIC non valido.";
                break;
            case 253:
                $error = "Formato telefono non valido.";
                break;
            case 204:
                $error = "La mail è già associata ad un Wallet.";
                break;
            case 147: 
                $error = "Il wallet non esiste.";
                break;
            case 110: 
                $error = "Credito insufficiente.";
                break;
        }
        return $error;
    }

}

?>
