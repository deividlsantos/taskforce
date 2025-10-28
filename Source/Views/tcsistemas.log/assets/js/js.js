let ordemCrescente = true;
//função usada para ordernar os campos de forma crescente e decrescente
function ordenarTabela(coluna) {
  let tabela = document.getElementById("minhaTabela");
  let tbody = tabela.querySelector("tbody");
  let linhas = Array.from(tbody.querySelectorAll("tr")); // pega só as linhas do corpo

  linhas.sort((a, b) => {
    let valorA = parseInt(a.cells[coluna].innerText);
    let valorB = parseInt(b.cells[coluna].innerText);
    return ordemCrescente ? valorA - valorB : valorB - valorA;
  });

  // recoloca as linhas ordenadas
  linhas.forEach(linha => tbody.appendChild(linha));

  ordemCrescente = !ordemCrescente;
}