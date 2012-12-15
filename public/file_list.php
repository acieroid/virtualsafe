<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else {
  $user = session_get_user();
  $filenames = $user->list_owned_files();
  echo '<ul>';
  foreach($filenames as $f) {
    echo '<li>' . $f . ': <a href="download.php?name=' . urlencode($f) . '">download</a> - <a href="share.php?name=' . urlencode($f) . '">share</a> - <a href="delete.php?name=' . urlencode($f) . '">delete</a></li>';
  }
  echo '</ul>';
}
?>
