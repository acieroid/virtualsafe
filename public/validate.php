<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');

if (!session_has_admin()) {
?>
  <p>You are not allowed to view this page</>
<?php
} else if (isset($_POST['uid'])) {
  if (CSRF::check($_POST['csrf'])) {
    $user = new User();
    $user->id = $_POST['uid'];
    if ($user->validate()) {
?>
    <p>The user has been validated</p>
<?php
    } else {
?>
    <p>There was a problem</p>
<?php
    }
  } else {
?>
    <p>The CSRF token is invalid</p>
<?php
  }
} else {
?>
<p>Invalid request</p>
<?php
}
?>
    