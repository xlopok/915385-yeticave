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
}
else {
    $categories_rows = get_catagories($link); // Есть ресурс соединения - передаем список категорий
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //ПРОВЕРКА БЫЛА ЛЕ ОТПРАВЛЕНА ФОРМА
	$lot = $_POST;

		$required = ['lot-name', 'category', 'message', 
		 'lot-rate', 'lot-step', 'lot-date']; //Обязательные для заполнения поля
		$dict = ['lot-name' => 'Введите название лота', 'message' => 'Описание лота', 'lot-photo' => 'Изображение товара', 'lot-rate' => 'Начальная цена', 'lot-step' => 'Шаг ставки', 'lot-date' => 'Дата окончания торгов']; //Описание полей (не пригодились)
    $errors = []; // Массив с ошибками

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

		// ПРОВЕРКА ФАЙЛА 
		if (!empty($_FILES['lot-photo']['name'])) {
			$tmp_name = $_FILES['lot-photo']['tmp_name'];
			$path = $_FILES['lot-photo']['name'];
	
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$file_type = finfo_file($finfo, $tmp_name);
			if ($file_type !== "image/jpg" && $file_type !== "image/png" && $file_type !== "image/jpeg") {
				$errors['lot-photo'] = 'Загрузите картинку в формате jpg/png/jpeg';	
			}
			else {
				move_uploaded_file($tmp_name, 'img/' . $path);
				$lot['lot-photo'] = $path;
			}
		}
		else {
			$errors['lot-photo'] = 'Загрузите картинку в формате jpg/png/jpeg';
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
			$min_date = date_create('tomorrow + 1 day')->format('Y-m-d');
			$end_date = date('Y-m-d', strtotime($lot['lot-date']));
			$end = date('Y-m-d', strtotime("January 19, 2038"));
			
			if ($end_date < $min_date) {
				$errors['lot-date'] = 'Дата не может быть меньше чем ' . date('d.m.Y', strtotime($min_date));
			}

			if ($end_date > $end ) {
				$errors['lot-date'] = 'Дата не может быть больше 19 Января 2038'  ;
			}
		}
		
    // ЕСЛИ ЕСТЬ ОШИБКИ В ФОРМЕ - СОХРАНЯЕМ ИХ И СНОВА ПОДКЛЮЧАЕМ ФОРМУ 
    if (count($errors)) {
		$page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict, 'categories_rows' => $categories_rows]);	
		}
		// ДОБАВЛЕНИЕ ЛОТА В БД ЕСЛИ ФОРМА ВАЛИДНА
		else {
			// СОЗДАЕМ ЗАПРОС В БД НА ДОБАВЛЕНИЕ НОВОГО ЛОТА
			add_lot ($link, $lot);

		}


		if (isset($_SESSION['user'])) {
			$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'user_name' => $_SESSION['user']['user_name'], 'is_auth' => $_SESSION['user'], 'categories_rows' => $categories_rows]);
	
		}
		else {
			$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'ОШИБКА', 'user_name' => null, 'is_auth' => null, 'categories_rows' => $categories_rows]);
		}

}
else { // ЕСЛИ ФОРМА ОТПРАВЛЕНА НЕ БЫЛА
	$errors = [];

	if (isset($_SESSION['user'])) {
		$page_content = include_template('add.php', ['categories_rows' => $categories_rows,'errors' => $errors]);
		$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'user_name' => $_SESSION['user']['user_name'], 'is_auth' => $_SESSION['user'], 'categories_rows' => $categories_rows]);

	}
	else {
		http_response_code(404);
			$page_content = include_template('404.php', ['categories_rows' => $categories_rows,'errors' => $errors, 'error' => 'ВОЙДИТЕ НА САЙТ']);
			$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'ОШИБКА', 'user_name' => null, 'is_auth' => null, 'categories_rows' => $categories_rows]);
	}

}

print($layout_content);
