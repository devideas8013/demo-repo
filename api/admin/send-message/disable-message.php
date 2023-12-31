<?php
if(isset($_GET['title']) && isset($_GET['message'])){
  require_once("../../services/send-notification-to-users.php");
?>
    <script>
      alert("Message Disabled!");
      window.close();
    </script>
<?php } ?>
