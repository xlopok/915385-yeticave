<?php	
require_once('functions.php');	
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8");
		
session_start();				
unset($_SESSION['user']);
header("Location: /index.php");	