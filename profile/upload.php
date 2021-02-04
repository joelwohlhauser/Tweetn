<?php
session_start();

require_once('../database/functions.php');

// get user data
$userData = GetUserClass($_SESSION["username"]);

$message = '';
if (isset($_POST['uploadBtn']))
{
	$needToUpdateUser = false;

	// email
	if (isset($_POST["email"])) {
	  if ($_POST["email"] != $userData->userEmail) {
	    $userData->userEmail = $_POST["email"];
	    $needToUpdateUser = true;
	  }
	}

	// bio
	if (isset($_POST["bio"])) {
	  if ($_POST["bio"] != $userData->userBio) {
	    $userData->userBio = base64_encode($_POST["bio"]);
	    $needToUpdateUser = true;
	  }
	}

	// birthday
	if (isset($_POST["bday"])) {
	  if ($_POST["bday"] != $userData->userBirthday) {
	      $userData->userBirthday = $_POST["bday"];
	      $needToUpdateUser = true;
	  }
	}

	// avatar
  if (isset($_FILES['uploadedFile']) && $_FILES['uploadedFile']['error'] === UPLOAD_ERR_OK)
  {
    // get details of the uploaded file
    $fileTmpPath = $_FILES['uploadedFile']['tmp_name'];
    $fileName = $_FILES['uploadedFile']['name'];
    $fileSize = $_FILES['uploadedFile']['size'];
    $fileType = $_FILES['uploadedFile']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    // sanitize file-name
    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

    // check if file has one of the following extensions
    $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

    if (in_array($fileExtension, $allowedfileExtensions))
    {
      // directory in which the uploaded file will be moved
      $uploadFileDir = '../images/avatars/';
      $dest_path = $uploadFileDir . $newFileName;

      if(move_uploaded_file($fileTmpPath, $dest_path))
      {
      	$userData->userAvatar = $newFileName;
      	$needToUpdateUser = true;
      }
      else
      {
        $message = 'There was some error moving the file to upload directory. Please make sure the upload directory is writable by web server.';
      }
    }
    else
    {
      $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
    }
  }
}
$_SESSION['message'] = $message;


// update user in DB
if ($needToUpdateUser) {
  $updateUserResults = UpdateUser($_SESSION["username"], $userData->userEmail, $userData->userBio,
                        $userData->userBirthday, $userData->userAvatar);
  if ($updateUserResults == NULL) {
    header("Location: myProfile.php");
    die();
 } else {
   echo "Error: " . $updateUserResults;
 }
}

header("Location: editProfile.php");
?>
