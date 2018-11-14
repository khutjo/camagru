<?php

session_start();
unset($_SESSION['TechCOM']);
unset($_SESSION['user_loged_in']);
session_destroy();
echo "<script>window.close();</script>";
header("location:index.php");