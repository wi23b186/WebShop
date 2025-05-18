<?php
session_start();
session_destroy();
setcookie('remember_user', '', time() - 3600, "/");
header('Location: ../../../frontend/index.html');
exit();
