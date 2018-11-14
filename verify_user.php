<?php

include "connection.php";

session_start();

if (!isset($_COOKIE['TechCOM']) && $_COOKIE['TechCOM'] == false){
    $_SESSION['login_now'] = "could not login";
    // echo "its me 1";
    header("location:index.php");
}

class verify_user extends connection {
    private $email = "to verify your TechCOM account enter this OTP: ";
    private $email_link_head = "or click on the link http://127.0.0.1:8081/wedsite_php/verify_by_link.php?OTP=";
    private $email_link_mid = "&stmp_man_filWR=";
    private $email_link_end = "&link=true&Email=";
    public $OTP_error = 0;
    public $email_adde;
    public $user;
                        /*md5($str)*/
    function __construct ($dsn, $user, $password){
        parent::__construct($dsn, $user, $password);
        $sql = "SELECT * FROM user_database.unverified WHERE Email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_COOKIE['TechCOM']]);
        $user = $stmt->fetch();
        if ($user ){
            $this->email_adde = $user['Email'];
        }
        else{
            $_SESSION['login_now'] = "could not login";
            echo "its me 2";
            // header("location:index.php");
        }
    }

    function send_email(){
        $set_stamp = substr(md5(time()), -6, 6);
        $OTP = password_hash($set_stamp, PASSWORD_DEFAULT);
        $concat_email = $this->email.$set_stamp."\n";
        $concat_email = $concat_email.$this->email_link_head.
        $set_stamp.$this->email_link_mid.(time() + 1800).$this->email_link_end.$this->email_adde;
        $concat_email = wordwrap($concat_email , 70);
        $sql = "UPDATE user_database.unverified SET verify_state=? WHERE Email=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$OTP, $this->email_adde]);
        return ($concat_email);
    }
    function varify_OTP ()
    {
        $sql = "SELECT * FROM user_database.unverified WHERE Email=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->email_adde]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST["OTP"], $user["verify_state"])){
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
        else {
            $this->OTP_error = 1;
        }
    }
}
if (isset($_POST["re_send"]) && $_POST["re_send"] == "SEND"){
    $_SESSION['email'] = 'sent';
    $mailler = new verify_user($dsn, $user, $password);  
    mail($mailler->email_adde, "verify TechCOM account", $mailler->send_email());
}

if (isset($_POST["re_send"]) && $_POST["re_send"] == "RESEND"){
    $mailler = new verify_user($dsn, $user, $password);  
    mail($mailler->email_adde, "verify TechCOM account", $mailler->send_email());
}

if (isset($_POST["submit_otp"]) && $_POST["submit_otp"] == "VERIFY"){
    $mailler = new verify_user($dsn, $user, $password);
    $mailler->varify_OTP();
    
}

if (!isset($_SESSION['email']))
{
  $mailler = new verify_user($dsn, $user, $password);  
}


// if (isset($_GET["re_send"]) && $_GET["re_send"] == "SEND"){
//     $_SESSION['email'] = 'sent';
// }
// if (isset($_GET["re_send"]) && $_GET["re_send"] == "RESEND"){
// }
// if (isset($_GET['submit_otp']) && $_GET['submit_otp'] == "VERIFY"){
//     $mailler = new verify_user($dsn, $user, $password);
// }

?>
<html>
<head>
<title>varify email</title>
<link rel="shortcut icon" type="image/x-icon" href="web_logo.png" />
    <style>
div.top_div {
    background-color: rgb(10, 1, 8);
    height: 100px;
    min-width: 650px;
    width: 100%;
    }
img.home_logo {
    width: 100px;
    height: 100px;
}
body.home_screen_background {
    background: rgb(98, 59, 88);
}
div.main_div {
    margin: 30 auto;
    min-width: 560px;
    width: 30vw;
    height: 690px;
    background: rgba(0, 0, 0, 0.489);
}
div.bottom_div_bar {
    background-color: rgb(10, 1, 8);
    height: 100px;
    min-width: 800px;
    width: 100%;
}
b.set_text_color {
    margin: 75px;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 46px;
    color: white;
}
input.opt_box {
    margin: 30px 165px;
    height: 30px;
    width: 240px;
    background: white;
    font-size: 16px;
}
input.submit_button {
    font-size: 16px;
    margin: 0px 162px;
    height: 30px;
    width: 90px;
    color: white;
    border-color: rgb(98, 59, 88);
    background: rgb(98, 59, 88);
}
input.resend_button {
    font-size: 16px;
    margin: 0px -100px;
    height: 30px;
    width: 90px;
    color: white;
    border-color: rgb(98, 59, 88);
    background: rgb(98, 59, 88);
}
div.form_div {
}
    </style>
</head>
<body class="home_screen_background">
<div class="top_div">
<a href="index.php"><img class="home_logo" src="web_logo.png" alt="home"></a>
</div>
<div class="main_div">
<b class="set_text_color">VERIFY ACCOUNT</b>
<div class="form_div">
    <p style="color: white; text-align: center;"><?php if ($_SESSION['email'] != 'sent') echo"click SEND to send the OTP to $mailler->email_adde"; else {echo "if you did not receive the email click RESEND";}?></p>
<form method="post">
    <p><?php if ($mailler->OTP_error == 1){echo "invalid OTP";}?></p>
    <input class="opt_box" type="text" name="OTP" value="" placeholder="enter OTP"><br />
    <input class="submit_button" type="submit" name="submit_otp" value="VERIFY">
    <input class="resend_button" type="submit" name="re_send" value="<?php if ($_SESSION['email'] === 'sent'){ echo"RESEND";} else {echo "SEND";}?>">
</form>
</div>
</div>
<div class="bottom_div_bar">
</div>
</body>
</html>