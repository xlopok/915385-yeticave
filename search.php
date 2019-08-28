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


// $lots_rows = get_lots($link);
$categories_rows = get_catagories($link); // Передаем список категорий

$is_auth = $_SESSION['user']?? "";

$search = $_GET['search']?? "";
$searched_lots = [];
$searched_lots_pages = [];

if($search) {
    $searched_lots = search_results($link, $search);
    
    $cur_page = $_GET['page'] ?? 1; 
    $page_items = 2; // Лотов на странице 
    $items_count = count($searched_lots); // Количесво лотов, которое дал поиск

    $pages_count = ceil($items_count / $page_items); // Колличесво нужных страниц
    $offset = ($cur_page - 1) * $page_items; // Смещение 

    $pages = range(1, $pages_count);

    $sql = "SELECT l.id, l.dt_add ,l.name as lot_name, description ,starting_price, l.dt_end, img, c.name as category_name
    FROM lots l
    JOIN  categories c
    ON category_id = c.id
    WHERE MATCH(l.name, description)
    AGAINST(?)
    ORDER BY l.dt_add DESC

    LIMIT $page_items
    OFFSET $offset";

    $stmt = db_get_prepare_stmt($link, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $searched_lots_pages = mysqli_fetch_all($result, MYSQLI_ASSOC);
}



$page_content = include_template('search.php', ['categories_rows' => $categories_rows, 'is_auth' => $is_auth, 'searched_lots_pages' => $searched_lots_pages, 'search' => $search, 'pages' => $pages?? null, 'pages_count' => $pages_count?? null, 'cur_page' => $cur_page?? null  ] );
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Результаты поиска', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows, 'search' => $search ]);

print($layout_content);