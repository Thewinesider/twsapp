<?php 
/*

http://www.daterangepicker.com/

QUERY

QUERY DIVISIONE SOMMA BOTTIGLIE VENDUTE PER GIORNO E PER VINO

SELECT winelist.sku, winelist.name, WEEKDAY(winesold.date), SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 GROUP BY DAY(winesold.date), winesold.sku

QUERY DIVISIONE SOMMA BOTTIGLIE VENDUTE PER ORA IN UN DETERMINATO GIORNO
SELECT HOUR(winesold.date), SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 AND date BETWEEN "2016-10-17 00:00:00" AND "2016-10-17 23:59:59" GROUP BY HOUR(winesold.date)

QUERY DIVISIONE SOMMA BOTTIGLIE VENDUTE PER GIORNO
SELECT WEEKDAY(winesold.date), SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 GROUP BY DAY(winesold.date)

QUERY DIVISIONE SOMMA BOTTIGLIE VENDUTE PER SETTIMANA
SELECT winelist.sku, winelist.name, WEEK(winesold.date), SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 GROUP BY WEEK(winesold.date)

QUERY DIVISIONE SOMMA BOTTIGLIE VENDUTE PER MESE
SELECT MONTH(winesold.date), SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 GROUP BY MONTH(winesold.date)

QUERY DIVISIONE SOMMA BOTTIGLIE VENDUTE PER ANNO
SELECT YEAR(winesold.date), SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 GROUP BY YEAR(winesold.date)

SELECT 
    SUM(IF(period = 0, sold, 0)) AS 'Lunedì',
    SUM(IF(period = 1, sold, 0)) AS 'Martedì',
    SUM(IF(period = 2, sold, 0)) AS 'Mercoledì',
    SUM(IF(period = 3, sold, 0)) AS 'Giovedì',
    SUM(IF(period = 4, sold, 0)) AS 'Venerdì',
    SUM(IF(period = 5, sold, 0)) AS 'Sabato',
    SUM(IF(period = 6, sold, 0)) AS 'Domenica',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winesold.value) as sold, WEEKDAY(winesold.date) as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = 1 AND winelist.id_user = 1 AND winesold.date >= '2016-10-31 12:00:00' AND winesold.date <= '2016-11-07 12:00:00' GROUP BY WEEKDAY(winesold.date)
    ) AS SubTable1

*/


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

/* Get the full list of wine sold in a certain period */
$app->post('/getWineSoldList', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $period = json_decode($app->request->getBody());
    $query = "SELECT winelist.name,SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY winesold.sku ORDER BY sold DESC";
    $wines = $db->getRecord($query);
    echoResponse(200, $wines);
});    

/* Get the wine sold with hour aggregation */
$app->post('/getWineSoldDaily', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $period = json_decode($app->request->getBody());
    $query = "SELECT 
    SUM(IF(period = 1, sold, 0)) AS '1',
    SUM(IF(period = 2, sold, 0)) AS '2',
    SUM(IF(period = 3, sold, 0)) AS '3',
    SUM(IF(period = 4, sold, 0)) AS '4',
    SUM(IF(period = 5, sold, 0)) AS '5',
    SUM(IF(period = 6, sold, 0)) AS '6',
    SUM(IF(period = 7, sold, 0)) AS '7',
    SUM(IF(period = 8, sold, 0)) AS '8',
    SUM(IF(period = 9, sold, 0)) AS '9',
    SUM(IF(period = 10, sold, 0)) AS '10',
    SUM(IF(period = 11, sold, 0)) AS '11',
    SUM(IF(period = 12, sold, 0)) AS '12',
    SUM(IF(period = 13, sold, 0)) AS '13',
    SUM(IF(period = 14, sold, 0)) AS '14',
    SUM(IF(period = 15, sold, 0)) AS '15',
    SUM(IF(period = 16, sold, 0)) AS '16',
    SUM(IF(period = 17, sold, 0)) AS '17',
    SUM(IF(period = 18, sold, 0)) AS '18',
    SUM(IF(period = 19, sold, 0)) AS '19',
    SUM(IF(period = 20, sold, 0)) AS '20',
    SUM(IF(period = 21, sold, 0)) AS '21',
    SUM(IF(period = 22, sold, 0)) AS '22',
    SUM(IF(period = 23, sold, 0)) AS '23',
    SUM(IF(period = 24, sold, 0)) AS '24',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winesold.value) as sold, HOUR(winesold.date) as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY HOUR(winesold.date)
    ) AS SubTable1";
    $wines = $db->getRecord($query);
    echoResponse(200, $wines);
});

/* Get the wine sold with daily aggregation */
$app->post('/getWineSoldWeekly', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $period = json_decode($app->request->getBody());
    $query = "SELECT 
    SUM(IF(period = 0, sold, 0)) AS 'Lunedì',
    SUM(IF(period = 1, sold, 0)) AS 'Martedì',
    SUM(IF(period = 2, sold, 0)) AS 'Mercoledì',
    SUM(IF(period = 3, sold, 0)) AS 'Giovedì',
    SUM(IF(period = 4, sold, 0)) AS 'Venerdì',
    SUM(IF(period = 5, sold, 0)) AS 'Sabato',
    SUM(IF(period = 6, sold, 0)) AS 'Domenica',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winesold.value) as sold, WEEKDAY(winesold.date) as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY WEEKDAY(winesold.date)
    ) AS SubTable1";
    $bottles = $db->getRecord($query);
    $query = "SELECT 
    SUM(IF(period = 0, sold, 0)) AS 'Lunedì',
    SUM(IF(period = 1, sold, 0)) AS 'Martedì',
    SUM(IF(period = 2, sold, 0)) AS 'Mercoledì',
    SUM(IF(period = 3, sold, 0)) AS 'Giovedì',
    SUM(IF(period = 4, sold, 0)) AS 'Venerdì',
    SUM(IF(period = 5, sold, 0)) AS 'Sabato',
    SUM(IF(period = 6, sold, 0)) AS 'Domenica',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winelist.suggested_price) as sold, WEEKDAY(winesold.date) as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY WEEKDAY(winesold.date)
    ) AS SubTable1";
    $revenue = $db->getRecord($query);
    $query = "SELECT winelist.name,SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY winesold.sku ORDER BY sold DESC";
    $wines = $db->getRecord($query);
    $query = "SELECT SUM(winesold.value) as sold, catalog.type as type FROM winesold, winelist, catalog WHERE winelist.sku = winesold.sku AND catalog.SKU = winelist.SKU AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY catalog.type";
    $wineType = $db->getRecord($query);
    $response['bottles'] = $bottles;
    $response['revenue'] = $revenue;
    $response['wines'] = $wines;
    $response['type'] = $wineType;
    echoResponse(200, $response);
});

/* Get the wine sold with weekly aggregation */
$app->post('/getWineSoldMonthly', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $period = json_decode($app->request->getBody());
    $query = "SELECT 
    SUM(IF(period = 1, sold, 0)) AS 'Dall 1 al 7',
    SUM(IF(period = 2, sold, 0)) AS 'Dall 8 al 14',
    SUM(IF(period = 3, sold, 0)) AS 'Dall 15 al 21',
    SUM(IF(period = 4, sold, 0)) AS 'Dall 22 al 28',
    SUM(IF(period = 5, sold, 0)) AS 'Dall 28 al 31',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winesold.value) as sold, (DAY(winesold.date)+6)%7 as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY (DAY(winesold.date)+6)%7
    ) AS SubTable1";
    $bottles = $db->getRecord($query);
    $query = "SELECT 
    SUM(IF(period = 1, sold, 0)) AS 'Dall 1 al 7',
    SUM(IF(period = 2, sold, 0)) AS 'Dall 8 al 14',
    SUM(IF(period = 3, sold, 0)) AS 'Dall 15 al 21',
    SUM(IF(period = 4, sold, 0)) AS 'Dall 22 al 28',
    SUM(IF(period = 5, sold, 0)) AS 'Dall 28 al 31',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winelist.suggested_price) as sold, (DAY(winesold.date)+6)%7 as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY (DAY(winesold.date)+6)%7
    ) AS SubTable1";
    $revenue = $db->getRecord($query);
    $query = "SELECT winelist.name,SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY winesold.sku ORDER BY sold DESC";
    $wines = $db->getRecord($query);
    $query = "SELECT SUM(winesold.value) as sold, catalog.type as type FROM winesold, winelist, catalog WHERE winelist.sku = winesold.sku AND catalog.SKU = winelist.SKU AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY catalog.type";
    $wineType = $db->getRecord($query);
    $response['bottles'] = $bottles;
    $response['revenue'] = $revenue;
    $response['wines'] = $wines;
    $response['type'] = $wineType;
    echoResponse(200, $response);
});

/* Get the wine sold with monthly aggregation */
$app->post('/getWineSoldYearly', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $period = json_decode($app->request->getBody());
    $query = "SELECT 
    SUM(IF(period = 1, sold, 0)) AS 'Gennaio',
    SUM(IF(period = 2, sold, 0)) AS 'Febbraio',
    SUM(IF(period = 3, sold, 0)) AS 'Marzo',
    SUM(IF(period = 4, sold, 0)) AS 'Aprile',
    SUM(IF(period = 5, sold, 0)) AS 'Maggio',
    SUM(IF(period = 6, sold, 0)) AS 'Giugno',
    SUM(IF(period = 7, sold, 0)) AS 'Luglio',
    SUM(IF(period = 8, sold, 0)) AS 'Agosto',
    SUM(IF(period = 9, sold, 0)) AS 'Settembre',
    SUM(IF(period = 10, sold, 0)) AS 'Ottobre',
    SUM(IF(period = 11, sold, 0)) AS 'Novembre',
    SUM(IF(period = 12, sold, 0)) AS 'Dicembre',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winesold.value) as sold, MONTH(winesold.date) as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY MONTH(winesold.date)
    ) AS SubTable1";
    $bottles = $db->getRecord($query);
    
    $query = "SELECT 
    SUM(IF(period = 1, sold, 0)) AS 'Gennaio',
    SUM(IF(period = 2, sold, 0)) AS 'Febbraio',
    SUM(IF(period = 3, sold, 0)) AS 'Marzo',
    SUM(IF(period = 4, sold, 0)) AS 'Aprile',
    SUM(IF(period = 5, sold, 0)) AS 'Maggio',
    SUM(IF(period = 6, sold, 0)) AS 'Giugno',
    SUM(IF(period = 7, sold, 0)) AS 'Luglio',
    SUM(IF(period = 8, sold, 0)) AS 'Agosto',
    SUM(IF(period = 9, sold, 0)) AS 'Settembre',
    SUM(IF(period = 10, sold, 0)) AS 'Ottobre',
    SUM(IF(period = 11, sold, 0)) AS 'Novembre',
    SUM(IF(period = 12, sold, 0)) AS 'Dicembre',
    SUM(sold) AS total
    FROM (
        SELECT SUM(winelist.suggested_price) as sold, MONTH(winesold.date) as period FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY MONTH(winesold.date)
    ) AS SubTable1";
    $revenue = $db->getRecord($query);
    
    $query = "SELECT winelist.name,SUM(winesold.value) as sold FROM winesold, winelist WHERE winelist.sku = winesold.sku AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY winesold.sku ORDER BY sold DESC";
    $wines = $db->getRecord($query);
    
    $query = "SELECT SUM(winesold.value) as sold, catalog.type as type FROM winesold, winelist, catalog WHERE winelist.sku = winesold.sku AND catalog.SKU = winelist.SKU AND winesold.id_winelist = ". $session["uid"] ." AND winelist.id_user = ". $session["uid"] ." AND winesold.date >= '". $period->periodStart ."' AND winesold.date <= '". $period->periodEnd ."' GROUP BY catalog.type";
    $wineType = $db->getRecord($query);
    
    $response['bottles'] = $bottles;
    $response['revenue'] = $revenue;
    $response['wines'] = $wines;
    $response['type'] = $wineType;
    echoResponse(200, $response);
});

/* Get the wines downloaded between yesterday and today */
$app->get('/getWineSoldYesterday', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $r = json_decode($app->request->getBody());
    $today = date("Y-m-d h:i:s", strtotime('12:00:00'));
    $yesterday = date("Y-m-d h:i:s", strtotime('-1 day', strtotime('12:00:00')));
    $query = "SELECT winesold.value, winesold.date, winelist.name FROM winesold,winelist WHERE id_winelist = ".$session['uid']." AND date >= '".$yesterday."' AND date <= '".$today."' AND winesold.sku = winelist.sku GROUP BY winesold.date";
    $wines = $db->getRecord($query);
    echoResponse(200, $wines);
});

/* Get the wines downloaded today */
$app->get('/getWineSoldToday', function() use ($app) {
    $db = new DbHandler();
    $session = $db->getSession();
    $today = date("Y-m-d h:i:s", strtotime('12:00:00'));
    $tomorrow = date("Y-m-d h:i:s", strtotime('+1 day', strtotime('12:00:00')));
    $query = "SELECT winesold.value, winesold.date, winelist.name FROM winesold,winelist WHERE id_winelist = ".$session['uid']." AND date >= '".$today."' AND date <= '".$tomorrow."' AND winesold.sku = winelist.sku GROUP BY winesold.date";
    $wines = $db->getRecord($query);
    echoResponse(200, $wines);
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