<?php
// Подключение БД

$link = mysqli_connect("localhost", "root", "", "yeticave");
 
if ($link == false){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
// else {
//     print("Соединение установлено успешно");
// };

mysqli_set_charset($link, "utf8");
// запрос на показ лотов
$sql_lots = "SELECT l.lot_name, starting_price, img, c.category_name 
FROM lots l
JOIN  categories c
ON category_id = c.id
-- откинул цену из таблицы bets
WHERE winner_user_id IS NULL
ORDER BY l.dt_add DESC";

$result_lots = mysqli_query($link, $sql_lots);

$lots_rows = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);

// запрос на показ категорий в футере
$sql_footer_categories = "SELECT * FROM categories";

$result_footer_categories = mysqli_query($link, $sql_footer_categories);

$footer_categories_rows = mysqli_fetch_all($result_footer_categories, MYSQLI_ASSOC);


// Подключение файла с функциями
require_once('functions.php');

$is_auth = rand(0, 1);

$user_name = 'Nikita Vorobev'; // укажите здесь ваше имя

// массив категорий

$categories_array = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

// массив объявлений 

$list_array = [
    [
        'name' => '2014 Rossignol District Snowboard',
        'category' => 'Доски и лыжи', 
        'price' => '10999',
        'url' => 'img/lot-1.jpg'],
    [
        'name' => 'DC Ply Mens 2016/2017 Snowboard',
        'category' => 'Доски и лыжи', 
        'price' => '159999', 
        'url' => 'img/lot-2.jpg'],
    [
        'name' => 'Крепления Union Contact Pro 2015 года размер L/XL', 
        'category' => 'Крепления', 
        'price' => '8000', 
        'url' => 'img/lot-3.jpg'],
    [
        'name' => 'Ботинки для сноуборда DC Mutiny Charocal',
        'category' => 'Крепления', 
        'price' => '10999', 
        'url' => 'img/lot-4.jpg'],
    [
        'name' => 'Куртка для сноуборда DC Mutiny Charocal',
        'category' => 'Одежда', 
        'price' => '7500', 
        'url' => 'img/lot-5.jpg'],
    [
        'name' => 'Маска Oakley Canopy',
        'category' => 'Разное', 
        'price' => '5400', 
        'url' => 'img/lot-6.jpg']
]
;

// Поках времени до исчесзновления лота со страницы



?>

<?php

$page_content = include_template('index.php', ['categories_array' => $categories_array, 'list_array' => $list_array, 'lots_rows' => $lots_rows, 'footer_categories_rows' => $footer_categories_rows] );
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Главная YetiCave', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_array' => $categories_array, 'footer_categories_rows' => $footer_categories_rows]);

print($layout_content);



