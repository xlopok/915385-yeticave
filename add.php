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
		if (isset($_FILES['lot-photo']['name'])) {
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
			$errors['lot-photo'] = 'Вы не загрузили файл';
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

	
		if (empty($lot['lot-date'])) { //Проверка даты
			$min_date = date_create('tomorrow + 1 day')->format('Y-m-d');
			$end_date = date('Y-m-d', strtotime($data['lot-date']));
			if ($end_date < $min_date) {
				$errors['lot-date'] = 'Дата не может быть меньше чем ' . date('d.m.Y', strtotime($min_date));
			}
	}
		
    // ЕСЛИ ЕСТЬ ОШИБКИ В ФОРМЕ - СОХРАНЯЕМ ИХ И СНОВА ПОДКЛЮЧАЕМ ФОРМУ 
    if (count($errors)) {
		$page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict, 'categories_rows' => $categories_rows]);	
		}
		// ДОБАВЛЕНИЕ ЛОТА В БД ЕСЛИ ФОРМА ВАЛИДНА
		else {
			// СОЗДАЕМ ЗАПРОС В БД
			$sql = 'INSERT INTO
			 lots (
				dt_add,
			  name, 
				description,
				img,
				starting_price, 
				dt_end,
				bet_step,
				author_id, 
				category_id) 
			VALUES (
				NOW(),?, ?, ?, ?, ?, ?, 1, ?)';

			$stmt = db_get_prepare_stmt($link, $sql, 
			[
				$lot['lot-name'],
				$lot['message'], 
				$lot['lot-photo'], 
				$lot['lot-rate'],
				$lot['lot-date'], 
				$lot['lot-step'],
				$lot['category']
			]);
			$res = mysqli_stmt_execute($stmt);


			// Перенаправляем польщователя на страницу созданного лота
			if ($res) {
				$lot_id = mysqli_insert_id($link);
	
				header("Location: lot.php?lot_id=" . $lot_id);
			}
	
			else {
				$page_content = include_template('404.php', 
				['error' => 'Такого лота нет'] );
			}
		}
}
else { // ЕСЛИ ФОРМА ОТПРАВЛЕНА НЕ БЫЛА
	$page_content = include_template('add.php', ['categories_rows' => $categories_rows]);
	 
}
    





// $page_content = include_template('add.php', ['categories_rows' => $categories_rows] );

$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);
