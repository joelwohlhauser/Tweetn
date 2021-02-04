<?php
session_start();
$_SESSION["fileError"] = null;

// if not loged in -> to Login
if (!isset($_SESSION["username"])) {
  header("Location: ../authentication/login.php");
  die();
}

require_once('../database/functions.php');

// get user data
$userData = GetUserClass($_SESSION["username"]);

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tweetn - Profile</title>
  <link rel="stylesheet" href="../stylesheets/profile.css">
  <link rel="stylesheet" href="../stylesheets/personalTimeline.css">
  
  <?php include '../htmlContainer/headerLinks.php';?>
</head>
<body>

  <!-- Navbar -->
  <?php include '../htmlContainer/navbar.php'; ?>

  <div class="profileContainer">

    <div class="left">
      <?php
        if (strlen($userData->userAvatar) > 5) {
          echo '<img class="avatar" src="../images/avatars/'.$userData->userAvatar.'">';
        } else {
          echo '<img class="avatar" src="../images/defaultAvatar.png">';
        }
      ?>
    </div>

    <div class="right">
      <h2 class="name"><?php echo $_SESSION["username"] ?></h2>
      <br>
      <center>
        <!-- user data -->
        <table>
          <?php if ($userData->userEmail != null) {
            echo "<tr><td>Email: </td><td>". $userData->userEmail ."</td></tr>";
          } ?>
          <?php if ($userData->userBio != null) {
            echo "<tr><td>Bio: </td><td>". base64_decode($userData->userBio) ."</td></tr>";
          } ?>
          <?php if ($userData->userBirthday != null) {
            echo "<tr><td>Birthday: </td><td>". $userData->userBirthday ."</td></tr>";
          } ?>
          <?php if ($userData->userJoined != null) {
            echo "<tr><td>Joined: </td><td>". $userData->userJoined ."</td></tr>";
          } ?>
        </table>
      </center>
    </div>

    <div class="toolbar">
      <a href="editProfile.php"><button>Edit Profile</button></a>
      <a href="../authentication/login.php"><button style="float:right">Log out</button></a>
    </div>

    <div class="timelineContainer">
      <div class="container-main">
        <?php
          // personal timeline
          $posts = GetPersonalPosts($userData->userName);
          if ($posts != null) {
            for ($i=0; $i < count($posts); $i++) {
              echo "<div class='container-bullets'>
                      <div class='bullet'></div>
                      <div class='vertical-line'></div>
                      <div class='vertical-line'></div>
                    </div>";
              echo "<div class='container-content'>";
                echo "<p class='timeline-username'>" . $posts[$i][1] . ":</p>";
                echo "<p class='timeline-content'>" . base64_decode($posts[$i][0]) . "</p>";
                echo "<p class='timeline-time'>" . $posts[$i][2] . "</p>";
              echo "</div>";
            }
          } else {
            echo "<center>Looks like you haven't written a post yet...</center>";
            echo "<style>
            		.container-main {
						grid-template-columns: auto !important;
						padding-bottom: 70px;
				 }</style>";
          }
        ?>
      </div>
    </div>

  </div>



</body>
</html>
