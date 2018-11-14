<?php
include "connection.php";
session_start();

// function ERROR($place){
//     echo "<script type='text/javascript'>alert('error uploading image".$_FILES["fileToUpload"]["name"].'  ('.$place.")');</script>";
// }

$_SESSION['img_time_stamp'] = time();

$target_filer = "media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png";
$target_dir = "media/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        // echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        // echo "File is not an image.";
        $_SESSION['image_up'] = "it failed";
        $_SESSION['it failed were'] = 1;
        header("location:nope.php");
        $uploadOk = 0;
    }
}
// Check if file already exists

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    // echo "Sorry, your file is too large.";
    $_SESSION['image_up'] = "it failed";
    $_SESSION['it failed were'] = 2;
    header("location:nope.php");
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    // echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    $_SESSION['image_up'] = "it failed";
    $_SESSION['it failed were'] = 3;
    header("location:nope.php");
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    // echo "Sorry, your file was not uploaded.";
    $_SESSION['image_up'] = "it failed";
    $_SESSION['it failed were'] = 4;
    header("location:nope.php");
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_filer)) {
        // echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        // echo $_POST["sticker_used"];
        // echo "<br />",$target_filer;
        $_SESSION['image_up'] = "success";
    } else {
        // echo "Sorry, there was an error uploading your file.";
        $_SESSION['image_up'] = "it failed";
        $_SESSION['it failed were'] = 5;
        header("location:nope.php");
    }
}

$stickercoded = base64_decode($_POST["sticker_used"]);
file_put_contents("stickers/temp.png", $stickercoded);

$dest = imagecreatefrompng("media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png");
$src = imagecreatefrompng("stickers/temp.png");

$width = ImageSx($src);
$height = ImageSy($src);
// echo $height,$width;
$x = $width;
$y = $height;

ImageCopyResampled($dest,$src,0,0,0,0,$x,$y,$width,$height);

header('Content-Type: image/jpeg');
imagepng($dest, "media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png");

imagedestroy($dest);
imagedestroy($src);
unlink("stickers/temp.png");
header("location:nope.php");
?>