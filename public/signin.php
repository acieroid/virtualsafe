<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (isset($_GET['signin'], $_POST['name'], $_POST['password'])) {
  /* The user wants to log in */
  $user = new User(get_pdo());
  if ($user->identify($_POST['name'], $_POST['password'])) {
    session_store_user($user);
?>
<p>You are now logged.</p>
<?php
    if ($user->valid) {
?>
     <p>You can now <a href="manage.php">manage your data</a></p>
<?php
    } else {
?>
     <p>You are not validated yet. Please wait until an admin validates your account</p>
<?php
    }
  } else {
?>
  <p>Wrong user or password</p>
<?php
  }
} else if (isset($_GET['signout'])) {
  /* The user wants to sign out */
  session_remove_user();
?>
  <p>You are now logged out</p>
<?php
} else if (session_has_user()) {
  /* The user is already logged */
?>
  <p>You are already logged. Do you want to <a href="signin.php?signout">sign out</a>?</p>
<?php
} else {
?>
<form action="signin.php?signin" method="post">
  <p><label for="name">Name: </label><input type="text" name="name" id="name"/></p>
  <p><label for="password">Password: </label><input type="password" name="password" id="password"/></p>
  <p><input type="submit" value="Sign in" /></p>
</form>
<?php
}
?>
