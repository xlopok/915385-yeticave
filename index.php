<?php
// Подключение файла с функциями
require_once('functions.php');
session_start();

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8"); // установка кодировки к бд

if (!$link) { //ЕСЛИЛ НЕТ РЕСУРСА СОЕДИНЕНИЯ, ТО ОШИБКА
    $error = mysqli_connect_error();
	show_error($error);
	exit();
}

// Вызов функция для показа лотов 
$lots_rows = get_lots($link);

// Вызов функция для показа категорий 
$categories_rows = get_catagories($link);

$is_auth = $_SESSION['user']?? "";
$page_content = include_template('index.php', ['lots_rows' => $lots_rows, 'categories_rows' => $categories_rows]);
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Главная YetiCave', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);