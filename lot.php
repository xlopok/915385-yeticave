<?php
// Подключение файла с функциями
require_once('functions.php');
session_start();

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



if (isset($_SESSION['user'])) {
    
    $layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Страница лота', 'user_name' => $_SESSION['user']['user_name'], 'is_auth' => $_SESSION['user'], 'categories_rows' => $categories_rows]);

}
else {  
    
    $layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Страница лота', 'user_name' => null, 'is_auth' => null, 'categories_rows' => $categories_rows]);
}


print($layout_content);

