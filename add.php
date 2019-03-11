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

$is_auth = $_SESSION['user']?? ""; // ПЕРЕМЕННАЯ, ПРОВЕРЯЮЩАЯ ПРОШЕЛ ЛИ ЮЗЕР АУТЕНТИФИКАЦИЮ

$lot = $_POST; // МАССИВ С ДАННЫМИ ИЗ ФОРМЫ
$errors = []; //МАССИВ С ОШИБКАМИ

if(!$is_auth) { // ЮЗЕР НЕ АВТОРИЗОВАН - СТРАНИЦА ДОБАВЛЕНИЯ НЕ ДОСТУПНА (ОШИБКА)
	http_response_code(403);
	$page_content = include_template('403.php', []);
	$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);
	print($layout_content);
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //ПРОВЕРКА БЫЛА ЛЕ ОТПРАВЛЕНА ФОРМА

	$required = ['lot-name', 'category', 'message', 'lot-rate', 'lot-step', 'lot-date']; //Обязательные для заполнения поля

	foreach ($required as $field) { // Проходим по массиву с обязательными полями и проверяем их на заполненность (не на пустоту)
		if (empty($lot[$field])) {  // Если путой, то добавляем ошибку 
						$errors[$field] = 'Это поле надо заполнить';
		}
	}

	if (empty($lot['lot-name'])) { //Проверка на пустоту имени лота
		$errors['lot-name'] = 'Введите наименование лота';
	}

	if (!is_numeric($lot['category'])) { //Проверка дропдауна категорий
		$errors['category'] = 'Выберите категорию';
	}

	if (empty($lot['message'])) { //Проверка на пустоту описания
		$errors['message'] = 'Напишите описание лота';
	}

	if ($lot['lot-rate'] <= 0) { //Проверка стартинг прайса
			$errors['lot-rate'] = 'Введите число больше нуля';
	}

	if ($lot['lot-step'] <= 0) { // Проверка шага ставки 
		$errors['lot-step'] = 'Введите число больше нуля';
	} 

	if (!empty($lot['lot-date'])) { //Проверка даты
		$min_date = date_create('tomorrow')->format('Y-m-d');
		$end_date = date('Y-m-d', strtotime($lot['lot-date']));
		$end = date('Y-m-d', strtotime("January 19, 2038"));
		
		if ($end_date < $min_date) {
			$errors['lot-date'] = 'Дата не может быть меньше чем ' . date('d.m.Y', strtotime($min_date));
		}

		if ($end_date > $end ) {
			$errors['lot-date'] = 'Дата не может быть больше 19 Января 2038'  ;
		}
	}

	// ПРОВЕРКА ФАЙЛА 
	if (!empty($_FILES['lot-photo']['name'])) {
		$tmp_name = $_FILES['lot-photo']['tmp_name'];
		$path = $_FILES['lot-photo']['name'];

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$file_type = finfo_file($finfo, $tmp_name);
		if ($file_type !== "image/jpg" && $file_type !== "image/png" && $file_type !== "image/jpeg" ) {
			$errors['lot-photo'] = 'Загрузите картинку в формате jpg/png/jpeg';	
		}
	}
	else {
		$errors['lot-photo'] = 'Загрузите картинку в формате jpg/png/jpeg';
	}
	
	// НЕТ ОШИБОК - ДОБАВЛЯЕМ ЛОТ
	if (!count($errors)) {
		move_uploaded_file($tmp_name, 'img/' . $path);
		$lot['lot-photo'] = $path;

		$add_lot = add_lot ($link, $lot, $is_auth);

		if ($add_lot) {
			$lot_id = mysqli_insert_id($link);
		
			header("Location: lot.php?lot_id=" . $lot_id);
		}
	}
}

$page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'categories_rows' => $categories_rows]);	
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);
