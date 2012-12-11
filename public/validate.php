<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (!session_has_admin()) {
?>
  <p>You are not allowed to view this page</>
<?php
} else {
  $id = $_GET['uid'];
  $user = new User(get_pdo());
  $user->id;
  if ($user->validate()) {
?>
    <p>The user has been validated</p>
<?php
  } else {
?>
    <p>There was a problem</p>
<?php
  }
}
?>
    