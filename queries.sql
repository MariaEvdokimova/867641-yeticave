USE yeticave;

INSERT INTO categories (category_name)
VALUES ('Доски и лыжи'), ('Крепления'), ('Ботинки'), ('Одежда'), ('Инструменты'), ('Разное');

INSERT INTO users (email, name, password, contacts)
VALUES
	('vasy@mail.ru', 'Вася', 'Qq123', 'тел. (495)123-45-67'),
	('pasha@mail.ru', 'Паша', 'Aa456', 'тел. (495)897-88-99'),
	('anna@mail.ru', 'Анна', 'Zz123', 'тел. (495)787-55-66');

INSERT INTO lot (lot_name, description, img_url, start_price, end_datetime, step_bet, id_author, id_category)
VALUES
	('2014 Rossignol District Snowboard', 'Сноуборд', 'img/lot-1.jpg', 10999, '2019-03-20', 11000, 1, 1),
	('DC Ply Mens 2016/2017 Snowboard', 'Сноуборд', 'img/lot-2.jpg', 159999, '2019-02-20', 160000, 2, 1),
	('Крепления Union Contact Pro 2015 года размер L/XL', 'Крепления', 'img/lot-3.jpg', 8000, '2019-03-02', 8500, 1, 2),
	('Ботинки для сноуборда DC Mutiny Charocal', 'Ботинки', 'img/lot-4.jpg', 10999, '2019-03-01', 11000, 2, 3),
	('Куртка для сноуборда DC Mutiny Charocal', 'Куртка', 'img/lot-5.jpg', 7500, '2019-02-24', 8000, 1, 4),
	('Маска Oakley Canopy', 'Маска', 'img/lot-6.jpg', 5400, '2019-03-17', 6000, 2, 6);

INSERT INTO bet (sum_bet, id_user, id_lot)
VALUES
	(12000, 2, 1),
	(13000, 3, 1);

SELECT category_name FROM categories;

SELECT l.lot_name, l.start_price, l.img_url, l.step_bet, c.category_name
FROM
	lot l
	LEFT JOIN categories c ON l.id_category = c.id_category
WHERE
	l.end_datetime > NOW()
;

SELECT l.lot_name, c.category_name
FROM
	lot l
	LEFT JOIN categories c ON c.id_category = l.id_category
WHERE
	l.id_lot = 1
;

UPDATE lot SET lot_name = 'Маска Oakley Canopy_test'
WHERE id_lot = 6;

SELECT l.lot_name, b.sum_bet
FROM bet b
	LEFT JOIN lot l ON l.id_lot = b.id_lot
WHERE
	 b.id_lot = 1
ORDER BY b.sum_bet DESC
LIMIT 2
;	