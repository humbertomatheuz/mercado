<?php
require 'database.php';

function cadastrarProduto($nome, $codigo_barras, $preco)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO produtos (nome, codigo_barras, preco) VALUES (?, ?, ?)");
    return $stmt->execute([$nome, $codigo_barras, $preco]);
}

function excluirProduto($id)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
    return $stmt->execute([$id]);
}

function alterarPrecoProduto($id, $novo_preco)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE produtos SET preco = ? WHERE id = ?");
    return $stmt->execute([$novo_preco, $id]);
}

function consultarProdutoPorCodigo($codigo_barras)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE codigo_barras = ?");
    $stmt->execute([$codigo_barras]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>