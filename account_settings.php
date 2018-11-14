<?php

session_start();
include "new_account.php";

if (!isset($_SESSION['TechCOM']) && !isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] != 'its_on'){
    echo "<script>window.close();</script>";
    header("location:main_page.php");
}

if (isset($_SESSION['image_delete']) && isset($_SESSION['image_delete']) && $_SESSION['image_delete'] == "image_delete"){
    echo "<script type='text/javascript'>alert('image deleted');</script>";
    unset($_SESSION['image_delete']);
}

if (isset($_SESSION['account_changes']) && $_SESSION['account_changes'] == "they_were_made"){
    echo "<script type='text/javascript'>alert('changes were made to you account check your email to verify');</script>";
    unset($_SESSION['account_changes']);
}

if (isset($_SESSION['account_changes']) && $_SESSION['account_changes'] == "they_were_not_made"){
    echo "<script type='text/javascript'>alert('input error something you entered was incorrect i would tell you what but you know better');</script>";
    unset($_SESSION['account_changes']);
}

function base64_to_jpeg($base64_string, $start) {
    file_put_contents("media/user_pics/user_pictures".$start.".jpeg", base64_decode($base64_string)); 
}

    $my_details = new get_my_pictures_from_database($dsn, $user, $password);
 
    $find = 0;
    while ($find < count($my_details->my_pictures)) {
        if (isset($_POST["delete".$find]) && $_POST["delete".$find] == "DELETE"){
            $my_details->delete_pic($my_details->my_pictures[$find]["image_id"]);
            $_SESSION['image_delete'] = "image_delete";
            echo "<script>window.close();</script>";
            header("location:account_settings.php");
        }
        $find++;
        // echo "i did it";
    }

    if (isset($_POST["edit"]) && $_POST["edit"] == "EDIT"){
        
        $my_details->check_changes(); 
    }
?>
<html>
<head>
<link rel="shortcut icon" type="image/x-icon" href="web_logo.png" />
<link rel="stylesheet" href="forgot_pass.css">
</head>
<body class="home_screen_background">
    <div class="top_div">
        <a href="index.php"><img class="home_logo" src="web_logo.png" alt="home"></a>
    <div>
        <div class="dropdown">
  <button class="dropbtn"><?php echo $_SESSION['user_loged_in'];?></button>
  <div class="dropdown-content">
    <a href="main_page.php">Home</a>
    <a href="logout.php">Logout</a>
    <a href="nope.php">uploads</a>
    </div>
    </div>
</div>

</div>
<div style="width: 780; height: 700px; margin:20px auto;">
    <div style="width: 500px; height: 700px; border-radius: 10px; margin: 20px auto; background: rgba(0, 0, 0, 0.489);float: left;">
        <form method="post">
            <p class="fy_info">Your account details</p>
            <input class="input_form" type="text" name="fname" value="<?php echo $my_details->my_account["FirstName"]?>" placeholder="First Name" maxlength="15" required>
            <input class="input_form" type="text" name="lname" value="<?php echo $my_details->my_account["LastName"]?>" placeholder="Surname" maxlength="15" required>
            <input class="input_form" type="email" name="email" value="<?php echo $my_details->my_account["Email"]?>" placeholder="Email"  minlength=6; maxlength="55" required>
            <input class="input_form" type="text" name="s_username" value="<?php echo $my_details->my_account["UserName"]?>" placeholder="USERNAME" minlength=6; maxlength="15" required>
            <input style="margin: 20px 50px;" type="checkbox" name="notify" value="true" <?php if($my_details->my_account["notify"] == "true"){echo "checked='checked'";}?>><p class="check_box">receive comment notifications by email</p><br>
            <p class="pin_change">To change pin enter old pin then the new one</p>
            <input class="input_form" type="password" name="password0" value="" placeholder="OLD PASSWORD"  minlength=8; maxlength="55">            
            <input class="input_form" type="password" name="password1" value="" placeholder="NEW PASSWORD"  minlength=8; maxlength="55">
            <input class="input_form" type="password" name="password2" value="" placeholder="RE-ENTER PASSWORD"  minlength=8; maxlength="55">
            <input class="login_button" type="submit" name="edit" value="EDIT" >
        </form>
    </div>
    <div style="height: 700px; width: 240px; border-radius: 10px; background: rgba(0, 0, 0, 0.489);float: right; margin: 20px auto; overflow-y: scroll;">    
        <form method="post">
            <?php
            $start = 0;
            while ($start < count($my_details->my_pictures)){
            base64_to_jpeg($my_details->my_pictures[$start][4], $start);
            echo "<img style=\"margin: 5px 15px; height:190; width:190;\" src=\"media/user_pics/user_pictures",$start,".jpeg\" alt=\"my_pics\">";
            echo "<input class=\"delete_button\" type=\"submit\" name=\"delete",$start,"\" value=\"DELETE\" >";
            $start++;
            }
            ?>
        </form>
    </div>
</div>
</body>
</html>