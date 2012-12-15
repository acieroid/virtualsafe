<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else if (!session_has_valid_user()) {
  echo '<p>Your account is not valid. Please wait while an administrator validates it</p>';
} else if (isset($_GET['change_password'], $_POST['old_password'], $_POST['new_password1'], $_POST['new_password2'])) {
  /* The user wants to change his password */
  $user = session_get_user();
  if (strcmp($_POST['new_password1'], $_POST['new_password2']) == 0){ /* compare the passwords */
    if ($user->change_password($_POST['old_password'],$_POST['new_password1'])) {  
      echo '<p>Password changed</p>';
    } else {
      echo '<p>The old password is not correct</p>';
    }
  } else {
    echo '<p>The passwords do not match</p>';
  }
} else {
  ?>
  <form action="modify_password.php?change_password" method="post">
  <p><label for="old_password">Old password: </label><input type="password" name="old_password" id="old_password"/></p>
  <p><label for="new_password1">New password: </label><input type="password" name="new_password1" id="new_password1"/></p>
  <p><label for="new_password2">Confirm new password: </label><input type="password" name="new_password2" id="new_password2"/></p>
  <p><input type="submit" value="Change password" /></p>
  </form>
  <?php
}
?>