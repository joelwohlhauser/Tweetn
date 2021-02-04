<?php
function GetKeys() {
  //write your configurations
  $configargs = array(
    "config" => "../phpMyAdmin/vendor/phpseclib/phpseclib/phpseclib/openssl.cnf",
    'private_key_bits'=> 2048,
    'default_md' => "sha256",
  );

  // Create the keypair
  $res=openssl_pkey_new($configargs);

  // Get private key
  openssl_pkey_export($res, $privKey,NULL,$configargs);

  // Get public key
  $publickey=openssl_pkey_get_details($res);
  $publickey=$publickey["key"];

  // create an array
  $keys = array("public"=>$publickey, "private"=>$privKey);

  return $keys;
}

function EncryptMessage($message, $publicKey){
  $openSSLResult = openssl_public_encrypt($message, $encryptedMessage, $publicKey);
  if ($openSSLResult == true) {
    return base64_encode($encryptedMessage);
  } else {
    return null;
  }
}

function DecryptMessage($encryptedBase64Message, $privateKey){
  $encryptedBase64Message = base64_decode($encryptedBase64Message);
  $openSSLResult = openssl_private_decrypt($encryptedBase64Message, $decryptedMessage, $privateKey);
  if($openSSLResult){
    return $decryptedMessage;
  } else {
    return null;
  }
}
?>
