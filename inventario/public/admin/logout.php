<?php
session_start();
session_destroy();
header("Location: ../admin/login.php"); // o ajusta la ruta si está en otra carpeta
exit();
?>
