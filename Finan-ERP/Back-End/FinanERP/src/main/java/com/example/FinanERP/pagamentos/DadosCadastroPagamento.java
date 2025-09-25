package com.example.FinanERP.pagamentos;

import java.time.LocalDate;

public record DadosCadastroPagamento(Tipo tipo, String nomeCliente, TipoSaida saida, double valor,
                                     LocalDate dataPagamento, String descricao) {
}
