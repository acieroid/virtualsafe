<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
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
    echo '<p>The file ' . $file . ' does not exists</p>';
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
    echo '<p>The key file does not exists</p>';
  }
} else if (isset($_GET['name'])) {
  /* Show the URL for downloading the file and the key */
  include('menu.php');
?>

  <p>Download the following files:</p>
  <ul>
  <li><a href="download.php?name=<?php echo $_GET['name'] ?>&encrypted">The encrypted file</a></li>
  <li><a href="download.php?name=<?php echo $_GET['name'] ?>&key">The key</a></li>
  </ul>
<?php
} else {
  echo '<p>Invalid request</p>';
}
?>