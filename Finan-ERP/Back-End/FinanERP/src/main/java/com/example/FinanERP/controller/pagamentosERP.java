package com.example.FinanERP.controller;

import com.example.FinanERP.pagamentos.*;
import jakarta.transaction.Transactional;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@CrossOrigin(origins = "*")
@RestController
@RequestMapping("/pagamentos")
public class pagamentosERP {

    @Autowired
    private PagamentoRepository repository;

    @PostMapping
    @Transactional
    public void cadastrarPagamento(@RequestBody DadosCadastroPagamento dados){
        repository.save(new Pagamento(dados));
    }

    @GetMapping("/tipo/{tipo}")
    public List<Pagamento> listarPorTipo(@PathVariable Tipo tipo){
        return repository.findByTipo(tipo);
    }

    @PutMapping
    @Transactional
    public void atualizar(@RequestBody DadosAtualizacaoPagamentos dados){
        var pagamento = repository.getReferenceById(dados.id());
        pagamento.atualizarInformacoes(dados);
    }

    @DeleteMapping("/{id}")
    @Transactional
    public  void excluirPagamento(@PathVariable Long id){
        repository.deleteById(id);
    }


}
