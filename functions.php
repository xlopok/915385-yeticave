<?php


function include_template($name, $data) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return 'Файл недоступен для чтения';
      
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


// Функция для оставшегося времени лотов до удаления со страницы 

function time_for_lots() {
    date_default_timezone_set("Europe/Moscow");
    
    $tomorrow_midnight = strtotime("tomorrow 24:00"); //полуночь след дня
    
    $current_time = time(); // текущее время 
    
    $secs_to_midnight = $tomorrow_midnight - $current_time ; // останется времени до полуночи след дня
    
    $hours = floor($secs_to_midnight / 3600);
    $minutes = floor(($secs_to_midnight % 3600) / 60);
    
    $formatted_time = $hours . ":" . $minutes;
    return $formatted_time;
}

// Функция для БД - чтение категорий 

function get_catagories($link) {
    $categories_rows = [];

    $sql_categories = "SELECT * FROM categories";

    $result_categories = mysqli_query($link, $sql_categories);

    $categories_rows = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
    return $categories_rows;
}

// Функция для БД - чтение лотов

function get_lots($link) {
    $lots_rows = [];

    $sql_lots = "SELECT l.name as lot_name, starting_price, img, c.name as category_name
    FROM lots l
    JOIN  categories c
    ON category_id = c.id
    -- откинул цену из таблицы bets
    WHERE winner_user_id IS NULL
    ORDER BY l.dt_add DESC";

    $result_lots = mysqli_query($link, $sql_lots);

    $lots_rows = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
    return $lots_rows;
}