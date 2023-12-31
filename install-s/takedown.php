<?php

$SERVER_URL = $_SERVER['SERVER_NAME'];

if($SERVER_URL==""){
    echo "Server URL error";
    return;
}

define("ACCESS_SECURITY","true");
include '../api.'.$SERVER_URL.'/security/constants.php';
include '../api.'.$SERVER_URL.'/security/config.php';
include '../api.'.$SERVER_URL.'/security/auth_secret.php';

if (isset($_POST['submit'])){
    
    $inp_starter_token = $_POST['inp_starter_token'];
    
    if($inp_starter_token!=""){
        
      $authObj = new AuthSecret("STARTER",$inp_starter_token);
      $auth_secret = $authObj -> validateSimpleKey();

      if($auth_secret!="true"){
        echo "<script>alert('Invalid Starter Token!!');</script>";
      }else{
          
        function delTree($dir){
          if (is_dir($dir)) {
           $files = array_diff(scandir($dir), array('.', '..')); 

           foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
           }

           return rmdir($dir);
          }else{
           return 3;
          }
        }
    
        if(delTree('../api.'.$SERVER_URL)==1){
          echo "<script>alert('Destroyed!!');</script>";
        }else{
          echo "<script>alert('Already Destroyed!!');</script>";
        }
          
      }
    }else{
        echo "<script>alert('Invalid Starter Token!!');</script>";
    }

}

?>
<!DOCTYPE html>
<html lang="en" translate="no">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<title>Takedown</title>
<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'poppins', sans-serif;
    }
      
      .resp-w{
          width: 550px;
      }
      
      .w-100{
          width: 100%;
      }
      
      .col-view{
        display: flex;
        justify-content: center;
        flex-direction: column;
      }
      
      .row-view{
        display: flex;
        align-items: center;
        justify-content: center;
      }
      
      .a-center{
          align-items: center;
      }
      
      .sb-view{
         justify-content: space-between !important;
      }
      
      .content-view{
          height: 100vh;
          width: 100%;
          background: #1c1c1c;
      }
      
      .cus-inp{
          width: 100%;
          height: 50px;
          padding: 5px 10px;
          color: #FFFFFF;
          font-size: 18px;
          border: 1px solid rgba(255,255,255,0.05);
          background: transparent;
      }
      
      .action-btn{
          color: #fff;
          font-size: 25px;
          font-weight: 700;
          background: #2ca90a;
          text-align: center;
          border-radius: 3px;
          cursor: pointer;
          padding: 10px 0;
          box-shadow: 0 4px rgba(255,255,255,0.3);
          text-shadow: 0px 1px 1px #30662b;
      }
      
      .cl-white{
          color: #FFFFFF !important;
      }
      
      .cl-red{
          color: #E74C3C !important;
      }
      
      .bg-red{
          background: #E74C3C !important;
      }
      
      .bg-green{
          background: #2ca90a !important;
      }
      
      .mg-t-10{
          margin-top: 10px !important;
      }
      
      .mg-t-20{
          margin-top: 20px !important;
      }
      
      .mg-t-30{
          margin-top: 30px !important;
      }
      
      .ft-sz-13{
          font-size: 13px;
      }
      
      .ft-sz-18{
          font-size: 18px;
      }
      
      .ft-sz-25{
          font-size: 25px;
      }
      
      .ft-wgt-b{
          font-weight: bold;
      }
      
      .pd-5-10{
          padding: 5px 10px;
      }
      
      .view-disable{
          filter: grayscale(100%) !important;
      }
      
      @media (max-width: 550px) {
        .resp-w{
          width: 90% !important;
        }
      }
    </style>
</head>
<body>

<div class="content-view col-view a-center">
    
    <div class="col-view a-center resp-w">
        
     <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST" class="mg-t-20 w-100">
      
      <div class="col-view w-100">
        <p class="cl-white ft-sz-18">Starter Token</p>
        <input name="inp_starter_token" type="text" placeholder="Enter Starter Token" class="cus-inp mg-t-10"></input>
      </div>
      
      <br>
      <input name="submit" type="submit" class="action-btn bg-red mg-t-30 w-100" value="Take Down"></input>
      
     </form>
      
    </div>
    
</div>

</body>
</html>