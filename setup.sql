-- SQL script to create the database and table for client payments
CREATE DATABASE IF NOT EXISTS payment_tracker;
USE payment_tracker;

CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    photo VARCHAR(255)
);