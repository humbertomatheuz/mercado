<?php
require 'database.php';       // Arquivo de configuração para conexão ao banco de dados
require 'models/Users.php';  // Arquivo onde estão as funções do modelo

header('Content-Type: application/json'); // Define o conteúdo da resposta como JSON

// Verifica o método da requisição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cadastrar Cliente
    if (isset($_POST['action']) && $_POST['action'] === 'cadastrar') {
        $nome = $_POST['nome'] ?? null;
        $cpf = $_POST['cpf'] ?? null;
        $email = $_POST['email'] ?? null;
        $telefone = $_POST['telefone'] ?? null;

        // Verificação de campos obrigatórios
        if (!$nome || !$cpf || !$email) {
            echo json_encode(['status' => 'error', 'message' => 'Campos obrigatórios não preenchidos']);
            exit;
        }

        // Chama a função de cadastro do modelo
        if (cadastrarCliente($nome, $cpf, $email, $telefone)) {
            echo json_encode(['status' => 'success', 'message' => 'Cliente cadastrado com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar cliente']);
        }
    }

    // Excluir Cliente
    elseif (isset($_POST['action']) && $_POST['action'] === 'excluir') {
        $id = $_POST['id'] ?? null;

        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'ID do cliente não fornecido']);
            exit;
        }

        if (excluirCliente($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Cliente excluído com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir cliente']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
    }
}

// Consulta de Cliente
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'consultar') {
        $cpf = $_GET['cpf'] ?? null;

        if (!$cpf) {
            echo json_encode(['status' => 'error', 'message' => 'CPF do cliente não fornecido']);
            exit;
        }

        // Chama a função de consulta do modelo
        $cliente = consultarCliente($cpf);

        if ($cliente) {
            echo json_encode(['status' => 'success', 'data' => $cliente]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Cliente não encontrado']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ação inválida']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método HTTP não permitido']);
}
