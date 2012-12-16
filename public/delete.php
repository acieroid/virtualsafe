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
} else if (isset($_GET['name'], $_POST['validation'])) {
  /* Delete the file */
  $user = session_get_user();
  $name = urldecode($_GET['name']);

  if (CSRF::check($_POST['csrf'])) {
    if ($user->delete_file($name)) {
      echo '<p>The file ' . $name . ' has successfuly been deleted</p>';
    } else {
      echo '<p>An error occured while deleting the file</p>';
    }
  } else {
    echo '<p>The CSRF token is invalid</p>';
  }
} else if (isset($_GET['name'])) {
  /* Ask the user to validate its choice */
  $name = urldecode($_GET['name']);
  $token = CSRF::generate();
?>
  <p>Are you sure that you want to delete the file <?php echo $name; ?> ?</p>
  <form method="post" action="delete.php?name=<?php echo $_GET['name']; ?>">
  <?php echo $token->get(); ?>
  <input type="hidden" name="validation" value="yes"/>
  <input type="submit" value="Yes, I'm sure"/></p>
  </form>
<?php
} else {
  echo '<p>Invalid request</p>';
}
?>