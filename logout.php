<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hapus semua data session
$_SESSION = [];     
session_destroy(); // Hancurkan session 

//redirecrt ke halaman login
header("Location: login.php");
exit;
?>