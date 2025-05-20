<?php
session_start();
//alter CheckAccess oder so hieß das
function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header('Location: ../../../frontend/login.html');
        exit();
    }
}

function requireAdmin() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ../../../frontend/index.html');
        exit();
    }
}
?>
