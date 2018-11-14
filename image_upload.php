<?php

// echo "<br />",strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION)),"<br />";
// print_r($_FILES["fileToUpload"]);
// echo "<br />";
// print_r(getimagesize($_FILES["fileToUpload"]["tmp_name"]));

// jpg
// [name] => funny-fake-illness-awkward-moment-seal.jpg 
// [type] => image/jpeg 
// [tmp_name] => /goinfre/kmaputla/Desktop/database/php/tmp/php1unoXP 
// [error] => 0 
// [size] => 57065  
// [0] => 474 
// [1] => 347 
// [2] => 2 
// [3] => width="474" height="347" 
// [bits] => 8 
// [channels] => 3 
// [mime] => image/jpeg 

include "connection.php";

class Image_upload extends connection{
    private $image;
    public $errors = 0;
    
    function verify_image (){
        if (is_uploaded_file($_FILES['fileToUpload']['tmp_name']) && $image = getimagesize($_FILES["fileToUpload"]["tmp_name"]) &&
        $_FILES["fileToUpload"]["size"] < 500000 && $_FILES["fileToUpload"]["error"] == 0){
            $file_type = strtolower(pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION));
            if ($file_type == 'jpeg' || $file_type == 'png' || $file_type == 'jpg' || $file_type == 'gif'){
                
            }$this->$errors = 2;
        }
        else{
            $this->$errors = 1;
        }
    }

    function upload_image (){
        // $image = file_get_contents($target_file);
        // $image = base64_encode($image);
        // echo $image;
        // $connect = new upload_file ($dsn, $user, $password);
        // $connect->upload_pic($image);
        $sql = "INSERT INTO user_database.media (userlink ,likes ,unlikes, image_src) VALUE (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['picture', 0, 0, $target_file]);
    }   
}



function base64_to_jpeg($base64_string, $output_file) {
    // open the output file for writing
    $ifp = fopen( $output_file, 'wb' ); 

    // split the string on commas
    // $data[ 0 ] == "data:image/png;base64"
    // $data[ 1 ] == <actual base64 string>
    $data = explode( ',', $base64_string );

    // we could add validation here with ensuring count( $data ) > 1
    fwrite( $ifp, base64_decode( $data[ 1 ] ) );

    // clean up the file resource
    fclose( $ifp ); 

    return $output_file; 
}
?>
