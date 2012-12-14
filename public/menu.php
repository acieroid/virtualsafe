<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');
require_once('../include/csrf.php');

?>
<p>Menu:
<?php
$menu = array();

if (session_has_user()) {
  $user = session_get_user();
  array_push($menu, 'Connected as ' . $user->name . '(<a href="signin.php">signout</a>)');
  array_push($menu, '<a href="manage.php">Manage your data</a>');
} else {
  array_push($menu, '<a href="signin.php">Sign in</a>');
  array_push($menu, '<a href="signup.php">Sign up</a>');
}

if (session_has_admin()) {
  $admin = session_get_admin();
  array_push($menu, 'Connected as adminstrator as ' . $admin->name . '(<a href="signin_admin.php">signout</a>)');
  array_push($menu, '<a href="admin.php">Validate some users</a>');
} else {
  array_push($menu, '<a href="signin_admin.php">Sign in as admin</a>');
}

foreach ($menu as $element) {
  echo $element . ' - ';
}

?>
</p>