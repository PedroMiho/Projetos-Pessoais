package com.example.FinanERP.pagamentos;
import jakarta.persistence.*;
import lombok.*;

import java.time.LocalDate;

@Table(name = "pagamento")
@Entity(name = "Pagamento")
@Getter
@AllArgsConstructor
@NoArgsConstructor
@EqualsAndHashCode(of = "id")
@ToString

public class Pagamento {

    @Id @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;
    @Enumerated(EnumType.STRING)
    private Tipo tipo;
    private String nomeCliente;
    @Enumerated(EnumType.STRING)
    private TipoSaida saida;
    private double valor;
    private LocalDate dataPagamento;
    private String descricao;

    public Pagamento(DadosCadastroPagamento dados) {
        this.tipo = dados.tipo();
        this.nomeCliente = dados.nomeCliente();
        this.saida = dados.saida();
        this.valor = dados.valor();
        this.dataPagamento = dados.dataPagamento();
        this.descricao = dados.descricao();

    }

    public void atualizarInformacoes(DadosAtualizacaoPagamentos dados) {
        if (this.tipo == Tipo.ENTRADA){
            if (dados.nomeCliente() != null){
                this.nomeCliente = dados.nomeCliente();
            }
        }else if (this.tipo == Tipo.SAIDA){
            if (dados.saida() != null) {
                this.saida = dados.saida();
            }
        }

        if (dados.valor() >= 0){
            this.valor = dados.valor();
        }
        if (dados.dataPagamento() != null){
            this.dataPagamento = dados.dataPagamento();
        }
        if(dados.descricao() != null){
            this.descricao = dados.descricao();
        }

    }
}
