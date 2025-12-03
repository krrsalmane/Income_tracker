CREATE DATABASE smartWallet;
USE smartWallet;

CREATE TABLE income (
    id INT PRIMARY KEY AUTO_INCREMENT,
    Income_Source VARCHAR(255),     
    amount DECIMAL(10,2) NOT NULL,  
    description TEXT,
    my_date DATE DEFAULT (CURRENT_DATE)
);

CREATE TABLE expense (
    id INT PRIMARY KEY AUTO_INCREMENT,
    amount DECIMAL(10,2) NOT NULL,  
    description TEXT,
    categorie VARCHAR(30),
    my_date DATE DEFAULT (CURRENT_DATE)
);

select * from expense;
