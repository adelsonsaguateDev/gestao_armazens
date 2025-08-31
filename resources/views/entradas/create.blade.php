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

        function calculateTotals() {
            var total_iva = 0;
            $('#itens_entrada_table tbody tr').each(function() {
                var preco_compra_caixa = parseFloat($(this).find('.preco_compra_caixa').val()) || 0;
                var qtd_caixas = parseInt($(this).find('.qtd_caixas').val()) || 0;
                var iva = parseFloat($(this).find('.iva').val()) || 0;
                var subtotal = preco_compra_caixa * qtd_caixas;
                total_iva += subtotal * (iva / 100);
            });
            $('#total_iva_sum').text(total_iva.toFixed(2));
        }

        // Adicionar item
        $('#add_item_entrada').click(function() {
            var rowIndex = $('#itens_entrada_table tbody tr').length;
            var newRow = `
                <tr>
                    <td>
                        <select class="form-control produto_id" name="itens[${rowIndex}][produto_id]" required>
                            <option value="">Selecione...</option>
                            @foreach ($produtos as $produto)
                                <option value="{{ $produto->id }}">{{ $produto->descricao }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" class="form-control qtd_caixas" name="itens[${rowIndex}][qtd_caixas]" required></td>
                    <td><input type="number" class="form-control qtd_por_caixa" name="itens[${rowIndex}][qtd_por_caixa]" required></td>
                    <td><input type="number" class="form-control preco_compra_caixa" name="itens[${rowIndex}][preco_compra_caixa]" step="0.01" required></td>
                    <td><input type="number" class="form-control preco_compra_unitario" name="itens[${rowIndex}][preco_compra_unitario]" step="0.01" required></td>
                    <td><input type="number" class="form-control preco_venda_caixa" name="itens[${rowIndex}][preco_venda_caixa]" step="0.01" required></td>
                    <td><input type="number" class="form-control preco_venda_unitario" name="itens[${rowIndex}][preco_venda_unitario]" step="0.01" required></td>
                    <td><input type="number" class="form-control iva" name="itens[${rowIndex}][iva]" step="0.01"></td>
                    <td><input type="date" class="form-control data_validade" name="itens[${rowIndex}][data_validade]"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove_item_entrada">Remover</button></td>
                </tr>
            `;
            $('#itens_entrada_table tbody').append(newRow);
            // Inicializar o select2 no novo elemento
            $(`select[name="itens[${rowIndex}][produto_id]"]`).select2({
                allowClear: true
            });
            calculateTotals();
        });

        // Remover item
        $(document).on('click', '.remove_item_entrada', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        // Calcular totais ao alterar valores
        $(document).on('input', '.qtd_caixas, .preco_compra_caixa, .iva', function() {
            calculateTotals();
        });

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
            formData.append('total', $('#total').val());
            formData.append('total_factura', $('#total_factura').val());
            formData.append('total_desconto', $('#total_desconto').val());
            formData.append('total_iva', $('#total_iva').val());
            formData.append('valor_remanescente', $('#valor_remanescente').val());

            // Anexar o ficheiro
            if ($('#ficheiro_entrada')[0].files.length > 0) {
                formData.append('ficheiro_entrada', $('#ficheiro_entrada')[0].files[0]);
            }

            var itens = [];
            $('#itens_entrada_table tbody tr').each(function() {
                var item = {
                    produto_id: $(this).find('select[name$="[produto_id]"]').val(),
                    qtd_caixas: $(this).find('.qtd_caixas').val(),
                    qtd_por_caixa: $(this).find('.qtd_por_caixa').val(),
                    preco_compra_caixa: $(this).find('.preco_compra_caixa').val(),
                    preco_compra_unitario: $(this).find('.preco_compra_unitario').val(),
                    preco_venda_caixa: $(this).find('.preco_venda_caixa').val(),
                    preco_venda_unitario: $(this).find('.preco_venda_unitario').val(),
                    iva: $(this).find('.iva').val(),
                    data_validade: $(this).find('.data_validade').val()
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
