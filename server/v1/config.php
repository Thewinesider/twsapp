<?php

$path = "prod";

if($path=="local") {
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'root');
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'winespy');
}elseif($path=="staging"){
    define('DB_USERNAME', 'bcfdeeef602154');
    define('DB_PASSWORD', 'ceee67d0');
    define('DB_HOST', 'eu-cdbr-west-01.cleardb.com');
    define('DB_NAME', 'heroku_98351473a97f46b');
}elseif($path=="prod"){
    define('DB_USERNAME', 'admin_tws');
    define('DB_PASSWORD', 'Gaib!lre1098');
    define('DB_HOST', '93.186.252.54:8443');
    define('DB_NAME', 'winespy');
}else{
    define('DB_USERNAME', 'bb09e944f961bf');
    define('DB_PASSWORD', 'cb04c6ff');
    define('DB_HOST', 'eu-cdbr-west-01.cleardb.com');
    define('DB_NAME', 'heroku_941cbffbc91d114');
}


?>

