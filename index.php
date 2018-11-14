<?php
$err = 0;
include "new_account.php";

session_start();

if (isset($_SESSION['TechCOM']) && isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] === 'its_on'){
    echo "<script>window.close();</script>";
    header("location:main_page.php");
}

function base64_to_jpeg($base64_string) {
    $ifp = fopen("media/tmp_pic.jpeg", 'wb'); 
    fwrite($ifp, base64_decode($base64_string));
    fclose($ifp); 
}



$pictures = new get_pictures_from_database($dsn, $user, $password);

if (!isset($_SESSION['home_pic'])){
    $_SESSION['home_pic'] = 0;
}

if (isset($_POST['left_button']) && $_POST['left_button'] == ">"){
    $_SESSION['home_pic'] = $_SESSION['home_pic'] -1;
    if ($_SESSION['home_pic'] < 0){
        $_SESSION['home_pic'] = -1 + count($pictures->pic);
        header("location:main_page.php");
    }
}
if (isset($_POST['right_button']) && $_POST['right_button'] == "<"){
    $_SESSION['home_pic'] = $_SESSION['home_pic'] +1;
    if ($_SESSION['home_pic'] == count($pictures->pic)){
        $_SESSION['home_pic'] = 0;
        header("location:main_page.php");
    }
}

base64_to_jpeg($pictures->pic[$_SESSION['home_pic']][4]);

function remove_miscellaneous_signup (){
    $_POST['fname'] = strip_tags(stripslashes(trim($_POST['fname'])));
    $_POST['lname'] = strip_tags(stripslashes(trim($_POST['lname'])));
    $_POST['email'] = strip_tags(stripslashes(trim($_POST['email'])));
    $_POST['s_username'] = strip_tags(stripslashes(trim($_POST['s_username'])));
    $_POST['password1'] = strip_tags(stripslashes(trim($_POST['password1'])));
    $_POST['password2'] = strip_tags(stripslashes(trim($_POST['password2'])));
}
function remove_miscellaneous_login (){
    $_POST["username"] = strip_tags(stripslashes(trim($_POST["username"])));
    $_POST["password"] = strip_tags(stripslashes(trim($_POST["password"])));
}

if (isset($_SESSION['email'])){
    unset($_SESSION['email']);
}

if (isset($_SESSION['login_now']) && $_SESSION['login_now'] == "you may log in now"){
    echo "<script type='text/javascript'>alert('account created you can log in now');</script>";
    unset($_SESSION['login_now']);
}

if (isset($_SESSION['reset']) && $_SESSION['reset'] == "lets_do_it"){
    echo "<script type='text/javascript'>alert('reset email sent to ",$_SESSION['theguy'],"');</script>";
    unset($_SESSION['reset']);
    unset($_SESSION['theguy']);
}
if (isset($_SESSION['reset']) && $_SESSION['reset'] == "no_go_you_cant"){
    echo "<script type='text/javascript'>alert('unable to reset password now try again later (ERROR 924)');</script>";
    unset($_SESSION['reset']);
}

if (isset($_SESSION['reset']) && $_SESSION['reset'] = "password_changed"){
    echo "<script type='text/javascript'>alert('password reset');</script>";
    unset($_SESSION['reset']);
}
if (!isset($valid_cred)){
    $valid_cred = new new_account($dsn, $user, $password);
}

if (isset($_POST['submit_signup']) && $_POST['submit_signup'] == "SIGN UP"){
    remove_miscellaneous_signup();
    if ($valid_cred->is_val()){
        header("location:verify_user.php");
    }
}
if (isset($_POST['submit_login']) && $_POST['submit_login'] == "Login"){
    remove_miscellaneous_login();
    if ($valid_cred->varify_login()){
        $_SESSION['TechCOM'] = 'its_on';
        echo "<script>window.close();</script>";
        header("location:main_page.php");
    }
    else
        $err = 1;
}

?>

<html>
<head>
<title>Login</title>
<link rel="shortcut icon" type="image/x-icon" href="web_logo.png" />
<link rel="stylesheet" href="login_style.css">
</head>
<body class="home_screen_background">
    <div class="top_div">
            <img class="home_logo" src="web_logo.png" alt="home">
            <div class="login_form_div">
                <form method="post">
                    <input class="input_form" type="text" name="username" value="" placeholder="USERNAME" minlength=5; maxlength="15"  required>
                    <input class="input_form" type="password" name="password" value="" placeholder="PASSWORD" maxlength="55" required>
                    <input class="login_button" type="submit" name="submit_login" value="Login">
                    <a href="forgot_password.php">Forgot password</a></form>
                    <p style="font-family: Arial, Helvetica, sans-serif; color: red;">
                    <?php   if ($err == 1){echo "USERNAME OR PASSWORD INCORRECT";}?></p>
                    
            </div>
    </div>
    <div class="main_div">
        <div >
            <form method="post" style="float: left;">
                <input class="pic_nav_B" type="submit" name="right_button" value="<" >
            </form> 
            <img class="whaaat" src="media/tmp_pic.jpeg" alt="what the what">
            <form method="post" style="float: right;">
                <input class="pic_nav_B" type="submit" name="left_button" value=">" >
            </form>
        </div> 
        <div class="SIGNUP_DIV">
            <b class="set_text_color">CREATE AN ACCOUNT</b>
            <div class="form_div">
                <form method="post">
                    <p class="php_interface"><?php if ($valid_cred->f_val == 0){echo "invalid characters entered";}?></p>
                    <input class="signup_in" type="text" name="fname" value="<?php if (isset($_POST['fname']) && $valid_cred->clear == 0){echo $_POST['fname'];}?>" placeholder="First Name" maxlength="15" required>
                    <p class="php_interface"><?php if ($valid_cred->l_val == 0){echo "invalid characters entered";}?></p>
                    <input class="signup_in" type="text" name="lname" value="<?php if (isset($_POST['lname']) && $valid_cred->clear == 0){echo $_POST['lname'];}?>" placeholder="Surname" maxlength="15" required>
                    <p class="php_interface"><?php if ($valid_cred->e_val == 0){echo "invalid Email account";}
                                                   if ($valid_cred->e_val == -1){echo "Email already in use";}?></p>
                    <input class="signup_in" type="text" name="email" value="<?php if (isset($_POST['email']) && $valid_cred->clear == 0){echo $_POST['email'];}?>" placeholder="Email"  minlength=6; maxlength="55" required>
                    <p class="php_interface"><?php if ($valid_cred->u_val == 0){echo "invalid characters entered in user name";}
                                                   else if ($valid_cred->u_val == -1){echo "username taken";}?></p>
                    <input class="signup_in" type="text" name="s_username" value="<?php if (isset($_POST['s_username']) && $valid_cred->clear == 0){echo $_POST['s_username'];}?>" placeholder="USERNAME" minlength=6; maxlength="15" required>
                    <p class="php_interface"><?php if ($valid_cred->p_val != 3){echo "password must have at least one uppercase letter and one number";}?></p>
                    <input class="signup_in" type="password" name="password1" value="<?php if (isset($_POST['password1']) && $valid_cred->clear == 0 && $valid_cred->p_val == 3){echo $_POST['password1'];}?>" placeholder="PASSWORD"  minlength=8; maxlength="55" required>
                    <p class="php_interface"><?php if ($valid_cred->s_val != 0 && $valid_cred->p_val == 3){echo "passwords dont match";}?></p>
                    <input class="signup_in" type="password" name="password2" value="" placeholder="RE-ENTER PASSWORD"  minlength=8; maxlength="55" required>
                    <input class="signup_button" type="submit" name="submit_signup" value="SIGN UP" >
                </form>
            </div>
        </div>
    </div>
    <div class="bottom_div_bar">

    </div>
</body>
</html>