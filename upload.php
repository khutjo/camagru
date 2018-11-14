<?php

include "connection.php";
session_start();
if (!isset($_SESSION['TechCOM']) && !isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] != 'its_on'){
    echo "<script>window.close();</script>";
    header("location:index.php");
}
class upload_file extends connection {

    function upload_pic (){
        $sql = "INSERT INTO user_database.media (userlink, likes, dislikes, image_src) VALUE (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['user_loged_in'], 0, 0, base64_encode(file_get_contents("media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png"))]);
        
    }
}

$sendup = new upload_file($dsn, $user, $password);
$sendup->upload_pic();
?>
