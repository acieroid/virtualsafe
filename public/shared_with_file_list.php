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
  $files = $user->list_shared_files();
  echo '<ul>';
  if (count($files) == 0) {
    echo '<p>Nobody shares any file with you</p>';
  } else {
    foreach($files as $f) {
      echo '<li><a href="download_shared.php?name=' . urlencode($f['filename']) . '?user=' . urlencode($f['username']) . '">' . $f['filename'] . '</a> is shared with you by ' . $f['username'] . ' - <a href="unshare_shared.php?name="' . urlencode($f['filename']) . '&user=' . urlencode($f['username']) . '">unshare</a></li>';
    }
  }
  echo '</ul>';
}
?>
