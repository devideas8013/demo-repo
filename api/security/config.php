<?php
if(defined("ACCESS_SECURITY")){
 $is_db_connected = "false";

 // database config
 $hostname_db = "adsexcha_data";
 $username_db = "adsexcha_user";
 $password_db = "@Welcom1234";

 try{
    if ($conn = mysqli_connect("localhost",$username_db, $password_db, $hostname_db ))
    {
        $is_db_connected = "true";
    }
    else
    {
        throw new Exception('Unable to connect');
    }
 }catch (Throwable $e) {
    // Handle error
    echo $e->getMessage();
    echo "Please setup extension properly.";
  }   
    
}else{
 echo "permission denied!";
 return;
}
?>