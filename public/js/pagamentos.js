$(document).ready(function() {
    // Configuração global do AJAX para incluir o token CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Quando o modal de pagamento é aberto
    $('#pagamentoModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Botão que acionou o modal
        var saidaId = button.data('id');
        var valorTotal = button.data('valor-total');
        var valorRemanescente = button.data('valor-remanescente');

        var modal = $(this);

        // Formata os valores para moeda
        var formattedValorTotal = parseFloat(valorTotal).toLocaleString('pt-BR', { style: 'currency', currency: 'MZN' });
        var formattedValorRemanescente = parseFloat(valorRemanescente).toLocaleString('pt-BR', { style: 'currency', currency: 'MZN' });

        // Preenche os campos do modal
        modal.find('#pagamento_saida_id').val(saidaId);
        modal.find('#pagamento_valor_total').val(formattedValorTotal);
        modal.find('#pagamento_valor_remanescente_display').val(formattedValorRemanescente);
        modal.find('#pagamento_valor_a_pagar').val('').attr('max', valorRemanescente); // Limpa e define o max
        modal.find('#pagamento_novo_remanescente').val(formattedValorRemanescente);
        modal.find('#pagamento_tipo_pagamento_id').val('');
    });

    // Calcula o novo valor remanescente em tempo real
    $('#pagamento_valor_a_pagar').on('input', function() {
        var valorAPagar = parseFloat($(this).val()) || 0;
        var valorRemanescente = parseFloat($('#pagamentoModal').find('.btn-pagar').data('valor-remanescente')) || 0;
        
        // O valor remanescente original é o que está no atributo data do botão que abriu o modal
        var btnPagar = $('.btn-pagar[data-target="#pagamentoModal"]:visible');
        if(btnPagar.length > 0){
             valorRemanescente = parseFloat(btnPagar.data('valor-remanescente')) || 0;
        }
        
        var novoRemanescente = valorRemanescente - valorAPagar;

        var formattedNovoRemanescente = novoRemanescente.toLocaleString('pt-BR', { style: 'currency', currency: 'MZN' });
        $('#pagamento_novo_remanescente').val(formattedNovoRemanescente);
    });

    // Submete o formulário de pagamento
    $('#btn_salvar_pagamento').on('click', function() {
        showLoader();
        var form = $('#form_pagamento');
        var saidaId = form.find('#pagamento_saida_id').val();
        var valorAPagar = form.find('#pagamento_valor_a_pagar').val();
        var tipoPagamentoId = form.find('#pagamento_tipo_pagamento_id').val();

        // Validação simples no frontend
        if (!valorAPagar || valorAPagar <= 0) {
            Swal.fire('Erro', 'O valor a pagar deve ser maior que zero.', 'error');
            hideLoader();
            return;
        }
        if (!tipoPagamentoId) {
            Swal.fire('Erro', 'Por favor, selecione um método de pagamento.', 'error');
            hideLoader();
            return;
        }

        $.ajax({
            url: storePagamentoUrl, // Corrigido
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                saida_id: saidaId,
                valor_a_pagar: valorAPagar,
                tipo_pagamento_id: tipoPagamentoId
            },
            dataType: 'json',
            success: function(response) {
                hideLoader();
                $('#pagamentoModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    showConfirmButton: false,
                    timer: 2000
                });

                // Atualiza a lista de saídas para refletir a mudança
                $('.pesquisar').click(); 
            },
            error: function(xhr) {
                hideLoader();
                var errors = xhr.responseJSON.errors;
                var errorMessage = "Ocorreu um erro. Tente novamente.";
                if (errors) {
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                Swal.fire('Erro', errorMessage, 'error');
            }
        });
    });
});
