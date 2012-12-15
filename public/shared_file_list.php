<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else if (!session_has_valid_user()) {
  echo '<p>Your account is not valid. Please wait while an administrator validates it</p>';
} else {
  $user = session_get_user();
  $files = $user->list_shared_files();
  echo '<ul>';
  if (count($files) == 0) {
    echo '<p>You do not share any file</p>';
  } else {
    foreach($files as $f) {
      echo '<li><a href="download.php?name=' . urlencode($f['filename']) . '">' . $f['filename'] . '</a> is shared with ' . $f['username'] . ' - <a href="unshare.php?name=' . urlencode($f['filename']) . '&user=' . urlencode($f['username']) . '">unshare</a></li>';
    }
  }
  echo '</ul>';
}
?>
