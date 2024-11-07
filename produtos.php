<?php include 'db.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    try {
        if ($action == 'add') {
            $nome = $_POST['nome'];
            $valor = $_POST['valor'];
            $codigo_barras = $_POST['codigo_barras'];

            if (empty($nome) || empty($valor) || empty($codigo_barras)) {
                throw new Exception("Todos os campos são obrigatórios.");
            }

            $stmt = $pdo->prepare("SELECT * FROM produtos WHERE codigo_barras = ?");
            $stmt->execute([$codigo_barras]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Código de barras já existe.");
            }

            $stmt = $pdo->prepare("INSERT INTO produtos (nome, valor, codigo_barras) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $valor, $codigo_barras]);
        } elseif ($action == 'delete') {
            $id = $_POST['id'];
            if (empty($id)) {
                throw new Exception("ID inválido.");
            }

            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($action == 'update') {
            $id = $_POST['id'];
            $nome = $_POST['nome'];
            $valor = $_POST['valor'];

            if (empty($id) || empty($nome) || empty($valor)) {
                throw new Exception("Todos os campos são obrigatórios.");
            }

            $stmt = $pdo->prepare("UPDATE produtos SET nome = ?, valor = ? WHERE id = ?");
            $stmt->execute([$nome, $valor, $id]);
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE ? OR codigo_barras LIKE ?");
$stmt->execute(["%$query%", "%$query%"]);
$produtos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Produtos</title>
    <link rel="stylesheet" href="assets/index.css">
</head>

<body>
    <div class="container_table">
        <div class="row">
            <h1>Gerenciamento de Produtos</h1>
        </div>
        <form method="POST" style="display:inline-block;">
            <h2>Adicionar Produto</h2>
            <input type="hidden" name="action" value="add">
            <input type="text" name="nome" placeholder="Nome do Produto" required>
            <input type="number" name="valor" step="0.01" placeholder="Valor" required>
            <input type="text" name="codigo_barras" placeholder="Código de Barras" required>
            <button type="submit">Adicionar</button>
        </form>
        <table>
            <tr>
                <th>Nome</th>
                <th>Valor</th>
                <th>Código de Barras</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo $produto['nome']; ?></td>
                    <td><?php echo $produto['valor']; ?></td>
                    <td><?php echo $produto['codigo_barras']; ?></td>
                    <td>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                            <input type="text" name="nome" value="<?php echo $produto['nome']; ?>" required>
                            <input type="number" name="valor" step="0.01" value="<?php echo $produto['valor']; ?>" required>
                            <input type="hidden" name="action" value="update">
                            <button type="submit">Atualizar</button>
                        </form>
                        <form method="POST" style="display:inline-block;">
                            <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">
                            <input type="hidden" name="action" value="delete">
                            <button type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
    </div>
    </table>
    <button class="voltar" onclick="goTo('index')">Voltar</button>
    <script src="assets/main.js"></script>
</body>

</html>