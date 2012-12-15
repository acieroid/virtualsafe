<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');

include('menu.php');

if (!session_has_admin()) {
  echo '<p>You are not allowed to view this page</>';
} else if (isset($_POST['uid'])) {
  if (CSRF::check($_POST['csrf'])) {
    $user = new User();
    $user->id = $_POST['uid'];
    if ($user->validate()) {
      echo '<p>The user has been validated</p>';
    } else {
      echo '<p>There was a problem</p>';
    }
  } else {
    echo '<p>The CSRF token is invalid</p>';
  }
} else {
  echo '<p>Invalid request</p>';
}
?>
    