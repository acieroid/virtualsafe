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
else {
	if(isset($_GET['name'])) {
		$user = session_get_user();
		$name = urldecode($_GET['name']);
		$file = $user->get_file_path($name);

		if(file_exists($file)) {
			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename=$name");
			header("Content-Transfer-Encoding: binary");
			readfile($file);
		}
		else {
?>
<p>Error : the file does not exists</p>
<?php
		}
	}
	else {
?>
<p>Error</p>
<?php
	}
}
?>