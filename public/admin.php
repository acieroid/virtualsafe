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
  $invalid_users = User::invalidated_users();
  ?>
  <ul>
  <?php   
  foreach ($invalid_user as $u) {
    /* TODO: Using POST instead of GET reduce the vulnerabilities to
       CSRF, but we're still not entirely protected */
    echo '<li>' . $u->name . ' <form action="validate.php" method="post"><input type="hidden" name="uid" value="' . $u->id . '"/><input type="submit" value="Validate" /></form></li>';
  }
}
?>
    
    