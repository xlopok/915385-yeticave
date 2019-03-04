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
			if (empty($_POST[$field])) {  // Если путой, то добавляем ошибку 
							$errors[$field] = 'Это поле надо заполнить';
			}
		}

		if (empty($_POST['lot-name'])) { //Проверка на пустоту имени лота
			$errors['lot-name'] = 'Введите наименование лота';
		}

		if (!is_numeric($_POST['category'])) { //Проверка дропдауна категорий
			$errors['category'] = 'Выберите категорию-ошибка';

			foreach($categories_rows as $categories_item) {
				if ($_POST['category'] !== $categories_item['id']) {
					$errors['category'] = 'Выберите категорию-ошибка';
				}
			}
		}

		// ПРОВЕРКА ФАЙЛА 
		if (isset($_FILES['lot-photo']['name'])) {
			$tmp_name = $_FILES['lot-photo']['tmp_name'];
			$path = $_FILES['lot-photo']['name'];
	
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$file_type = finfo_file($finfo, $tmp_name);
			if ($file_type !== "image/jpg" && $file_type !== "image/png" && $file_type !== "image/jpeg") {
				$errors['lot-photo'] = 'Загрузите картинку в формате jpg/png/jpeg';
				var_dump($file_type);
			}
			else {
				move_uploaded_file($tmp_name, 'img/' . $path);
				$lot['path'] = $path;
			}
		}
		else {
			$errors['lot-photo'] = 'Вы не загрузили файл';
		}



		if (empty($_POST['message'])) { //Проверка на пустоту описания
			$errors['message'] = 'Напишите описание лота';
		}

		if ($_POST['lot-rate'] <= 0) { //Проверка стартинг прайса
				$errors['lot-rate'] = 'Введите число больше нуля';

				if(empty($_POST['lot-rate']) ) {
					$errors['lot-rate'] = 'Введите начальную цену';
				}
		}

		if ($_POST['lot-step'] <= 0) { // Проверка шага ставки 
			$errors['lot-step'] = 'Введите число больше нуля';

			if(empty($_POST['lot-step']) ) {
				$errors['lot-step'] = 'Введите шаг ставки';
			}
		} 

		

		// if (empty($_POST['lot-date'])) {
			
		// 	$errors['lot-date'] = 'Введите дату окончания торгов';

		// 	if (check_date_format($_POST['lot-date']) === false) { //Проверяем дату
		// 		$errors['lot-date'] = 'Введите дату в формате «ДД.ММ.ГГГГ»';
		// 	}
		// }
		if (empty($_POST['lot-date'])) { //Проверка даты
			$min_date = date_create('tomorrow + 1 day')->format('Y-m-d');
			$end_date = date('Y-m-d', strtotime($data['lot-date']));
			if ($end_date < $min_date) {
				$errors['lot-date'] = 'Дата не может быть меньше чем ' . date('d.m.Y', strtotime($min_date));
			}
	}
		

		



    
    // if (isset($_FILES['lot-photo']['name'])) {
		// $tmp_name = $_FILES['lot-photo']['tmp_name'];
		// $path = $_FILES['lot-photo']['name'];

		// $finfo = finfo_open(FILEINFO_MIME_TYPE);
		// $file_type = finfo_file($finfo, $tmp_name);
		// if ($file_type !== "image/jpg" || $file_type !== "image/png" || $file_type !== "image/jpeg") {
		// 	$errors['file'] = 'Загрузите картинку в формате GIF';
		// }
		// else {
		// 	move_uploaded_file($tmp_name, 'img/' . $path);
		// 	$lot['path'] = $path;
		// }
    // }
    
    // else {
		// $errors['file'] = 'Вы не загрузили файл';
    // }
    
    if (count($errors)) {
		$page_content = include_template('add.php', ['lot' => $lot, 'errors' => $errors, 'dict' => $dict, 'categories_rows' => $categories_rows]);
		print_r($errors);
		// var_dump($_POST['lot-name']);
		// var_dump($_POST['category']);
		// var_dump($_POST['lot-date']);
		
		// print_r($categories_rows);
	
		}

		else { // НУЖНО БУДЕТ ИЗМЕНИТЬ НА ПОКАЗ СТРАНИЦЫ СЛОТОМ -НЕТ ОШИБОК _СНОВА ПОКАЗ ФОРМЫ
			$page_content = include_template('add.php', ['categories_rows' => $categories_rows]);
			 
		}
    
    // else {
		// $page_content = include_template('lot.php', ['lot' => $lot]);
    // }
    
}
else { // ЕСЛИ ФОРМА ОТПРАВЛЕНА НЕ БЫЛА
	$page_content = include_template('add.php', ['categories_rows' => $categories_rows]);
	 
}
    





// $page_content = include_template('add.php', ['categories_rows' => $categories_rows] );

$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);
