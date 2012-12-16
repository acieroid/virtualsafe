<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else if (!session_has_valid_user()) {
  echo '<p>Your account is not valid. Please wait while an administrator validates it</p>';
} else if (isset($_GET['name'], $_GET['encrypted'])) {
  /* Download the encrypted file */
  $user = session_get_user();
  $name = urldecode($_GET['name']);
  $file = $user->get_file_path($name);

  if ($user->has_file($name) && file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($file);
  } else {
    include('menu.php');
    echo '<p>The file does not exists or you do not own this file</p>';
  }
} else if (isset($_GET['name'], $_GET['key'])) {
  /* Download the key */
  $user = session_get_user();
  $name = urldecode($_GET['name']);
  $key = $user->get_key_path($name);

  if ($user->has_file($name) && file_exists($key)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '.key"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($key);
  } else {
    include('menu.php');
    echo '<p>The key file does not exists or you do not own this file</p>';
  }
} else if (isset($_GET['name'], $_GET['signature'])) {
  /* Download the signature */
  $user = session_get_user();
  $name = urldecode($_GET['name']);
  $signature = $user->get_signature_path($name);

  if ($user->has_file($name) && file_exists($signature)) {
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename="' . $name . '.sign"');
    header("Content-type: application/octet-stream");
    header('Content-Transfer-Encoding: binary');
    readfile($key);
  } else {
    include('menu.php');
    echo '<p>The signature file does not exists or you do not own this file</p>';
  }
} else if (isset($_GET['name'])) {
  /* Show the URL for downloading the file and the key */
  include('menu.php');
  $user = session_get_user();
  $signature = $user->get_signature_path(urldecode($_GET['name']));
?>

  <p>Download the following files:</p>
  <ul>
  <li><a href="download.php?name=<?php echo $_GET['name']; ?>&encrypted">The encrypted file</a></li>
  <li><a href="download.php?name=<?php echo $_GET['name']; ?>&key">The key</a></li>
  <li><?php
  if (file_exists($signature)) {
    echo '<a href="download.php?name=' . $_GET['name'] . '&signature">The signature</a>';
  } else {
    echo 'There is no signature for this file.';
  }
  ?></li>
  </ul>
  <p>Then run <pre>java -jar signer.jar -d file_in key.key file_out</pre> to decrypt the file, and <pre>java -jar signer.jar -c file_out signature.sign</pre> to check that the file matches the signature.</p>
<?php
} else {
  echo '<p>Invalid request</p>';
}
?>