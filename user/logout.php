<?php
session_start();

$_SESSION = [];

session_destroy();


header("Location: /TurningPage/home.php");
exit();
?>
