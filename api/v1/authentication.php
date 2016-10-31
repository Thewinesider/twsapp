<?php 

/* Set a new session */
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    echoResponse(200, $session);
});

/* Get the logged user winelist*/
$app->get('/getWineList', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $wines= $db->getRecord("SELECT winelist.sku, catalog.type, winelist.name, catalog.description, catalog.producer, catalog.alcohol, catalog.region  FROM winelist,catalog WHERE id_user = ". $session['uid'] . " AND catalog.sku = winelist.sku");
    echoResponse(200, $wines);
});

/* Get the full catalog from the DB */
$app->get('/getFullCatalog', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $wines= $db->getRecord("SELECT * FROM catalog WHERE catalog_in = 1");
    echoResponse(200, $wines);
});

/* Get the full producer list */
$app->get('/getProducerList', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $producer= $db->getRecord("SELECT DISTINCT producer FROM catalog");
    echoResponse(200, $producer);
});

/* Get the full region list from the DB */
$app->get('/getRegionList', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $region = $db->getRecord("SELECT DISTINCT region FROM catalog");
    echoResponse(200, $region);
});

/* Get the wines downloaded between yesterday and today */
$app->get('/getWineYesterday', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $r = json_decode($app->request->getBody());
    $today = date("Y-m-d h:i:s", strtotime('12:00:00'));
    $yesterday = date("Y-m-d h:i:s", strtotime('-1 day', strtotime('12:00:00')));
    $query = "SELECT winesold.value, winesold.date, winelist.name FROM winesold,winelist WHERE id_winelist = ".$session['uid']." AND date >= '".$yesterday."' AND date <= '".$today."' AND winesold.sku = winelist.sku";
    $wines = $db->getRecord($query);
    echoResponse(200, $wines);
});

/* Get the wines downloaded between today */
$app->get('/getWineToday', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $today = date("Y-m-d h:i:s", strtotime('12:00:00'));
    $tomorrow = date("Y-m-d h:i:s", strtotime('+1 day', strtotime('12:00:00')));
    $query = "SELECT winesold.value, winesold.date, winelist.name FROM winesold,winelist WHERE id_winelist = ".$session['uid']." AND date >= '".$today."' AND date <= '".$tomorrow."' AND winesold.sku = winelist.sku";
    echo $query;
    //$wines = $db->getRecord($query);
    //echoResponse(200, $wines);
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