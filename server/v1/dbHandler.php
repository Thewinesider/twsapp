<?php
class DbHandler {

    private $conn;

    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();
        $this->conn = $db->connect();
    }

    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();    
    }

    /**
     * Fetching multiple record
     */
    public function getRecord($query) {
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);
        $response = array();
        if($r->num_rows > 0) {
            while($row = $r->fetch_assoc()) {
                array_push($response,$row);	               
            }
        }
        return $response;    
    }

    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name) {
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
            if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        //error_log("somewhere here");
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
        $r = $this->conn->query($query) or error_log($this->conn->error.__LINE__);

        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
        } else {
            return NULL;
        }
    }

    /**
     * Creating new record
     */
    public function execQuery($query) {
        $r = $this->conn->query($query) or error_log($this->conn->error.__LINE__);
        return $r;
    }

    public function getSession(){
        session_start();
        $sess = array();
        if(isset($_COOKIE['uid']))
        {
            $sess["uid"] = $_COOKIE['uid'];
            $sess["name"] = $_COOKIE['name'];
            $sess["email"] = $_COOKIE['email'];
            $sess["role"] = $_COOKIE['role'];
            $sess["associated_to"] = $_COOKIE['associated_to'];
            $sess["payment_is_set"] = $_COOKIE['payment_is_set'];
        }
        else
        {
            $sess["uid"] = '';
            $sess["name"] = 'Guest';
            $sess["email"] = '';
            $sess["role"] = 'Guest';
            $sess["associated_to"] = 0;
            $sess["payment_is_set"] = 0;
        }
        return $sess;
    }

    public function destroySession(){
        session_start();
        if(isset($_COOKIE['uid']))
        {
            setcookie("uid", '', time()-9999);
            setcookie("email", '', time()-9999);
            setcookie("name", '', time()-9999);
            setcookie("role", '', time()-9999);
            setcookie("associated_to", '', time()-9999);
            $msg="Utente disconnesso.";
        } else {
            $msg = "Non sei loggato.";
        }
        return $msg;
    }

}

?>
