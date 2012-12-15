<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (isset($_GET['signout'])) {
  session_remove_user(); /* remove the user now to have the good menu displayed */
}
include('menu.php');

if (isset($_GET['signin'], $_POST['name'], $_POST['password'])) {
  /* The user wants to log in */
  $user = new User();
  if ($user->identify($_POST['name'], $_POST['password'])) {
    session_store_user($user);
    echo '<p>You are now logged.</p>';
    if ($user->valid) {
      echo '<p>You can now <a href="manage.php">manage your data</a></p>';
    } else {
      echo '<p>You are not validated yet. Please wait until an admin validates your account</p>';
    }
  } else {
    echo '<p>Wrong user or password</p>';
  }
} else if (isset($_GET['signout'])) {
  /* The user wants to sign out */
  session_remove_user();
  echo '<p>You are now logged out</p>';
} else if (session_has_user()) {
  /* The user is already logged */
  echo '<p>You are already logged. Do you want to <a href="signin.php?signout">sign out</a>?</p>';
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
