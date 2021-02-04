<?php
session_start();

// include
require_once('../database/functions.php');
require_once('../crypt/CryptFunctions.php');

  if ($_GET["selectedUsers"] != "showNoUserSelectedError") {

    // get all messages
    $messages = array();
    $messages = GetMessages($_SESSION["username"], $_GET["selectedUsers"]);

    // output messages
    if ($messages != NULL) {
      for ($i=0; $i < count($messages); $i++) {

        // me
        if ($messages[$i][1] == strtolower($_SESSION["username"])) {
          // decrypt users message
          $c = base64_decode($messages[$i][0]);
          $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
          $iv = substr($c, 0, $ivlen);
          $hmac = substr($c, $ivlen, $sha2len=32);
          $ciphertext_raw = substr($c, $ivlen+$sha2len);
          $nearlyDecryptedMessage = openssl_decrypt($ciphertext_raw, $cipher, $_SESSION["privateKey"], $options=OPENSSL_RAW_DATA, $iv);
          $calcmac = hash_hmac('sha256', $ciphertext_raw, $_SESSION["privateKey"], $as_binary=true);
          if (hash_equals($hmac, $calcmac))//PHP 5.6+ timing attack safe comparison
          {
              $decryptedMessage = $nearlyDecryptedMessage;
          }
          // output
          if ($decryptedMessage != null) {
            echo "<div class='personalMessage'><p>". $decryptedMessage . "</p></div>";
          }
          else {
            echo "<div class='personalMessage'><p>Failed to decrypt message</p></div>";
          }

        // other user
        } elseif ($messages[$i][1] == strtolower($_GET["selectedUsers"])) {
          // decrypt selected users message
          $decryptedMessage = DecryptMessage($messages[$i][0], $_SESSION["privateKey"]);
          if ($decryptedMessage == null) {
            echo "<div class='otherUsersMessage'><p>Failed to decrypt message</p></div>";
          }
          else {
            echo "<div class='otherUsersMessage'><p>". $decryptedMessage . "</p></div>";
          }
        }
      }
    } else {
      echo "No Messages";
    }
  } else {
    echo "Please select a User";
  }
?>
