<?php
if(isset($_GET['title']) && isset($_GET['message'])){
  require_once("../../security/constants.php");
  require_once("../../services/send-notification-to-users.php");
  sendNotification($_GET['title'],$_GET['message'],$MESSAGE_TOKEN) ?>
    <script>
      alert("Notification sended!");
      window.close();
    </script>
 <?php } ?>
