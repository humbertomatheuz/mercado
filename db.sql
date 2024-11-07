CREATE DATABASE supermercado;
USE supermercado;

CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    valor DECIMAL(10, 2) NOT NULL,
    codigo_barras VARCHAR(13) UNIQUE NOT NULL
);