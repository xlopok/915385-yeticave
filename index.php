<?php
// Подключение файла с функциями
require_once('functions.php');

$is_auth = rand(0, 1);

$user_name = 'Nikita Vorobev'; // укажите здесь ваше имя

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8");
// запрос на показ лотов
$sql_lots = "SELECT l.name as lot_name, starting_price, img, c.name as category_name
FROM lots l
JOIN  categories c
ON category_id = c.id
-- откинул цену из таблицы bets
WHERE winner_user_id IS NULL
ORDER BY l.dt_add DESC";

$result_lots = mysqli_query($link, $sql_lots);

$lots_rows = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

// запрос на показ категорий в футере
$sql_categories = "SELECT * FROM categories";

$result_categories = mysqli_query($link, $sql_categories);

$categories_rows = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

// get_catagories();

?>

<?php

$page_content = include_template('index.php', ['lots_rows' => $lots_rows, 'categories_rows' => $categories_rows] );
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Главная YetiCave', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);



