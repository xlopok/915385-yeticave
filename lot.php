<?php
// Подключение файла с функциями
require_once('functions.php');
session_start();

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8"); // установка кодировки к бд

if (!$link) { //ЕСЛИЛ НЕТ РЕСУРСА СОЕДИНЕНИЯ, ТО ОШИБКА
	$error = mysqli_connect_error();
	show_error($page_content, $error);
	exit();
}

$categories_rows = get_catagories($link); // Передаем список категорий

$is_auth = $_SESSION['user']?? "";

$error['bet'] = null;
$lot_id = $_GET['lot_id']; 

// Условие для параметра запроса и отображение нужногоа лота по его id

if (isset($_GET['lot_id']) && $_GET['lot_id'] !== '') {

     $lot = get_lot($link, $lot_id);
     $bets = get_bets_for_lot($link, $lot_id);   

}

if( !isset($lot['id']) || $_GET['lot_id'] === '') {
    http_response_code(404);
    $page_content = include_template('404.php', ['categories_rows' => $categories_rows, 'error' => 'Такого лота нет']);
    $layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Ошибка', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);
    print($layout_content);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bet'])) {
    $bet = intval($_POST['bet']);
    $current_price = $lot['max_bet']? $lot['max_bet']: $lot['starting_price'];
    $min_bet = $current_price + $lot['bet_step'];
    

    if(empty($bet)) {
        $error['bet'] = 'Поле не может быть пустым';     
    }

    if(!is_numeric($bet)) {
        $error['bet'] = 'Введите сумму';
    }

    if($bet < $min_bet ) {
        $error['bet'] = 'Сумма не может быть меньше ' .$min_bet;
    }

    if(empty($error['bet'])) { 
        $add_bet = add_bet($link, $lot, $bet, $is_auth);
        header("Location: lot.php?lot_id=$lot_id");
        exit();
    }

}

$page_content = include_template('lot.php', ['categories_rows' => $categories_rows,'lot' => $lot, 'error' => $error, 'is_auth' => $is_auth, 'bets' => $bets] );
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Страница лота', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);

