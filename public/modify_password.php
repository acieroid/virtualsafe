<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

else if (!session_has_user()) {
?>
  <p>Please <a href="signin.php">sign in</a></p>
 <?php
if (isset($_GET['change_password'], $_POST['new_password'])) {
  /* The user wants to change his password */
  $user = session_get_user();
  if ($user->change_password($_POST['new_password'])) {  
?>
	<p>Password changed</p>
<?php
} else{
?>
<form action="modify_password.php?change_password" method="post">
  <p><label for="old_password">Old Password: </label><input type="password" name="old_password" id="old_password"/></p>
  <p><label for="new_password">New Password: </label><input type="password" name="new_password" id="new_password"/></p>
  <p><input type="submit" value="Change My Password" /></p>
</form>
<?php
}
?>