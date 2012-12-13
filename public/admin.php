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
} else {
  $invalid_users = User::invalidated_users();
  $token = CSRF::generate();
  ?>
  <ul>
  <?php   
  foreach ($invalid_users as $u) {
    echo '<li>' . $u->name . ' <form action="validate.php" method="post"><input type="hidden" name="uid" value="' . $u->id . '"/>' . $token->get() . '<input type="submit" value="Validate" /></form></li>';
  }
}
?>
    
    