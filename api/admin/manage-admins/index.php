<?php
define("ACCESS_SECURITY","true");
include '../../security/config.php';
include '../../security/constants.php';

 session_start();
 if (!isset($_SESSION["pb_admin_user_id"])) {
  header('location:../index.php');
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
 
 if (in_array("access_admins", $account_access_arr)){
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
<title>Admin: Admins Data</title>

<style>
*{
	margin: 0;
  padding: 0;
  box-sizing: border-box;
	font-family: Arial, Helvetica, sans-serif;
}
.main{
	padding: 10px;
}
.admin-control-view{
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
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
  font-family: Arial, Helvetica, sans-serif;
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
    background-color: <?php echo $ADMIN_COLOR; ?>;
    color: white;
}

.view{
    height: 0.5px;
    width: 100%;
    background-color:rgb(207, 202, 202);
}

.admin-control-view a,#action_btn{
    padding: 10px;
    display: inline-block;
    text-decoration: none;
    color: #ffffff;
    font-size: 18px;
    border-radius: 5px;
    margin-top: 10px;
    background: <?php echo $ADMIN_COLOR; ?>;
}

.admin-control-view button{
    padding: 5px 10px;
    margin: 0px !important;
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
    cursor: pointer;
    background: <?php echo $ADMIN_COLOR; ?>;
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

td label{
    padding: 3px 5px;
    color: #ffffff;
    background: #27AE60;
    margin-left: 5px;
    border-radius: 5px;
}

</style>

</head>
<body>

<div class="main">
  
  <div class="admin-control-view">
     <h2><i class='bx bx-grid-alt'></i>&nbsp;Admins Data</h2>
     <a href="../add-admin">Add Admin</a>   
  </div>

  <form action="<?php $_SERVER['PHP_SELF'] ?>" method="POST">
    <input type="text" name="searched" placeholder="Search User ID,Mobile Number,Balance">
    <input type="submit" name="submit" class="action_btn" value="Search Records">
  </form>

  <table id="data">
	<tr>
		<th>Manage</th>
		<th>Mobile</th>
		<th>Joined</th>
	</tr>

    <?php
      if($searched!=""){
        $sql = "SELECT * FROM adminauth where uniq_id like '%$searched%' or user_id like '%$searched%' ";
      }else{
        $sql = "SELECT * FROM adminauth ORDER BY id DESC LIMIT {$offset},{$content}";
      }
        
      $result = mysqli_query($conn, $sql) or die('search failed');
      if (mysqli_num_rows($result) > 0){
          
        while ($row = mysqli_fetch_assoc($result)){ ?> 
       </tr>
       <td><a href="edit-manager.php/?user-id=<?php echo $row['uniq_id']; ?>" id="edit_btn">Manage</a></td>
       <td><?php if($account_category=="admin"){ echo $row['user_id']; }else{echo "XXXXXXXXXX";} ?></td>

		<td><?php echo $row['date_time']; ?></td>
		</tr>
      <?php } }else{ ?>
        <tr>
		 <td>No Data Found!</td>
		 <td></td>
	     <td></td>
		</tr>
      <?php } ?>

	</table><br>
	
	<div class="item">
    <?php
      $sql1 = "SELECT * FROM adminauth";
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
                echo '<a href="?page_no='.$no.'">Next</a>';
            }
      }
    ?>
    </div>
  </div>
	
 </div>

</body>
</html>