INSERT INTO categories (name) 
VALUES 
    ('Доски и лыжи'), 
    ('Крепления'), 
    ('Ботинки'), 
    ('Одежда'), 
    ('Инструменты'), 
    ('Разное');

INSERT INTO users (email, user_name, password, contacts) 
VALUES
    ("vasek001@mail.ru", "Василий Васильев", "vasek96", "+79999999999"),
    ("JohnJr123@gmail.com", "John_Jones123", "jones12345", "+17042412122"),
    ("alex188@mail.ru", "alexEnot", "unnknownEnot3", "alex188@mail.ru"),
    ("kendrick-lamar95@gmail.com", "aint-that-simple123", "12345", "same as my email yo");

INSERT INTO lots (name, description, starting_price, bet_step, author_id, winner_user_id, category_id, img, dt_end) 
VALUES
    ("2014 Rossignol District Snowboardа","Дешевая, но сердитая доска", 10999, 1000, 1, null, 1, "img/lot-1.jpg", "2020-01-01"),
    ("DC Ply Mens 2016/2017 Snowboardа","Дорогущая и быстрая как ламба", 159999, 5000, 2, null, 1, "img/lot-2.jpg", "2020-01-01"),
    ("Крепления Union Contact Pro 2015 года размер L/XLа","Не вылетишь, крепят намертво", 8000, 500, 3, 4, 2, "img/lot-3.jpg", "2020-01-01"),
    ("Ботинки для сноуборда DC Mutiny Charocalа","Тепло, надежно, леопардово", 10999, 1000, 4, null, 3, "img/lot-4.jpg", "2020-01-01"),
    ("Куртка для сноуборда DC Mutiny Charocalа","Яркая и теплая куртка", 7500, 300, 1, 3, 4, "img/lot-5.jpg", "2020-01-01"),
    ("Маска Oakley Canopyа", "Сквозь эти очки хорошо видно трассу и не видно хейтеров", 5400, 200, 2, null, 6, "img/lot-6.jpg", "2020-01-01");

INSERT INTO bets (pricetag, user_id, lot_id) 
VALUES
    (11999, 2, 1),
    (12999, 3, 1);

-- Запросы:

-- получить все категории
SELECT * FROM categories;

-- получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
