<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');

if (isset($_GET('signin') &&
    isset($_POST('name')) && isset($_POST('password')))) {
  /* Connect the admin */
  $admin = new Admin(get_pdo());
  if ($admin->identify($_POST('name'), $_POST('password'))) {
?>
<p>You are now logged. You can now go to the <a href="admin.php">admin page</a></p>
<?php
  } else {
?>
  <p>Wrong user or password</p>
<?php
  }
?>
<form action="signin_admin.php?signin" method="post">
  <p><label for="name" value="Name: "><input type="text" name="name" /></label></p>
  <p><label for="password" value="Password: "><input type="password" name="password" /></label></p>
  <p><input type="submit" value="Sign in as admin" /></p>
</form>
<?php
}
?>
