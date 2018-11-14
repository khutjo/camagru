<?php
// print_r($_GET);

session_start();

include "connection.php";

if (isset($_SESSION['TechCOM']) && isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] === 'its_on'){
    echo "<script>window.close();</script>";
    header("location:main_page.php");
}

    class reset_password extends connection {
        private $user;
        public $p_val = 3;
        public $s_val = 0;

        function __construct ($dsn, $user, $password){
            parent::__construct($dsn, $user, $password);
            $sql = "SELECT * FROM user_database.forgot WHERE ID=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$_GET["id"]]);
            $this->user = $stmt->fetch();
            if (!$this->user || !password_verify($_GET["enterkey"], $this->user["enter_key"])){
                $_SESSION['reset'] = "no_go_you_cant";
                echo "<script>window.close();</script>";
                header("location:index.php");
            }
        }
        
        function check_password() {
            $p_cap_val = preg_match( "/[A-Z]+/", $_POST['password1']);
            $p_low_val = preg_match( "/[a-z]+/", $_POST['password1']);
            $p_num_val = preg_match( "/[0-9]+/", $_POST['password1']);
            $this->p_val = $p_cap_val + $p_low_val + $p_num_val;
            $this->s_val = strcmp($_POST['password1'], $_POST['password2']);
            if ($this->p_val == 3 && $this->s_val == 0){
                return (1);
            }
            else{
                return (0);
            }
        }
        function change_password(){
            $sql = "UPDATE user_database.accounts SET `PassWord`=? WHERE Email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([password_hash($_POST['password1'], PASSWORD_DEFAULT), $this->user["Email"]]);
            $sql = "DELETE  FROM user_database.forgot WHERE Email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$this->user['Email']]);
            $_SESSION['reset'] = "password_changed";
            echo "<script>window.close();</script>";
            header("location:index.php");
        }
    }
    
    if (isset($_GET['enterkey']) && isset($_GET['forgot']) && $_GET['forgot'] === "true" && isset($_GET[idiot])){
        $change = new reset_password($dsn, $user, $password);
    }
    else{
        $_SESSION['reset'] = "no_go_you_cant";
        echo "<script>window.close();</script>";
        header("location:index.php");
    }

    if (isset($_POST["submit_cred"]) && $_POST["submit_cred"] == "submit"){
        $_POST['password1'] = strip_tags(stripslashes(trim($_POST['password1'])));
        $_POST['password2'] = strip_tags(stripslashes(trim($_POST['password2'])));
        if ($change->check_password()){
            $change->change_password();
        }
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
    </div>
    <div class="forgot">
        <form method="post">
            <p class="fy_info">Enter new password</p>
                <input class="input_form" type="password" name="password1" value="" placeholder="PASSWORD" minlength=8; maxlength="55"  required>
                <input class="input_form" type="password" name="password2" value="" placeholder="RE-ENTER PASSWORD" minlength=8; maxlength="55" required>
                <input class="login_button" type="submit" name="submit_cred" value="submit">
                <p class="error_m">
                    <?php if ($change->p_val != 3){echo "passwords must have a lowwer case, upper case and a number character";}
                    else if ($change->s_val != 0){echo "passwords do not match any in our database";}?></p>
        </form>
    </div>
</body>
</html>