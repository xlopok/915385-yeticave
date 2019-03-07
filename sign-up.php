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


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reg_form = $_POST;
    $errors = [];

    $req_fields = ['email', 'password', 'name', 'message'];

    foreach ($req_fields as $field) {
        if (empty($reg_form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }
    }
    // var_dump($_POST);
    // var_dump($errors);

    $file_name = $_FILES['avatar']['name'];
    // ПРОВЕРКА ФАЙЛА 
		if (!empty($_FILES['avatar']['name'])) {
			$tmp_name = $_FILES['avatar']['tmp_name'];
			$path = $_FILES['avatar']['name'];
	
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$file_type = finfo_file($finfo, $tmp_name);
			if ($file_type !== "image/jpg" && $file_type !== "image/png" && $file_type !== "image/jpeg") {
				$errors['avatar'] = 'Загрузите картинку в формате jpg/png/jpeg';	
			}
			else {
				move_uploaded_file($tmp_name, 'img/' . $path);
				$reg_form['avatar'] = $path;
			}
		}
		

    if (!empty($errors)) {
    $page_content = include_template('sign-up.php', ['categories_rows' => $categories_rows, 'errors' => $errors, 'reg_form' => $reg_form] );
    }

    else {
        // Проверим существование пользователя с email из формы

        $email = mysqli_real_escape_string($link, $reg_form['email']);
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $res = mysqli_query($link, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors['email'] = 'Пользователь с этим email уже зарегистрирован';
            $page_content = include_template('sign-up.php', ['categories_rows' => $categories_rows, 'errors' => $errors, 'reg_form' => $reg_form] );
        }

        else {
            $password = password_hash($reg_form['password'], PASSWORD_DEFAULT);
            if(!empty($_FILES['avatar']['name'])) {
            $sql = 'INSERT INTO users (
                registration_date,
                email,
                user_name,
                password, 
                avatar, 
                contacts) VALUES (NOW(), ?, ?, ?, ?, ?)';
            $stmt = db_get_prepare_stmt($link, $sql, [
                $reg_form['email'],
                $reg_form['name'],
                $password, 
                $reg_form['avatar'],
                $reg_form['message'] ]);
            }
            else {
                $sql = 'INSERT INTO users (
                    registration_date,
                    email,
                    user_name,
                    password, 
                    -- avatar, 
                    contacts) VALUES (NOW(), ?, ?, ?, ?)';
                $stmt = db_get_prepare_stmt($link, $sql, [
                    $reg_form['email'],
                    $reg_form['name'],
                    $password, 
                    // $reg_form['avatar'],
                    $reg_form['message'] ]);

            }

            // add_user ($link, $reg_form, $_FILES['avatar']['name']);
            
                $res = mysqli_stmt_execute($stmt);
        }


        if ($res && empty($errors)) {
            // header("Location: /login.php"); //ОТПРАВИТЬ НА СТРАНИЦУ ВХОДА
            // exit();
            $page_content = include_template('login.php', ['categories_rows' => $categories_rows]);
        }
        else {
            $page_content = include_template('404.php', 
            ['error' => 'Такого лота нет'] );
        }
    }

}

else { // ЕСЛИ ФОРМА ОТПРАВЛЕНА НЕ БЫЛА
	$errors = [];
	$page_content = include_template('sign-up.php', ['categories_rows' => $categories_rows, 'errors' => $errors ]);
	 
}






$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Регистрация', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);