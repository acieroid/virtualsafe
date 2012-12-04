<?php
require_once('../include/utils.php');
require_once('../include/database.php');
require_once('../include/model.php');

echo 'Creating user object<br/>';
$user = new User(get_pdo());
echo 'Identifying user<br/>';
if ($user->identify('foo', 'bar')) {
  echo 'User identfied<br/>';
  echo 'User id: ' . $user->id . '<br/>';
  echo 'User name: ' . $user->name . '<br/>';
} else {
  echo 'Incorrect credentials<br/>';
}

$user->invalidate();
if ($user->valid) {
  echo 'User is valid<br/>';
} else {
  echo 'User is invalid<br/>';
}

$user->validate();
if ($user->valid) {
  echo 'User is valid<br/>';
} else {
  echo 'User is invalid<br/>';
}

$user = new User(get_pdo());
$user->name = "lol";
if ($user->create("lol")) {
  echo 'Success when creating user<br/>';
} else {
  echo 'Error when creating user<br/>';
}



echo 'Random salt: ' . generate_salt() . '<br/>';

?>
