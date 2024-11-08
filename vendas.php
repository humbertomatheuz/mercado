<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        if ($action == 'add') {
            $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
            $valor = isset($_POST['valor']) ? $_POST['valor'] : '';
            $codigo_barras = isset($_POST['codigo_barras']) ? $_POST['codigo_barras'] : '';

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
            $id = isset($_POST['id']) ? $_POST['id'] : '';
            if (empty($id)) {
                throw new Exception("ID inválido.");
            }

            $stmt = $pdo->prepare("DELETE FROM produtos WHERE id = ?");
            $stmt->execute([$id]);
        } elseif ($action == 'update_quantity') {
            $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
            $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
            if (isset($_SESSION['sale_items'][$item_id])) {
                $_SESSION['sale_items'][$item_id]['quantity'] = $quantity;
            }
        } elseif ($action == 'remove') {
            $item_id = isset($_POST['item_id']) ? $_POST['item_id'] : '';
            if (isset($_SESSION['sale_items'][$item_id])) {
                unset($_SESSION['sale_items'][$item_id]);
            }
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

function gerarCSV($sale_items, $pdo)
{
    if (empty($sale_items)) {
        echo "Nenhum item para imprimir!";
        exit;
    }

    $filename = "venda_" . date('Y-m-d_H-i-s') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['Produto', 'Valor', 'Quantidade', 'Subtotal']);

    $total = 0;
    foreach ($sale_items as $item_id => $item_data) {
        $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$item_id]);
        $produto = $stmt->fetch();

        if ($produto) {
            $subtotal = $produto['valor'] * $item_data['quantity'];
            $total += $subtotal;

            fputcsv($output, [
                $produto['nome'],
                'R$ ' . number_format($produto['valor'], 2, ',', '.'),
                $item_data['quantity'],
                'R$ ' . number_format($subtotal, 2, ',', '.')
            ]);
        }
    }

    fputcsv($output, ['Total', '', '', 'R$ ' . number_format($total, 2, ',', '.')]);

    fclose($output);
    exit;
}

if (isset($_POST['imprimir'])) {
    gerarCSV($_SESSION['sale_items'], $pdo);
}

$query = isset($_GET['query']) ? $_GET['query'] : '';
$stmt = $pdo->prepare("SELECT * FROM produtos WHERE nome LIKE ? OR codigo_barras LIKE ?");
$stmt->execute(["%$query%", "%$query%"]);
$produtos = $stmt->fetchAll();

if (!isset($_SESSION['sale_items'])) {
    $_SESSION['sale_items'] = [];
}

if (isset($_POST['sale_items']) && !empty($_POST['sale_items'])) {
    foreach ($_POST['sale_items'] as $item_id) {
        if (!isset($_SESSION['sale_items'][$item_id])) {
            $_SESSION['sale_items'][$item_id] = ['id' => $item_id, 'quantity' => 1];
        }
    }
}

$total = 0;
foreach ($_SESSION['sale_items'] as $item_id => $item_data) {
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
    $stmt->execute([$item_id]);
    $produto = $stmt->fetch();
    if ($produto) {
        $total += $produto['valor'] * $item_data['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Vendas</title>
    <link rel="stylesheet" href="assets/index.css">
</head>

<body>
    <div class="container_table">
        <h1>Vendas</h1>

        <form method="GET">
            <input type="text" name="query" placeholder="Pesquise por nome ou código de barras" value="<?php echo $query; ?>" required>
            <button type="submit">Pesquisar</button>
        </form>

        <table>
            <tr>
                <th>Nome</th>
                <th>Valor</th>
                <th>Código de Barras</th>
                <th>Adicionar</th>
            </tr>
            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?php echo $produto['nome']; ?></td>
                        <td><?php echo $produto['valor']; ?></td>
                        <td><?php echo $produto['codigo_barras']; ?></td>
                        <td>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="sale_items[]" value="<?php echo $produto['id']; ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit">Adicionar ao carrinho</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum produto encontrado.</td>
                </tr>
            <?php endif; ?>
        </table>

        <h2>Itens na Venda</h2>
        <table>
            <tr>
                <th>Nome</th>
                <th>Valor</th>
                <th>Quantidade</th>
                <th>Remover</th>
            </tr>
            <?php if (!empty($_SESSION['sale_items'])): ?>
                <?php foreach ($_SESSION['sale_items'] as $item_id => $item_data): ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id = ?");
                    $stmt->execute([$item_id]);
                    $produto = $stmt->fetch();
                    if ($produto):
                    ?>
                        <tr>
                            <td><?php echo $produto['nome']; ?></td>
                            <td><?php echo $produto['valor']; ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="number" name="quantity" value="<?php echo $item_data['quantity']; ?>" min="1" required>
                                    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                    <input type="hidden" name="action" value="update_quantity">
                                    <button type="submit">Atualizar</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit">Remover</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum item adicionado.</td>
                </tr>
            <?php endif; ?>
        </table>

        <h3>Total: R$ <?php echo number_format($total, 2, ',', '.'); ?></h3>

        <form method="POST" action="vendas.php">
            <button type="submit" name="imprimir">Imprimir Venda</button>
        </form>
        <button class="voltar" onclick="goTo('index')">Voltar</button>
    </div>

    <script src="assets/main.js"></script>
</body>

</html>