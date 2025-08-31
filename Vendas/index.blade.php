@extends('layout')

@section('title','SGF | Saidas')


@section('content')

<?php
    session_start();
    use App\Helpers\RedisHelper;

$codigoEncriptado = $_COOKIE['codigo_loja'] ?? null;

// Obter dados da empresa e lidar com diferentes tipos de retorno
$empresaData = RedisHelper::getValue2($codigoEncriptado);
// dd($empresaData);

// Verificar se o valor já é um array ou objeto
if (is_array($empresaData)) {
    $empresa = (object)$empresaData;
} elseif (is_object($empresaData)) {
    $empresa = $empresaData;
    // dd($empresa);
} else {
    // Tentar decodificar a string JSON
    $empresa = json_decode($empresaData);

    
}


    $permissao = implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray());
    $versao_oficial = config('app.oficial_version');
?>

    <div class="se-pre-con"></div>

    <div class="page-header">
        <h4 class="page-title">Lista de Saídas</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="mt-3 card-body">
                    <?php    if(implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray()) == 'admin'){ ?>
                    <?php if($empresa->sincronizar_vendas == '1') {?>
                        <button type="button" class="btn btn-primary btnSync"  title="Sincronizar vendas">
                            <i class="fas fa-sync-alt" style="color:white"> SINCRONIZAR VENDAS</i>
                        </button>
                    <?php } ?>

                    <?php //if($empresa->fonte == '1') {?>
                        {{-- <button type="button" class="btn btn-primary btnSyncImportadoras"  title="Sincronizar vendas importadoras">
                            <i class="fas fa-sync-alt" style="color:white"> SINCRONIZAR VENDAS  </i>
                        </button>
                        <button type="button" class="btn btn-success btnSyncNotasCredito"  title="Sincronizar vendas notas_credito">
                            <i class="fas fa-sync-alt" style="color:white"> SINCRONIZAR NOTAS DE CRÉDITO  </i>
                        </button> --}}
                    <?php //} ?>

                    <?php } ?>

                    <div class="row">
                        <div class="col-md-12 form-group text-right">
                            @if($versao_oficial)
                                @if($empresa->pacote != 1)
                                    <a type="button" href="{{route('saida.cotacao')}}" class="btn btn-secondary mb-3" data-toggle="tooltip" title="Cotação">
                                        <span style="font-weight: bold"><i class="fas fa-plus-circle"></i> COTAÇÃO</span>
                                    </a>
                                @endif
                                <?php if($empresa->fonte != '1') {?>
                                <a type="button" href="{{route('saida.create')}}" class="btn btn-success mb-3" data-toggle="tooltip" title="Venda à Dinheiro">
                                    <span style="font-weight: bold"><i class="fas fa-plus-circle"></i> V. DINHEIRO</span>
                                </a>
                                <?php } ?>
                                <a type="button" href="{{route('saida.credito')}}" class="btn btn-info mb-3" data-toggle="tooltip" title="Venda à Crédito">
                                    <span style="font-weight: bold"><i class="fas fa-plus-circle"></i> V. CRÉDITO</span>
                                </a>
                                @if(in_array($permissao, ['admin', 'gestor', 'gestor_vendedor']))
                                    <a type="button" href="{{route('saida.dividas')}}" class="btn btn-danger mb-3" data-toggle="tooltip" title="Devedores">
                                        <span style="font-weight: bold"><i class="fas fa-search-dollar"></i> DEVEDORES</span>
                                    </a>
                                @endif
                                <!-- <a type="button" href="{{route('devolucoes')}}" class="btn btn-dark" data-toggle="tooltip" title="Devoluções">
                                    <i class="fa fa-search-dollar" style="color:white"> Devoluções</i>
                                </a> -->
                                <button class="btn btn-danger mb-3" id="print" data-toggle="tooltip" title="Exportar lista de produtos para PDF">
                                    <span style="font-weight: bold"><i class="far fa-file-pdf"></i> EXPORTAR PDF</span>
                                </button>
                            @else
                                <a type="button" href="{{route('saida.cotacao')}}" class="btn btn-secondary" data-toggle="tooltip" title="Cotação">
                                    <i class="fa fa-plus" style="color:white"> Cotação</i>
                                </a>
                                <?php if($empresa->fonte != '1') {?>
                                <a type="button" href="{{route('saida.create')}}" class="btn btn-warning" data-toggle="tooltip" title="Registar Saida">
                                    <i class="fa fa-plus" style="color:white"> V. DINHEIRO</i>
                                </a>
                                <?php } ?>
                                <a type="button" href="{{route('saida.credito')}}" class="btn btn-info" data-toggle="tooltip" title="Registar Saida">
                                    <i class="fa fa-plus" style="color:white"> V. CRÉDITO</i>
                                </a>
                                @if(in_array($permissao, ['admin', 'gestor', 'gestor_vendedor']))
                                    <a type="button" href="{{route('saida.dividas')}}" class="btn btn-danger" data-toggle="tooltip" title="Devedores">
                                        <i class="fa fa-search-dollar" style="color:white"> Devedores</i>
                                    </a>
                                @endif
                                <!-- <a type="button" href="{{route('devolucoes')}}" class="btn btn-dark" data-toggle="tooltip" title="Devoluções">
                                    <i class="fa fa-search-dollar" style="color:white"> Devoluções</i>
                                </a> -->
                                <button class="btn btn-success" id="print"> <i class="fas fa-print"></i></button>
                            @endif
                        </div>
                    </div>
                    {{-- <br/> --}}
                    <div class="row">

                        <div class="col-md-3  ">
                            <label for="nome">Cliente</label>
                            <select class="select2 form-control" name="cliente" id="cliente">
                                <option value="">Todos...</option>
                                @foreach ($clientes as $item)
                                    <option value="{{$item->id}}">{{$item->nome}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3  ">
                            <label for="nome">Tipo de Saida</label>
                            <select class=" select2 form-control" name="tipo_saida" id="tipo_saida">
                                <option value="">Todos...</option>
                                @if($empresa->pacote != 1)
                                    @foreach ($tipo_saidas as $item)
                                        <option {{$item->id==1?"selected":""}} value="{{$item->id}}">{{$item->descricao}}</option>
                                    @endforeach
                                @else
                                    @foreach ($tipo_saidas as $item)
                                        <option value="{{$item->id}}">{{$item->descricao}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="nome">Desconto</label>
                            <select class=" select2 form-control" name="desconto" id="desconto">
                                <option value="">Todos</option>
                                <option value="1">Com desconto</option>
                                <option value="2">Sem desconto</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="nome">Vendas Devolvidas?</label>
                            <select class=" select2 form-control" name="devolvida" id="devolvida">
                                <option value="">Todos</option>
                                <option value="sim">SIM</option>
                                <option value="nao">NÃO</option>
                            </select>
                        </div>



                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
                            <label for=""><b>Produto</b></label>
                            <input name="produto_id" id="produto_id" class="mySelect2_all form-control" placeholder="Escreva o código de barra ou descrição do produto" required>
                        </div>
                        <div class="col col-md">
                            <label for="nome">Data início</label>
                            <input type="date" class=" form-control date" name="date" id="date" value="{{date('Y-m-d')}}" placeholder="Pesquisar por Data...">
                        </div>
                        <div class="col col-md">
                            <label for="nome">Data fim</label>
                            <input type="date" class=" form-control date" name="date2" id="date2" value="{{date('Y-m-d')}}" placeholder="Data limite...">

                        </div>

                        <div class="col-md">
                            <label for="">Código da venda</label>
                            <input type="text" name="codigo_venda" id="codigo_venda" class="form-control">
                        </div>
                    </div>
                    <br>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="car">
                                <div class="row">
                                    <div class="col col-md-3">
                                        <label>Limite</label>
                                        <select class="form-control limite">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="100">100</option>
                                            <option value="500">500</option>
                                            <option value="">Todos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        @if($versao_oficial)
                                            <button class="btn btn-secondary pesquisar"><span style="font-weight: bold"><i class="fas fa-search"></i> PESQUISAR</span></button>
                                        @else
                                            <button class="btn btn-primary pesquisar">Pesquisar</button>
                                        @endif

                                    </div>
                                </div>

                                <br>
                                <div class="card-bod table-responsive saida-lista">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade showDetalhes" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <!-- <div class="modal fade" id="showDetalhes" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"> -->
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title titulo_modal" id="staticBackdropLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body conteudo">

                </div>
                <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div> -->
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade pay_invoice" id="pay_invoice" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="titulo_modal_payment"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body content_invoice">
                    <div class="user-profile text-center">
                        <div class="name font-weight-bold" id="cliente_nome"></div>
                    </div>
                    <form method="post" action="{{route('saida.pay')}}" id="form_payment" name="form_payment">
                        <div class="row user-stats text-center">
                            <div class="col-md-6">
                                <label for="valor_remanescente">Valor Remanescente</label>
                                <input class="form-control" value="0" name="valor_remanescente" id="valor_remanescente" readonly/>
                            </div>
                            <div class="col-md-6">
                                <label for="data_pagamento">Data de pagamento</label>
                                <input class="form-control" value="{{date('d-m-Y')}}" name="data_pagamento" id="data_pagamento" readonly/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="valor_pagar">Valor Por Pagar</label>
                                <input class="form-control" value="" name="valor_pagar" id="valor_pagar" required/>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="numero_recibo">Número do Recibo</label>
                                <input class="form-control" value="" name="numero_recibo" id="numero_recibo" required/>
                            </div>

                            @if($empresa->fonte == 1)
                            <div class="col-md-6 form-group">
                                <label for="valor_pagar2">Valor Por Pagar (MT)</label>
                                <input class="form-control" value="" name="valor_pagar2" id="valor_pagar2" required/>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="cambio">Câmbio</label>
                                <input class="form-control" value="" name="cambio" id="cambio" required/>
                            </div>
                            @endif
                            <div class="col-md-6 form-group">
                                <label>Tipo de Pagamento</label>
                                <select name="tipo_pagamento_id" id="tipo_pagamento_id" class="form-control" required>
                                    <option value="">Escolha aqui...</option>
                                    @foreach($tipo_pagamentos AS $tipo_pagamento)
                                        <option value="{{$tipo_pagamento->id}}">{{$tipo_pagamento->designacao}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group bancos_view" style="display: none">
                                <label>Banco</label>
                                <select name="banco_id" id="banco_id" class="form-control">
                                    <option value="">Escolha aqui...</option>
                                    @foreach($bancos AS $banco)
                                        <option value="{{$banco->id}}">{{$banco->nome}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @csrf
                    </form>
                </div>
                 <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                  <button type="submit" id="btn_payment" form="form_payment" class="btn btn-primary">Pagar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {

            //First Script in page
            var page = 1;
            var limite = 10;
            // listarSaida(page, limite);
            hideLoader()

            /* Devolucao FormSubmit */
            $(document).on("keydown", function (e) {
                if (e.key === "Enter") {
                    listarSaida(page, limite);
                }
            })

            $(document).on("change","#tipoDevolucao", function () {
                // $("#submitDevolver").attr("disabled", false);
                if($(this).val()==1){//alert("parcial");
                    $(".quantidade_devolucao").each(function(){
                        $(this).val('');
                        $(this).attr("readonly",false);
                        var id = $(this).attr("lineId");

                        var qnt = $("#remanescente2_"+id).val();
                        $("#remanescente_"+id).val(qnt);

                        var preco_total = $("#total_"+id);
                        preco_total.val(0);



                        var remanescente = $("#remanescente_"+id).val();


                        // alert("remanescente=> "+remanescente);
                        // alert("qnt=> "+qnt);

                        // $("#remanescente_"+id).val(qnt.val());

                        $("#quantidade_"+id).val(0);
                    });
                }
                if($(this).val()==2){ //alert("completa");
                    // alert("2")
                    $(".quantidade_devolucao").each(function(){
                        $(this).attr("readonly",true);
                        var id = $(this).attr("lineId");
                        var remanescente = $("#remanescente2_"+id).val();
                        $("#quantidade_"+id).val(remanescente);
                        // $("#remanescente_"+id).val();

                        var lineId = $(this).attr("lineId");
                        var quantidade = parseInt($(this).val());
                        if($(this).val()==""){
                            quantidade=0
                        }
                        var quantidade_total = parseInt($("#t_"+lineId).val());
                        var preco_unitario = parseFloat($("#preco2_"+lineId).val());
                        var preco_total = $("#total_"+lineId);
                        var remanescente = $("#remanescente_"+lineId);
                        preco_total.val(quantidade*preco_unitario)
                        remanescente.val(quantidade_total-quantidade)

                        // alert($(this).attr("lineId"))
                    });
                }
                if($(this).val()==''){
                    $(".quantidade_devolucao").each(function(){
                        $(this).attr("readonly",false);
                        var lineId = $(this).attr("lineId");
                        var quantidade = parseInt($(this).val());
                        if($(this).val()==""){
                            quantidade=0
                        }
                        var quantidade_total = parseInt($("#t_"+lineId).val());
                        var preco_unitario = parseFloat($("#preco2_"+lineId).val());
                        var preco_total = $("#total_"+lineId);
                        var remanescente = $("#remanescente_"+lineId);
                        var remanescente2 = $("#remanescente2_"+lineId);
                        $(this).val(0);
                        preco_total.val(0);
                        remanescente.val(remanescente2.val());
                        // alert(remanescente2.val())
                    })
                }
            })

            $(document).on("submit","#frmDevolucao", function () {
                var url = $(this).attr("action");
                // alert(url)
                $("#submitDevolver").attr("disabled", true)
                $.ajax({
                    dataType:"json",
                    type:"POST",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    url:url,
                    success:function(response){
                        $('.showDetalhes').modal('hide');
                        if (response.success) {
                            $.notify("Venda Devolvida com sucesso!", {
                                icon: "success"
                                , timer: 1000
                                , });
                            listarSaida(page, limite);
                            printReciboDevolucao(response.devolucao_agregada_id,response.saida_id);
                        }else{
                            $.notify("Erro na inserção,", "error")
                        }
                        $("#submitDevolver").removeAttr("disabled")
                    },
                    error:function(err){
                        $("#submitDevolver").removeAttr("disabled")
                        console.log(err)
                    }
                });
                return false;
            })


            //Pagination
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                page = $(this).attr('href').split('page=')[1];
                $(this).attr('href', '');
                listarSaida(page, limite);
            });
            /* Payment Invoice Start */
            $(document).on('click', '.pagar_factura', function(event) {
                event.preventDefault();
                showLoader();
                // $("#numero_recibo").val('');
                // $("#tipo_pagamento_id").val('');
                // $("#banco_id").val('');
                var id = $(this).val();
                $.ajax({
                    url: '{{url("/saida/pay")}}/' + id
                    , method: "get"
                    , data: {
                        _token: '{{csrf_token()}}'
                        , id: id
                    }
                    , dataType: "json"
                    , success: function(response) {
                        $("#titulo_modal_payment").text(response.cliente);
                        $("#valor_remanescente").val(response.valor_remanescente);
                        $("#valor_pagar").val(response.valor_remanescente).attr('max',response.valor_remanescente);
                        $('#pay_invoice').modal('show');

                        $(document).off("submit", "#form_payment").on("submit","#form_payment", function () {
                            var url = $(this).attr("action");
                            $("#btn_payment").attr("disabled", true)
                            var formData = new FormData(this);
                            formData.append("cliente_id", response.cliente_id)
                            formData.append("saida_id", response.id)
                            formData.append("valor_remanescente", response.valor_remanescente)
                            $.ajax({
                                dataType:"json",
                                type:"POST",
                                data: formData,
                                contentType: false,
                                processData: false,
                                url:url,
                                success:function(response){
                                    $('#pay_invoice').modal('hide');
                                    if (response.success) {
                                        $.notify(response.message, {
                                            icon: "success"
                                            , timer: 1000
                                            , });
                                         // Limpar os campos do formulário após o sucesso
                                        $('#form_payment')[0].reset();
                                        $("#tipo_pagamento_id").val("").change();
                                        $(".bancos_view").hide();
                                        $("#banco_id").removeAttr("required")
                                        listarSaida(page, limite);
                                    }else{
                                        $.notify(response.message, "error")
                                    }
                                    $("#btn_payment").removeAttr("disabled")
                                },
                                error:function(err){
                                    $("#btn_payment").removeAttr("disabled")
                                    console.log(err)
                                }
                            });
                            return false;
                        })
                    }
                }).always(function () {
                    hideLoader(); //loader
                });
            });

            $(document).on('change', '#tipo_pagamento_id', function(event) {
                event.preventDefault();
                var id = $(this).val()
                if (id == 3){
                    $(".bancos_view").show()
                    $("#banco_id").attr("required", true)
                }
                else{
                    $(".bancos_view").hide()
                    $("#banco_id").removeAttr("required")
                }

            });
            /* Payment Invoice End*/

            // $('select[name="produto_id"]').change(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            // });
            //
            // $('#date').change(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            //
            // });
            // $('#date2').change(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            //
            // });
            // $('#cliente').change(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            //
            // });
            // $('#tipo_saida').change(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            //
            // });
            //
            // $('#codigo_venda').keyup(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            // });

            // $('#estado').change(function(event) {
            //     event.preventDefault();
            //     listarSaida(page, limite);
            // });

            $(document).on('change','.limite',function(event) {
                event.preventDefault();
                page = 1;
                limite = $(this).val();
                listarSaida(page, limite);
            });

            $(document).on('click', '.apagar-saida', function(event) {
                event.preventDefault();
                var ele = $(this);

                swal({
                    title: 'Tem a certeza?'
                    , text: "Nota: Após cancelar a venda o registo será inútilizado!"
                    , type: 'warning'
                    , buttons: {
                        cancel: {
                            visible: true
                            , text: 'Não'
                            , className: 'btn btn-danger'
                        }
                        , confirm: {
                            text: 'Sim'
                            , className: 'btn btn-success'
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            url: '{{url("saida/cancelar")}}/' + ele.val()
                            , method: "POST"
                            , data: {
                                produto_id: ele.val()
                                , _token: '{{ csrf_token() }}'
                            }
                            , dataType: 'JSON'
                            , success: function(response) {

                                if (response.success) {
                                    swal("Venda cancelada!", {
                                        icon: "success"
                                        , buttons: false
                                        , timer: 1000
                                        , });
                                    listarSaida(page, limite);
                                } else {
                                    swal("Opps, ocorreu um erro!", {
                                        icon: "error"
                                        , buttons: false
                                        , timer: 1000
                                        , });
                                }
                            }

                        });

                    } else {
                        swal.close();
                    }
                });

            });

            $('.pesquisar').click(function(event) {
                event.preventDefault();
                listarSaida(page, limite);
            });

            function listarSaida(page, limite) {
                var produto = $('#produto_id').val();
                var tipo = $('#tipo_saida').val();
                var cliente = $('#cliente').val();
                var date = $('#date').val();
                var date2 = $('#date2').val();
                // var estado = $('#estado').val();
                var codigo_venda = $('#codigo_venda').val();
                var desconto = $('#desconto').val();
                var devolvida = $('#devolvida').val();
                showLoader();
                $.ajax({
                    url: '{{url("/saida/listar")}}?page=' + page
                    , method: "post"
                    , data: {
                        _token: '{{csrf_token()}}'
                        , tipo_saida_id: tipo
                        , cliente_id: cliente
                        , data: date
                        , data2: date2
                        // , estado: estado
                        , codigo_venda: codigo_venda
                        , produto: produto
                        , desconto: desconto
                        , devolvida: devolvida
                        , limite: limite
                    }
                    , dataType: "html"
                    , success: function(response) {
                        $(".saida-lista").html(response);
                    }
                }).always(function () {
                    hideLoader(); //loader
                });
            }

        });
        $(function() {
            $('.recibo').on('click',function () {
                var saida_id = $(this).val();
                // alert(saida_id)
                // alert("#recibo"+saida_id)
                myFunction(saida_id)
            })
        });
        function myFunction(saida_id) {
            var rota = $(".rota"+saida_id).val();
            // window.open(rota, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=1, left=1, width=1, height=1");
            var TheNewWin = window.open(rota, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=100, left=150, width=1024, height=700");
            // TheNewWin.close();
        }
        function showLoader() {
            setTimeout(function() {
                $('.se-pre-con').fadeIn();
            }, 500);
        }

        function hideLoader() {
            setTimeout(function() {
                $('.se-pre-con').fadeOut();
            }, 500);
        }

        $(document).on('click', '.btnSync', function(event) {
            event.preventDefault();
            showLoader();
            $.ajax({
                url: '{{url("saida/sync_vendas")}}'
                , method: 'POST'
                , data: {_token: '{{csrf_token()}}'}
                , dataType: 'json'
                , success: function(response) {
                    // alert("success")
                    if(response.success){
                        $.notify(response.message, {
                            icon: "success"
                            , buttons: false
                            , timer: 4500
                        });
                    }else{
                        $.notify(response.message, {
                            icon: "error"
                            , buttons: false
                            , timer: 4500
                        });
                    }
                    console.log(response)
                    // $('.lista-cliente').html(response);
                }
                , error: function(err) {
                    $.notify("Falha na sincronização!", {
                            icon: "error"
                            , buttons: false
                            , timer: 4500
                        });
                }
            }).always(function () {
                hideLoader(); //loader
            });
        })


        // $(document).on('click', '.btnSyncImportadoras', function(event) {
        //     event.preventDefault();
        //     showLoader();
        //     $.ajax({
        //         url: '{{url("saida/sync_importadoras")}}'
        //         , method: 'POST'
        //         , data: {_token: '{{csrf_token()}}'}
        //         , dataType: 'json'
        //         , success: function(response) {
        //             // alert("success")
        //             if(response.success){
        //                 $.notify(response.message, {
        //                     icon: "success"
        //                     , buttons: false
        //                     , timer: 1000
        //                 });
        //             }else{
        //                 $.notify(response.message, {
        //                     icon: "error"
        //                     , buttons: false
        //                     , timer: 1000
        //                 });
        //             }
        //             console.log(response)
        //             // $('.lista-cliente').html(response);
        //         }
        //         , error: function(err) {
        //             $.notify("Falha na sincronização!", {
        //                     icon: "error"
        //                     , buttons: false
        //                     , timer: 1000
        //                 });
        //         }
        //     }).always(function () {
        //         hideLoader(); //loader
        //     });
        // })






    </script>
@endsection
