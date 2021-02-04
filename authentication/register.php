<?php
session_start();

// include
require_once('../database/functions.php');
require_once('../crypt/bcrypt.php');

$message = "";
$style = "style='display:none;'";

$bcrypt = new Bcrypt(15);

// if all infos are posted and can connect to DB
if (isset($_POST['password']) && isset($_POST['password']) && $_POST['password']!="" && $_POST['username']!="") {

  // create public and private
  $keys = GetKeys();

  // encrypt password with one-way hash
  $hashPassword = $bcrypt->hash(stripslashes(htmlspecialchars($_POST['password'])));

  // encrypt privateKey with two-way hash
  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = openssl_random_pseudo_bytes($ivlen);
  $ciphertext_raw = openssl_encrypt($keys['private'], $cipher, $_POST['password'], $options=OPENSSL_RAW_DATA, $iv);
  $hmac = hash_hmac('sha256', $ciphertext_raw, $_POST['password'], $as_binary=true);
  $hashPrivateKey = base64_encode( $iv.$hmac.$ciphertext_raw );

  // try to add the user to the DB
  if (RegisterNewUser(stripslashes(htmlspecialchars(strtolower($_POST['username']))), $hashPassword, $hashPrivateKey, $keys['public'])) {
    $_SESSION["username"] = strtolower($_POST['username']);
    $_SESSION["privateKey"] = $keys['private'];
    // go to profile page
    header("Location: ../profile/myProfile.php");
    die();
  }
  else {
    $message = "Something went wrong...<br>Please change the username and try again or try to login.";
    $style = "style='display:none;'";
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tweetn - Register</title>
  <link rel="stylesheet" href="../stylesheets/authentication.css">
  <link rel="stylesheet" href="../stylesheets/loader.css">

  <?php include '../htmlContainer/headerLinks.php';?>
</head>
<body>
  <section class="hero">

    <!-- Sign up Panel -->
    <div class="panel">
      <form action="" method="post">
        <h1>Sign up!</h1>
        <input type="text" placeholder="Username" name="username" id="username" autofocus/><br>
        <input type="password" placeholder="Password" name="password" id="password"/><br>
        <input type="submit" name="enter" onclick="showLoader()" id="enter" value="Sign up" />
      </form>
      <p><?php echo $message ?><p>
      <br><a href="../authentication/login.php">to the login page</a>
    </div>

    <!-- Loading animation -->
    <div class="cs-loader" id="loader" <?php echo $style ?>>
      <div class="cs-loader-inner">
        <label>	●</label>
        <label>	●</label>
        <label>	●</label>
        <label>	●</label>
        <label>	●</label>
        <label>	●</label>
      </div>
    </div>

  </section>
</body>
<script>
function showLoader() {
   document.getElementById('loader').style.display = "block";
}
</script>
<style>
  .hero {
    background-image: url("../images/backgrounds/sky.jpg");
  }
  .panel {
    background: #E7E6E4;
  }
</style>
</html>
