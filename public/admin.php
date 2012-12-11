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
    /* TODO: avoid CSRF */
    echo '<li>' . $u->name . ' <a href="validate.php?uid=' . $u->id . '">validate</a></li>';
  }
}
?>
    
    