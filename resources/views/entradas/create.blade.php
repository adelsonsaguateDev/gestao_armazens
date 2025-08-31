@extends('layouts.main')

@section('title', 'Registar Nova Entrada | Gestão de Armazens')

@section('content')

    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Registar Nova Entrada</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>

                <div class="row">
                    <div class="col-md-12">
                        <form action="{{ route('entrada.add') }}" method="POST" id="form_registrar_entrada" enctype="multipart/form-data">
                            @csrf
                            @include('entradas.form')

                            <div class="card-footer">
                                <a href="{{ route('entrada.list') }}" class="btn btn-danger">Cancelar</a>
                                <button class="btn btn-success ml-2" id="registrar_entrada" type="button">Submeter</button>
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

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $(".select2").select2({
            allowClear: true,
        });

        hideLoader();

        // Show/hide price per box fields
        $('#item_preco_por_caixa').change(function() {
            if ($(this).val() === 'sim') {
                $('#item_preco_compra_caixa_div').show();
                $('#item_preco_venda_caixa_div').show();
                $('#item_qtd_por_caixa_div').show();
            } else {
                $('#item_preco_compra_caixa_div').hide();
                $('#item_preco_venda_caixa_div').hide();
                $('#item_qtd_por_caixa_div').hide();
            }
        });

        // Add item to cart
        $('#add_item_to_cart').click(function() {
            var produto_id = $('#item_produto_id').val();
            var produto_text = $('#item_produto_id option:selected').text();
            var qtd = $('#item_qtd').val();
            var tem_iva = $('#item_tem_iva').val();
            var preco_por_caixa = $('#item_preco_por_caixa').val();
            var preco_compra_caixa = $('#item_preco_compra_caixa').val();
            var preco_venda_caixa = $('#item_preco_venda_caixa').val();
            var qtd_por_caixa = $('#item_qtd_por_caixa').val();
            var preco_compra = $('#item_preco_compra').val();
            var preco_venda = $('#item_preco_venda').val();
            var data_validade = $('#item_data_validade').val();
            var iva = (tem_iva === 'sim') ? 16 : 0;

            if (!produto_id || !qtd) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "Preencha o produto e a quantidade.",
                });
                return;
            }

            if (preco_por_caixa === 'sim' && (!preco_compra_caixa || !preco_venda_caixa || !qtd_por_caixa)) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "Preencha os preços por caixa e a quantidade por caixa.",
                });
                return;
            }

            if (preco_por_caixa === 'nao' && (!preco_compra || !preco_venda)) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "Preencha os preços unitários.",
                });
                return;
            }
            
            var today = new Date().toISOString().slice(0, 10);
            if (data_validade && data_validade < today) {
                Swal.fire({
                    icon: "error",
                    title: "Erro de Validação",
                    html: "A data de validade não pode ser menor que a data actual.",
                });
                return;
            }

            var preco_compra_final = (preco_por_caixa === 'sim') ? preco_compra_caixa : preco_compra;
            var preco_venda_final = (preco_por_caixa === 'sim') ? preco_venda_caixa : preco_venda;
            var qtd_final = (preco_por_caixa === 'sim') ? qtd * qtd_por_caixa : qtd;
            var preco_compra_unitario = (preco_por_caixa === 'sim') ? preco_compra_caixa / qtd_por_caixa : preco_compra;
            var preco_venda_unitario = (preco_por_caixa === 'sim') ? preco_venda_caixa / qtd_por_caixa : preco_venda;

            var total_compra = qtd * preco_compra_final;
            var total_venda = qtd * preco_venda_final;

            var rowIndex = $('#itens_entrada_table tbody tr').length;
            var newRow = `
                <tr>
                    <td>${rowIndex + 1}</td>
                    <td>${produto_text}<input type="hidden" name="itens[${rowIndex}][produto_id]" value="${produto_id}"></td>
                    <td>${qtd_final}<input type="hidden" name="itens[${rowIndex}][qtd_caixas]" value="${qtd}"><input type="hidden" name="itens[${rowIndex}][qtd_por_caixa]" value="${qtd_por_caixa || 1}"></td>
                    <td>${preco_compra_final}<input type="hidden" name="itens[${rowIndex}][preco_compra_caixa]" value="${preco_compra_caixa}"><input type="hidden" name="itens[${rowIndex}][preco_compra_unitario]" value="${preco_compra_unitario}"></td>
                    <td>${total_compra.toFixed(2)}</td>
                    <td>${preco_venda_final}<input type="hidden" name="itens[${rowIndex}][preco_venda_caixa]" value="${preco_venda_caixa}"><input type="hidden" name="itens[${rowIndex}][preco_venda_unitario]" value="${preco_venda_unitario}"></td>
                    <td>${total_venda.toFixed(2)}</td>
                    <td>${iva}%<input type="hidden" name="itens[${rowIndex}][iva]" value="${iva}"></td>
                    <td>${data_validade}<input type="hidden" name="itens[${rowIndex}][data_validade]" value="${data_validade}"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove_item_entrada">Remover</button></td>
                </tr>
            `;
            $('#itens_entrada_table tbody').append(newRow);

            // Clear form fields
            $('#item_produto_id').val('').trigger('change');
            $('#item_qtd').val('');
            $('#item_tem_iva').val('nao');
            $('#item_preco_por_caixa').val('nao').trigger('change');
            $('#item_preco_compra_caixa').val('');
            $('#item_preco_venda_caixa').val('');
            $('#item_preco_compra').val('');
            $('#item_preco_venda').val('');
            $('#item_data_validade').val('');

            calculateTotals();
        });

        // Remover item
        $(document).on('click', '.remove_item_entrada', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        function calculateTotals() {
            var total_compra_sum = 0;
            var total_venda_sum = 0;
            var total_iva_sum = 0;

            $('#itens_entrada_table tbody tr').each(function() {
                var qtd_caixas = parseFloat($(this).find('input[name$="[qtd_caixas]"]').val()) || 0;
                var preco_compra_caixa = parseFloat($(this).find('input[name$="[preco_compra_caixa]"]').val()) || 0;
                var preco_venda_caixa = parseFloat($(this).find('input[name$="[preco_venda_caixa]"]').val()) || 0;
                var iva = parseFloat($(this).find('input[name$="[iva]"]').val()) || 0;

                total_compra_sum += qtd_caixas * preco_compra_caixa;
                total_venda_sum += qtd_caixas * preco_venda_caixa;
                total_iva_sum += (qtd_caixas * preco_compra_caixa) * (iva / 100);
            });

            $('#total_compra_sum').text(total_compra_sum.toFixed(2));
            $('#total_venda_sum').text(total_venda_sum.toFixed(2));
            $('#total_iva_sum').text(total_iva_sum.toFixed(2));
        }

        // Submeter formulário com AJAX
        $('#registrar_entrada').click(function() {
            showLoader();

            var formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('tipo_entrada_id', $('#tipo_entrada_id').val());
            formData.append('fornecedor_id', $('#fornecedor_id').val());
            formData.append('numero_factura', $('#numero_factura').val());
            formData.append('data_aquisicao', $('#data_aquisicao').val());
            formData.append('data_factura', $('#data_factura').val());
            
            // Anexar o ficheiro
            if ($('#ficheiro_entrada')[0].files.length > 0) {
                formData.append('ficheiro_entrada', $('#ficheiro_entrada')[0].files[0]);
            }

            var itens = [];
            $('#itens_entrada_table tbody tr').each(function() {
                var item = {
                    produto_id: $(this).find('input[name$="[produto_id]"]').val(),
                    qtd_caixas: $(this).find('input[name$="[qtd_caixas]"]').val(),
                    qtd_por_caixa: $(this).find('input[name$="[qtd_por_caixa]"]').val(),
                    preco_compra_caixa: $(this).find('input[name$="[preco_compra_caixa]"]').val(),
                    preco_compra_unitario: $(this).find('input[name$="[preco_compra_unitario]"]').val(),
                    preco_venda_caixa: $(this).find('input[name$="[preco_venda_caixa]"]').val(),
                    preco_venda_unitario: $(this).find('input[name$="[preco_venda_unitario]"]').val(),
                    iva: $(this).find('input[name$="[iva]"]').val(),
                    data_validade: $(this).find('input[name$="[data_validade]"]').val()
                };
                itens.push(item);
            });

            formData.append('itens', JSON.stringify(itens));

            $.ajax({
                url: '{{ route('entrada.add') }}',
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
                            window.location.href = "{{ route('entrada.list') }}";
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