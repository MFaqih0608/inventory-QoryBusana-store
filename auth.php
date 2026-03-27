<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kalau belum login, redirect ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>