<?php

include "connection.php";
session_start();
class verify extends connection {
    function check(){
        $sql = "SELECT * FROM user_database.unverified WHERE Email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_GET['Email']]);
        $user = $stmt->fetch();
        if ($user && password_verify($_GET["OTP"], $user["verify_state"])){
            $sql = "INSERT INTO user_database.accounts (LastName, FirstName, Email, UserName, `PassWord`) VALUE (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user['LastName'], $user['FirstName'], $user['Email'], $user['UserName'], $user['PassWord']]);
            $sql = "DELETE  FROM user_database.unverified WHERE Email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user['Email']]);
            $_SESSION['login_now'] ="you may log in now";
            echo "<script>window.close();</script>";
            header("location:index.php");
        }
        else{
            $_SESSION['login_now'] = "could not login";
            echo "<script>window.close();</script>";
            header("location:index.php");
        }
    }
}

$get_con = new verify($dsn, $user, $password);
$get_con->check();
