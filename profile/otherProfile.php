<?php
session_start();

// if not loged in -> to Login
if (!isset($_SESSION["username"])) {
  header("Location: ../authentication/login.php");
  die();
}

require_once('../database/functions.php');

// get user data
$userData = GetUserClass($_GET["username"]);

if ($userData == false) {
  header("Location: ../profile/searchProfile.php");
  die();
}

// Follow/Unfollow Button
if (isset($_POST["follow"])) {
  NewRelationship($_SESSION["username"], $userData->userName);
}
if (isset($_POST["unfollow"])) {
  BreakRelationship($_SESSION["username"], $userData->userName);
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tweetn - other Profile</title>
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
      <h2 class="name"><?php echo $userData->userName ?></h2>
      <br>
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
    </div>

    <div class="toolbar">
      <form action="" method="post">
        <!-- Follow/Unfollow button -->
        <?php
          if (CheckRelationship($_SESSION["username"], $userData->userName)) {
            // follows
            echo '<button type="submit" name="unfollow" style="float:right">Unfollow</button>';
          }
          else {
            // follows not
            echo '<button type="submit" name="follow" style="float:right">Follow</button>';
          }
        ?>
      </form>
    </div>

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
            // error message
            echo "<center>".$userData->userName." hasn't written a post yet</center>";
            echo "<style>
            		.container-main {
						grid-template-columns: auto !important;
						padding-bottom: 70px;
				 }</style>";
          }
        ?>
      </div>

  </div>

</body>
</html>
