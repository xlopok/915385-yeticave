<?php


function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
        echo 'Файл недоступен для чтения';
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}


// Функция для прайстега
function price_tag ($number) {
    $ceil_number = ceil($number);
    return number_format( $ceil_number, 0,"." ," ") . " ₽";    
}