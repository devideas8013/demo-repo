<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

 session_start();
 
 if (!isset($_SESSION["pb_admin_user_id"])) {
  header('location:../index.php');
 }
 
 if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
 }else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
 }
 
 if (in_array("access_settings", $account_access_arr)){
 }else{
  echo "You're not allowed! Please grant the access.";
  return;
 }

 $searched="";
 if (isset($_POST['submit'])){
   $searched = $_POST['searched'];
 }

 $content = 30;
 if (isset($_GET['page_no'])){
 	$page_no = $_GET['page_no'];
 	$offset = ($page_no-1)*$content;
 }else{
 	$page_no = 1;
 	$offset = ($page_no-1)*$content;
 }
?>

<!DOCTYPE html>
<html>
<head>
<?php include "../../components/header.php"; ?>
<title>Admin: Game Settings</title>

<style>
*{
	margin: 0;
  padding: 0;
  box-sizing: border-box;
	font-family: 'poppins',sans-serif;
}
.main{
	padding: 10px;
}
.head{
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	padding: 10px;
	width: auto;
	border-bottom: 1px dashed #000000;
}
         
#data{
  border-collapse: collapse;
  width: 100%;
  margin-top: 25px;
}

#title{
  font-weight: bold;
}

#data td, #data th{
  border: 1px solid #ddd;
  padding: 8px;
}
#data td a{
    text-decoration: none;
    padding: 8px 10px;
    color: #ffffff;
    display: inline-block;
    background-color: <?php echo $ADMIN_COLOR; ?>;
}

#data tr:nth-child(even){
    background-color: #f2f2f2;
  }
#data tr:hover{
  background-color: #ddd;
}

#data tr td:nth-child(3){
  max-width: 200px;
  overflow: hidden;
  white-space: nowrap;
}
#data th{
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    color: #ffffff;
    background-color: <?php echo $ADMIN_COLOR; ?>;
}

.view{
    height: 0.5px;
    width: 100%;
    background-color:rgb(207, 202, 202);
}

#action_btn{
    padding: 10px;
    display: inline-block;
    text-decoration: none;
    margin: 5px 0 10px 0;
    color: #ffffff;
    font-size: 18px;
    border-radius: 5px;
    margin-top: 10px;
    background-color: <?php echo $ADMIN_COLOR; ?>;
}

.item{
    display: flex;
    align-items: center;
}

.info p{
    font-size: 1.1em;
}

.nxt_pre_btn a{
    text-decoration: none;
    padding: 5px 10px;
    color: #fff;
    display: inline-block;
    border-radius: 10px;
    margin-left: 10px;
    background-color: <?php echo $ADMIN_COLOR; ?>;
}
form{
  width: 100%;
  display: flex;
  flex-direction: column;
  margin-top: 10px;
}
form input{
  width: 100%;
  padding: 10px;
  font-size: 18px;
}
form .action_btn{
  height: auto;
  width: 180px;
  color: #fff;
  outline: none;
  border: none;
  cursor: pointer;
  border-radius: 5px;
  margin-top: 10px;
  background-color: <?php echo $ADMIN_COLOR; ?>;
}
.main .scripts-view{
  background: #F4ECF7 !important;
}

</style>

</head>
<body>

<div class="main">
    
  <h2><a href="../../admin/dashboard.php"><i class='bx bx-grid-alt'></i></a>&nbsp;Game Settings</h2>

  <table id="data">
	<tr>
		<th>Manage</th>
		<th>Service Name</th>
		<th>Service Value</th>
	</tr>

    <?php
      if($searched!=""){
        $sql = "SELECT * FROM allservices where service_name like '%$searched%' or service_value like '%$searched%'";
      }else{
        $sql = "SELECT * FROM allservices ORDER BY id DESC LIMIT {$offset},{$content}";
      }
        
      $result = mysqli_query($conn, $sql) or die('search failed');
      if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){ ?>
      <tr>
       <td><a href="edit-manager.php?service-id=<?php echo $row['id']; ?>" id="edit_btn">Manage</a></td>
       <td><?php echo $row['service_name'] ?></td>
       <td><?php echo $row['service_value'] ?></td>
	  </tr>
      <?php } }else{ ?>
        <tr>
		  <td>No Data Found!</td>
		  <td></td>
		  <td></td>
		</tr>
      <?php } ?>

	</table><br>
	
 </div>

</body>
</html>