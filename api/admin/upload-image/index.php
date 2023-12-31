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
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
<title>Admin: All Images</title>

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
.head{
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	padding: 10px;
	width: auto;
	border-bottom: 1px dashed #000;
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
    background: r<?php echo $ADMIN_COLOR; ?>;
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

.action_btn{
    padding: 10px;
    display: inline-block;
    text-decoration: none;
    color: #ffffff;
    font-size: 18px;
    border-radius: 5px;
    background: <?php echo $ADMIN_COLOR; ?>;
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
    font-size: 18px;
    background:  <?php echo $ADMIN_COLOR; ?>;
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

/* #data td{
  display: flex;
  align-items: center;
} */

td .color_box{
  height: 15px;
  width: 15px;
  border-radius: 50%;
}

td .red_box{
  background: #EC7063;
}

td .green_box{
  background: #52BE80;
}

td .violet_box{
  background: #A569BD;
}

table #match_loss_tv{
  color: #FFFFFF;
  padding: 2px 8px;
  border-radius: 5px;
  background: #E74C3C;
}

table #match_won_tv{
  color: #FFFFFF;
  padding: 2px 8px;
  border-radius: 5px;
  background: #27AE60;
}
#img_uploaded_tv{
    color: green;
    margin-top: 10px;
    display: none;
}

</style>

</head>
<body>

<div class="main">
    
  <h2><i class='bx bx-grid-alt'></i>&nbsp;All Images</h2>
  </br>
  <a id="upload_image_btn" class="action_btn">Upload Image</a>  
  <input type="file" name="selected_image" id="inp_selected_image" accept="image/*" hidden></input>
  <p id="img_uploaded_tv">Image uploaded!</p>

  <table id="data">
	<tr>
		<th>Action</th>
		<th>Image URL</th>
		<th>Uploaded</th>
	</tr>

    <?php
      $sql = "SELECT * FROM allimages ORDER BY id DESC LIMIT {$offset},{$content}";
        
      $result = mysqli_query($conn, $sql) or die('search failed');
      if (mysqli_num_rows($result) > 0){
        while ($row = mysqli_fetch_assoc($result)){ ?>
        <tr>
         <td><a onclick="deleteImg('<?php echo $row['imageid'] ?>')" class="action_btn">Delete</a></td>
         <td><?php echo 'https://'.$MAIN_DOMAIN_URL.'/storage/images/'.$row['imageid'] ?></td>
         <td><?php echo $row['upload_date_time'] ?></td>
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
      $sql1 = "SELECT * FROM allimages";
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
	
  <br><br>
 </div>
 
<script>
let inp_selected_image = document.querySelector("#inp_selected_image");
let upload_image_btn = document.querySelector("#upload_image_btn");
let img_uploaded_tv = document.querySelector("#img_uploaded_tv");
let ComImgQuality = 98;

inp_selected_image.addEventListener("change", handleFiles, false);
function handleFiles() {
   img_uploaded_tv.style.display = "none";
    SetImageData(this.files[0]);
}

upload_image_btn.addEventListener("click", ()=>{
    inp_selected_image.click();
});

const SetImageData = (file) => {
  img_uploaded_tv.style.display = "block";
  img_uploaded_tv.innerHTML = "please wait....";
  upload_image(file);
}

const upload_image = (file) => {
  let fd = new FormData();
  fd.append("ImgURL", file);
  fd.append("ImgQuality", ComImgQuality);

  fetch('upload-image.php',{
      method: 'POST',
      body: fd
  })
  .then((response) => response.json())
  .then((data)=>{
    if(data[0] == "404" || data[0] == "wrong" || data[0] == "false"){
      img_uploaded_tv.style.display = "block";
      img_uploaded_tv.innerHTML = "Failed!";
      alert("Failed to upload files!");
      return;
    }else{
      img_uploaded_tv.style.display = "block";
      img_uploaded_tv.innerHTML = "Image Uploaded!";
    }
      
  }).catch((error)=>{
    alert("Failed to upload files!");
    img_uploaded_tv.style.display = "block";
    img_uploaded_tv.innerHTML = "Failed!";
    return;
  });
  
}

function deleteImg(id){
    if (confirm("Are you sure want to delete?") == true) {
      window.open('delete-image.php?imageid='+id);
    }
}

</script>

</body>
</html>

</body>
</html>