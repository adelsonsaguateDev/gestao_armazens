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
                        <form action="{{ route('saida.add') }}" method="POST" id="form_registrar_saida"
                            enctype="multipart/form-data">
                            @csrf
                            <div><a style="color: red;text-align:center;">Nota: O ASTERISCO(*) indica que o campo é
                                    obrigatório.</a> </div>

                            <div class="card">
                                <div class="mt-3">
                                    <div class="col-md text-left">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="col-md-3 form-group">
                                                        <label for="cliente_id"><b>Cliente</b></label>
                                                        <select class="form-control select2" name="cliente_id"
                                                            id="cliente_id">
                                                            <option value="">Selecione...</option>
                                                            @foreach ($clientes as $item)
                                                                <option value="{{ $item->id }}">{{ $item->nome }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3 form-group">
                                                        <label for="item_produto_id"><b>Produto</b><span
                                                                class="obrigatorio">*</span></label>
                                                        <select class="form-control select2" name="item_produto_id" id="item_produto_id">
                                                            <option value="">Selecione...</option>
                                                            @foreach ($produtos as $produto)
                                                                <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 form-group">
                                                        <label for="item_lote_id"><b>Lote</b><span
                                                                class="obrigatorio">*</span></label>
                                                        <select class="form-control select2" id="item_lote_id" disabled>
                                                            <option value="">Selecione um produto primeiro...</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 form-group">
                                                        <label for="item_quantidade"><b>Quantidade</b><span
                                                                class="obrigatorio">*</span></label>
                                                        <input type="number" class="form-control" id="item_quantidade"
                                                            required min="1">
                                                    </div>
                                                    <div class="col-md-2 form-group">
                                                        <label for="item_preco_unitario"><b>Preço Unitário</b><span
                                                                class="obrigatorio">*</span></label>
                                                        <input type="number" class="form-control" id="item_preco_unitario"
                                                            step="0.01" required min="0.01">
                                                    </div>
                                                    <div class="col-md-2 form-group">
                                                        <label for="item_total"><b>Total</b></label>
                                                        <input type="number" class="form-control" id="item_total"
                                                            step="0.01" readonly>
                                                    </div>
                                                </div>
                                                <div class="text-right mt-4 mb-2">
                                                    <button type="button" class="btn btn-primary btn-lg"
                                                        id="add_item_to_cart"> <i class="fas fa-cart-plus"></i> </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="card mt-4">
                                <div class="card-header"><b>Produtos Adicionados</b></div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="itens_saida_table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Produto</th>
                                                    <th>Qtd</th>
                                                    <th>Preço Unitário</th>
                                                    <th>Total</th>
                                                    <th>Acções</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Linhas de item serão adicionadas aqui via JS -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" style="text-align: right;">Subtotal:</th>
                                                    <th id="total_venda_sum">0.00</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" style="text-align: right;">Total IVA:</th>
                                                    <th id="total_valor_iva_sum">0.00</th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" style="text-align: right;">Total Geral:</th>
                                                    <th id="total_geral_sum">0.00</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('saida.list') }}" class="btn btn-danger">Cancelar</a>
                                <button class="btn btn-success ml-2" id="confirmar_venda_btn" type="button"
                                    data-toggle="modal" data-target="#confirmarSaidaModal">Confirmar Venda</button>
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

            // Handle product selection to load batches
            $('#item_produto_id').on('change', function() {
                var productId = $(this).val();
                var loteSelect = $('#item_lote_id');
                loteSelect.empty().append('<option value="">Carregando lotes...</option>').prop('disabled', true);
                $('#item_preco_unitario').val('');
                $('#item_quantidade').val('');
                $('#item_total').val('');

                if (productId) {
                    $.ajax({
                        url: '{{ route('saida.getBatchesByProduct') }}', // Need to define this route
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId
                        },
                        dataType: 'json',
                        success: function(batches) {
                            loteSelect.empty().append('<option value="">Selecione um lote...</option>');
                            if (batches.length > 0) {
                                $.each(batches, function(index, batch) {
                                    loteSelect.append('<option value="' + batch.entrada_item_id + '" ' +
                                        'data-preco="' + batch.preco_actual + '" ' +
                                        'data-qnt-actual="' + batch.qnt_actual_entrada_item + '">' +
                                        'Lote: ' + batch.entrada_item_id + ' | Stock: ' + batch.qnt_actual_entrada_item +
                                        '</option>');
                                });
                                loteSelect.prop('disabled', false);
                            } else {
                                loteSelect.append('<option value="">Nenhum lote disponível.</option>');
                            }
                        },
                        error: function(err) {
                            console.error("Erro ao carregar lotes:", err);
                            loteSelect.empty().append('<option value="">Erro ao carregar lotes.</option>');
                        }
                    });
                } else {
                    loteSelect.empty().append('<option value="">Selecione um produto primeiro...</option>').prop('disabled', true);
                }
            });

            // Handle batch selection
            $('#item_lote_id').on('change', function() {
                var selectedBatchOption = $(this).find('option:selected');
                var preco_actual = parseFloat(selectedBatchOption.data('preco')) || 0;
                var qnt_actual_entrada_item = parseFloat(selectedBatchOption.data('qnt-actual')) || 0;

                $('#item_preco_unitario').val(preco_actual.toFixed(2));
                $('#item_quantidade').attr('max', qnt_actual_entrada_item); // Set max quantity
                $('#item_quantidade').trigger('input'); // Trigger input event to update total
            });

            // Update item_total when quantity or unit price changes
            $('#item_quantidade, #item_preco_unitario').on('input', function() {
                var quantidade = parseFloat($('#item_quantidade').val()) || 0;
                var preco_unitario = parseFloat($('#item_preco_unitario').val()) || 0;
                $('#item_total').val((quantidade * preco_unitario).toFixed(2));
            });

            // Calculate trocos when valor_entregue changes
            $(document).on('input', '#modal_valor_entregue', function() {
                var valor_entregue = parseFloat($(this).val()) || 0;
                var total_geral = parseFloat($('#modal_custo_total').val()) || 0;
                var trocos = valor_entregue - total_geral;
                $('#modal_trocos').val(trocos.toFixed(2));
            });

            // Add item to cart
            $('#add_item_to_cart').click(function() {
                var produto_id = $('#item_produto_id').val(); // Get product_id from product dropdown
                var entrada_item_id = $('#item_lote_id').val(); // Get entrada_item_id from batch dropdown
                var selectedProductOption = $('#item_produto_id option:selected');
                var selectedBatchOption = $('#item_lote_id option:selected');

                var produto_text = selectedProductOption.text() + ' - ' + selectedBatchOption.text(); // Combine product and batch text
                var quantidade = parseFloat($('#item_quantidade').val()) || 0;
                var preco_unitario = parseFloat($('#item_preco_unitario').val()) || 0;
                var item_total = parseFloat($('#item_total').val()) || 0;

                // Get data attributes from selected batch
                var preco_actual = parseFloat(selectedBatchOption.data('preco')) || 0;
                var iva_rate = parseFloat(selectedBatchOption.data('iva')) || 0;
                var qnt_actual_entrada_item = parseFloat(selectedBatchOption.data('qnt-actual')) || 0;

                if (!produto_id || !entrada_item_id || quantidade <= 0 || preco_unitario <= 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Erro de Validação",
                        html: "Preencha o produto, lote, quantidade e preço unitário com valores válidos.",
                    });
                    return;
                }

                // Validate quantity against available stock for this entrada_item
                if (quantidade > qnt_actual_entrada_item) {
                    Swal.fire({
                        icon: "error",
                        title: "Stock Insuficiente",
                        html: `A quantidade (${quantidade}) excede o stock disponível para este lote (${qnt_actual_entrada_item}).`,
                    });
                    return;
                }

                // Check for duplicate product (now by entrada_item_id)
                var isDuplicate = false;
                $('#itens_saida_table tbody tr').each(function() {
                    if ($(this).find('input[name$="[entrada_item_id]"]').val() == entrada_item_id) {
                        isDuplicate = true;
                        return false; // Exit loop
                    }
                });

                if (isDuplicate) {
                    Swal.fire({
                        icon: "warning",
                        title: "Produto Duplicado",
                        html: "Este lote do produto já foi adicionado à lista.",
                    });
                    return;
                }

                // Calculate IVA and other default values
                var valor_iva = (item_total * iva_rate / 100).toFixed(2);
                var preco_compra =
                    preco_actual; // Assuming preco_actual from product is preco_compra for simplicity
                var custo = item_total; // Assuming custo is item_total for simplicity
                var desconto_percentual = 0;
                var desconto_valor = 0;
                var tipo_motivo = '';
                var motivo = '';

                var preco_compra =
                    preco_actual; // Assuming preco_actual from product is preco_compra for simplicity
                var custo = item_total; // Assuming custo is item_total for simplicity
                var desconto_percentual = 0;
                var desconto_valor = 0;

                var rowIndex = $('#itens_saida_table tbody tr').length;
                var newRow = `
                <tr data-produto-id="${produto_id}" data-entrada-item-id="${entrada_item_id}" data-row-index="${rowIndex}">
                    <td>${rowIndex + 1}</td>
                    <td>${produto_text}<input type="hidden" name="itens[${rowIndex}][produto_id]" value="${produto_id}"></td>
                    <td class="editable-quantity" data-max="${qnt_actual_entrada_item}">
                        <span class="quantity-display" title="Duplo clique para editar">${quantidade}</span>
                        <input type="number" class="form-control quantity-input" value="${quantidade}" min="1" max="${qnt_actual_entrada_item}" style="display: none;">
                        <input type="hidden" name="itens[${rowIndex}][quantidade]" value="${quantidade}">
                    </td>
                    <td class="editable-price">
                        <span class="price-display" title="Duplo clique para editar">${preco_unitario.toFixed(2)}</span>
                        <input type="number" class="form-control price-input" value="${preco_unitario}" step="0.01" min="0.01" style="display: none;">
                        <input type="hidden" name="itens[${rowIndex}][preco_unitario]" value="${preco_unitario}">
                    </td>
                    <td class="item-total">
                        <span class="total-display">${item_total.toFixed(2)}</span>
                        <input type="hidden" name="itens[${rowIndex}][item_total]" value="${item_total}">
                    </td>
                    <td><button type="button" class="btn btn-danger btn-sm remove_item_saida">Remover</button></td>
                    <input type="hidden" name="itens[${rowIndex}][preco_compra]" value="${preco_compra}">
                    <input type="hidden" name="itens[${rowIndex}][iva]" value="${iva_rate}">
                    <input type="hidden" name="itens[${rowIndex}][valor_iva]" value="${valor_iva}">
                    <input type="hidden" name="itens[${rowIndex}][custo]" value="${custo}">
                    <input type="hidden" name="itens[${rowIndex}][desconto_percentual]" value="${desconto_percentual}">
                    <input type="hidden" name="itens[${rowIndex}][desconto_valor]" value="${desconto_valor}">
                    <input type="hidden" name="itens[${rowIndex}][entrada_item_id]" value="${entrada_item_id}">
                </tr>
            `;
                $('#itens_saida_table tbody').append(newRow);

                // Clear form fields
                $('#item_produto_id').val('').trigger('change');
                $('#item_lote_id').empty().append('<option value="">Selecione um produto primeiro...</option>').prop('disabled', true); // Clear and disable lote
                $('#item_quantidade').val('');
                $('#item_preco_unitario').val('');
                $('#item_total').val('');

                calculateTotals();
            });
        });

        // Remover item
        $(document).on('click', '.remove_item_saida', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        // No direct input for valor_total, valor_pago in simplified form
        // These will be handled in the modal

        function calculateTotals() {
            var total_venda_sum = 0; // Subtotal
            var total_valor_iva_sum = 0; // Total IVA

            $('#itens_saida_table tbody tr').each(function() {
                var item_total = parseFloat($(this).find('input[name$="[item_total]"]').val()) || 0;
                var valor_iva = parseFloat($(this).find('input[name$="[valor_iva]"]').val()) || 0;

                total_venda_sum += item_total;
                total_valor_iva_sum += valor_iva;
            });

            var total_geral_sum = total_venda_sum + total_valor_iva_sum;

            $('#total_venda_sum').text(total_venda_sum.toFixed(2));
            $('#total_valor_iva_sum').text(total_valor_iva_sum.toFixed(2));
            $('#total_geral_sum').text(total_geral_sum.toFixed(2));

            // Update modal fields
            $('#modal_custo').val(total_venda_sum.toFixed(2)); // Subtotal
            $('#modal_total_taxa').val(total_valor_iva_sum.toFixed(2)); // Total IVA
            $('#modal_custo_total').val(total_geral_sum.toFixed(2)); // Total Geral

            // Set initial valor_entregue and calculate trocos
            $('#modal_valor_entregue').val(total_geral_sum.toFixed(2)); // Default to total
            $('#modal_trocos').val('0.00'); // Default trocos to 0
        }

        // When modal is shown, update values
        $('#confirmarSaidaModal').on('show.bs.modal', function(e) {
            calculateTotals(); // Recalculate just before showing
            $('#modal_valor_entregue').trigger('input'); // Trigger trocos calculation
        });

        // Submeter formulário com AJAX (agora do modal)
        $('#registar_venda_modal').click(function() {
            showLoader();

            var total_geral_sum_calculated = parseFloat($('#modal_custo_total').val()) || 0;
            if (total_geral_sum_calculated <= 0) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "Adicione produtos à saída antes de confirmar.",
                });
                hideLoader();
                return;
            }

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('cliente_id', $('#cliente_id').val());
            formData.append('tipo_saida_id', 1); // Default to 1 for normal sale

            // Get totals from the cart summary (which are updated by calculateTotals)
            formData.append('valor_total', $('#total_geral_sum').text());
            formData.append('valor_total_iva', $('#total_valor_iva_sum').text());

            // Get payment details from modal
            var valor_entregue = parseFloat($('#modal_valor_entregue').val()) || 0;
            var total_geral = parseFloat($('#modal_custo_total').val()) || 0;
            var trocos = parseFloat($('#modal_trocos').val()) || 0; // This will be valor_entregue - total_geral

            formData.append('valor_pago', valor_entregue); // Valor pago é o que o cliente entregou
            formData.append('valor_remanescente', trocos); // Remanescente é o troco (pode ser negativo se faltar pagar)
            formData.append('desconto', $('#modal_total_desconto').val());
            formData.append('valor_entregue', valor_entregue);
            formData.append('trocos', trocos);
            formData.append('tipo_pagamento_id', $('#modal_forma_pagamento').val());

            // Determine estado_pagamento
            if (valor_entregue >= total_geral) {
                formData.append('estado_pagamento', 'pago');
            } else if (valor_entregue > 0 && valor_entregue < total_geral) {
                formData.append('estado_pagamento', 'parcial');
            } else {
                formData.append('estado_pagamento', 'nao_pago');
            }
            formData.append('activo', 1); // Always active

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
                    entrada_item_id: $(this).find('input[name$="[entrada_item_id]"]').val(), // Add this
                };
                itens.push(item);
            });

            formData.append('itens', JSON.stringify(itens));

            $.ajax({
                url: '{{ route('saida.add') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.success == true) {
                        Swal.fire({
                            icon: "success",
                            title: `${response.message}`,
                            showConfirmButton: false,
                            timer: 2000,
                        }).then(() => {
                            window.open("{{ route('saida.recibo', ['id' => '__saida_id__']) }}".replace('__saida_id__', response.saida_id), '_blank');
                            window.location.href = "{{ route('saida.list') }}"; // Redirect to list after opening receipt
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
                    hideLoader(); // Ensure loader hides on error
                }
            }).always(function() {
                hideLoader();
            });
        });
        // Edição simples com duplo clique
        $(document).on('dblclick', '.quantity-display', function() {
            const row = $(this).closest('tr');
            const display = $(this);
            const input = row.find('.quantity-input');
            const maxValue = parseInt(row.find('.editable-quantity').attr('data-max'));
            
            display.hide();
            input.show().focus().attr('max', maxValue);
        });
        
        $(document).on('dblclick', '.price-display', function() {
            const row = $(this).closest('tr');
            const display = $(this);
            const input = row.find('.price-input');
            
            display.hide();
            input.show().focus();
        });
        
        // Salvar ao perder foco
        $(document).on('blur', '.quantity-input', function() {
            const row = $(this).closest('tr');
            const input = $(this);
            const display = row.find('.quantity-display');
            const newValue = parseInt(input.val());
            const maxValue = parseInt(row.find('.editable-quantity').attr('data-max'));
            
            // Validações
            if (newValue <= 0) {
                Swal.fire("Erro!", "A quantidade deve ser maior que zero.", "error");
                input.val(display.text());
                input.hide();
                display.show();
                return;
            }
            
            if (newValue > maxValue) {
                Swal.fire("Erro!", `A quantidade não pode ser maior que ${maxValue} (disponível).`, "error");
                input.val(display.text());
                input.hide();
                display.show();
                return;
            }
            
            // Atualizar display e campo hidden
            display.text(newValue);
            row.find('input[name$="[quantidade]"]').val(newValue);
            
            // Esconder input e mostrar display
            input.hide();
            display.show();
            
            // Recalcular total do item
            recalcularItemTotal(row);
        });
        
        $(document).on('blur', '.price-input', function() {
            const row = $(this).closest('tr');
            const input = $(this);
            const display = row.find('.price-display');
            const newValue = parseFloat(input.val());
            
            // Validações
            if (newValue <= 0) {
                Swal.fire("Erro!", "O preço deve ser maior que zero.", "error");
                input.val(display.text());
                input.hide();
                display.show();
                return;
            }
            
            // Atualizar display e campo hidden
            display.text(newValue.toFixed(2));
            row.find('input[name$="[preco_unitario]"]').val(newValue);
            
            // Esconder input e mostrar display
            input.hide();
            display.show();
            
            // Recalcular total do item
            recalcularItemTotal(row);
        });
        
        // Função para recalcular total do item
        function recalcularItemTotal(row) {
            const quantidade = parseInt(row.find('input[name$="[quantidade]"]').val());
            const precoUnitario = parseFloat(row.find('input[name$="[preco_unitario]"]').val());
            const ivaRate = parseFloat(row.find('input[name$="[iva]"]').val());
            
            const itemTotal = quantidade * precoUnitario;
            const valorIva = (itemTotal * ivaRate / 100);
            
            // Atualizar campos
            row.find('input[name$="[item_total]"]').val(itemTotal.toFixed(2));
            row.find('input[name$="[valor_iva]"]').val(valorIva.toFixed(2));
            row.find('.item-total .total-display').text(itemTotal.toFixed(2));
            
            // Recalcular totais gerais
            calculateTotals();
        }
    </script>

    <style>
        .quantity-display, .price-display {
            cursor: pointer;
            padding: 5px;
            border-radius: 3px;
            display: inline-block;
            min-width: 50px;
        }

        .quantity-display:hover, .price-display:hover {
            background-color: #f8f9fa;
        }

        .quantity-input, .price-input {
            border: 1px solid #007bff;
            border-radius: 3px;
            text-align: center;
            width: 80px;
        }

        .quantity-input:focus, .price-input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .editable-quantity, .editable-price {
            text-align: center;
        }
    </style>
@endsection