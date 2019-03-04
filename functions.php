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

/**
* Проверяет, что переданная дата соответствует формату ДД.ММ.ГГГГ
* @param string $date строка с датой
* @return bool
*/
function check_date_format($date) {
   $result = false;
   $regexp = '/(\d{2})\.(\d{2})\.(\d{4})/m';
   if (preg_match($regexp, $date, $parts) && count($parts) == 4) {
       $result = checkdate($parts[2], $parts[1], $parts[3]);
   }
   return $result;
}


// Функция подключения страницы с ошибкой

function show_error(&$content, $error) {
    $page_content = include_template('404.php', ['error' => $error]);
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

    $sql_lots = "SELECT l.id ,l.name as lot_name, starting_price, img, c.name as category_name
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

function get_lot($link, $lot_id) {
    $show_lot = [];

    $sql_lot = "SELECT l.id, l.name AS lot_name, l.description, l.img, l.starting_price, c.name AS category 
    FROM lots l 
    JOIN categories c
    ON l.category_id = c.id
    WHERE l.id ='$lot_id';";

    $result_lot = mysqli_query($link, $sql_lot);

    $show_lot = mysqli_fetch_assoc($result_lot);

    return $show_lot;
}