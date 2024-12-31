<?php
setcookie('user_id', '', time() - 3600, '/');
header('Location: ../index.html');
exit;
?>
