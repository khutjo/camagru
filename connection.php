<?php

include "database.php";

class connection {
    protected $conn;

    function __construct ($dsn, $user, $password){
        try{
            $this->conn = new PDO($dsn, $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        catch (PDOException $e) {
            echo "<script type='text/javascript'>alert('unable to create account now try again later (947)');</script>";
            }

    }
    


    function set_trecking ($err){

        $sql = "SELECT *
                FROM follow.accounts
                WHERE UserLink= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();
        if ($user)
        {
            return ($user["beacon"]);
        } else {
            $sql = "INSERT INTO follow.accounts (UserLink, beacon)VALUE ('";
            $hold = $sql.$_POST['username']."', '".
            password_hash($err, PASSWORD_DEFAULT)."')";
            $this->conn->exec($hold);
            return ($err);
        }
    }
    function logout_user ($user_hash_id)
    {
        $sql = "DROP * FROM follow.account WHERE beacon=";
        $hold = $sql.$user_hash_id;
        $this->conn->exec($hold);
        return ($err);
    }

    // function user_cmp($add_user)
    // {
    //     $sql = "SELECT username
    //     FROM user_database.accounts
    //     WHERE UserName= ?";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute([$_POST['username']]);
    //     $user = $stmt->fetch();
    //     if ($user && $add_user == $user['username'])
    //     {
    //         echo "what";
    //         return 0;
    //     } else {
    //         echo "yeap";
    //         return 1;
    //     }
    // }
}