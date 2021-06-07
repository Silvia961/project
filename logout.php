<?php
include("path.php");

session_start();

unset($_SESSION['id']);
unset($_SESSION['nome_user']); 
unset($_SESSION['is_admin']);
unset($_SESSION['message']);
unset($_SESSION['type']);

session_destroy();

header('location:' . BASE_URL . '/index.php');

