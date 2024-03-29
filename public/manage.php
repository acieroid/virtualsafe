<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

include('menu.php');

if (!session_has_user()) {
  echo '<p>Please <a href="signin.php">sign in</a></p>';
} else if (!session_has_valid_user()) {
  echo '<p>Your account has not been activated yet, please wait until an admin validates it</p>';
} else {
?>
  <p><ul>
    <li><a href="modify_password.php">Change your password</a></li>
    <li><a href="revocate.php">Revocate your certificate</a></li>
    <li><a href="revocate_key.php">Revocate your key pair</a></li>
    <li><a href="upload.php">Upload a file</a></li>
    <li><a href="file_list.php">Manage your files</a></li>
    <li><a href="shared_file_list.php">List the files you share with others</a></li>
    <li><a href="shared_with_file_list.php">List the files shared with you</a></li>
  </ul></p>
<?php
}
?>