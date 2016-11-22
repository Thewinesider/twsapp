<?php 
/* Set a new session */
$app->get('/session', function() {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["uid"] = $session['uid'];
    $response["email"] = $session['email'];
    $response["name"] = $session['name'];
    $response["role"] = $session['role'];
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
$app->get('/fullCatalogInfo', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $response["wines"]= $db->getRecord("SELECT * FROM catalog WHERE catalog_in = 1");
    $response["producers"]= $db->getRecord("SELECT DISTINCT producer FROM catalog WHERE catalog_in = 1");
    $response["regions"] = $db->getRecord("SELECT DISTINCT region FROM catalog WHERE catalog_in = 1");
    echoResponse(200, $response);
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

/* Get the user list from the DB */
$app->post('/getUserList', function() use ($app) {
    $db = new DbHandler();
    $user = json_decode($app->request->getBody());
    $user = $db->getRecord("SELECT * FROM users WHERE role LIKE '". $user->role ."'");
    echoResponse(200, $user);
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
    echo $r;
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