<?php
    include "connection.php";

    class save_changes extends connection {
        public $user;

        function __construct ($dsn, $user, $password){
            parent::__construct($dsn, $user, $password);
            $sql = "SELECT * FROM user_database.stage WHERE ID=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$_GET["id"]]);
            $this->user = $stmt->fetch();
                      
            if ($this->user && password_verify($_GET["enterkey"], $this->user["enter_key"])){
                print_r($_GET);  $sql = "UPDATE user_database.accounts SET `LastName`=?,`FirstName`=?
                ,`Email`=?,`UserName`=?,`PassWord`=? WHERE ID=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$this->user["LastName"], $this->user["FirstName"], 
                $this->user["Email"], $this->user["UserName"], $this->user["PassWord"], $_GET["id"]]);
                $sql = "DELETE FROM user_database.stage WHERE ID=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([$_GET["id"]]);
                
                header("location:logout.php");
            }
        }
    }

$data = new save_changes($dsn, $user, $password);
