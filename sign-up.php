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

$reg_form = $_POST;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $req_fields = ['email', 'password', 'name', 'message'];

    foreach ($req_fields as $field) {
        if (empty($reg_form[$field])) {
            $errors[$field] = "Не заполнено поле " . $field;
        }

        if (!isset($reg_form[$field])) {
            http_response_code(404);
            $page_content = include_template('404.php', ['categories_rows' => $categories_rows, 'error' => 'Заполните все обязательные поля']);
            $layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Yeticave - Добавление товара', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);
            print($layout_content);
            exit();
        }
        
    }

    // Проверяем формат имейла
    if(!filter_var($reg_form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Введите валидный имейл в формате name@mail.com';
    }

    // ПРОВЕРЯЕМ ИМЕЙЛ, УНИКАЛЕН ЛИ ОН
    $unique_email = unique_email_give_all($link, $reg_form, $errors);

    if (mysqli_num_rows($unique_email) > 0) { 
        $errors['email'] = 'Пользователь с этим email уже зарегистрирован';    
    }

     // ПРОВЕРКА ФАЙЛА 
    if (!empty($_FILES['avatar']['name'])) {
        $tmp_name = $_FILES['avatar']['tmp_name'];
        $path = uniqid() .$_FILES['avatar']['name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_name);
        if ($file_type !== "image/jpg" && $file_type !== "image/png" && $file_type !== "image/jpeg") {
            $errors['avatar'] = 'Загрузите картинку в формате jpg/png/jpeg';
           	
        }
        elseif(!count($errors)) {
            move_uploaded_file($tmp_name, 'img/' . $path);
            $reg_form['avatar'] = $path;
        }
    }
    else {
        $reg_form['avatar'] = '';
    }

    if (!count($errors)) { // ОШИБОК НЕТ - ДОБАВЛЯКМ ЮЗЕРА
        $res = add_user($link, $reg_form);

        if ($res && empty($errors)) {
            header("Location: /login.php"); //ОТПРАВИТЬ НА СТРАНИЦУ ВХОДА
            exit();
        }
        else {
            $page_content = include_template('404.php', 
            ['error' => 'Такого лота нет'] );
        }
    }
}

$page_content = include_template('sign-up.php', ['categories_rows' => $categories_rows, 'errors' => $errors, 'reg_form' => $reg_form] );
$layout_content = include_template('layout.php', ['content' =>$page_content, 'title' => 'Регистрация', 'is_auth' => $is_auth, 'categories_rows' => $categories_rows]);

print($layout_content);