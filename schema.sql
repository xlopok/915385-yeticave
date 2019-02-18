CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci; 

USE yeticave;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name CHAR(128) NOT NULL
);

CREATE TABLE lots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    name CHAR(128) NOT NULL,
    description TEXT,
    img TEXT,
    starting_price DECIMAL NOT NULL,
    dt_end TIMESTAMP, 
    bet_step INT NOT NULL,
    author_id INT,
    winner_user_id INT,
    category_id INT
);

CREATE INDEX author_id ON lots(author_id);
CREATE INDEX winner_user_id ON lots(winner_user_id);
CREATE INDEX category_id ON lots(category_id);

CREATE TABLE bets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dt_add TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    pricetag INT NOT NULL,
    user_id INT,
    lot_id INT
);

CREATE INDEX user_id ON bets(user_id);
CREATE INDEX lot_id ON bets(lot_id);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email CHAR(128) NOT NULL UNIQUE,
    user_name CHAR(128) NOT NULL,
    password CHAR(64) NOT NULL,
    avatar TEXT,
    contacts TEXT NOT NULL,
    created_lots INT,
    bets_id INT
);

CREATE INDEX created_lots ON users(created_lots);
CREATE INDEX bets_id ON users(bets_id);