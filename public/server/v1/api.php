<?php
/* 
    PWD caminetti: $2a$10$3bb415d3ff88550530a75OKhOiE4pSxeWMzlks4UXOpgUJ5ZC26t.
*/
/* Set a new session */
$app->get('/session', function () {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    $response["role"] = $session['role'];
    echoResponse(200, $session);
});

/* Get the user list from the DB */
$app->post('/getUserList', function() use ($app) {
    $db = new DbHandler();
    $user = json_decode($app->request->getBody());
    $user = $db->getRecord("SELECT * FROM users WHERE role LIKE '". $user->role ."'");
    echoResponse(200, $user);
});

$app->post('/customer', function () use ($app) {
    $response = array();
    $db = new DbHandler();
    $session = $db->getSession();
    $cookieLifetime = 365 * 24 * 60 * 60;
    //get the data
    $r = json_decode($app->request->getBody());
    //check if the customer already exist 
    $userExist = $db->getOneRecord("SELECT 1 FROM customers WHERE email = '" . $r->customer->email ."'");
    if ($userExist != null) {
        $response["status"] = "error";
        $response["message"] =  "L'utente associato alla mail è già presente.";
        echoResponse(201, $response);
    } else {
        //register the user on TWS
        $column_names = array('business_name','vat_number','tax_code','email','pec','legal_address','legal_city','legal_cap','attorney_name','attorney_surname','attorney_taxcode','attorney_phone_number','shipping_address','shipping_city','shipping_cap','shipping_comment','shipping_hour','associated_to');
        $result = $db->insertIntoTable($r->customer, $column_names, 'customers');
        if ($result != NULL) {
            //update the cookie
            setcookie("associated_to", $session['uid'], time()+$cookieLifetime);
            $response["status"] = "success";
            $response["message"] = "Informazioni registrate correttamente";
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Si è verificato un problema nella registrazione. Per favore, riprovare.";
            echoResponse(201, $response);
        }  
    }
});

$app->post('/registerSDD', function () use ($app) {
    $lemonway = lwConnect::getApiInstance();
    $response = array();
    $db = new DbHandler();
    $session = $db->getSession();

    //get the data
    $r = json_decode($app->request->getBody());
    $cookieLifetime = 365 * 24 * 60 * 60;
    //check if the user already exist 
    $customer = $db->getOneRecord("SELECT * FROM customers WHERE associated_to = '" . $session["uid"] ."'");
    if ($customer == null) {
        $code = 201;
        $response["status"] = "error";
        $response["message"] =  "L'utente non è presente nel DB.";
        echoResponse($code, $response);
    } else { 
        $error_log = "";     
        //Register a new wallet on Lemonway
        $walletExist = $lemonway->GetWalletDetails(array('wallet'=>$customer["id"]));
        //chek if the wallet is already set
        if (isset($walletExist->lwError)) {
            //Create the lemonway Wallet
            $lmw_wallet_response = $lemonway->RegisterWallet(array('wallet' => $customer["id"],
                                                                   'clientMail' => $customer["email"],
                                                                   'clientFirstName' => $customer["attorney_name"],
                                                                   'clientLastName' => $customer["attorney_surname"],
                                                                   'phoneNumber' => $customer["attorney_phone_number"],
                                                                   'street' => $customer["legal_address"],
                                                                   'postCode' => $customer["legal_cap"],
                                                                   'city' => $customer["legal_city"],
                                                                   'companyName' => $customer["business_name"]));
            //check if an error occured or if the wallet is already set on Lemonway
            if(isset($lmw_wallet_response->lwError) && $lmw_wallet_response->lwError->CODE != 152){
                $error = lwConnect::errorException($lmw_wallet_response->lwError->CODE);
                $error_log .=  "<br>" . $lmw_wallet_response->lwError->CODE .": ".$error;
            }
        }

        //add a new IBAN to the wallet
        $lmw_iban_response = $lemonway->RegisterIBAN(array('wallet'=> $customer["id"],
                                                           'holder' => $r->lemonway->bank_holder,
                                                           'bic' => $r->lemonway->bank_bic,
                                                           'iban' => $r->lemonway->bank_iban,
                                                           'dom1' => $r->lemonway->bank_name,
                                                           'dom2' => $r->lemonway->bank_address));

        if(isset($lmw_iban_response->lwError)){
            $error = lwConnect::errorException($lmw_iban_response->lwError->CODE);
            $error_log .= "<br>" . $lmw_iban_response->lwError->CODE .": ".$error;
        }


        if ($error_log == "") {
            $code = 200;
            $response["status"] = "success";
            $response["message"] = "Utente creato correttamente";  
            //updating the payment type
            $db->execQuery("UPDATE customers SET payment_type = 2 WHERE associated_to = " . $session["uid"]);
            ob_start();
            setcookie("payment_is_set", 2, time()+$cookieLifetime);
        } else {
            $code = 201;
            $response["status"] = "error";
            $response["message"] = "Si è verificato un errore. Utente non creato. " . $error_log;
        }   
    }
    echoResponse($code, $response);
});

$app->post('/registerCC', function () use ($app) {
    $lemonway = lwConnect::getApiInstance();
    $response = array();
    $db = new DbHandler();
    $session = $db->getSession();

    //get the data
    $r = json_decode($app->request->getBody());

    //check if the user already exist 
    $customer = $db->getOneRecord("SELECT * FROM customers WHERE associated_to = '" . $session["uid"] ."'");

    if ($customer == null) {
        $code = 201;
        $response["status"] = "error";
        $response["message"] =  "L'utente non è presente nel DB.";
        echoResponse($code, $response);
    } else { 
        $error_log = '';
        //create a token to pass through lemonway
        $token = JWT::encode($customer["id"], 'secret_server_key');
        $walletExist = $lemonway->GetWalletDetails(array('wallet'=>$customer["id"]));
        //chek if the wallet is already set
        if (isset($walletExist->lwError)) {
            //Create the lemonway Wallet
            $lmw_wallet_response = $lemonway->RegisterWallet(array('wallet' => $customer["id"],
                                                                   'clientMail' => $customer["email"],
                                                                   'clientFirstName' => $customer["attorney_name"],
                                                                   'clientLastName' => $customer["attorney_surname"],
                                                                   'phoneNumber' => $customer["attorney_phone_number"],
                                                                   'street' => $customer["legal_address"],
                                                                   'postCode' => $customer["legal_cap"],
                                                                   'city' => $customer["legal_city"],
                                                                   'companyName' => $customer["business_name"]));

            if (isset($lmw_wallet_response->lwError)){
                $error = lwConnect::errorException($lmw_wallet_response->lwError->CODE);
                $error_log .=  "<br>" . $lmw_wallet_response->lwError->CODE .": ".$error;
            }

        }

        //Register the cc on Lemonway
        $lmw_cc_response = $lemonway->MoneyInWebInit(array('wallet' => $customer["id"],
                                                           'amountTot' => '1.00',
                                                           'wkToken' => $token,
                                                           'comment'=>'Registrazione su The Winesider',
                                                           'returnUrl'=>"server/v1/lemonwayStatus.php",
                                                           'cancelUrl'=>"server/v1/lemonwayStatus.php",
                                                           'errorUrl'=>"server/v1/lemonwayStatus.php",
                                                           'registerCard' => '1'));
        if(isset($lmw_cc_response->lwError)){
            $error = lwConnect::errorException($lmw_cc_response->lwError->CODE);
            $error_log .=  "<br>" . $lmw_cc_response->lwError->CODE .": ".$error;
        }

        if ($error_log == "") {
            $code = 200;
            $response["status"] = "success";
            $response["message"] = "Utente creato correttamente";
            $response["url"] = $lemonway->config->wkUrl . "?moneyintoken=" . $lmw_cc_response->lwXml->MONEYINWEB->TOKEN . '&p=' . $lemonway->config->css . '&lang=' . $lemonway->config->lang;
            echoResponse($code, $response);
        } else {
            $code = 201;
            $response["status"] = "error";
            $response["message"] = "Si è verificato un errore. " . $error_log;
            echoResponse($code, $response);
        }   
    }
});

/* Get the logged user winelist*/
$app->get('/wineList', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $wines= $db->getRecord("SELECT winelist.sku, winelist.type, winelist.name, winelist.price, winelist.region, winelist.price, winelist.suggested_price  FROM winelist WHERE id_user = ". $session['uid']);
    echoResponse(200, $wines);
});

/* Get the full catalog from the DB */
$app->get('/catalog', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["wines"]= $db->getRecord("SELECT * FROM catalog WHERE catalog_in = 1");
    $response["producers"]= $db->getRecord("SELECT DISTINCT producer FROM catalog WHERE catalog_in = 1");
    $response["regions"] = $db->getRecord("SELECT DISTINCT region FROM catalog WHERE catalog_in = 1");
    echoResponse(200, $response);
});

/* Get the full list of wine sold in a certain period */
$app->get('/statistics', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $periodStart = $app->request->get("periodStart");
    $periodEnd = $app->request->get("periodEnd");
    $uid = $app->request->get("uid");
    $q = "";
    $qJoin = "";
    if($uid == null && $session['role'] == 'customer') { // if uid not set get the logged user statistics
        $q = " AND winesold.id_winelist = " . $session['uid'] . " AND winelist.id_user = " . $session['uid'] . "";
        $qJoin = " AND ws.id_winelist = " . $session['uid'] . " ";
    }elseif($uid == null && $session['role'] == 'admin'){ // if uid not set and the user is 'admin' get all stat
        $q = " AND winesold.id_winelist = winelist.id_user AND winesold.id_winelist <> 1";
        $qJoin = " AND ws.id_winelist != 1 ";
    }else{//else get a specific user
        $q = " AND winesold.id_winelist = " . $uid . " AND winelist.id_user = " . $uid . "";
        $qJoin = " AND ws.id_winelist = " . $uid . " ";
    }

    $query = "SELECT catalog.type AS type, winesold.sku, winelist.name, SUM(winesold.value) as sold, (SUM(winesold.value)*winelist.price) as total_revenues, (SUM(winesold.value)*winelist.suggested_price) as total_restaurants, DATE(winesold.date) as date, HOUR(winesold.date) as hour FROM winesold, winelist, catalog WHERE winelist.sku = winesold.sku". $q. " AND catalog.SKU = winelist.SKU AND winesold.date >= '". $periodStart ."' AND winesold.date <= '". $periodEnd ."' GROUP BY winesold.sku ORDER BY sold DESC";

    $response["wines"] = $db->getRecord($query);

    $query = "SELECT DATE_FORMAT(cl.datefield, '%e %b') as days, IFNULL(SUM(ws.value),0) AS total_sales, IFNULL((SUM(ws.value)*wl.price),0) AS total_revenues, IFNULL((SUM(ws.value)*wl.suggested_price),0) AS total_restaurants FROM winesold ws 
    INNER JOIN winelist wl ON ws.sku = wl.sku AND wl.id_user = ws.id_winelist 
    RIGHT JOIN calendar cl  ON (DATE(ws.date) = cl.datefield) " . $qJoin . " 
    WHERE cl.datefield BETWEEN (DATE('". $periodStart ."')) AND (DATE('". $periodEnd ."')) 
    GROUP BY DATE(cl.datefield) 
    ORDER BY DATE(cl.datefield) ASC";

    $response["data"] = $db->getRecord($query);

    $query = "SELECT SUM(winesold.value) as sold, catalog.type as type FROM winesold, winelist, catalog WHERE winelist.sku = winesold.sku AND catalog.SKU = winelist.SKU". $q. "  AND winesold.date >= '". $periodStart ."' AND winesold.date <= '". $periodEnd ."' GROUP BY catalog.type";
    $response["wineType"] = $db->getRecord($query);

    $query = "SELECT SUM(winesold.value) as sold, (SUM(winesold.value)*winelist.price) as total_revenues, (SUM(winesold.value)*winelist.suggested_price) as total_restaurants
    FROM winesold, winelist WHERE winelist.sku = winesold.sku". $q. " AND winesold.date >= '". $periodStart ."' AND winesold.date <= '". $periodEnd ."'";
    $response["totals"] = $db->getRecord($query);    

    echoResponse(200, $response);
});   

/* Get the full list of wine sold in a certain period */
$app->get('/wineSold', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $periodStart = $app->request->get("periodStart");
    $periodEnd = $app->request->get("periodEnd");
    $uid = $app->request->get("uid");  
    $q = "";
    if($uid != null) { //if 0 get all the records
        $q = " AND winesold.id_winelist = " . $uid . " AND winelist.id_user = " . $uid . "";
    }else{
        $q = " AND winesold.id_winelist = winelist.id_user AND winesold.id_winelist <> 1";
    }

    $query = "SELECT catalog.type AS type, winesold.sku AS sku, winelist.name AS name, SUM(winesold.value) AS sold, (SUM(winesold.value)*winelist.price) AS total_revenues, (SUM(winesold.value)*winelist.suggested_price) AS total_restaurants, DATE(winesold.date) AS date, HOUR(winesold.date) AS hour FROM winesold, winelist, catalog WHERE winelist.sku = winesold.sku". $q. " AND winesold.date >= '". $periodStart ."' AND winesold.date <= '". $periodEnd ."' AND catalog.sku = winesold.sku GROUP BY winesold.sku ORDER BY sold DESC";
    $response["wines"] = $db->getRecord($query);

    echoResponse(200, $response);
});    

/* Download a specific wine with a specific quantity */
$app->post('/downloadWine', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $post =  json_decode($app->request()->getBody());
    $now = date("Y-m-d H:i:s", strtotime("now"));
    $query= "INSERT INTO winesold (date,value,id_winelist,sku) VALUES ('" . $now . "', " . $post->value . ", " . $session['uid'] . ", '" . $post->sku . "')";
    $wines = $db->execQuery($query);
    echoResponse(200, $wines);
});

/* LOGIN */
$app->post('/login', function() use ($app) {
    require_once 'passwordHash.php';
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'password'),$r->customer);
    $response = array();
    $db = new DbHandler();
    $cookieLifetime = 365 * 24 * 60 * 60; //a year in second
    $password = $r->customer->password;
    $email = $r->customer->email;
    $user = $db->getOneRecord("SELECT users.uid,users.name,users.password,users.email,users.created,users.role FROM users WHERE users.email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
            //get the customer related to the user
            $customer = $db->getOneRecord("SELECT customers.associated_to, customers.payment_type FROM users, customers WHERE users.uid = customers.associated_to AND users.uid = ".$user['uid']);
            if($customer == null) {
                $customer['associated_to'] = 0;
                $customer['payment_type'] = 0;
            }
            $response['status'] = "success";
            $response['message'] = 'Benvenuto in The Winesider.';
            $response['uid'] = $user['uid'];
            $response['role'] = $user['role'];
            $response['associated_to'] = $customer['associated_to']; 
            $response['payment_is_set'] = $customer['payment_type']; 
            if (!isset($_COOKIE)) {
                session_start();
            }
            setcookie("uid", $user['uid'], time()+$cookieLifetime);
            setcookie("email", $user['email'], time()+$cookieLifetime);
            setcookie("name", $user['name'], time()+$cookieLifetime);
            setcookie("role", $user['role'], time()+$cookieLifetime);
            setcookie("associated_to", $customer['associated_to'], time()+$cookieLifetime);
            setcookie("payment_is_set", $customer['payment_type'], time()+$cookieLifetime);
        } else {
            $response['status'] = "error";
            $response['message'] = 'Attenzione, credenziali sbagliate.';
        }
    }else {
        $response['status'] = "error";
        $response['message'] = 'Attenzione, non esiste nessun utente con questa mail.';
    }
    echoResponse(200, $response);
});

/* SignUp a new user */
$app->post('/signUp', function() use ($app) {
    $response = array();
    $r = json_decode($app->request->getBody());
    verifyRequiredParams(array('email', 'name', 'password'),$r->customer);
    require_once 'passwordHash.php';
    $db = new DbHandler();
    $name = $r->customer->name;
    $surname = $r->customer->surname;
    $email = $r->customer->email;
    $password = $r->customer->password;
    $cookieLifetime = 365 * 24 * 60 * 60; //a year in second
    $isUserExists = $db->getOneRecord("SELECT 1 FROM users WHERE email='$email'");
    if(!$isUserExists){
        $r->customer->password = passwordHash::hash($password);
        $tabble_name = "users";
        $column_names = array('name','surname','email','password');
        $result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "Utente creato correttamente";
            $response["uid"] = $result;
            session_start();
            setcookie("uid", $result, time()+$cookieLifetime);
            setcookie("email", $email, time()+$cookieLifetime);
            setcookie("name", $name, time()+$cookieLifetime);
            setcookie("role", 'customer', time()+$cookieLifetime);
            setcookie("associated_to", 0, time()+$cookieLifetime);
            setcookie("payment_is_set", 0, time()+$cookieLifetime);
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Si è verificato un problema. Per favore, riprovare.";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "La mail è già presente.";
        echoResponse(201, $response);
    }
});

/* Logout */
$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = $session;
    echoResponse(200, $response);
});
?>