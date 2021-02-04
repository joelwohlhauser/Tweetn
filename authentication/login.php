<?php
session_start();

// delete session variables
session_unset();

// include
require_once('../database/functions.php');
require_once('../crypt/bcrypt.php');

$message = "";
$style = "";

$bcrypt = new Bcrypt(15);

// if all infos are posted and can connect to DB
if (isset($_POST['password']) && isset($_POST['password']) && $_POST['password']!="" && $_POST['username']!="") {
  $errorMessage = "Login failed!<br>The password or username was incorrect.<br>Please try again.";

  // check password of username
  try {
    $isGood = $bcrypt->verify($_POST['password'], GetHash(stripslashes(htmlspecialchars($_POST['username']))));
    if ($isGood) {

      // decrypt privateKey
      $encryptedKey = GetPrivateKey($_POST['username']);
      $c = base64_decode($encryptedKey);
      $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
      $iv = substr($c, 0, $ivlen);
      $hmac = substr($c, $ivlen, $sha2len=32);
      $ciphertext_raw = substr($c, $ivlen+$sha2len);
      $privateKey = openssl_decrypt($ciphertext_raw, $cipher, $_POST['password'], $options=OPENSSL_RAW_DATA, $iv);
      $calcmac = hash_hmac('sha256', $ciphertext_raw, $_POST['password'], $as_binary=true);
      if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
      {
        $_SESSION["privateKey"] = $privateKey;
      }

      $_SESSION["username"] = strtolower($_POST['username']);
      echo '<script type="text/javascript">','hideLoader();','</script>';
      // go to profile page
      header("Location: ../profile/myProfile.php");
      die();
    }
    else {
      $message = $errorMessage;
      echo '<script type="text/javascript">','hideLoader();','</script>';
    }
  } catch (\Exception $e) {
    $message = $errorMessage;
    echo '<script type="text/javascript">','hideLoader();','</script>';
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>Tweetn - Login</title>
  <link rel="stylesheet" href="../stylesheets/authentication.css">
  <link rel="stylesheet" href="../stylesheets/loader.css">

  <?php include '../htmlContainer/headerLinks.php';?>
</head>
<body>
  <section class="hero">

    <!-- Login Panel -->
    <div class="panel">
      <form action="" method="post">
        <h1>Login</h1>
        <input type="text" placeholder="Username" name="username" id="username" autofocus/><br>
        <input type="password" placeholder="Password" name="password" id="password"/><br>
        <input type="submit" name="enter" onclick="showLoader()" id="enter" value="Login" />
      </form>
      <p><?php echo $message ?><p>
      <br><a href="../authentication/register.php">to the register page</a>
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
function hideLoader() {
   document.getElementById('loader').style.display = "none";
}

function showLoader() {
   document.getElementById('loader').style.display = "block";
}
</script>
<style>
  .hero {
    background-image: url("../images/backgrounds/paint.jpg");
  }
  .panel {
    background: #ebeef4;
  }
</style>
</html>
