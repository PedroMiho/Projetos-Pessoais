package com.example.FinanERP.pagamentos;

import java.time.LocalDate;

public record DadosAtualizacaoPagamentos(
        Long id,
        Tipo tipo,
        String nomeCliente,
        TipoSaida saida,
        double valor,
        LocalDate dataPagamento,
        String descricao
) {
}
