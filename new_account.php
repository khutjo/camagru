<?php
include "connection.php";
include "database.php";
session_start();



class get_my_pictures_from_database extends connection {
    public $my_pictures;
    public $my_account;
    private $patten = "/[a-zA-Z0-9]+@[a-zA-Z-0-9]+.[a-zA-Z-0-9]+/";
    public $clear_cerd = 0;
    public $clear_pass = 1;
    public $f_val = 1;
    public $l_val = 1;
    public $e_val = 1;
    public $u_val = 1;
    public $p_val = 3;
    public $s_val = 0;

    function __construct ($dsn, $user, $password){
        parent::__construct($dsn, $user, $password);
        $sql = "SELECT * FROM user_database.media WHERE userlink=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['user_loged_in']]);
        $this->my_pictures = $stmt->fetchAll();
        $sql = "SELECT * FROM user_database.accounts WHERE UserName=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['user_loged_in']]);
        $this->my_account = $stmt->fetch();
    }

    function delete_pic($image_id){
        $sql = "DELETE  FROM user_database.media WHERE image_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$image_id]); 
    }

    function add_account (){
        $email_head = "changes have been made to your account to keep changes click on the link\n";
        $email_link = "http://127.0.0.1:8081/wedsite_php/apply_change.php?enterkey=";
        $email_tail = "&id=";
        $enter_key = substr(md5(time()),  -6, 6);
        $concat_email = wordwrap($email_head.$email_link.$enter_key.$email_tail.$this->my_account['ID'] , 70);
        $sql = "DELETE  FROM user_database.stage WHERE ID=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->my_account['ID']]);
        $sql = "INSERT INTO user_database.stage(`enter_key`, `ID`, `LastName`, 
        `FirstName`, `Email`, `UserName`, `PassWord`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        // $this->conn->exec($sql);
        $stmt = $this->conn->prepare($sql);
        // $stmt->execute(['hello', 1, 'hello', 'hello', 'hello', 'hello', 'hello']);
        $enter_key_hash = password_hash($enter_key, PASSWORD_DEFAULT);
        $stmt->execute([$enter_key_hash, $this->my_account['ID'], $this->my_account['LastName']
        , $this->my_account['FirstName'], $this->my_account['Email'], $this->my_account['UserName'], $this->my_account['PassWord']]);
        mail($this->my_account['Email'], "Save Changes", $concat_email);
    }

    

    function user_cmp()
    {
        $sql = "SELECT ID, UserName, Email
        FROM user_database.accounts
        WHERE UserName= ? OR Email= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['s_username'], $_POST['email']]);
        $user = $stmt->fetch();
        if ($user && $user["ID"] != $this->my_account['ID'] && strcmp($_POST['s_username'], $user['UserName']) == 0)
        {
            $this->u_val = -1;
        }
        if ($user && $user["ID"] != $this->my_account['ID'] && strcmp($_POST['email'], $user['Email']) == 0)
        {
            $this->e_val = -1;
        }
        $sql = "SELECT username, Email
        FROM user_database.unverified
        WHERE UserName= ? OR Email= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['s_username'], $_POST['email']]);
        $user = $stmt->fetch();
        if ($user && strcmp($_POST['s_username'], $user['UserName']) == 0)
        {
            $this->u_val = -1;
        }
        if ($user && strcmp($_POST['email'], $user['Email']) == 0)
        {
            $this->e_val = -1;
        }
    }

    function is_val_pass(){
        $p_cap_val = preg_match( "/[A-Z]+/", $_POST['password1']);
        $p_low_val = preg_match( "/[a-z]+/", $_POST['password1']);
        $p_num_val = preg_match( "/[0-9]+/", $_POST['password1']);
        $this->p_val = $p_cap_val + $p_low_val + $p_num_val;
        $this->s_val = strcmp($_POST['password1'], $_POST['password2']);
        if ($this->p_val == 3 && $this->s_val == 0){
            $this->clear_val = 1;
            // echo "its cool3";
        return (1);
        }
        $this->clear = 0;
        // echo "not cool4";
    return (0);
    }
    function is_val_norm(){
        $this->f_val = preg_match( "/[a-zA-Z]+/", $_POST['fname']);
        $this->l_val = preg_match( "/[a-zA-Z]+/", $_POST['lname']);
        $this->e_val = preg_match( $this->patten, $_POST['email']);
        $this->u_val = preg_match( "/[a-zA-Z0-9]+/", $_POST['s_username']);
        $this->user_cmp();
        if ($this->f_val == 1 && $this->l_val == 1 && $this->e_val == 1 &&
            $this->u_val == 1){
                $this->clear_val = 1;
            // echo "its cool1";
            return (1);
            }
            $this->clear = 0;
            // echo "not cool2";
        return (0);
    }
                function check_changes(){
        if ($_POST["notify"] != "true"){$_POST["notify"] = "false";}
        $sql = "UPDATE user_database.accounts SET notify=? WHERE UserName=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST["notify"], $_SESSION['user_loged_in']]);
        if (strcmp($_POST["fname"], $this->my_account['FirstName']) ||
        strcmp($_POST["lname"], $this->my_account['LastName']) ||
        strcmp($_POST["email"], $this->my_account['Email']) ||
        strcmp($_POST["s_username"], $this->my_account['UserName'])){
            $this->clear_cerd = $this->is_val_norm();
            if ($this->clear_cerd == 1){
                $this->my_account['LastName'] = $_POST['lname'];
                $this->my_account['FirstName'] = $_POST['fname'];
                $this->my_account['Email'] = $_POST['email'];
                $this->my_account['UserName'] = $_POST['s_username'];
            }
        }
        else {$this->clear_cerd = 2;}
        if (strlen($_POST['password0']) > 7){
        if (password_verify($_POST['password0'], $this->my_account['PassWord'])){
            $this->clear_pass = $this->is_val_pass();
            echo "been hear";
            if ($this->clear_pass == 1){
                $this->my_account['PassWord'] = password_hash($_POST['password1'], PASSWORD_DEFAULT);
                if ($this->clear_cerd == 2){$this->clear_cerd = 1;}
            }
            
        }
        else{$this->clear_pass = 0;}
        }
        // print_r($this->my_account);
        // echo "<br />";
        // print_r($_POST);
        // echo "<br >",$this->clear_pass,$this->clear_cerd; 
        if ($this->clear_pass == 1 && $this->clear_cerd == 1){
            $this->add_account();
            $_SESSION['account_changes'] = "they_were_made";
        }
        else {$_SESSION['account_changes'] = "they_were_not_made";}
        header("location:account_settings.php");
    }
}	

class get_pictures_from_database extends connection {
    public $pic;

    function __construct ($dsn, $user, $password){
        parent::__construct($dsn, $user, $password);
        $sql = "SELECT * FROM user_database.media";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $this->pic = $stmt->fetchAll();
    }
    function likes_and_dislike (){
        $likes = $this->pic[$_SESSION['gallary']]["likes"] + 1;
        $dislikes = $this->pic[$_SESSION['gallary']]["dislikes"] + 1;
        $sqla = "UPDATE user_database.media SET likes=? WHERE image_id=?";
        $sqlb = "UPDATE user_database.media SET dislikes=? WHERE image_id=?";
        if ($_POST["like"]){
        $stmt = $this->conn->prepare($sqla);
        $stmt->execute([$likes, $this->pic[$_SESSION['gallary']]["image_id"]]);
        }
        if ($_POST["dislike"]){
        $stmt = $this->conn->prepare($sqlb);
        $stmt->execute([$dislikes, $this->pic[$_SESSION['gallary']]["image_id"]]);
        }
    }
}	


class get_comment_data extends get_pictures_from_database {
    public $comment;

    function __construct ($dsn, $user, $password){
        parent::__construct($dsn, $user, $password);
        $sql = "SELECT * FROM user_database.comments WHERE image_link=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->pic[$_SESSION['gallary']]["image_id"]]);
        $this->comment = $stmt->fetchAll();
    }
    
    function add_comment (){
        $sql = "INSERT INTO user_database.comments (userlink, image_link, comment) VALUE (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_SESSION['user_loged_in'], $this->pic[$_SESSION['gallary']]["image_id"], $_POST["comments"]]);
        $sql = "SELECT * FROM user_database.accounts WHERE UserName=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$this->pic[$_SESSION['gallary']]["userlink"]]);
        $user_info = $stmt->fetch();
        if ($user_info && $user_info["notify"] == "true"){
            $comment = wordwrap($_POST["comments"] , 70);
            mail($user_info["Email"], $_SESSION['user_loged_in']." commented on you picture", $comment);
        }
    }
}

class new_account extends connection {
    private $patten = "/[a-zA-Z0-9]+@[a-zA-Z-0-9]+.[a-zA-Z-0-9]+/";
    public $clear;
    public $f_val = 1;
    public $l_val = 1;
    public $e_val = 1;
    public $u_val = 1;
    public $p_val = 3;
    public $s_val = 0;

    function add_account (){
        $hash_pass = password_hash($_POST['password1'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO user_database.unverified (LastName, FirstName,
        Email, UserName, `PassWord`, verify_state, time_stamp) VALUE (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['lname'],$_POST['fname'],$_POST['email'],$_POST['s_username'],$hash_pass,"not_verified", (time() + 1800)]);
        setcookie("TechCOM", $_POST['email'], time() + 1800, "/");
    }

    function user_cmp()
    {
        $sql = "SELECT UserName, Email
        FROM user_database.accounts
        WHERE UserName= ? OR Email= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['s_username'], $_POST['email']]);
        $user = $stmt->fetch();
        if ($user && strcmp($_POST['s_username'], $user['UserName']) == 0)
        {
            $this->u_val = -1;
        }
        if ($user && strcmp($_POST['email'], $user['Email']) == 0)
        {
            $this->e_val = -1;
        }
        $sql = "SELECT username, Email
        FROM user_database.unverified
        WHERE UserName= ? OR Email= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['s_username'], $_POST['email']]);
        $user = $stmt->fetch();
        if ($user && strcmp($_POST['s_username'], $user['UserName']) == 0)
        {
            $this->u_val = -1;
        }
        if ($user && strcmp($_POST['email'], $user['Email']) == 0)
        {
            $this->e_val = -1;
        }
    }

    function is_val(){
        $this->f_val = preg_match( "/[a-zA-Z]+/", $_POST['fname']);
        $this->l_val = preg_match( "/[a-zA-Z]+/", $_POST['lname']);
        $this->e_val = preg_match( $this->patten, $_POST['email']);
        $this->u_val = preg_match( "/[a-zA-Z0-9]+/", $_POST['s_username']);
        $p_cap_val = preg_match( "/[A-Z]+/", $_POST['password1']);
        $p_low_val = preg_match( "/[a-z]+/", $_POST['password1']);
        $p_num_val = preg_match( "/[0-9]+/", $_POST['password1']);
        $this->p_val = $p_cap_val + $p_low_val + $p_num_val;
        $this->s_val = strcmp($_POST['password1'], $_POST['password2']);
        $this->user_cmp();
        if ($this->f_val == 1 && $this->l_val == 1 && $this->e_val == 1 &&
            $this->p_val == 3 && $this->u_val == 1 && $this->s_val == 0 ){
                $this->clear = 1;
                $this->add_account();
                return (1);
            }
            $this->clear = 0;
        return (0);
    }
    function varify_login (){

        $sql = "SELECT `PassWord`, Email FROM user_database.accounts WHERE UserName= ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$_POST['username']]);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['password'], $user['PassWord'])){
            $_SESSION['user_loged_in'] = $_POST['username'];
            $sql = "DELETE  FROM user_database.forgot WHERE Email=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user['Email']]);    
            return 1;
        }
        else {
            return 0;
        }
    }
}
?>
