<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Supermercado Super</title>
    <link rel="stylesheet" href="assets/index.css" />
</head>

<body>
    <div class="container">
        <div class="left-section">
            <img src="https://www.designi.com.br/images/preview/10438126.jpg" alt="Imagem do Supermercado" class="main-image" />
        </div>

        <div class="right-section">
            <button onclick="goTo('produtos')">Gerenciamento de Produtos</button>
            <button onclick="goTo('vendas')">Vendas</button>
        </div>
    </div>

    <script src="assets/main.js"></script>
</body>

</html>