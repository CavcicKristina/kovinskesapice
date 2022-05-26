<?php 

class Connection {

    private static $instance = null;
    private $conn;

    private function __construct()
    {
        $this->conn = mysqli_connect(SQLSERVER, SQLUSER, SQLPASS, SQLDB) or die("Database conn not established.");
		$this->conn->set_charset("utf8mb4");
		$this->conn->query("SET collation_connection = utf8mb4_general_ci");
    }

    public static function link() {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance; 
    }

    public function getConnection(){
        return $this->conn;
    }

    /* Spriječava dupliciranje konekcije */
    private function __clone(){}
}

?>