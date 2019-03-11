<?php
// Подключение БД

$link = mysqli_connect("localhost", "root", "", "yeticave");
 
if ($link == false){
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
}
