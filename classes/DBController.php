<?php

require_once dirname(__DIR__) . '/config/database.php';

class DBController
{
    private $conn;

    public function __construct()
    {
        $this->conn = database_mysqli();
    }

    public function connectDB()
    {
        return $this->conn;
    }

    public function runQuery($query)
    {
        $result = mysqli_query($this->conn, $query);
        if ($result === false) {
            return array();
        }
        $resultset = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }
        return $resultset;
    }

    public function numRows($query)
    {
        $result = mysqli_query($this->conn, $query);
        return $result === false ? 0 : mysqli_num_rows($result);
    }
}

