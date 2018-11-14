<?php
// echo date("H:i:s");
//echo $_GET['name'];

session_start();

$xml = file_get_contents("php://input");

$photo = explode(',',$xml);
$photodecoded = base64_decode($photo[1]);
$stickercoded = base64_decode($photo[3]);

unlink("media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png");
$_SESSION['img_time_stamp'] = time();
file_put_contents("media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png", $photodecoded);
file_put_contents("stickers/temp.png", $stickercoded);

$dest = imagecreatefrompng("media/test".$_SESSION['user_loged_in'].$_SESSION['img_time_stamp'].".png");
$src = imagecreatefrompng('stickers/temp.png');

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
?>
