<?php

/**
   *  Фукция шаблонизатор
   *  Функция принимает два аргумента: имя файла шаблона и ассоциативный массив с данными для этого шаблона.
   *  Функция возвращает строку — итоговый HTML-код с подставленными данными.
   *
   *  @param string $name - имя файла шаблона.
   *  @param mixed [] $data - ассоциативный массив с данными для этого шаблона.
   *
   *  @return string - возвращает строку - итоговый HTML-код с подставленными данными 
   *
   */
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

/**
   * Функция для прайстега - принимает сумму товара, преобразует ее в формат с разделителем и знаком рубля
   * Функция принимает один аргумент — целое число.
   * 
   * @param  $price int - исходящая цена лота.
   * @return string - возвращает строку с округленным, разделенными на разряды число со знаком ₽.
   *
   */
function price_tag ($number) {
    $ceil_number = ceil($number);
    return number_format( $ceil_number, 0,"." ," ") . " ₽";    
}

/**
   * Функция - Время, которое останется до исчезновения лота
   * Функция принимает один аргумент — целое число.
   * 
   * @param  $price int - дата, до которой лот будет учавствовать в аукционе.
   * @return string - возвращает  строку в формате ЧЧ:ММ
   *
   */
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
    * Функция провкрка даты
    * Функция проверяет, что переданная дата соответствует формату ДД.ММ.ГГГГ
    * @param string $date строка с датой
    * @return bool true/false
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

function show_error($error) {
    $page_content = include_template('404.php', ['error' => $error]);
    print($page_content);
}

/**
    *
    * Функция получает список категорий из бд
    * @param $link mysqli Ресурс соединения
    * 
    * @return array - возвращает массив со списком категорий.
    *
    */
function get_catagories($link) {
    $sql_categories = "SELECT * FROM categories";

    $result_categories = mysqli_query($link, $sql_categories);
    if( $result_categories) {
        $categories_rows = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
    }
    else {
        $error = mysqli_error($link);
        exit();
    }
    return $categories_rows;
}

/**
    * Функция получает список лотов из бд
    * @param $link mysqli Ресурс соединения
    * 
    * @return array - возвращает массив со списком лотов.
    *
    */
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
        exit();
    }
    return $lots_rows;
}

/**
    * Функция получает список данных по конкретному лоту из бд
    * @param $link mysqli Ресурс соединения
    * @param $lot_id string - id лота
    * @return array - возвращает массив со списком данных о конкретном лоте.
    *
    */
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
        exit();
    }
   
    return $show_lot;
}

/**
    * Функция получает список ставок для конкретного лота из бд
    * @param $link mysqli Ресурс соединения
    * @param $lot_id string - id лота
    * @return array - возвращает массив со списком ставок на конкретном лоте.
    *
    */
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
        exit();
    }
    return $show_bets;

}

/**
   * Функция добавляет новую запись в таблице лотов lots
   * @param $link mysqli Ресурс соединения
   * @param $lot array - данные формы из массива $_POST
   * @param $is_auth string - данные о сессии юзера
   * @return $res bool - ресурс/результат запроса к бд.
   */
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
    exit();
   }
}

 
/**
   * Функция добавляет новую запись ставки в таблице bets
   * @param $link mysqli Ресурс соединения
   * @param $lot array - массив со списком данных о лоте
   * @param $bet string - данные формы ставки из массива $_POST
   * @param $is_auth string - данные о сессии юзера
   * @return $res bool - ресурс/результат запроса к бд.
   */

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
        exit();
    }
}

/**
   * Функция добавляет пользователя
   * @param $link mysqli Ресурс соединения
   * @param $reg_form array - данные формы из массива $_POST
   * @return $res bool - ресурс/результат запроса к бд.
   *
   */

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
        exit();
    }     
}

/**
   * Функция Проверяем имейл на уникальность
   * @param $link mysqli Ресурс соединения
   * @param $login_form array - данные формы из массива $_POST
   * @return array - ресурс/результат запроса к бд.
   *
*/
function unique_email_give_all($link, $login_form) {
    $email = mysqli_real_escape_string($link, $login_form['email']);
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $res = mysqli_query($link, $sql);
    
    if($res) {
        return $res;
    }
    else {
        $error = mysqli_error($link);
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

