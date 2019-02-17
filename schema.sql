CREATE DATABASE yeticave
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci; 

USE yeticave;

create table categories (
id int auto_increment primary key,
category char(128) not null
);

create table lots (
id int auto_increment primary key,
dt_add timestamp default current_timestamp,
name char(128) not null,
description text,
img text,
starting_price decimal not null,
dt_end timestamp, 
bet_step int not null
);

create table bets (
id int auto_increment primary key,
dt_add timestamp default current_timestamp,
pricetag int not null);

create table users (
id int auto_increment primary key,
registration_date timestamp default current_timestamp,
email char(128) not null unique,
user_name char(128) not null unique,
passmord char(64) not null,
avatar text,
contacts text not null
)
;