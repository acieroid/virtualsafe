<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else if (!session_has_valid_user()) {
  echo '<p>Your account is not valid. Please wait while an administrator validates it</p>';
} else if (isset($_GET['name'], $_GET['user'], $_GET['encrypted'])) {
  /* Download the encrypted file. It is stored in the owner's directory */
  $user = session_get_user();
  $owner = User::find(urldecode($_GET['user']));
  if ($owner == null) {
    die('No such user');
  }
  $name = urldecode($_GET['name']);
  $file = $owner->get_file_path($name);

  if ($user->has_access($owner, $name) && file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($file);
  } else {
    include('menu.php');
    echo '<p>The file ' . $file . ' does not exists or you cannot access it</p>';
  }
} else if (isset($_GET['name'], $_GET['user'], $_GET['key'])) {
  /* Download the key. It is stored in the user's directory */
  $user = session_get_user();
  $owner = User::find(urldecode($_GET['user']));
  if ($owner == null) {
    die('No such user');
  }
  $name = urldecode($_GET['name']);
  /* take the key of the user, not the owner */
  $key = $user->get_key_path($name);

  if ($user->has_access($owner, $name) && file_exists($key)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '.key"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($key);
  } else {
    include('menu.php');
    echo '<p>The key file does not exists or this file has not been shared with you</p>';
  }
} else if (isset($_GET['name'], $_GET['user'], $_GET['signature'])) {
  /* Download the signature */
  $user = session_get_user();
  $owner = User::find(urldecode($_GET['user']));
  if ($owner == null) {
    die('No such user');
  }
  $name = urldecode($_GET['name']);
  $signature = $owner->get_signature_path($name);

  if ($user->has_access($owner, $name) && file_exists($signature)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '.sign"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($signature);
  } else {
    include('menu.php');
    echo '<p>The signature file does not exists or this file has not been shared with you</p>';
  }
} else if (isset($_GET['name'], $_GET['user'], $_GET['certificate'])) {
  /* Download the certificate */
  $user = session_get_user();
  $owner = User::find(urldecode($_GET['user']));
  if ($owner == null) {
    die('No such user');
  }
  $name = urldecode($_GET['name']);
  $certificate = $owner->get_certificate_file($name);

  if ($user->has_access($owner, $name) && file_exists($certificate)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '.crt"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($certificate);
  } else {
    include('menu.php');
    echo '<p>The certificate file does not exists or this file has not been shared with you</p>';
  }
} else if (isset($_GET['name'], $_GET['user'])) {
  /* Show the URL for downloading the file and the key */
  include('menu.php');
?>

  <p>Download the following files:</p>
  <ul>
  <li><a href="download_shared.php?name=<?php echo $_GET['name']; ?>&user=<?php echo $_GET['user']; ?>&encrypted">The encrypted file</a></li>
  <li><a href="download_shared.php?name=<?php echo $_GET['name']; ?>&user=<?php echo $_GET['user']; ?>&key">The key</a></li>
  <li><a href="download_shared.php?name=<?php echo $_GET['name']; ?>&user=<?php echo $_GET['user']; ?>&signature">The signature</a></li>
  <li><a href="download_shared.php?name=<?php echo $_GET['name']; ?>&user=<?php echo $_GET['user']; ?>&certificate">The certificate</a></li>  
  </ul>
  <p>Then run <pre>java -jar signer.jar -d file_in key.key file_out</pre> to decrypt the file, and <pre>java -jar signer.jar -c file_out signature.sign certificate.crt</pre> to check that the file matches the signature.</p>
<?php
} else {
  echo '<p>Invalid request</p>';
}
?>