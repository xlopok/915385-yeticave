<?php
// Подключение файла с функциями
require_once('functions.php');

$is_auth = rand(0, 1);

$user_name = 'Nikita Vorobev'; // укажите здесь ваше имя

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8"); // установка кодировки к бд



// Вызов функция для показа категорий 


// Условие для параметра запроса и отображение нужногоа лота по его id

if (isset($_GET['lot_id']) && $_GET['lot_id'] !== '') {

     $lot_id = $_GET['lot_id']; 
     $lot = get_lot($link, $lot_id);

     if(!is_null(($lot))) {
        $categories_rows = get_catagories($link);

        $page_content = include_template('lot.php', 
            [
            'categories_rows' => $categories_rows,
            'lot' => $lot] );
     }

     else {
        $page_content = include_template('404.php', 
        ['error' => 'Такого лота нет'] );
        
     }
}
else {
    $page_content = include_template('404.php', 
    ['error' => 'Такого лота нет'] );
}


$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Страница лота', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);

