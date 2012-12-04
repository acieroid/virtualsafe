<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');

if (isset($_GET('signin') &&
    isset($_POST('name')) && isset($_POST('password')))) {
  /* Connect the user */
  $user = new User(get_pdo());
  if ($user->identify($_POST('name'), $_POST('password'))) {
?>
<p>You're now logged. You can now <a href="manage.php">manage your data</a></p>
<?php
  } else {
?>
  <p>Wrong user or password</p>
<?php
  }
?>
<form action="signin.php?signin" method="post">
  <p><label for="name" value="Name: "><input type="text" name="name"/></label></p>
  <p><label for="password" value="Password: "><input type="password" name="password" /></label></p>
  <p><input type="submit" value="Sign in" /></p>
</form>
<?php
}
?>
