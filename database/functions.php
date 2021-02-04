<?php
require_once('../classes/userClass.php');
require_once('../crypt/CryptFunctions.php');

// Database properties
$servername = "     your server name    ";
$username = "       your username       ";
$password = "       your password       ";
$dbname = "Tweetn";



function RegisterNewUser($_newUsername, $_newHash, $_newPrivateKey, $_newPublicKey) {
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;
  $date = date("Y-m-d");

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  // Attempt insert query execution
  $sql = "INSERT INTO Users (UserName, UserHash, UserPrivateKey, UserPublicKey, UserJoined)
          VALUES ('" . $_newUsername . "','" . $_newHash . "','" . $_newPrivateKey . "','" . $_newPublicKey . "', '" . $date . "')";
  if(mysqli_query($conn, $sql)){
    return true;
  } else{
    return false;
  }

  // Close connection
  mysqli_close($conn);
}

function GetHash($_checkUsername){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT UserHash FROM Users WHERE UserName='" . $_checkUsername . "'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        return $row["UserHash"];
    }
  } else {
    return false;
  }

  // close connection
  mysqli_close($conn);
}

function GetUserClass($_checkUsername){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT UserEmail, UserAvatar, UserBio, UserBirthday, UserJoined FROM Users WHERE UserName='" . $_checkUsername . "'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        // create new userClass
        $sessionUser = new User();

        $sessionUser->userName = $_checkUsername;
        $sessionUser->userEmail = $row["UserEmail"];
        $sessionUser->userAvatar = $row["UserAvatar"];
        $sessionUser->userBio = $row["UserBio"];
        $sessionUser->userBirthday = $row["UserBirthday"];
        $sessionUser->userJoined = $row["UserJoined"];

        return $sessionUser;
    }
  } else {
    return false;
  }

  // close connection
  mysqli_close($conn);
}

function UpdateUser($_updateUsername, $_updateEmail, $_updateBio, $_updateBirthday, $_updateAvatar){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if($conn === false){
    die($ErrorMessage = "ERROR: Could not connect. " . mysqli_connect_error());
  }

  // Attempt update query execution
  $sql = "UPDATE Users SET
          UserEmail=(" . ($_updateEmail == '' ? 'NULL' : "'$_updateEmail'") . "),
          UserBio=(" . ($_updateBio == '' ? 'NULL' : "'$_updateBio'") . "),
          UserBirthday=(" . ($_updateBirthday == '' ? 'NULL' : "'$_updateBirthday'") . "),
          UserAvatar=(" . ($_updateAvatar == '' ? 'NULL' : "'$_updateAvatar'") . ")
          WHERE UserName='". $_updateUsername ."'";
  mysqli_query($conn, $sql) or die($ErrorMessage = mysqli_error($conn));
  if (isset($ErrorMessage)) {
    return $ErrorMessage;
  }

  // Close connection
  mysqli_close($conn);
}

function SearchUser($_searchUser, $_username){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT UserAvatar, UserName FROM Users WHERE UserName
          LIKE '%". $_searchUser ."%' AND NOT UserName='".$_username."'";
  $result = $conn->query($sql);

  $searchedUsers = array();
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $searchedUsers[] = array($row["UserAvatar"], $row["UserName"]);
    }
  } else {
    return false;
  }
  return $searchedUsers;

  // close connection
  $conn->close();
}



function SaveTextMessage($_sender, $_receiver, $_content, $_privateKey){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;
  // get timestamp
  $date = date("Y-m-d-h-i-s");

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if($conn === false){
    die($ErrorMessage = "ERROR: Could not connect. " . mysqli_connect_error());
  }

  // get public key of receiver
  $key = GetPublicKey($_receiver);

  // encrypt the message
  $encryptedText = EncryptMessage($_content, $key);
  if ($encryptedText == null) {
    $ErrorMessage = "Something went wrong with the encryption";
  }

  // Attempt insert query execution
  $sql = "INSERT INTO Messages (MessageSender, MessageReceiver, MessageContent)
          VALUES ('" . $_sender . "','" . $_receiver . "','" . $encryptedText . "')";
   mysqli_query($conn, $sql) or die($ErrorMessage = mysqli_error($conn));
   if (isset($ErrorMessage)) {
     return $ErrorMessage;
   }

   // SendedMessages
   // encrypt message with two-way hash
   $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
   $iv = openssl_random_pseudo_bytes($ivlen);
   $ciphertext_raw = openssl_encrypt($_content, $cipher, $_privateKey, $options=OPENSSL_RAW_DATA, $iv);
   $hmac = hash_hmac('sha256', $ciphertext_raw, $_privateKey, $as_binary=true);
   $hashMessage = base64_encode( $iv.$hmac.$ciphertext_raw );

   $sql = "INSERT INTO SendedMessages (SendedMessageUser, SendedMessageReceiver, SendedMessageContent)
           VALUES ('" . $_sender . "','" . $_receiver . "','" . $hashMessage . "')";
    mysqli_query($conn, $sql) or die($ErrorMessage = mysqli_error($conn));
    if (isset($ErrorMessage)) {
      return $ErrorMessage;
    }

  // Close connection
  mysqli_close($conn);
}

function GetPublicKey($_username){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT UserPublicKey FROM Users WHERE UserName='" . $_username . "'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        return $row["UserPublicKey"];
    }
  } else {
    return false;
  }

  // close connection
  mysqli_close($conn);
}

function GetPrivateKey($_username){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT UserPrivateKey FROM Users WHERE UserName='" . $_username . "'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        return $row["UserPrivateKey"];
    }
  } else {
    return false;
  }

  // close connection
  mysqli_close($conn);
}

function GetMessages($_sender, $_receiver){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT DISTINCT MessageContent AS 'Content', Messages.MessageSender AS 'FromUser', Messages.MessageTimestamp AS 'Time'
          FROM Messages
          LEFT JOIN SendedMessages ON Messages.MessageSender = SendedMessages.SendedMessageUser
          WHERE Messages.MessageReceiver = '".$_sender."' AND Messages.MessageSender = '".$_receiver."'

          UNION ALL

          SELECT DISTINCT SendedMessages.SendedMessageContent AS 'Content', SendedMessages.SendedMessageUser AS 'FromUser', SendedMessages.SendedMessageTimestamp AS 'Time'
          FROM SendedMessages
          RIGHT JOIN Messages ON SendedMessages.SendedMessageUser = Messages.MessageSender
          WHERE SendedMessages.SendedMessageUser = '".$_sender."' AND SendedMessages.SendedMessageReceiver = '".$_receiver."'
          ORDER BY Time";
  $result = mysqli_query($conn, $sql);

  $messages = array();
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $messages[] = array($row["Content"], $row["FromUser"]);
    }
  } else {
    return false;
  }
  return $messages;

  // close connection
  mysqli_close($conn);
}



function NewRelationship($_user, $_followsUser){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if($conn === false){
    die($ErrorMessage = "ERROR: Could not connect. " . mysqli_connect_error());
  }

  // Attempt insert query execution
  $sql = "INSERT INTO Relationships (RelationshipUser, RelationshipFollowsUser)
          VALUES ('".$_user."', '".$_followsUser."');";

   mysqli_query($conn, $sql) or die($ErrorMessage = mysqli_error($conn));
   if (isset($ErrorMessage)) {
     return $ErrorMessage;
   }

   // close connection
   mysqli_close($conn);
}

function BreakRelationship($_user, $_followsUser){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;
  // get timestamp
  $date = date("Y-m-d-h-i-s");

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if($conn === false){
    die($ErrorMessage = "ERROR: Could not connect. " . mysqli_connect_error());
  }

  // Attempt insert query execution
  $sql = "DELETE FROM Relationships WHERE RelationshipUser='" . $_user . "'
          AND RelationshipFollowsUser='" . $_followsUser . "'";
   mysqli_query($conn, $sql) or die($ErrorMessage = mysqli_error($conn));
   if (isset($ErrorMessage)) {
     return $ErrorMessage;
   }

   // close connection
   mysqli_close($conn);
}

function CheckRelationship($_user, $_followsUser){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT RelationshipUser FROM Relationships
          WHERE RelationshipUser='" . $_user . "' AND
          RelationshipFollowsUser='" . $_followsUser . "'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        if ($row["RelationshipUser"] != null) {
          return true; // follows
        }
        else {
          return false; // follows not
        }
    }
  } else {
    return false;
  }

  // close connection
  mysqli_close($conn);
}

function GetAllFollowers($_user){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT RelationshipFollowsUser FROM Relationships
          WHERE RelationshipUser='" . $_user . "'";
  $result = mysqli_query($conn, $sql);

  $followers = array();
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $followers[] = $row["RelationshipFollowsUser"];
    }
  } else {
    return false;
  }
  return $followers;

  mysqli_close($conn);
}

function GetAllFollowersToChat($_user){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT DISTINCT RelationshipFollowsUser AS 'Users'
          FROM Relationships
          LEFT JOIN Messages ON Messages.MessageReceiver = Relationships.RelationshipUser
          WHERE Relationships.RelationshipUser='". $_user ."'

          UNION

          SELECT DISTINCT MessageSender AS 'Users'
          FROM Messages
          LEFT JOIN Relationships ON Relationships.RelationshipUser = Messages.MessageReceiver
          WHERE Messages.MessageReceiver='". $_user ."'";
  $result = mysqli_query($conn, $sql);

  $followers = array();
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $followers[] = $row["Users"];
    }
  } else {
    return false;
  }
  return $followers;

  // close connection
  mysqli_close($conn);
}



function SavePost($_user, $_content){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if($conn === false){
    die($ErrorMessage = "ERROR: Could not connect. " . mysqli_connect_error());
  }

  // Attempt insert query execution
  $sql = "INSERT INTO Posts (PostUser, PostContent)
          VALUES ('" . $_user . "','" . $_content . "')";
   mysqli_query($conn, $sql) or die($ErrorMessage = mysqli_error($conn));
   if (isset($ErrorMessage)) {
     return $ErrorMessage;
   }

   // close connection
   mysqli_close($conn);
}

function GetPosts($_user){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  // get all folowers
  $followers = array();
  $followers = GetAllFollowers($_user);

  // get sql string
  $sqlFollowersString = "PostUser='".$_user."'";
  for ($i=0; $i < count($followers); $i++) {
    $sqlFollowersString = $sqlFollowersString . " OR PostUser='" . $followers[$i] . "'";
  }
  if ($sqlFollowersString == "") {
    return false;
  }

  $sql = "SELECT PostContent, PostUser, PostTimestamp FROM Posts
          WHERE " . $sqlFollowersString . "ORDER BY PostTimestamp DESC;";
  $result = mysqli_query($conn, $sql);

  $messages = array();
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $messages[] = array($row["PostContent"], $row["PostUser"], $row["PostTimestamp"]);
    }
  } else {
    return false;
  }
  return $messages;

  // close connection
  mysqli_close($conn);
}

function GetPersonalPosts($_user){
  // variables
  global $servername;
  global $username;
  global $password;
  global $dbname;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $sql = "SELECT PostContent, PostUser, PostTimestamp FROM Posts
          WHERE PostUser='" . $_user . "' ORDER BY PostTimestamp DESC;";
  $result = mysqli_query($conn, $sql);

  $messages = array();
  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $messages[] = array($row["PostContent"], $row["PostUser"], $row["PostTimestamp"]);
    }
  } else {
    return false;
  }
  return $messages;

  // close connection
  mysqli_close($conn);
}
?>
