<?php
$errors = 0;

include "new_account.php";
session_start();

if (!isset($_SESSION['TechCOM']) && !isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] != 'its_on'){
    echo "<script>window.close();</script>";
    header("location:index.php");
}

function base64_to_jpeg($base64_string, $send_num) {
    $ifp = fopen("media/tmp_pic".$send_num.".jpeg", 'wb'); 
    fwrite($ifp, base64_decode($base64_string));
    fclose($ifp); 
}

$pictures_lineup = new get_pictures_from_database($dsn, $user, $password);

// print_r($pictures_lineup);
if (!isset($_SESSION['gallary'])){
    $_SESSION['gallary'] = -1 + count($pictures_lineup->pic);
}

if (isset($_POST['left_button']) && $_POST['left_button'] == ">"){
    $_SESSION['gallary'] = $_SESSION['gallary'] - 1;
    if ($_SESSION['gallary'] < 0){
        $_SESSION['gallary'] = -1 + count($pictures_lineup->pic);
    }
    header("location:main_page.php");
}
if (isset($_POST['right_button']) && $_POST['right_button'] == "<"){
    $_SESSION['gallary'] = $_SESSION['gallary'] + 1;
    if ($_SESSION['gallary'] == count($pictures_lineup->pic)){
        $_SESSION['gallary'] = 0; 
    }
    header("location:main_page.php");
}

if (isset($_POST['left_button']) && $_POST['left_button'] == "<"){
    $run = 0;
    while ($run < 8){
        $_SESSION['gallary'] = $_SESSION['gallary'] - 1;
        if ($_SESSION['gallary'] < 0){
            $_SESSION['gallary'] = -1 + count($pictures_lineup->pic);
        }
        $run++;
    }
    header("location:main_page.php");
}
if (isset($_POST['right_button']) && $_POST['right_button'] == ">"){
    $run = 0;
    while ($run < 8){
        $_SESSION['gallary'] = $_SESSION['gallary'] + 1;
        if ($_SESSION['gallary'] == count($pictures_lineup->pic)){
            $_SESSION['gallary'] = 0; 
        }
        $run++;
    }
    header("location:main_page.php");
}

if (isset($_POST['like']) && $_POST['like'] == "like"){
    $pictures_lineup->likes_and_dislike();
    header("location:main_page.php");
}
if (isset($_POST['dislike']) && $_POST['dislike'] == "dislike"){
    $pictures_lineup->likes_and_dislike();
    header("location:main_page.php");
}

$comment_tag = new get_comment_data($dsn, $user, $password);

$pictures_lineup = new get_pictures_from_database($dsn, $user, $password);

$start = $_SESSION['gallary'];
$index = 0;
while ($index < 8){
    base64_to_jpeg($pictures_lineup->pic[$start][4], $index);
    $start--;
    $index++;
    if ($start == -1){
        $start = -1 + count($pictures_lineup->pic);
    }
}

if (isset($_POST["submit_comment"]) && $_POST["submit_comment"] == "comment"){
    $_POST["comments"] = strip_tags(stripslashes(trim($_POST["comments"])));
    $comment_tag->add_comment();
    unset($_POST["comments"]);
    unset($_POST["submit_comment"]);
    $comment_tag = new get_comment_data($dsn, $user, $password);
    header("location:main_page.php");
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
    <div class="logout_form_div">

    <div class="dropdown">
  <button class="dropbtn"><?php echo $_SESSION['user_loged_in'];?></button>
  <div class="dropdown-content">
    <a href="logout.php">Logout</a>
    <a href="nope.php">uploads</a>
    <a href="account_settings.php">Settings</a>
  </div>
</div>
    </div>
</div>
<div class="pic_holder_div">
<form method="post" style="float: left;">
                <input class="pic_nav_B" style="margin:300 auto;"type="submit" name="right_button" value="<" >
            </form> 
<div style="height: 600px; width: 600px; margin: auto;">
        <img style="height: 480px; width: 600px; margin:20px auto;"src="media/tmp_pic3.jpeg" alt="preview"></div>
            <form method="post" style="float: right;">
                <input class="pic_nav_B" style="margin:-300 auto;"type="submit" name="left_button" value=">" >
            </form>
    
  
        <div class="scroll_div">
            <?php foreach ($comment_tag->comment as list($a1, $a2, $a3, $a4, $a5, $a6, $c)){
                        echo "<div><p style=\"word-break: break-word;color: white\">( ",$a2," )   ",$a4,"</p></div>";}?>
        </div>
        <div class="comment_box">
            <form method="post">        
                <textarea name="comments" class="comment_form" minlength="1"; maxlength="300" ></textarea>
                <input style="margin: -20px 600px; width: 70px; height: 20px;" type="submit" name="submit_comment" value="comment">
                <input style="margin: -50px 600px; width: 50px" type="submit" name="dislike" value="dislike">
                <input style="margin: -80px 600px; width: 50px" type="submit" name="like" value="like">
                <p style="color: white; margin: -50px 700px;" ><?php echo $pictures_lineup->pic[$_SESSION['gallary']]["dislikes"];?></p>
                <p style="color: white; margin: -50px 700px;" ><?php echo $pictures_lineup->pic[$_SESSION['gallary']]["likes"];?></p>
            </form>
        </div></div>
        <div style=" width: 1410px; margin: 0px auto; background: rgba(0, 0, 0, 0.489);border-radius: 10px;">
<form method="post" style="float: left;">
                <input class="pic_nav_B" style="margin:70px 0px;"type="submit" name="left_button" value="<" >
            </form> 
<img style="height: 200px; width: 200px; margin: 10px;"src="media/tmp_pic0.jpeg" alt="preview">
<img style="height: 200px; width: 200px; margin: 10px;"src="media/tmp_pic1.jpeg" alt="preview">
<img style="height: 200px; width: 200px; margin: 10px;"src="media/tmp_pic2.jpeg" alt="preview">
<img style="height: 200px; width: 200px; margin: 10px;"src="media/tmp_pic4.jpeg" alt="preview">
<img style="height: 200px; width: 200px; margin: 10px;"src="media/tmp_pic5.jpeg" alt="preview">
<img style="height: 200px; width: 200px; margin: 10px;"src="media/tmp_pic6.jpeg" alt="preview">
            </form>
        <form method="post" style="float: right;">
                <input class="pic_nav_B" style="margin:70px 0px;"type="submit" name="right_button" value=">" >
    </div>
</body>
<html>
<!-- <div style="height: 220px; width: 1620px; mergin-bottom: 100px; border-radius: 10px; background: rgba(0, 0, 0, 0.489);">

</div> -->