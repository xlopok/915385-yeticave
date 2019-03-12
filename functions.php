<?php

// Подключает теймплейт, принимает имя темплейта и массив с данными
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

// Функция для прайстега - принимает сумму товара, преобразует ее в формат с разделителем и знаком рубля
function price_tag ($number) {
    $ceil_number = ceil($number);
    return number_format( $ceil_number, 0,"." ," ") . " ₽";    
}

// Время, которое останется до исчезновения лота - принимет время окончания торгов для лота и выдает его в формате ЧЧ:ММ
function time_interval ($time_end) {
    $time_now = strtotime('now');
    $time_end = strtotime($time_end);
    $interval = $time_end - $time_now;
    $hours = floor($interval/3600);
    $minutes = ceil(($interval - $hours*3600)/60);
    $time_lots = $hours . ":" . $minutes;
    return $time_lots;
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
    $sql_categories = "SELECT * FROM categories";

    $result_categories = mysqli_query($link, $sql_categories);
    if( $result_categories) {
        $categories_rows = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }
    return $categories_rows;
}

// Функция для БД - чтение лотов

function get_lots($link) {
    $sql_lots = "SELECT l.id ,l.name as lot_name, starting_price, l.dt_end, img, c.name as category_name
    FROM lots l
    JOIN  categories c
    ON category_id = c.id
    WHERE winner_user_id IS NULL
    ORDER BY l.dt_add DESC";
    
    $result_lots = mysqli_query($link, $sql_lots);
    if( $result_lots) {
        $lots_rows = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }
    return $lots_rows;
}

function get_lot($link, $lot_id) {
    $sql_lot = "SELECT l.id, l.name AS lot_name, l.description, l.img, l.starting_price, l.bet_step, l.dt_end, l.author_id, c.name AS category, MAX(b.pricetag) as max_bet 
    FROM lots l 
    JOIN categories c
    ON l.category_id = c.id
    JOIN bets b
    ON b.lot_id = l.id 
    WHERE l.id ='$lot_id';";

    $result_lot = mysqli_query($link, $sql_lot);

    if( $result_lot) {
        $show_lot = mysqli_fetch_assoc($result_lot);
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }
   
    return $show_lot;
}

// Получаем список ставок для конкретного лота на странице этого лота

function get_bets_for_lot($link, $lot_id) {
    $sql = "SELECT l.id, u.user_name, b.pricetag, b.user_id ,b.dt_add 
    FROM lots l
    JOIN bets b
    ON l.id = b.lot_id
    JOIN users u
    ON u.id = b.user_id
    WHERE l.id ='$lot_id'
    ORDER BY b.dt_add DESC
    LIMIT 10";
    
    $result_bets = mysqli_query($link, $sql);
    if($result_bets) {
        $show_bets = mysqli_fetch_all($result_bets, MYSQLI_ASSOC) ?? [];
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }
    return $show_bets;

}

// Добавляем лот

function add_lot ($link, $lot, $is_auth) {
    $sql = 'INSERT INTO lots (dt_add, name, description, img, starting_price, dt_end, bet_step, author_id, category_id) 
    VALUES (NOW(),?, ?, ?, ?, ?, ?, ?, ?)';

    $stmt = db_get_prepare_stmt($link, $sql,  
        [
        $lot['lot-name'],
        $lot['message'], 
        $lot['lot-photo'], 
        $lot['lot-rate'],
        $lot['lot-date'], 
        $lot['lot-step'],
        $is_auth['id'],
        $lot['category']
        ]);
   $res = mysqli_stmt_execute($stmt);
   if($res) {
    return $res;
   }
   else {
    $error = mysqli_error($link);
    $result = print('Ошибка MySQL ' . $error);
    exit();
   }
}


// Добавим ставку в таблицу ставок на странице лота 

function add_bet($link, $lot, $bet, $is_auth) {
    $sql = 'INSERT INTO bets (dt_add, pricetag, user_id, lot_id) 
    VALUES (NOW(), ?, ?, ?)';

    $stmt = db_get_prepare_stmt($link, $sql, 
        [
        $bet,
        $is_auth['id'],
        $lot['id']
        ]);

    $res = mysqli_stmt_execute($stmt);
    if($res) {
         return $res;
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }
}

// ФУНКЦИЯ НА СТРАНИЦЕ ФОРМЫ - РЕГИСТРАЦИИ ЮЗЕРА, ДОБАВЛЯЕТ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ

function add_user ($link, $reg_form) {
    $password = password_hash($reg_form['password'], PASSWORD_DEFAULT);

    $sql = 'INSERT INTO users (registration_date, email, user_name, password, avatar, contacts) 
    VALUES (NOW(), ?, ?, ?, ?, ?)';
    $stmt = db_get_prepare_stmt($link, $sql, 
        [
        $reg_form['email'],
        $reg_form['name'],
        $password, 
        $reg_form['avatar'],
        $reg_form['message'] 
        ]);

    $res = mysqli_stmt_execute($stmt);
    if($res) {
    return $res;
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }     
}

// Проверяем имейл на уникальность 
function unique_email_give_all($link, $login_form, $errors) {
    $email = mysqli_real_escape_string($link, $login_form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);
    
    if($res) {
        return $res;
    }
    else {
        $error = mysqli_error($link);
        $result = print('Ошибка MySQL ' . $error);
        exit();
    }
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = null;

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);
    }

    return $stmt;
}

