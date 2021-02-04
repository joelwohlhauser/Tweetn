<?php
session_start();
$_SESSION["fileError"] = null;

// if not loged in -> to Login
if (!isset($_SESSION["username"])) {
  header("Location: login.php");
  die();
}

require_once('../database/functions.php');
require_once('welcomePosts.php');

// get user data
$userData = GetUserClass($_SESSION["username"]);

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tweetn - Timeline</title>
  <link rel="stylesheet" href="../stylesheets/posts.css">

  <?php include '../htmlContainer/headerLinks.php';?>
</head>
<body>

  <!-- Navbar -->
  <?php include '../htmlContainer/navbar.php'; ?>

  <div class="timelineContainer">
    <a href="newPost.php" style="text-decoration:none">
      <!-- New post button -->
      <div class="newPostMenu">New Post</div>
    </a>
    <div class="container-main">
      <!-- display post in a timeline -->
      <?php
        $posts = GetPosts($_SESSION["username"]);
        if ($posts != null) {
          for ($i=0; $i < count($posts); $i++) {
            // post-container
            echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
            echo "<div class='container-content'>";
              echo "<p class='timeline-username'>" . $posts[$i][1] . ":</p>";
              echo "<p class='timeline-content'>" . base64_decode($posts[$i][0]) . "</p>";
              echo "<p class='timeline-time'>" . $posts[$i][2] . "</p>";
            echo "</div>";
          }
        } else {
        	// error message
        	echo "<div class='container-bullets'><div class='bullet'></div><div class='vertical-line'></div></div>";
	    	echo "<div class='container-content'>";
	          echo "<p class='timeline-username'>Tweetn:</p>";
	          echo "<p class='timeline-content'>Follow other users to see posts!</p>";
	          echo "<p class='timeline-time'>" . $userData->userJoined . "</p>";
	    	echo "</div>";
        }
        if (count($posts) <= 5) {
        	EchoWelcomePosts($userData->userJoined);
        }
      ?>
    </div>
  </div>

</body>
</html>
