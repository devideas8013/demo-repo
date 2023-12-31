<?php
header("Cache-Control: no cache");
session_cache_limiter("private_no_expire");

define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

if(!isset($_GET['user-id'])){
  echo "request block";
  return;
}else{
  $user_id = mysqli_real_escape_string($conn,$_GET['user-id']);
}

session_start();
 
if (!isset($_SESSION["pb_admin_user_id"])) {
  header('location:../index.php');
}else{
  $session_id = $_SESSION["pb_admin_user_id"];
}
 
if (!isset($_SESSION["pb_admin_category"])) {
  header('location:../index.php');
}else{
  $account_category = $_SESSION["pb_admin_category"];
}
 
if (!isset($_SESSION["pb_admin_access"])) {
  header('location:../index.php');
}else{
  $account_access = $_SESSION["pb_admin_access"];
  $account_access_arr = explode (",", $account_access);
}
 
if (in_array("access_users_data", $account_access_arr)){
}else{
  echo "You're not allowed! Please grant the access.";
  return;
}

$searched="";
if (isset($_POST['submit'])){
   $searched = $_POST['searched'];
}

$content = 15;
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
<title>Admin: All Refers</title>

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
#data td label{
  padding: 3px 8px;
  background: #2ECC71;
  margin-left: 5px;
  color: #FFFFFF;
  border-radius: 5px;
}
#data .approved_order{
  background: #ABEBC6 !important;
}
#data td a{
  text-decoration: none;
  padding: 8px 10px;
  color: #ffffff;
  display: inline-block;
  background: <?php echo $ADMIN_COLOR; ?>;
}

#data tr:nth-child(even){
  background-color: #f2f2f2;
}
#data tr:hover{
  background-color: #ddd;
}
#data th{
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  color: white;
  background-color: <?php echo $ADMIN_COLOR; ?>;
}

.view{
  height: 0.5px;
  width: 100%;
  background-color:rgb(207, 202, 202);
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
  color: #ffffff;
  display: inline-block;
  border-radius: 10px;
  margin-left: 10px;
  background: <?php echo $ADMIN_COLOR; ?>;
}

form{
  width: 100%;
  display: flex;
  flex-direction: column;
  margin-top: 10px;
}
form #in_search_bar{
  width: 100%;
  padding: 10px;
  font-size: 18px;
}

.control_btn{
  height: 40px;
  color: #ffffff;
  outline: none;
  border: none;
  cursor: pointer;
  font-size: 18px;
  border-radius: 5px;
  padding: 5px 16px;
  background-color: <?php echo $ADMIN_COLOR; ?>;
}

.secondary_color{
  background: #34495E;
}

form .filter_options{
  background: rgba(0,0,0,0.05);
  margin-top: 10px;
  line-height: 30px;
  padding: 10px;
  -webkit-user-select: none; /* Safari */
  -ms-user-select: none; /* IE 10 and IE 11 */
  user-select: none; /* Standard syntax */
}
form .filter_options label{
  cursor: pointer;
  margin-left: 5px;
}

.hide_view{
  display: none;
  overflow: hidden;
}

</style>

</head>
<body>

<div class="main">
    
  <h2><i class='bx bx-grid-alt'></i>&nbsp;Refers Data</h2>
  <p style="margin-top: 5px;">Users Under: <span style="color: green;font-weight:bold;"><?php echo $user_id; ?></span></p>
  <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
    <input type="text" name="searched" placeholder="Search User ID,Mobile Number,Balance" id="in_search_bar">
    <br>
    <div>
      <input type="submit" name="submit" class="control_btn" value="Search Records">
    </div>
  </form>

  <table id="data">
	<tr>
		<th>User Id</th>
		<th>Manage</th>
		<th>Name</th>
		<th>Mobile</th>
		<th>Balance</th>
		<th>Last Active</th>
	</tr>

    <?php
      if($searched!=""){
        $sql = "SELECT * FROM usersdata where user_refered_by = '$user_id' AND (uniq_id like '%$searched%' or user_mobile_num like '%$searched%' or user_balance like '%$searched%') ";
      }else{
        $sql = "SELECT * FROM usersdata WHERE user_refered_by = '$user_id' ORDER BY id DESC LIMIT {$offset},{$content}";
      }
      
      
        
      $result = mysqli_query($conn, $sql) or die('search failed');
      if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){ 
      
      if($row['user_status']=="ban"){ ?>
        <tr style="background: #FADBD8;">
      <?php }else{ ?> 
        <tr>
      <?php } ?>
	   <td><?php echo $row['uniq_id'] ?></td>
       <td><a href="edit-manager.php/?access-code=<?php echo $admin_acccess; ?>&user-id=<?php echo $row['uniq_id']; ?>" id="edit_btn">Manage</a></td>
       <td><?php echo $row['user_full_name']; ?></td>
       <?php if($row['account_level']=="2"){ ?>
          <td><?php if($account_category!="admin"){ echo "XXXXXXXXXX"; }else{ echo $row['user_mobile_num']; } ?><label>Premium</label></td>
       <?php }else{ ?>
          <td><?php if($account_category!="admin"){ echo "XXXXXXXXXX"; }else{ echo $row['user_mobile_num']; } ?></td>
       <?php } ?>
       <td><?php echo $row['user_balance']; ?></td>
       <td><?php echo $row['user_last_active_date'].' '.$row['user_last_active_time'] ?></td>
			</tr>
      <?php } }else{ ?>
        <tr>
		 <td>No Data Found!</td>
		 <td></td>
		 <td></td>
	     <td></td>
		</tr>
      <?php } ?>

	</table><br>
	
	<div class="item">
    <?php
      $sql1 = "SELECT * FROM usersdata WHERE user_refered_by = '$user_id'";
      $result1 = mysqli_query($conn, $sql1) or die('fetch failed');

      if (mysqli_num_rows($result1) > 0) {
        $total_post = mysqli_num_rows($result1);
        $total_page = ceil($total_post/ $content);
        ?>
        <div class="info">
          <p><?php echo $page_no; ?> / <?php echo $total_page; ?></p>
        </div>

        <div class="nxt_pre_btn">
          <?php
            if ($page_no > 1) {
              echo '<a onclick="window.history.back()">Previous</a>';
            }
            $no = $page_no + 1;
            if ($page_no != $total_page) {
                echo '<a href="?page_no='.$no.'&access-code='.$admin_acccess.'">Next</a>';
            }
      }
    ?>
    </div>
  </div>
	
 </div>

</body>
</html>