<?php
include "connection.php";
class install extends connection {
    function set_up (){
    $this->conn->exec("DROP DATABASE IF EXISTS User_database");
        $this->conn->exec("CREATE DATABASE User_database");
        $sql = "CREATE TABLE user_database.accounts(
                ID int NOT NULL AUTO_INCREMENT,
                LastName varchar(15) NOT NULL,
                FirstName varchar(15) NOT NULL,
                Email varchar(55) NOT NULL,
                UserName varchar(15) NOT NULL,
                `PassWord` varchar(515) NOT NULL,
                notify varchar(6) DEFAULT 'true',
                PRIMARY KEY (ID))";
        $this->conn->exec($sql);
        $sql = "CREATE TABLE user_database.stage(
                enter_key varchar(512) NOT NULL,
                ID int(11) NOT NULL,
                LastName varchar(15),
                FirstName varchar(15),
                Email varchar(55),
                UserName varchar(15),
                `PassWord` varchar(515))";
        $this->conn->exec($sql);
                $sql = "CREATE TABLE user_database.unverified (
                LastName varchar(15) NOT NULL,
                FirstName varchar(15) NOT NULL,
                Email varchar(55) NOT NULL,
                UserName varchar(15) NOT NULL,
                `PassWord` varchar(515) NOT NULL,
                verify_state varchar(512) NOT NULL,
                time_stamp int(11) NOT NULL)";
        $this->conn->exec($sql);
        $sql = "CREATE TABLE user_database.comments (
                comment_id INT NOT NULL AUTO_INCREMENT,
                userlink VARCHAR(15) NOT NULL,
                image_link int NOT NULL,
                comment varchar(300) NOT NULL,
                PRIMARY KEY (comment_id))";
        $this->conn->exec($sql);
        $sql = "CREATE TABLE user_database.media(
                image_id INT NOT NULL AUTO_INCREMENT,
                userlink VARCHAR(15) NOT NULL,
                likes INT,
                dislikes INT,
                image_src TEXT(500000),
                PRIMARY KEY (image_id))";
        $this->conn->exec($sql);
        $sql = "CREATE TABLE user_database.forgot(
                enter_key varchar(512) NOT NULL,
                ID int NOT NULL,
                LastName varchar(15) NOT NULL,
                FirstName varchar(15) NOT NULL,
                Email varchar(55) NOT NULL,
                UserName varchar(15) NOT NULL,
                `PassWord` varchar(515) NOT NULL)";
        $this->conn->exec($sql);
        $sql = "INSERT INTO user_database.accounts (
                LastName, FirstName, Email, UserName, `PassWord`)
                VALUE (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["admin", "admin", "admin@gmail.com",
                "ADMIN", "\$2y\$10\$oJ6GLzkywTDyZ.hKDCz9e.1YEj3eZMN2byrh4f3B.5DeaCvy/y6Q."]);
        $sql = "INSERT INTO user_database.media (userlink, likes, dislikes, image_src) VALUE (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."8159247953d5aa96a582d5238b13219c.jpg"))]);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."funny cat pictures captions (10).jpg"))]);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."funny-fake-illness-awkward-moment-seal.jpg"))]);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."priorities-funny-drinking-memes.jpg"))]);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."angry-meme1.jpg"))]);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."funny-animals-1-5.jpg"))]);
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."funny-soap-in-your-eye.jpg"))]);        
        $stmt->execute(["ADMIN", 100, 0, base64_encode(file_get_contents("media/resource/"."Untitled.png"))]);        
    }
}

$connect = new install($dsn, $user, $password);
$connect->set_up();
$connect = NULL;
echo "database setup";
?>