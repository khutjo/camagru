<?php
$errors = 0;

include "connection.php";
if (!isset($_SESSION['TechCOM']) && !isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] != 'its_on'){
    echo "<script>window.close();</script>";
    header("location:index.php");
}

class upload_file extends connection {
    function upload_pic ($target_file){
        $sql = "INSERT INTO user_database.media (userlink, comment_tag, likes, dislikes, image_src) VALUE (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([1, 1, 100, 0, base64_encode(file_get_contents($target_file))]);
        unlink($target_file);
    echo "<script type='text/javascript'>alert('picture uploaded');</script>";        
    }
}

if (isset($_POST['logout']) &&$_POST['logout'] == "LOGOUT"){
    unset($_SESSION['TechCOM']);
    echo "<script>window.close();</script>";
    header("location:index.php");
}


if (isset($_POST['submit']) && $_POST['submit'] == "Upload Image"){
        $target_dir = "media/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    // if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "media/tmp_up_pic") && $image = getimagesize($_FILES["fileToUpload"]["tmp_name"]) &&
    //         $_FILES["fileToUpload"]["size"] < 500000 && $_FILES["fileToUpload"]["error"] == 0){
    //     $file_type = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
    //     if ($file_type == 'jpeg' || $file_type == 'png' || $file_type == 'jpg' || $file_type == 'gif'){
    //         $connect = new upload_file ($dsn, $user, $password);
    //         $connect->upload_pic($image);
    //     }
    //     else {
    //         $errors = 2;
    //     }
    // }
    // else{

        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
            $connect = new upload_file ($dsn, $user, $password);
            // $connect->upload_pic($target_file);
            // echo "its not me";
            // Create image instances
            $dest = imagecreatefromjpeg($target_file);
            $src = imagecreatefromjpeg("media/");
            imagecopymerge($dest, $src, 10, 10, 0, 0, 100, 47, 75);
            // header('Content-Type: image/jpeg');
            // imagejpg($dest);
            // imagedestroy($dest);
            imagedestroy($src);
        }
        else {echo "its me<br />";
        print_r($_FILES["fileToUpload"]);
        }
    //     $errors = 1;
    // }
}

?>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="web_logo.png" />
<link rel="stylesheet" href="main_page_style.css">
</head>
<body class="picture_time">
<div class="top_div">
    <img class="home_logo" src="web_logo.png" alt="home">
    <b style="font-family: Arial, Helvetica, sans-serif; font-size: 56px; color: white;">CAM A GROUX</b>
    <div class="logout_form_div">
    <form method="post">
    <input class="logout_buttom" type="submit" name="logout" value="LOGOUT" >
    </form>
    </div>
</div>
<div class="pic_div">
<!-- <div>
<img style="height: 600px; width: 600px;"src="<?php echo $target_file;?>" alt="preview">
</div> -->
<!-- <form method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" name="submit" value="Upload Image">
</form> -->

</div>
</body>
<html>