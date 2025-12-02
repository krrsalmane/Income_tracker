create database smartWallet;
use smartWallet;

create table income(
id int primary key auto_increment,
Income_Source text,
amount decimal not null,
description text,
my_date DATE DEFAULT (CURRENT_DATE)
);

create table expense(
id int primary key auto_increment,
amount decimal not null,
description text,
categorie varchar(30),
my_date DATE DEFAULT (CURRENT_DATE)
);