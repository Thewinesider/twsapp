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

$app->post('/customerSDD', function () use ($app) {
    $lemonway = lwConnect::getApiInstance();
    //$token = JWT::encode($walletID, 'secret_server_key');
    $response = array();
    $db = new DbHandler();

    //get the data
    $r = json_decode($app->request->getBody());
    $business_name = $r->customer->business_name;
    $vat_number = $r->customer->vat_number;
    $tax_code = $r->customer->tax_code;
    $email = $r->customer->email;
    $pec = $r->customer->pec;
    $legal_address = $r->customer->legal_address;
    $legal_city = $r->customer->legal_city;
    $legal_cap = $r->customer->legal_cap;
    $attorney_name = $r->customer->attorney_name;
    $attorney_surname = $r->customer->attorney_surname;
    $attorney_taxcode = $r->customer->attorney_taxcode;
    if(!is_null($r->customer->attorney_phone_number)) {
        $attorney_phone_number = $r->customer->attorney_phone_number;
    }else{
        $attorney_phone_number = '';
    }
    $shipping_address = $r->customer->shipping_address;
    $shipping_city = $r->customer->shipping_city;
    $shipping_cap = $r->customer->shipping_cap;
    $shipping_day = $r->customer->shipping_day;
    $shipping_hour = $r->customer->shipping_hour;
    $payment_type = $r->customer->payment_type;
    $bank_holder = $r->customer->bank_holder;
    $bank_iban = $r->customer->bank_iban;
    $bank_bic = $r->customer->bank_bic;
    $bank_name = $r->customer->bank_name;
    $bank_address = $r->customer->bank_address;
    //check if the user already exist 
    $userExist = $db->getOneRecord("SELECT 1 FROM customer WHERE email = '" . $email ."'");
    if ($userExist != null) {
        $code = 201;
        $response["status"] = "error";
        $response["message"] =  "L'utente associato alla mail è già presente.";
    } else { 
        //register a new user
        //get the last ID and assign it as a new external WallletID in Lemonway
        $error_log = "";
        $walletID = $db->getOneRecord("SELECT MAX(ID) as ID FROM customer");
        $walletID = $walletID["ID"]+1;

        //register the user on TWS
        $column_names = array('business_name','vat_number','tax_code','email','pec','legal_address','legal_city','legal_cap','attorney_name','attorney_surname','attorney_taxcode','attorney_phone_number','shipping_address','shipping_city','shipping_cap','shipping_day','shipping_hour','payment_type','bank_holder','bank_iban','bank_bic');
        $result = $db->insertIntoTable($r->customer, $column_names, 'customer');         
        //register a new wallet on Lemonway
        $lmw_wallet_response = $lemonway->RegisterWallet(array('wallet' => $walletID,
                                                               'clientMail' => $email,
                                                               'clientFirstName' => $attorney_name,
                                                               'clientLastName' => $attorney_surname,
                                                               'phoneNumber' => $attorney_phone_number,
                                                               'street' => $legal_address,
                                                               'postCode' => $legal_cap,
                                                               'city' => $legal_city,
                                                               'companyName' => $business_name));
        error_log("qui arrivo");
        if(isset($lmw_wallet_response->lwError)){
            $error = lwConnect::errorException($lmw_wallet_response->lwError->CODE);
            $error_log .=  "<br>" . $lmw_wallet_response->lwError->CODE .": ".$error;
        }

        //add a new IBAN to the wallet
        $lmw_iban_response = $lemonway->RegisterIBAN(array('wallet'=>$walletID,
                                                           'holder' => $bank_holder,
                                                           'bic' => $bank_bic,
                                                           'iban' => $bank_iban,
                                                           'dom1' => $bank_name,
                                                           'dom2' => $bank_address));

        if(isset($lmw_iban_response->lwError)){
            $error = lwConnect::errorException($lmw_iban_response->lwError->CODE);
            $error_log .= "<br>" . $lmw_iban_response->lwError->CODE .": ".$error;
        }

        if ($error_log == "") {
            $code = 200;
            $response["status"] = "success";
            $response["message"] = "Utente creato correttamente";            
        } else {
            $code = 201;
            $response["status"] = "error";
            $response["message"] = "Si è verificato un errore. Utente non creato." . $error_log;
            //reverse the user
            $db->execQuery("DELETE FROM customer WHERE id = " . $walletID);
        }   
    }
    echoResponse($code, $response);


    /*$response2 = $lemonway->MoneyInWebInit(array('wallet' => $walletID,
                                                 'wkToken' => $token,
                                                 'returnUrl' => 'http://www.thewinesider.com/app/#/addcustomer',
                                                 'registerCard' => '1',
                                                 'amountTot' => '1.00'));
    //echoResponse(200, $response2);
    $lemonway->printCardForm($response2->lwXml->MONEYINWEB->TOKEN,'https://www.lemonway.fr/mercanet_lw.css','it');*/
});

$app->post('/customerCC', function () use ($app) {
    $lemonway = lwConnect::getApiInstance();
    $response = array();
    $db = new DbHandler();

    //get the data
    $r = json_decode($app->request->getBody());
    $business_name = $r->customer->business_name;
    $vat_number = $r->customer->vat_number;
    $tax_code = $r->customer->tax_code;
    $email = $r->customer->email;
    $pec = $r->customer->pec;
    $legal_address = $r->customer->legal_address;
    $legal_city = $r->customer->legal_city;
    $legal_cap = $r->customer->legal_cap;
    $attorney_name = $r->customer->attorney_name;
    $attorney_surname = $r->customer->attorney_surname;
    $attorney_taxcode = $r->customer->attorney_taxcode;
    if(isset($r->customer->attorney_phone_number)) {
        $attorney_phone_number = $r->customer->attorney_phone_number;
    }else{
        $attorney_phone_number = '';
    }
    $shipping_address = $r->customer->shipping_address;
    $shipping_city = $r->customer->shipping_city;
    $shipping_cap = $r->customer->shipping_cap;
    $shipping_day = $r->customer->shipping_day;
    $shipping_hour = $r->customer->shipping_hour;
    $payment_type = $r->customer->payment_type;
    if(isset($r->customer->bank_holder)) {
        $bank_holder = $r->customer->bank_holder;
        $bank_iban = $r->customer->bank_iban;
        $bank_bic = $r->customer->bank_bic;
        $bank_name = $r->customer->bank_name;
        $bank_address = $r->customer->bank_address;
    }
    
    //check if the user already exist 
    $userExist = $db->getOneRecord("SELECT 1 FROM customer WHERE email = '" . $email ."'");

    if ($userExist != null) {
        $code = 201;
        $response["status"] = "error";
        $response["message"] =  "L'utente associato alla mail è già presente.";
        echoResponse($code, $response);
    } else { 
        //register a new user
        //get the last ID and assign it as a new external WallletID in Lemonway
        $error_log = "";
        $walletID = $db->getOneRecord("SELECT MAX(ID) as ID FROM customer");
        $walletID = $walletID["ID"]+1;
        //create a token to pass through lemonway
        $token = JWT::encode($walletID, 'secret_server_key');
        //register the user on TWS
        $column_names = array('business_name','vat_number','tax_code','email','pec','legal_address','legal_city','legal_cap','attorney_name','attorney_surname','attorney_taxcode','attorney_phone_number','shipping_address','shipping_city','shipping_cap','shipping_day','shipping_hour','payment_type','bank_holder','bank_iban','bank_bic');
        $result = $db->insertIntoTable($r->customer, $column_names, 'customer');         
        if($result == null) {
            $error_log .=  "Utente non creato";
        }
        //register a new wallet on Lemonway*/
        $token = JWT::encode($walletID, 'secret_server_key');
        $lmw_wallet_response = $lemonway->RegisterWallet(array('wallet' => $walletID,
                                                               'clientMail' => $email,
                                                               'clientFirstName' => $attorney_name,
                                                               'clientLastName' => $attorney_surname,
                                                               'phoneNumber' => $attorney_phone_number,
                                                               'street' => $legal_address,
                                                               'postCode' => $legal_cap,
                                                               'city' => $legal_city,
                                                               'companyName' => $business_name));
        
        if(isset($lmw_wallet_response->lwError)){
            $error = lwConnect::errorException($lmw_wallet_response->lwError->CODE);
            $error_log .=  "<br>" . $lmw_wallet_response->lwError->CODE .": ".$error;
        }

        //register the cc on Lemonway
        $lmw_cc_response = $lemonway->MoneyInWebInit(array('wallet' => $walletID,
                                                           'amountTot' => '1.00',
                                                           'wkToken' => $token,
                                                           'comment'=>'Card register',
                                                           'returnUrl'=>"http://localhost:8888/twsapp/server/v1/lemonwayStatus.php",
                                                           'cancelUrl'=>"http://localhost:8888/twsapp/server/v1/lemonwayStatus.php",
                                                           'errorUrl'=>"http://localhost:8888/twsapp/server/v1/lemonwayStatus.php",
                                                           'registerCard' => '1'));
        if(isset($lmw_cc_response->lwError)){
            $error = lwConnect::errorException($lmw_wallet_response->lwError->CODE);
            $error_log .=  "<br>" . $lmw_wallet_response->lwError->CODE .": ".$error;
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
            //reverse the user
            $db->execQuery("DELETE FROM customer WHERE id = " . $walletID);
            echoResponse($code, $response);
        }   
    }
});

/* Get the logged user winelist*/
$app->get('/wineList', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $wines= $db->getRecord("SELECT winelist.sku, catalog.type, winelist.name, catalog.description, catalog.producer, catalog.alcohol, catalog.region  FROM winelist,catalog WHERE id_user = ". $session['uid'] . " AND catalog.sku = winelist.sku");
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
    if($uid != null) { //if 0 get all the records
        $q = " AND winesold.id_winelist = " . $uid . " AND winelist.id_user = " . $uid . "";
        $qJoin = " AND ws.id_winelist = " . $uid . " ";
    }else{
        $q = " AND winesold.id_winelist = winelist.id_user AND winesold.id_winelist <> 1";
        $qJoin = " AND ws.id_winelist != 1 ";
    }

    $query = "SELECT winesold.sku, winelist.name, SUM(winesold.value) as sold, (SUM(winesold.value)*winelist.price) as total_revenues, (SUM(winesold.value)*winelist.suggested_price) as total_restaurants, DATE(winesold.date) as date, HOUR(winesold.date) as hour FROM winesold, winelist WHERE winelist.sku = winesold.sku". $q. " AND winesold.date >= '". $periodStart ."' AND winesold.date <= '". $periodEnd ."' GROUP BY winesold.sku ORDER BY sold DESC";
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
    $password = $r->customer->password;
    $email = $r->customer->email;
    $user = $db->getOneRecord("SELECT uid,name,password,email,created,role FROM users WHERE email='$email'");
    if ($user != NULL) {
        if(passwordHash::check_password($user['password'],$password)){
            $response['status'] = "success";
            $response['message'] = '<strong>Welcome to The Winesider</strong><br>Logged in succesfully.';
            $response['name'] = $user['name'];
            $response['uid'] = $user['uid'];
            $response['email'] = $user['email'];
            $response['createdAt'] = $user['created'];
            $response['role'] = $user['role'];
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
        } else {
            $response['status'] = "error";
            $response['message'] = 'Login failed. Incorrect credentials';
        }
    }else {
        $response['status'] = "error";
        $response['message'] = 'No such user is registered';
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
    $role =  "customer";
    $restaurant = $r->customer->restaurant;
    $address =  "";
    $email = $r->customer->email;
    $password = $r->customer->password;
    $phone = "";
    $isUserExists = $db->getOneRecord("SELECT 1 FROM users WHERE email='$email'");
    if(!$isUserExists){
        $r->customer->password = passwordHash::hash($password);
        $tabble_name = "users";
        $column_names = array('name','surname','role','restaurant','address','email','password','phone');
        $result = $db->insertIntoTable($r->customer, $column_names, $tabble_name);
        if ($result != NULL) {
            $response["status"] = "success";
            $response["message"] = "User account created successfully";
            $response["uid"] = $result;
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['uid'] = $response["uid"];
            $_SESSION['phone'] = $phone;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            echoResponse(200, $response);
        } else {
            $response["status"] = "error";
            $response["message"] = "Failed to create customer. Please try again";
            echoResponse(201, $response);
        }            
    }else{
        $response["status"] = "error";
        $response["message"] = "An user with the provided phone or email exists!";
        echoResponse(201, $response);
    }
});

/* Logout */
$app->get('/logout', function() {
    $db = new DbHandler();
    $session = $db->destroySession();
    $response["status"] = "info";
    $response["message"] = "Logged out successfully";
    echoResponse(200, $response);
});
?>