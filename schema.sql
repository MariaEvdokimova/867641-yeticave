CREATE DATABASE yeticave
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE TABLE categories (
  id_category MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(128) UNIQUE NOT NULL
)ENGINE=InnoDB CHARACTER SET=UTF8;

CREATE TABLE users (
  id_user MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  email VARCHAR(128) NOT NULL UNIQUE,
  name VARCHAR(128) NOT NULL,
  password VARCHAR(64) NOT NULL,
  avatar TEXT,
  contacts VARCHAR(255) NOT NULL,

  INDEX email_password (email, password)
)ENGINE=InnoDB CHARACTER SET=UTF8;

CREATE TABLE lot (
  id_lot MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  lot_name VARCHAR(128) NOT NULL,
  description TEXT NOT NULL,
  img_url CHAR(128) NOT NULL,
  start_price INT NOT NULL DEFAULT 0,
  end_datetime TIMESTAMP NOT NULL,
  step_bet INT NOT NULL DEFAULT 0,
  id_author MEDIUMINT NOT NULL,
  id_winner MEDIUMINT,
  id_category MEDIUMINT NOT NULL,

  FOREIGN KEY (id_author) REFERENCES users(id_user),
  FOREIGN KEY (id_category) REFERENCES categories(id_category),

  INDEX lot_category (lot_name, id_category)
)ENGINE=InnoDB CHARACTER SET=UTF8;

CREATE FULLTEXT INDEX lot_ft_search ON lot(lot_name, description);

CREATE TABLE bet (
  id_bet MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  sum_bet INT NOT NULL DEFAULT 0,
  id_user MEDIUMINT NOT NULL,
  id_lot MEDIUMINT NOT NULL,

  FOREIGN KEY (id_user) REFERENCES users(id_user),
  FOREIGN KEY (id_lot) REFERENCES lot(id_lot),

  INDEX user_lot (id_user, id_lot)
)ENGINE=InnoDB CHARACTER SET=UTF8;
