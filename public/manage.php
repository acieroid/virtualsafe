<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');
require_once('../include/sessions.php');

if (!session_has_user()) {
?>
  <p>Please <a href="signin.php">sign in</a></p>
<?php
} else {
?>
  <p><ul>
    <li><a href="user_modification.php">Change your password</a></li>
    <li><a href="recovate.php">Revocate your certificate</a></li>
    <li><a href="upload.php">Upload a file</a></li>
    <li><a href="file_list.php">List your file</a></li>
    <li><a href="share.php">Share a file</a></li>
  </ul></p>
<?php
}
?>