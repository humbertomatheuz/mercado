<?php
require 'database.php';

function cadastrarCliente($nome, $cpf, $email, $telefone)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO clientes (nome, cpf, email, telefone) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$nome, $cpf, $email, $telefone]);
}

function consultarCliente($cpf)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function excluirCliente($id)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id = ?");
    return $stmt->execute([$id]);
}
