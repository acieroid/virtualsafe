<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
?>
<p>Please <a href="signin.php">sign in</a></p>
<?php
}

else {
	$user = session_get_user();
	$filenames = $user->list_owned_files();
?>
<ul>
<?php
	foreach($filenames as $f)
	{
		echo '<li><a href="download.php?name=' . urlencode($f) . '">' . $f . '</a></li>';
	}
?>
</ul>
<?php
}
?>
	