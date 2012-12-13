<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (!session_has_user()) {
?>
  <p>Please <a href="signin.php">sign in</a></p>
 <?php
 }
else if (isset($_GET['change_password'], $_POST['new_password1'], $_POST['new_password2'])) {
  /* The user wants to change his password */
  $user = session_get_user();
  if (strcmp($_POST['new_password1'], $_POST['new_password2'])==0){//Compare the new password
	if ($user->change_password($_POST['new_password1'])) {  
?>
		<p>Password changed</p>
<?php
}	else{
?>
	<p>Fail to change password</p>
	<?php
	
}} 
	else {
	?>
			<p>The Passwords do not much</p>
<?php
}
	}
	else{
?>
<form action="modify_password.php?change_password" method="post">
  <p><label for="old_password">Old Password: </label><input type="password" name="old_password" id="old_password"/></p>
  <p><label for="new_password1">New Password: </label><input type="password" name="new_password1" id="new_password1"/></p>
  <p><label for="new_password2">New Password Again: </label><input type="password" name="new_password2" id="new_password2"/></p>
  <p><input type="submit" value="Change My Password" /></p>
</form>
<?php
}
?>