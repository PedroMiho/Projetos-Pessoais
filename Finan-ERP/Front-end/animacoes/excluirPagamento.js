function excluirPagamento(id){
    if (confirm("Tem certeza que deseja excluir este registro?")) {
    fetch(`http://localhost:8080/pagamentos/${id}`, {
        method: "DELETE"
    })
    .then(response => {
        if (response.ok) {
            alert("Registro excluído com sucesso!");
            buscaPagamentos();
        } else {
            alert("Erro ao excluir o registro.");
        }
    })
    .catch(error => {
        console.error("Erro:", error);
        alert("Erro na comunicação com o servidor.");
    });
  }
    
}