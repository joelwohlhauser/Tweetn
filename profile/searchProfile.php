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
  <title>Tweetn - Search User</title>
  <link rel="stylesheet" href="../stylesheets/profile.css">

  <?php include '../htmlContainer/headerLinks.php';?>
</head>
<body>

  <!-- Navbar -->
  <?php include '../htmlContainer/navbar.php'; ?>

  <div class="profileContainer">
    <form action="" method="POST">
      <center>
        <input id="searchInput" type="text" name="search" placeholder="Search..." autofocus>
      </center>
    </form>
    <?php
      if (isset($_POST["search"])) {
        $searchedUsers = array();
        $searchedUsers = SearchUser($_POST["search"], $_SESSION["username"]);

        // output following profiles
        if ($searchedUsers != NULL) {
          echo "<table class='searchResults'>";
          for ($i=0; $i < count($searchedUsers); $i++) {
            echo '<tr>';
            if (strlen($searchedUsers[$i][0]) > 5) {
              // with avatar
              echo '<td><a href="../profile/otherProfile.php?username='.$searchedUsers[$i][1].'">' .
                   '<img src="../images/avatars/'.$searchedUsers[$i][0].'">' .
                   '<span>' . $searchedUsers[$i][1] . '</span></a></td>';
            } else {
              // without avatar
              echo '<td><a href="../profile/otherProfile.php?username='.$searchedUsers[$i][1].'">' .
                   '<img src="../images/defaultAvatar.png">' .
                   '<span>' . $searchedUsers[$i][1] . '</span></a></td>';
            }
            echo "</tr>";
          }
          echo "</table>";
        } else {
          echo "<center style='margin-top: 10%'>No user found</center>";
        }
      }
    ?>
  </div>

</body>
</html>
