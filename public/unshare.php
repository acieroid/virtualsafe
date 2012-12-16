<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
  }
else {
?>
	<p> Which File do you want to unshare?</p>;
<?php
	$user = session_get_user();
	$files = $user->list_shared_files();
	//Affiche l'entièreté des fichiers partagé
	?>
<?php
}
?>