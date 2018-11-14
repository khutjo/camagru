<?php

    include "connection.php";

    session_start();

    if (isset($_SESSION['TechCOM']) && isset($_SESSION['user_loged_in']) && $_SESSION['TechCOM'] === 'its_on'){
        echo "<script>window.close();</script>";
        header("location:main_page.php");
    }

    class they_forgot_their_shirt extends connection {
        private $head = "To reset you password click on the link\n";
        private $link = "http://127.0.0.1:8081/wedsite_php/reset_password.php?enterkey=";
        private $tail = "&forgot=true&idiot=true&id=";
        public $err = 0;
        private $user;

        function __construct ($dsn, $user, $password){
            parent::__construct($dsn, $user, $password);
            $sql = "SELECT * FROM user_database.accounts WHERE UserName=? AND Email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$_POST["username"], $_POST["email"]]);
            $this->user = $stmt->fetch();
            if ($this->user && $this->user["UserName"] === $_POST["username"] && $this->user["Email"] === $_POST["email"]){
                return (1);
            }
            else {
                $this->err = 1;
                return (0);
            }
        }
        function send_email(){
            $time_stamp = time();
            $sql = "DELETE  FROM user_database.forgot WHERE Email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$this->user['Email']]);
            $enter_key = substr(md5(time()),  -6, 6);
            $concat_email = $this->head.$this->link.$enter_key.$this->tail.$time_stamp;
            $concat_email = wordwrap($concat_email , 70);
            $sql = "INSERT INTO user_database.forgot (enter_key, ID, LastName, FirstName, Email, UserName, `PassWord`) VALUE (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $enter_key_hash = password_hash($enter_key, PASSWORD_DEFAULT);
            $stmt->execute([$enter_key_hash, $time_stamp, $this->user['LastName'], $this->user['FirstName'], $this->user['Email'], $this->user['UserName'], $this->user['PassWord']]);
            mail($this->user['Email'], "Reset Password", $concat_email);
            $_SESSION['reset'] = "lets_do_it";
            $_SESSION['theguy'] = $this->user['Email'];
            echo "<script>window.close();</script>";
            header("location:main_page.php");
        }
    }

    if (isset($_POST["submit_cred"]) && $_POST["submit_cred"] === "submit"){
        $_POST['email'] = strip_tags(stripslashes(trim($_POST['email'])));
        $_POST['username'] = strip_tags(stripslashes(trim($_POST['username'])));
        if ($conn = new they_forgot_their_shirt($dsn, $user, $password)){
            $conn->send_email();
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
            <p class="fy_info">Enter Username and Email</p>
                <input class="input_form" type="text" name="username" value="" placeholder="USERNAME" minlength=5; maxlength="15"  required>
                <input class="input_form" type="email" name="email" value="" placeholder="Email" maxlength="55" required>
                <input class="login_button" type="submit" name="submit_cred" value="submit">
                <p class="error_m">
                    <?php   if ($err == 1){echo "Username or Email do not match any in our database";}?></p>
        </form>
    </div>
</body>
</html>