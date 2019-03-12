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

$login_form = $_POST;
$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') { //ЕСЛИ ФОРМА ОТПРАВЛЕНА
 
    $req_fields = ['email', 'password'];

    foreach($req_fields as $field) {
        if(empty($login_form[$field])) {
            $errors[$field] = 'Поле ' .$field .' не может быть пустым';
        }
        if (!isset($login_form[$field])) {
            http_response_code(404);
            $page_content = include_template('404.php', ['categories_rows' => $categories_rows, 'error' => 'Заполните все обязательные поля']);
            $layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);
            print($layout_content);
            exit();
        }
    }
    
    $res = unique_email_give_all($link, $login_form, $errors); 

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
    
    if (!count($errors)) { // НЕТ ОШИБОК В ФОРМЕ - СОХРАНЯЕМ И ПОКАЗЫВАЕМ
        header("Location: /");
        exit();
    }
}

$page_content = include_template('login.php', ['categories_rows' => $categories_rows, 'errors' => $errors, 'login_form' =>$login_form  ]);
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Вход', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);