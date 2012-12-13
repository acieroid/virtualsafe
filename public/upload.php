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
	if(isset($_FILES['file'])) {
	
	if($_FILES['file']['error'] == 0 ||  $_FILES['file']['error'] == UPLOAD_ERR_OK) {

}
}
	else {
?>
<form method="post" action="upload.php" enctype="multipart/form-data">
	<p><label for="file">File to upload</label><input type="file" name="file" id="file"/></p>
	<p><input type="submit" value="Send"></p>
</form>
<?php
	}
}
?>