<?php
// Подключение файла с функциями
require_once('functions.php');

$is_auth = rand(0, 1);

$user_name = 'Nikita Vorobev'; // укажите здесь ваше имя

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8"); // установка кодировки к бд

// Функции для показа лотов и категорий

// Вызов функция для показа лотов 
$lots_rows = get_lots($link);

// Вызов функция для показа категорий 
$categories_rows = get_catagories($link);
// var_dump($categories_rows);
?>

<?php


// if (!isset($_GET['lot_id'])) {
//     // die('<b>Отсутствует id лота в запросе или такого параметра нет</b>');
//     // header("HTTP/1.0 404 Not Found");
//     $page_content = include_template('404.php', [
//         'error' => 'Такого лота нет'
//     ]);

// } 
// else {
//     $lot_id = $_GET['lot_id'];
// }

// $lot = get_lot($link, $lot_id);
// // var_dump($lot);
// if (isset($lot)) {
//     $page_content = include_template('lot.php', 
//     ['lots_rows' => $lots_rows, 
//     'categories_rows' => $categories_rows,
//     'lot' => $lot] );
// }
if (isset($_GET['lot_id']) && $_GET['lot_id'] !== '') {

     $lot_id = $_GET['lot_id']; 
     $lot = get_lot($link, $lot_id);

     if(isset($lot)) {
        $page_content = include_template('lot.php', 
            ['lots_rows' => $lots_rows, 
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



// $page_content = include_template('lot.php', ['lots_rows' => $lots_rows, 'categories_rows' => $categories_rows] );
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Страница лота', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);

