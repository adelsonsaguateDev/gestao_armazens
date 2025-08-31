@extends('layouts.main')

@section('title', 'Registar Nova Saída | Gestão de Armazens')

@section('content')

    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Registar Nova Saída</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>

                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('saida.add') }}" method="POST" id="form_registrar_saida" enctype="multipart/form-data">
                            @csrf
                            @include('saidas.form')

                            <div class="card-footer">
                                <a href="{{ route('saida.list') }}" class="btn btn-danger">Cancelar</a>
                                <button class="btn btn-success ml-2" id="confirmar_venda_btn" type="button" data-toggle="modal" data-target="#confirmarSaidaModal">Confirmar Venda</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
            <!-- end of main-content -->

            <!-- Footer Start -->
            @include('components.footer')
            <!-- fotter end -->
        </div>
    </div>

    @include('saidas.modal.ConfirmarSaida')

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".select2").select2({
            allowClear: true,
        });

        hideLoader();
        calculateTotals(); // Initial calculation

        // Add item to cart
        $('#add_item_to_cart').click(function() {
            var produto_id = $('#item_produto_id').val();
            var produto_text = $('#item_produto_id option:selected').text();
            var quantidade = parseFloat($('#item_quantidade').val()) || 0;
            var preco_unitario = parseFloat($('#item_preco_unitario').val()) || 0;
            var preco_compra = parseFloat($('#item_preco_compra').val()) || 0;
            var iva = parseFloat($('#item_iva').val()) || 0;
            var valor_iva = parseFloat($('#item_valor_iva').val()) || 0;
            var custo = parseFloat($('#item_custo').val()) || 0;
            var desconto_percentual = parseFloat($('#item_desconto_percentual').val()) || 0;
            var desconto_valor = parseFloat($('#item_desconto_valor').val()) || 0;
            var tipo_motivo = $('#item_tipo_motivo').val();
            var motivo = $('#item_motivo').val();

            if (!produto_id || quantidade <= 0 || preco_unitario <= 0 || preco_compra <= 0) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "Preencha o produto, quantidade, preço unitário e preço de compra com valores válidos.",
                });
                return;
            }

            // Check for duplicate product
            var isDuplicate = false;
            $('#itens_saida_table tbody tr').each(function() {
                if ($(this).find('input[name$="[produto_id]"]').val() == produto_id) {
                    isDuplicate = true;
                    return false; // Exit loop
                }
            });

            if (isDuplicate) {
                Swal.fire({
                    icon: "warning",
                    title: "Produto Duplicado",
                    html: "Este produto já foi adicionado à lista.",
                });
                return;
            }

            var rowIndex = $('#itens_saida_table tbody tr').length;
            var newRow = `
                <tr data-produto-id="${produto_id}">
                    <td>${rowIndex + 1}</td>
                    <td>${produto_text}<input type="hidden" name="itens[${rowIndex}][produto_id]" value="${produto_id}"></td>
                    <td>${quantidade}<input type="hidden" name="itens[${rowIndex}][quantidade]" value="${quantidade}"></td>
                    <td>${preco_unitario.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][preco_unitario]" value="${preco_unitario}"></td>
                    <td>${preco_compra.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][preco_compra]" value="${preco_compra}"></td>
                    <td>${iva.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][iva]" value="${iva}"></td>
                    <td>${valor_iva.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][valor_iva]" value="${valor_iva}"></td>
                    <td>${custo.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][custo]" value="${custo}"></td>
                    <td>${desconto_percentual.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][desconto_percentual]" value="${desconto_percentual}"></td>
                    <td>${desconto_valor.toFixed(2)}<input type="hidden" name="itens[${rowIndex}][desconto_valor]" value="${desconto_valor}"></td>
                    <td>${tipo_motivo}<input type="hidden" name="itens[${rowIndex}][tipo_motivo]" value="${tipo_motivo}"></td>
                    <td>${motivo}<input type="hidden" name="itens[${rowIndex}][motivo]" value="${motivo}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove_item_saida">Remover</button></td>
                </tr>
            `;
            $('#itens_saida_table tbody').append(newRow);

            // Clear form fields
            $('#item_produto_id').val('').trigger('change');
            $('#item_quantidade').val('');
            $('#item_preco_unitario').val('');
            $('#item_preco_compra').val('');
            $('#item_iva').val('');
            $('#item_valor_iva').val('');
            $('#item_custo').val('');
            $('#item_desconto_percentual').val('');
            $('#item_desconto_valor').val('');
            $('#item_tipo_motivo').val('');
            $('#item_motivo').val('');

            calculateTotals();
        });

        // Remover item
        $(document).on('click', '.remove_item_saida', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        // Update remanescente when valor_total changes
        $('#valor_total, #valor_pago').on('input', function() {
            calculateTotals();
        });

        function calculateTotals() {
            var total_venda_sum = 0;
            var total_compra_sum = 0;
            var total_iva_sum = 0;
            var total_valor_iva_sum = 0;
            var total_custo_sum = 0;
            var total_desconto_percentual_sum = 0;
            var total_desconto_valor_sum = 0;

            $('#itens_saida_table tbody tr').each(function() {
                var quantidade = parseFloat($(this).find('input[name$="[quantidade]"]').val()) || 0;
                var preco_unitario = parseFloat($(this).find('input[name$="[preco_unitario]"]').val()) || 0;
                var preco_compra = parseFloat($(this).find('input[name$="[preco_compra]"]').val()) || 0;
                var iva = parseFloat($(this).find('input[name$="[iva]"]').val()) || 0;
                var valor_iva = parseFloat($(this).find('input[name$="[valor_iva]"]').val()) || 0;
                var custo = parseFloat($(this).find('input[name$="[custo]"]').val()) || 0;
                var desconto_percentual = parseFloat($(this).find('input[name$="[desconto_percentual]"]').val()) || 0;
                var desconto_valor = parseFloat($(this).find('input[name$="[desconto_valor]"]').val()) || 0;

                total_venda_sum += quantidade * preco_unitario;
                total_compra_sum += quantidade * preco_compra;
                total_iva_sum += iva;
                total_valor_iva_sum += valor_iva;
                total_custo_sum += custo;
                total_desconto_percentual_sum += desconto_percentual;
                total_desconto_valor_sum += desconto_valor;
            });

            var total_geral_sum = total_venda_sum + total_valor_iva_sum;

            $('#total_venda_sum').text(total_venda_sum.toFixed(2));
            $('#total_compra_sum').text(total_compra_sum.toFixed(2));
            $('#total_iva_sum').text(total_iva_sum.toFixed(2));
            $('#total_valor_iva_sum').text(total_valor_iva_sum.toFixed(2));
            $('#total_custo_sum').text(total_custo_sum.toFixed(2));
            $('#total_desconto_percentual_sum').text(total_desconto_percentual_sum.toFixed(2));
            $('#total_desconto_valor_sum').text(total_desconto_valor_sum.toFixed(2));
            $('#total_geral_sum').text(total_geral_sum.toFixed(2));

            $('#valor_total_iva').val(total_valor_iva_sum.toFixed(2));
            $('#valor_total').val(total_geral_sum.toFixed(2)); // Update valor_total input

            var valor_total_input = parseFloat($('#valor_total').val()) || 0;
            var valor_pago_input = parseFloat($('#valor_pago').val()) || 0;
            var valor_remanescente = valor_total_input - valor_pago_input;
            $('#valor_remanescente').val(valor_remanescente.toFixed(2));

            // Update modal fields
            $('#modal_custo').val(total_venda_sum.toFixed(2));
            $('#modal_total_taxa').val(total_valor_iva_sum.toFixed(2));
            $('#modal_custo_total').val(total_geral_sum.toFixed(2));
        }

        // When modal is shown, update values
        $('#confirmarSaidaModal').on('show.bs.modal', function (e) {
            calculateTotals(); // Recalculate just before showing
        });

        // Submeter formulário com AJAX (agora do modal)
        $('#registar_venda_modal').click(function() {
            showLoader();

            var total_venda_sum_calculated = 0;
            $('#itens_saida_table tbody tr').each(function() {
                var quantidade = parseFloat($(this).find('input[name$="[quantidade]"]').val()) || 0;
                var preco_unitario = parseFloat($(this).find('input[name$="[preco_unitario]"]').val()) || 0;
                total_venda_sum_calculated += quantidade * preco_unitario;
            });

            var valor_total_input = parseFloat($('#valor_total').val()) || 0;

            if (valor_total_input < total_venda_sum_calculated) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "O Valor Total da Factura não pode ser menor que o Total de Vendas dos itens.",
                });
                hideLoader();
                return;
            }

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('tipo_saida_id', $('#tipo_saida_id').val());
            formData.append('cliente_id', $('#cliente_id').val());
            formData.append('numero_factura', $('#numero_factura').val());
            formData.append('data', $('#data').val());
            formData.append('valor_total', $('#valor_total').val());
            formData.append('valor_total_iva', $('#valor_total_iva').val());
            formData.append('valor_pago', $('#valor_pago').val());
            formData.append('valor_remanescente', $('#valor_remanescente').val());
            formData.append('desconto', $('#desconto').val());
            formData.append('valor_entregue', $('#valor_entregue').val());
            formData.append('trocos', $('#trocos').val());
            formData.append('tipo_pagamento_id', $('#tipo_pagamento_id').val());
            formData.append('numero', $('#numero').val());
            formData.append('numero_cotacao', $('#numero_cotacao').val());
            formData.append('validade_cotacao', $('#validade_cotacao').val());
            formData.append('slip', $('#slip').val());
            formData.append('estado_pagamento', $('#estado_pagamento').val());
            formData.append('activo', $('#activo').val());

            var itens = [];
            $('#itens_saida_table tbody tr').each(function() {
                var item = {
                    produto_id: $(this).find('input[name$="[produto_id]"]').val(),
                    quantidade: $(this).find('input[name$="[quantidade]"]').val(),
                    preco_unitario: $(this).find('input[name$="[preco_unitario]"]').val(),
                    preco_compra: $(this).find('input[name$="[preco_compra]"]').val(),
                    iva: $(this).find('input[name$="[iva]"]').val(),
                    valor_iva: $(this).find('input[name$="[valor_iva]"]').val(),
                    custo: $(this).find('input[name$="[custo]"]').val(),
                    desconto_percentual: $(this).find('input[name$="[desconto_percentual]"]').val(),
                    desconto_valor: $(this).find('input[name$="[desconto_valor]"]').val(),
                    tipo_motivo: $(this).find('input[name$="[tipo_motivo]"]').val(),
                    motivo: $(this).find('input[name$="[motivo]"]').val(),
                };
                itens.push(item);
            });

            formData.append('itens', JSON.stringify(itens));

            // Add modal specific fields
            formData.append('modal_total_desconto', $('#modal_total_desconto').val());
            formData.append('modal_forma_pagamento', $('#modal_forma_pagamento').val());

            $.ajax({
                url: '{{ route('saida.add') }}',
                method: 'POST',
                data: formData,
                processData: false,  // Importante!
                contentType: false,  // Importante!
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {
                        Swal.fire({
                            icon: "success",
                            title: `${response.message}`,
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(() => {
                            window.location.href = "{{ route('saida.list') }}";
                        });
                    } else {
                        var errorMessages = response.message;
                        if (typeof errorMessages === 'object') {
                            errorMessages = Object.values(errorMessages).join('<br>');
                        }
                        Swal.fire({
                            icon: "error",
                            title: "Erro de Validação",
                            html: errorMessages,
                        });
                    }
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire({
                        icon: "error",
                        title: "Ocorreu um erro no servidor.",
                        showConfirmButton: false,
                        timer: 2000,
                    });
                }
            }).always(function() {
                hideLoader();
            });
        });
    });
</script>
@endsection