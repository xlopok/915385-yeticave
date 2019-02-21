INSERT INTO categories (category_name) 
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

INSERT INTO lots (lot_name, description, starting_price, bet_step, author_id, winner_user_id, category_id, img, dt_end) 
VALUES
    ("2014 Rossignol District Snowboardа","Дешевая, но сердитая доска", 10999, 1000, 1, null, 1, "lot-1.jpg", "2020-01-01"),
    ("DC Ply Mens 2016/2017 Snowboardа","Дорогущая и быстрая как ламба", 159999, 5000, 2, null, 1, "lot-2.jpg", "2020-01-01"),
    ("Крепления Union Contact Pro 2015 года размер L/XLа","Не вылетишь, крепят намертво", 8000, 500, 3, null, 2, "lot-3.jpg", "2020-01-01"),
    ("Ботинки для сноуборда DC Mutiny Charocalа","Тепло, надежно, леопардово", 10999, 1000, 4, null, 3, "lot-4.jpg", "2020-01-01"),
    ("Куртка для сноуборда DC Mutiny Charocalа","Яркая и теплая куртка", 7500, 300, 1, null, 4, "lot-5.jpg", "2020-01-01"),
    ("Маска Oakley Canopyа", "Сквозь эти очки хорошо видно трассу и не видно хейтеров", 5400, 200, 2, null, 6, "lot-6.jpg", "2020-01-01");

INSERT INTO bets (pricetag, user_id, lot_id) 
VALUES
    (11999, 2, 1),
    (12999, 3, 1);

-- Запросы:

-- получить все категории
SELECT * FROM categories
;

-- получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, название категории;
SELECT l.lot_name, starting_price, img, b.pricetag, c.category_name
FROM lots l
JOIN  categories c
ON category_id = c.id
JOIN bets b
ON l.id = b.lot_id
WHERE winner_user_id IS NULL
ORDER BY l.dt_add DESC 
;
--показать лот по его id. Получите также название категории, к которой принадлежит лот;
SELECT l.*, c.name 
FROM lots l 
JOIN  categories c 
ON category_id = c.id
WHERE l.id = 1
;

--обновить название лота по его идентификатору;
UPDATE lots
SET name = "Новое название лота"
WHERE id = 1
;

--получить список самых свежих ставок для лота по его идентификатору;
SELECT b.*, l.name
FROM bets b
JOIN lots l
ON b.lot_id = l.id
WHERE l.id = 1
;