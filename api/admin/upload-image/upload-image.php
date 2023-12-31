<?php 
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';
 
session_start();
 
if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_investments", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}
 
$ResponceArray = []; 
$result="Empty";

$ImgQuality = json_decode($_POST['ImgQuality']);

function compressImage($source, $destination, $quality) { 
    // Get image info 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
     
    // Create a new image from file 
    switch($mime){ 
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            break; 
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            break; 
        default: 
            $image = imagecreatefromjpeg($source); 
    } 

    // Save image 
    imagejpeg($image, $destination, $quality); 
     
    // Return compressed image 
    return $destination; 
} 
 
 function generateRandomString($length = 25) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$upload_folder = '../../../storage/images/';
 
if(isset($_FILES['ImgURL'])){
    $image_quality = 50;
    if($ImgQuality != ""){
        $image_quality = $ImgQuality;
    }
 
    if(!empty($_FILES["ImgURL"]["name"])) { 
        // File info 
        $fileName = basename($_FILES["ImgURL"]["name"]); 
        $imageUploadPath = $upload_folder . $fileName; 
        $fileType = pathinfo($imageUploadPath, PATHINFO_EXTENSION); 
         
        $randomID =  generateRandomString();
        $upload_destination = $upload_folder . $randomID . '.' . $fileType;

        // Allow certain file formats 
        $allowTypes = array('jpg','png','jpeg','gif'); 
        if(in_array($fileType, $allowTypes)){ 
            // Image temp source 
            $imageTemp = $_FILES["ImgURL"]["tmp_name"]; 
             
            // Compress size and upload image 
            $compressedImage = compressImage($imageTemp, $upload_destination, $image_quality); 
             
            if($compressedImage){ 
                // save into database
                date_default_timezone_set('Asia/Kolkata');
                $upload_time = date('d-m-Y h:i a');
                $temp_image_url = $randomID.'.'.$fileType;
                $sql = "INSERT INTO allimages(imageid,upload_date_time) VALUES('{$temp_image_url}','{$upload_time}')";
                $sql_result = mysqli_query($conn, $sql);

                $result = $compressedImage;
            }else{ 
                $result = "false";
            } 
        }else{ 
            $result = "wrong";
        } 
    }else{ 
        $result = "404"; 
    } 

    array_push($ResponceArray, $result);
   
    echo json_encode($ResponceArray);
}  

?>