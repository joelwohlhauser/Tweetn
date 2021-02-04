<?php
session_start();

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
  <title>Tweetn - Edit Profile</title>
  <link rel="stylesheet" href="../stylesheets/profile.css">

  <?php include '../htmlContainer/headerLinks.php';?>
</head>
<body>

  <!-- Navbar -->
  <?php include '../htmlContainer/navbar.php'; ?>

  <div class="profileContainer">

    <form method="POST" action="upload.php" enctype="multipart/form-data" id="editForm">

    <!-- Avatar -->
    <div class="left">
      <center>
      <?php
      if (strlen($userData->userAvatar) > 5) {
        echo '<img class="avatar" src="../images/avatars/'. $userData->userAvatar .'">';
      } else {
        echo '<img class="avatar" src="../images/defaultAvatar.png">';
      }
      ?>
      <input type="file" name="uploadedFile"><br>
      <?php
        if (isset($_SESSION["message"])) {
          echo "<p style=color:#b53f3f>" . $_SESSION["message"] . "</p>";
          unset($_SESSION['message']);
        }
      ?>
      <br>
      </center>
    </div>

    <!-- User details -->
    <div class="right" id="editUser">
      <h2 class="name"><?php echo $_SESSION["username"] ?></h2>
      <br>
        <table>
          <tr><td>Email: </td><td>
            <input type="email" name="email" value="<?php echo $userData->userEmail; ?>">
          </td></tr>
          <tr><td>Bio: </td><td>
            <textarea name="bio" style="height:120px"><?php echo base64_decode($userData->userBio); ?></textarea>
          </td></tr>
          <tr><td>Birthday: </td><td>
            <input type="date" name="bday" value="<?php echo $userData->userBirthday; ?>"
                   max="<?php echo date('Y-m-d', strtotime('-16 years')); ?>">
          </td></tr>
        </table>
    </div>

    </form>

    <div class="toolbar">
      <button form="editForm" type="submit" name="uploadBtn">Save Profile</button>
      <a href="myProfile.php"><button>Cancel</button></a>
    </div>

  </div>

</body>
</html>
