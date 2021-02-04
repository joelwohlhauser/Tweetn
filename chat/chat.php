<?php
session_start();
$_SESSION["fileError"] = null;

// if not loged in -> to Login
if (!isset($_SESSION["username"])) {
  header("Location: ../authentication/login.php");
  die();
}

// include
require_once('../database/functions.php');
require_once('../crypt/CryptFunctions.php');

// get user data
$userData = GetUserClass($_SESSION["username"]);

// send message
if (isset($_POST["message"]) && isset($_POST["selectedUsers"])) {
  if ($_POST["message"] != "") {
    $saveResults = SaveTextMessage($_SESSION["username"], $_POST["selectedUsers"], $_POST["message"], $_SESSION["privateKey"]);
    if ($saveResults != NULL) {
      echo "Encryption failed";
    }
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Tweetn - Chat</title>
  <link rel="stylesheet" href="../stylesheets/chat.css">

  <?php include '../htmlContainer/headerLinks.php';?>

  <!-- ajax jquery -->
  <script src="../scripts/jquery.min.js"></script>
</head>
<body>

  <!-- Navbar -->
  <?php include '../htmlContainer/navbar.php'; ?>

  <div class="profileContainer">
    <div class="form">


    	<form action="" method="post" id="usersForm">
        <div class="left">

          <!-- Sidebar -->
          <div class="s-layout__sidebar">
            <a class="s-sidebar__trigger" href="#0">
               <span class="verticalMenu">Chats</span>
            </a>

            <nav class="s-sidebar__nav">
              <?php
                $followers = array();
                $followers = GetAllFollowersToChat($_SESSION["username"]);

                // output following profiles
                if ($followers != NULL) {
                  echo "<ul class='chatUsers'>";
                  echo "<h2>Users</h2><hr><br>";
                  for ($i=0; $i < count($followers); $i++) {
                    // selected User?
                    $userColor = "grey";
                    if (isset($_POST["selectedUsers"])) {
                      if ($followers[$i] == $_POST["selectedUsers"]) {
                        $userColor = "#162c3d";
                      }
                    }

                    echo "<li>";
                      echo "<input type='radio' id='Css" . $followers[$i] . "' value='" . $followers[$i] . "'
                            name='selectedUsers'>";
                      echo "<label style='color:" . $userColor . " !important'
                            for='Css" . $followers[$i] . "'>" . $followers[$i] . "</label>";
                    echo "</li>";
                  }
                  echo "</ul>";
                } else {
                  echo "<p style='padding: 20px'>Follow a user to chat</p>";
                }
              ?>
            </nav>
          </div>

          <input type="submit" for="usersForm" style="display:none">
          </form>
        </div>
        <div class="right">
          <form action="" method="post" id="chatForm">
          <div class="chatbox" id="chatbox">
            <!-- messages chatbox (in chatbox.php) -->
          </div>
          <div class="writeMessageBar">
          <input type="text" name="message" autocomplete=off autofocus>
          <?php
              if (isset($_POST["selectedUsers"])) {
                echo '<input type="text" style="display:none" id="refreshThis" name="selectedUsers" value="'.$_POST["selectedUsers"].'">';
              }
              else {
                echo '<input style="display:none" id="refreshThis" value="showNoUserSelectedError">';
              }
            ?>
          <input type="submit" name="send" value="Send">
        </div>
        </form>
        </div>
    	</form>
    </div>

  </div>


</body>
<script type="text/javascript">

// autorefresh chatbox
(function worker() {
  $.ajax({
    url: 'chatbox.php?selectedUsers=' + document.getElementById('refreshThis').value,
    success: function(data) {
      $('#chatbox').html(data);
    },
    complete: function() {
      setTimeout(worker, 100);
    }
  });
})();


function scrollDown(){
    // chatbox scroll down
    var elem = document.getElementById('chatbox');
    elem.scrollTop = elem.scrollHeight;
}

// get selected User
function isOneChecked(){
    var radios = document.getElementsByName("selectedUsers");
    for (var i = 0, len = radios.length; i < len; i++) {
         if (radios[i].checked) {
             return true;
         }
    }
    return false;
}
setInterval(function() {
  var result;
  result = isOneChecked();
  if (result != false) {
    document.forms["usersForm"].submit();
  }
}, 100);


setTimeout(function() { scrollDown(); }, 150);

</script>
</html>
