function goTo(pagina) {
  switch (pagina) {
    case "clientes":
      window.location.href = "views/clientes.html";
      break;
    case "produtos":
      window.location.href = "views/produtos.html";
      break;
    case "vendas":
      window.location.href = "views/vendas.html";
      break;
    default:
      alert("Página não encontrada");
  }
}
let totalPrice = 0.0;

function abrirGerenciamentoClientes() {
  window.open("paginas/clientes.html", "_blank");
}

function incluirProduto() {
  const barcode = document.getElementById("barcode").value;
  const quantity = parseInt(document.getElementById("quantity").value);

  // Verificação básica dos campos
  if (!barcode || quantity <= 0) {
    alert("Por favor, insira um código de barras e uma quantidade válida.");
    return;
  }

  // Simulação de dados do produto
  const produto = {
    nome: "Produto Exemplo",
    precoUnitario: 10.0, // Valor fictício
    quantidade: quantity,
    precoTotal: 10.0 * quantity,
  };

  // Adiciona o produto à tabela
  const tableBody = document
    .getElementById("productTable")
    .querySelector("tbody");
  const row = document.createElement("tr");
  row.innerHTML = `
        <td>${produto.nome}</td>
        <td>R$ ${produto.precoUnitario.toFixed(2)}</td>
        <td>${produto.quantidade}</td>
        <td>R$ ${produto.precoTotal.toFixed(2)}</td>
    `;
  tableBody.appendChild(row);

  // Atualiza o total da compra
  totalPrice += produto.precoTotal;
  document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);

  // Limpa os campos de entrada
  document.getElementById("barcode").value = "";
  document.getElementById("quantity").value = 1;
}
