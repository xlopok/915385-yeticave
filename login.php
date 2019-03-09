<?php
// Подключение файла с функциями
require_once('functions.php');
session_start();
// $is_auth = rand(0, 1);

// $user_name = 'Nikita Vorobev'; // укажите здесь ваше имя

// БД
require_once('mysql_connect.php'); // Подключение к бд 

mysqli_set_charset($link, "utf8"); // установка кодировки к бд

if (!$link) { //ЕСЛИЛ НЕТ РЕСУРСА СОЕДИНЕНИЯ, ТО ОШИБКА
    $error = mysqli_connect_error();
    show_error($page_content, $error);
    exit();
}

$categories_rows = get_catagories($link); // Передаем список категорий


if($_SERVER['REQUEST_METHOD'] == 'POST') { //ЕСЛИ ФОРМА ОТПРАВЛЕНА
    $login_form = $_POST;
    $errors = [];

    $req_fields = ['email', 'password'];

    foreach($req_fields as $field) {
        if(empty($login_form[$field])) {
            $errors[$field] = 'Поле ' .$field .' не может быть пустым';
        }
    }


    $email = mysqli_real_escape_string($link, $login_form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    

    if (!count($errors) and $user) { // ЕСЛИ В ВАЛИДАЦИИ ОШИБОК НЕТ И ЕСТЬ РЕСУРС СОЕДИНЕНИЯ, ТО ПРОВЕРЯЕМ ВВЕДЕННЫЙ ПАРОЛЬ С ПАРОЛЕМ ИЗ БД
        if (password_verify($login_form['password'], $user['password'])) {
            $_SESSION['user'] = $user;  // Если пароли совпадают, то открываем сессию и передаем данные польщователся
        }
        else { // Если ХЭШИ паролей не совпадают, то ошибка
            $errors['password'] = 'Вы ввели неверный пароль';
        }
    }
    else {
        $errors['email'] = 'Такой пользователь не найден';
    }
    
    if (count($errors)) { // ЕСТЬ ОШИБКИ В ФОРМЕ - СОХРАНЯЕМ И ПОКАЗЫВАЕМ
        $page_content = include_template('login.php', ['categories_rows' => $categories_rows, 'errors' => $errors, 'login_form' =>$login_form  ]);
    }
    
    else {
        header("Location: /");
        exit();
    }

}

else { // ФОРМА ОТПРАВЛЕНА НЕ БЫЛА 
    $errors = [];
    $page_content = include_template('login.php', ['categories_rows' => $categories_rows,'errors' => $errors]);
} 

$user_name = $_SESSION['user']['user_name'] ?? "";
$is_auth = $_SESSION['user']?? "";
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Вход', 'user_name' => $user_name, 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);



print($layout_content);