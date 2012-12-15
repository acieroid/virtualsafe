<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (isset($_GET['signin'], $_POST['name'], $_POST['password'])) {
  /* The admin wants to log in */
  $admin = new Admin();
  if ($admin->identify($_POST['name'], $_POST['password'])) {
    session_store_admin($admin);
    echo '<p>You are now logged. You can now go to the <a href="admin.php">admin page</a></p>';
  } else {
    echo '<p>Wrong user or password</p>';
  }
} else if (isset($_GET['signout'])) {
  /* The admin wants to sign out */
  session_remove_admin();
  echo '<p>You are now logged out</p>';
} else if (session_has_admin()) {
  /* The admin is already logged */
  echo '<p>You are already logged. Do you want to <a href="signin_admin.php?signout">sign out</a>?</p>';
} else {
?>
<form action="signin_admin.php?signin" method="post">
  <p><label for="name">Name: </label><input type="text" name="name" id="name"/></p>
  <p><label for="password">Password: </label><input type="password" name="password" id="password"/></p>
  <p><input type="submit" value="Sign in as admin" /></p>
</form>
<?php
}
?>
