<?php
// Подключение файла с функциями
require_once('functions.php');

$is_auth = rand(0, 1);

$user_name = 'Nikita Vorobev'; // укажите здесь ваше имя

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8"); // установка кодировки к бд

if (!$link) { //ЕСЛИЛ НЕТ РЕСУРСА СОЕДИНЕНИЯ, ТО ОШИБКА
    $error = mysqli_connect_error();
    show_error($page_content, $error);
    exit();
}

$categories_rows = get_catagories($link); // Есть ресурс соединения - передаем список категорий





$page_content = include_template('login.php', ['categories_rows' => $categories_rows ]);
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Вход', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);