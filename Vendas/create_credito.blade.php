@extends('layout')

@section('content')




    <div class="se-pre-con"></div>
    @include('saida.modal.TipoCotacao')

    <div class="page-header">
        <h4 class="page-title">Venda à Crédito</h4>

        <div class="btn-group btn-group-page-header ml-auto">
            {{-- <a type="button" href="/saida" class="btn btn-warning"  data-toggle="tooltip" title="Registar Utilizador">
                <i class="" style="color:white">Voltar</i>
            </a> --}}
        </div>
    </div>

    <?php
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


        // print_r($empresa);
        $usar_lotes = $empresa->usar_lotes;
    ?>
    <input type="hidden" name="usar_lotes" id="usar_lotes" value="{{$empresa->usar_lotes}}">
    <input type="hidden" name="pacote" id="pacote" value="{{$empresa->pacote}}">

    <div class="row">
        <br>
        @include('cliente.modal.AddCliente')

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form id="playground" class="form-horizontal" hidden>
                        <div class="row">
                            <div class="col-12 col-xs-12">
                                <textarea style="margin: 5px 5px 0 5px" id="consoleTextField" readonly="readonly" class="form-control" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xs-12">
                                <input style="margin: 5px 5px 0 5px" type="button" id="clearTextArea" class="btn btn-light btn-default" onclick="javascript:document.getElementById('consoleTextField').value = '';" value="Clear Console Log" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xs-12 ">
                                <select name="forma_pesquisa" id="forma_pesquisa" class="select2 form-control pesquisar selDiv">
                                    <option value="">Todos</option>
                                    <option value="pre">8880005330102 | Prestes a esgotar</option>
                                    <option value="esgotado">6009670370028 | Stock esgotado</option>
                                    <option value="optimo">Stock óptimo</option>
                                </select>
                            </div>
                            <div class="col-12 col-xs-12">
                                <h3>Methods</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xs-12 col-md-5">
                                <div style="margin: 5px 5px 0 5px" class="input-group mb-3">
                                    <input id="iTestInput" class="form-control" placeholder="Enter scan code or [key,key,...]" title="Scan code as string or [keyCode,keyCode,...]" />
                                    <div class="input-group-append input-group-btn">
                                        <input type="button" class="btn btn-primary" id="bFireTestInput" onclick="fireTestInput()" value="simulate()">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xs-12 col-md-7">
                                <input style="margin: 5px 5px 0 5px" type="button" id="bGenerateonScan" class="btn btn-primary" onclick="initOnScan()" value="attachTo(document)" />
                                <input style="margin: 5px 5px 0 5px" type="button" id="bDestroyonScan" class="btn btn-primary" onclick="destroyOnScan()" value="detachFrom(document)" />
                                <input style="margin: 5px 5px 0 5px" type="button" id="bGgetonScanSettings" class="btn btn-primary" onclick="getonScanSettings()" value="getOptions()" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-xs-12 col-md-6">
                                <h3>Mode</h3>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">reactToKeydown:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iAcceptKeyInput" type="checkbox" checked="checked"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">reactToPaste:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iAcceptPasteInput" type="checkbox" checked="checked"/>
                                    </div>
                                </div>
                                <h3>Events / Callbacks</h3>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">onScan:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iOnComplete" type="checkbox" checked="checked"/>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">onScanButtonLongPress:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iOnScanButtonLongPressed" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">onScanError:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iOnError" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">onKeyDetect:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iOnKeyDetect" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">onKeyProcess:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iOnKeyProcessed" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">onPaste:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iOnPaste" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">Use event handlers, not callbacks:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iCompleteHandler" type="checkbox" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-xs-12 col-md-6">
                                <h3>Options</h3>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">timeBeforeScanTest:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iTimeBeforeScanTest" class="form-control" value="100" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">avgTimeByChar:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iAvgTimeByChar" class="form-control" value="30" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">minLength:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iMinLength" class="form-control" value="6" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">suffixKeyCodes:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iEndChar" class="form-control" value="9,13" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">prefixKeyCodes:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iStartChar" class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">ignoreIfFocusOn:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <div class="input-group">
                                            <div class="input-group-prepend input-group-addon">
                                                <div class="input-group-text">
                                                    <input id="iIgnoreIfFocusOn" type="checkbox" />
                                                </div>
                                            </div>
                                            <input id="iIgnoreIfFocusOnSelector" class="form-control" value="input" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">scanButtonKeyCode:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iScanButtonKeyCode" class="form-control" value="" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">scanButtonLongPressTime:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iScanButtonLongPressThreshold" class="form-control" value="500" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-12 col-xs-12 col-sm-7 col-lg-6">singleScanQty:</label>
                                    <div class="col-12 col-xs-12 col-sm-5">
                                        <input id="iSingleScanQty" class="form-control" value="1" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">keyCodeMapper:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="ikeyCodeMapper" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">stopPropagation:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iStopPropagation" type="checkbox" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-9 col-xs-9 col-sm-7 col-lg-6">preventDefault:</label>
                                    <div class="col-3 col-xs-3 col-sm-5 checkbox form-check">
                                        <input id="iPreventDefault" type="checkbox" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                    session_start();

                    if(isset($_GET['cliente_id']) && !empty(@$_GET['cliente_id'])){
                        $_SESSION['cliente_id2'] = $_GET['cliente_id'];
                    }
                    $cliente_id2 = @$_SESSION['cliente_id2'];

                    if(isset($_GET['saida_id']) && !empty(@$_GET['saida_id'])){
                        $_SESSION['saida_id2'] = $_GET['saida_id'];
                    }
                    $saida_id = @$_SESSION['saida_id2'];
                    ?>
{{--                    <div class="row">--}}
{{--                        <fieldset class="fildset col-md-4">--}}
                            {{-- <h5>Dados do Cliente</h5> --}}
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="nome">Tipo de clientes</label>
                                    <select name="tipo_cliente_id" id="tipo_cliente_id" class="select2 form-control tipo_cliente_id">
                                        <option value="">Escolha o tipo de cliente...</option>
                                        @foreach ($tipoClientes as $item)
                                            <option value="{{$item->id}}">{{$item->descricao}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="nome">Cliente</label>
                                    <div class="input-group">
                                        {{-- <input type="text" placeholder="Introduza o Nome..." class="form-control"> --}}
                                        <select name="cliente_id" id="cliente_id" class="select2 form-control">
                                            <option value="">Escolha o cliente...</option>
{{--                                            @foreach ($clientes as $item)--}}
{{--                                                @if($item->tipo_cliente_id != 1)--}}
{{--                                                    <option value="{{$item->id}}" @if(isset($cliente_id2) && !empty($cliente_id2) && $item->id==$cliente_id2) selected @else '' @endif>{{$item->nome}}</option>--}}
{{--                                                @endif--}}
{{--                                            @endforeach--}}
{{--                                            <div class="opt-cliente"></div>--}}

                                        </select>
                                        @if(implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray()) == 'admin' || implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray()) == 'gestor')
                                        <div class="input-group-prepend">
                                            <button data-toggle="modal" data-target="#AddCliente" class="btn btn-search pr-1">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                            </div>


{{--                        </fieldset>--}}

{{--                    </div>--}}
                    <br>

                    <input type="hidden" id="cart_lote">
                    <input type="hidden" id="codigo_barras_produto">
{{--                    @if(implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray()) == 'admin' || implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray()) == 'gestor')--}}
                    <div class="row">
                        <?php
                        $total=0;
                        $total_qnt=0;
                        $total_taxa=0;
                        ?>
                        <div class="col-md-4">
                            <label for="exampleInputEmail1">Produto</label>
                            <input name="cart_produto_id" id="cart_produto_id" @if($empresa->pacote==1) class="mySelect2_all form-control" @else class="mySelect2 form-control" @endif placeholder="Escreva o código de barra ou descrição do produto">
                            {{-- @if(implode(' | ',auth()->user()->permissoes()->pluck('nome')->toArray()) == 'vendedor')
                                <input type="text" name="product_name" class="form-control" id="product_name" placeholder="Nome do produto" readonly>
                            @endif --}}

                        </div>
                        <div class="col-md-3 mb-3" <?= $usar_lotes=='0' ? 'hidden' : '' ?>>
                            <label for="exampleInputEmail1">Lote</label>
                            <input name="cart_lote_id" id="cart_lote_id" codigo_barras="" preco_compra="" preco_venda="" qtd_disponivel="" taxa="" class="mySelect2LotesByProduto form-control" placeholder="Selecione o lote">
                        </div>
                        <?php if($empresa->ver_stock_venda==1){?>
                        <div class="col-md-2">
                            <label for="exampleInputEmail1">Disponivel</label>
                            <input readonly type="text" name="cart_qnt_disponivel" class="form-control" id="cart_qnt_disponivel" placeholder="0" readonly>
                        </div>
                        <?php } ?>
                        <div class="col-md">
                            <label for="exampleInputEmail1">Quantidade </label>
                            <input type="number" min="1" value="1" name="cart_qnt" class="form-control" id="cart_qnt" placeholder="1">
                        </div>
                        <div class="col-md">
                            <label for="exampleInputEmail1">P.Unitário</label>
                            <input type="number" step=".01" name="cart_unit_price" class="form-control" id="cart_unit_price" placeholder="0">
                        </div>

                        <div class="col-md">
                            <label for="exampleInputEmail1">Total</label>
                            <input type="text" name="cart_coast" class="form-control" id="cart_coast" placeholder="0" readonly>
                        </div>

                        <div class="col-md-12 mt-3 text-right">
                            <button id="addToCart" type="button" class='btn btn-success btn-lg'>
                                <i class="fas fa-cart-plus "></i>
                            </button>
                        </div>
                    </div>
{{--                    @endif--}}
                    <br>
                    <div class="cart-lista">

                    </div>

                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="iva" id="iva" class="form-control iva">

    {{-- abrir sessao --}}
    <!-- Modal -->
    <div class="modal fade" id="abrir_sessao_modal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Abrir Sessão</h5>
            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button> --}}
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mb-5">
                    <label for="valor_abertura">Valor de abertura</label>
                    <input type="number" name="valor_abertura_sessao" id="valor_abertura_sessao" class="form-control">
                </div>
                <div class="col-md-12 text-right">
                    <button class="btn btn-success" type="submit" id="btn_abrir_sessao">Submeter</button>
                </div>
            </div>
            <form id="logout-form-abrir-sessao" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- BarCode Scanner js -->

    <script src="{{asset('js/onscan.min.js')}}"></script>

    <script>

        //start section


        $(document).on('click', '#registar_cotacao', function() {

            var tipo_cotacao = $('.tipo_cotacao:checked').val();

            var cliente = $("#cliente_id").val();
            var desconto = $("#total_desconto").val();
            var numero = $('#payment_numero').val();
            var payment_ref = $('#payment_ref').val();
            var email = $('#payment_email').val();
            var tipo_pagamento = $('#forma_pagamento').val();
            var tipo_venda = "credito";
            var valor_entregue = $('#valor_entregue').val();
            var trocos = $('#trocos').val();
            var valor_pago = $('#valor_pago').val();
            var percentagem = $('#desconto_check').is(':checked');
            var validade_cotacao = $("#validade_cotacao").val() ?? "";
            var descricao_rascunho = $("#descricao_rascunho").val();

            if (descricao_rascunho) {
                // alert(tipo_cotacao);

                $.ajax({
                        url: "{{ url('saida/cotacao_store') }}",
                        method: "post",
                        data: {
                            _token: '{{ csrf_token() }}',
                            cliente: cliente,
                            desconto: desconto,
                            tipo_pagamento: tipo_pagamento,
                            numero: numero,
                            referencia: payment_ref,
                            email: email,
                            valor_entregue: valor_entregue,
                            trocos: trocos,
                            tipo_venda: tipo_venda,
                            valor_pago: valor_pago,
                            percentagem: percentagem,
                            validade_cotacao: validade_cotacao,
                            tipo_cotacao: tipo_cotacao,
                            descricao_rascunho: descricao_rascunho
                        },
                        dataType: "json",
                        success: function(response) {
                            console.log(response);
                            if (response.success) {
                                $('#confirmarCotacao').modal('hide');
                                $.notify(response.message);
                                // printReciboCotacao(response.saida_id);
                                listarCarinho();


                            } else {
                                hideLoader();
                                $("#registar_venda").removeAttr("disabled");
                                $.notify(response.message);

                            }

                        },
                        error: function(response) {
                            alert("error")
                            // console.log(response);
                            hideLoader();
                            $("#registar_venda").removeAttr("disabled");
                        }
                    })
            } else {
                $.notify('Por favor, digite a descrição da venda!');
            }

        });

        //endsection


        let isScanner = false;
        $(function () {
            if (typeof console != "undefined")
                if (typeof console.log != 'undefined')
                    console.olog = console.log;
                else
                    console.olog = function () {
                    };

            console.log = function (message, error) {
                console.olog(message);
                var oOutput = document.getElementById('consoleTextField');
                if (error) {
                    oOutput.value += "ERROR: " + message + '\n';
                } else {
                    oOutput.value += ('> ' + message + '\n');
                }
                oOutput.scrollTop = oOutput.scrollHeight;
            };
            console.error = console.debug = console.info = console.log;

            window.onerror = function (msg) {
                console.log(msg, true);
            }

            function initOnScan() {
                var prop;
                var array;
                var suffixKeyCodes = [];
                var prefixKeyCodes = [];


                if (document.getElementById("iEndChar").value) {
                    // alert("iEndChar")
                    array = document.getElementById("iEndChar").value.split(",");
                    for (prop in array)
                        suffixKeyCodes.push(parseInt(array[prop]));
                    //			suffixKeyCodes.push(parseInt(prop));
                }
                if (document.getElementById("iStartChar").value) {
                    // alert("iStartChar")
                    array = document.getElementById("iStartChar").value.split(",")
                    for (prop in document.getElementById("iStartChar").value.split(","))
                        prefixKeyCodes.push(parseInt(array[prop]));
                }

                var options = {
                    timeBeforeScanTest: parseInt(document.getElementById("iTimeBeforeScanTest").value),
                    avgTimeByChar: parseInt(document.getElementById("iAvgTimeByChar").value),
                    minLength: parseInt(document.getElementById("iMinLength").value),
                    suffixKeyCodes: suffixKeyCodes,
                    prefixKeyCodes: prefixKeyCodes,
                    scanButtonLongPressTime: parseInt(document.getElementById("iScanButtonLongPressThreshold").value),
                    stopPropagation: document.getElementById("iStopPropagation").checked,
                    preventDefault: document.getElementById("iPreventDefault").checked,
                    reactToPaste: document.getElementById("iAcceptPasteInput").checked,
                    reactToKeyDown: document.getElementById("iAcceptKeyInput").checked,
                    singleScanQty: parseInt(document.getElementById("iSingleScanQty").value)
                }

                if (document.getElementById("iOnComplete").checked) {
                    options.onScan = function (barcode, qty) {
                        addProductToCartBarCode(barcode, qty)
                    };
                } else {
                    options.onScan = function () {
                    };
                }

                if (document.getElementById("iOnError").checked) {
                    // alert("iOnError")
                    options.onScanError = function (err) {
                        var sFormatedErrorString = "Error Details: {\n";
                        for (var i in err) {
                            sFormatedErrorString += '    ' + i + ': ' + err[i] + ",\n";
                        }
                        sFormatedErrorString = sFormatedErrorString.trim().replace(/,$/, '') + "\n}";
                        console.log("[onScanError]: " + sFormatedErrorString);
                    };
                } else {
                    options.onScanError = function () {
                    };
                }


                if (document.getElementById("iOnKeyProcessed").checked) {
                    // alert("iOnKeyProcessed")
                    options.onKeyProcess = function (sChar, oEvent) {
                        console.log('[onKeyProcess]: Processed character "' + sChar + '"');
                    };
                } else {
                    options.onKeyProcess = function () {
                    };
                }


                if (document.getElementById("iOnKeyDetect").checked) {
                    // alert("iOnKeyDetect")
                    options.onKeyDetect = function (iKey, oEvent) {
                        var oEventProps = ''
                            + 'key:"' + oEvent.key + '", '
                            + 'ctrlKey:' + oEvent.ctrlKey + ', '
                            + 'altKey:' + oEvent.altKey + ', '
                            + 'shiftKey:' + oEvent.shiftKey + ', '
                            + 'metaKey:' + oEvent.metaKey + ', '
                            + 'keyCode:' + oEvent.keyCode + ', '
                            + 'charCode:' + oEvent.charCode + ', ';
                        console.log('[onKeyDetect]: Detected key code "' + iKey + '". Event dump: ' + oEventProps);
                    };
                } else {
                    options.onKeyDetect = function () {
                    };
                }

                if (document.getElementById("iIgnoreIfFocusOn").checked) {
                    // alert("iIgnoreIfFocusOn")
                    document.getElementById("iIgnoreIfFocusOnSelector").removeAttribute("disabled");
                    options.ignoreIfFocusOn = document.getElementById("iIgnoreIfFocusOnSelector").value;
                } else {
                    // alert("iIgnoreIfFocusOFF")
                    options.ignoreIfFocusOn = false;
                    document.getElementById("iIgnoreIfFocusOnSelector").disabled = "disabled";
                }

                if (document.getElementById("iScanButtonKeyCode").value) {
                    // alert("iScanButtonKeyCode")
                    options.scanButtonKeyCode = parseInt(document.getElementById("iScanButtonKeyCode").value);
                } else {
                    options.scanButtonKeyCode = false;
                }

                if (document.getElementById("iOnScanButtonLongPressed").checked) {
                    // alert("iOnScanButtonLongPressed")
                    options.onScanButtonLongPress = function () {
                        console.log("[onScanButtonLongPress]: ScanButton has been long-pressed");
                    };
                } else {
                    options.onScanButtonLongPress = function () {
                    };
                }

                if (document.getElementById("iOnPaste").checked) {
                    // alert("iOnPaste")
                    options.onPaste = function (sPasteString) {
                        console.log("[onPaste]: Data has been pasted: " + sPasteString);
                    }
                } else {
                    options.onPaste = function () {
                    };
                }

                if (document.getElementById("iCompleteHandler").checked) {
                    document.addEventListener('scan', scanHandler);
                } else {
                    document.removeEventListener('scan', scanHandler);
                }

                if (document.getElementById("iCompleteHandler").checked) {
                    document.addEventListener('scanError', scanErrorHandler);
                } else {
                    document.removeEventListener('scanError', scanErrorHandler);
                }

                if (document.getElementById("ikeyCodeMapper").checked) {
                    // alert("ikeyCodeMapper")
                    options.keyCodeMapper = function (e) {
                        var iKeyCode = e.which;
                        var sChar = onScan.decodeKeyEvent(e);
                        console.log('[keyCodeMapper]: Decoding key code "' + iKeyCode + '" to "' + sChar + '"')
                        return sChar;
                    }
                }

                try {
                    onScan.attachTo(document, options);
                    console.log("onScan Started!");
                } catch (e) {
                    onScan.setOptions(document, options);
                    console.log("onScansettings changed!");
                }


            }

            function destroyOnScan() {
                console.log("onScan destroyed!");
                onScan.detachFrom(document);
            }

            function clearTextArea() {
                document.getElementById('consoleTextField').value = "";
            }

            function scanHandler(e) {
                console.log("[scanHandler]: Code: " + e.detail.code);
            }

            function scanErrorHandler(e) {
                var sFormatedErrorString = "Error Details: {\n";
                for (var i in e.detail) {
                    sFormatedErrorString += '    ' + i + ': ' + e.detail[i] + ",\n";
                }
                sFormatedErrorString = sFormatedErrorString.trim().replace(/,$/, '') + "\n}";
                console.log("[scanErrorHandler]: " + sFormatedErrorString);
            }

            function getonScanSettings() {
                var sFormatedErrorString = "Scanner Settings: \n";
                var aJSONArray = JSON.stringify(onScan.getOptions(document)).split(",");
                for (prop = 0; prop < aJSONArray.length - 1; prop++) {
                    if (aJSONArray[prop + 1][0] == '\"') {
                        sFormatedErrorString += aJSONArray[prop] + "," + "\n";
                    } else {
                        sFormatedErrorString += aJSONArray[prop] + ",";
                    }
                }
                sFormatedErrorString += aJSONArray[aJSONArray.length - 1];

                console.log(sFormatedErrorString);


            }

            function fireTestInput() {
                var sInput = (document.getElementById("iTestInput").value || '').trim();
                if (sInput.startsWith('[') && sInput.endsWith(']')) {
                    onScan.simulate(document, JSON.parse(sInput));
                } else {
                    onScan.simulate(document, sInput);
                }
            }

            (function () {
                initOnScan();
                document.querySelectorAll("#playground input").forEach(function (oInput) {
                    if (oInput.type == 'button' || oInput.readonly) {
                        return;
                    }

                    oInput.addEventListener('change', function () {
                        console.log('onScan configuration updated');
                        onScan.detachFrom(document);
                        initOnScan();
                    });
                });
            })();
        })
        $(function() {
            listarCarinho();
            campos_extras();
            // clientesPorTipo(0)
            // notificar();

            $(document).on('click change keyup', '#total_desconto', function(event) {

                var total = 0;
                var custo = parseFloat($("#custo").val());
                var desconto = parseFloat($(this).val());

                $(this).attr('min', 0);
                $(this).attr('max', custo);

                if (desconto != 0) {

                    total = custo - desconto;

                    $("#custo_total").val(total);
                    $("#valor").val(total);
                }
                // alert(total)

            });

            var tp = '';
            var switchStatus = false;
            $(document).on('change',"#pagarParcial", function() {
                switchStatus = $(this).is(':checked');
                if ($(this).is(':checked')) {
                    $(".pagarParcial").removeAttr("hidden");
                    listarCarinhoTipoPagamento()
                }
                else {
                    $(".pagarParcial").attr("hidden", true);
                    $("#valor_pago").val(0);
                    cleanPagamentoList()
                }
            });

            //change tipo cliente
            $(document).on('change', '.tipo_cliente_id', function() {
                var tipo_cliente_id = $(this).val();
                clientesPorTipo(tipo_cliente_id);
            })
            //change tipo cliente

            // Para o sistema que não vai usar lotes
            if($("#usar_lotes").val()=='0'){
                // start sistema sem uso de lotes
                // alert("Sem uso de lotes")

                $(document).on('dblclick', '.showLabel', function() {
                    var qnt_anterior = $(this).next().val()
                    $(this).hide().next().show().focus().focusout(function () {
                        var qnt = $(this).val();
                        var qnt_disp = $(this).attr("disp");
                        var p_uni = $(this).attr("p_uni");
                        var id = $(this).attr("id");
                        var codigo_barras = $(this).attr("codigo_barras");
                        var taxa = $("#iva").val();
                        if(qnt_anterior != qnt)
                            addToCart(id,qnt,qnt_disp,p_uni,qnt_anterior,0,'direct',taxa)
                        $(this).hide().prev().show();
                    });
                });

                $(document).on('dblclick', '.showLabelPreco', function() {
                    var p_uni_anterior = $(this).next().val();
                    $(this).hide().next().show().focus().focusout(function () {
                        var p_uni = $(this).val();
                        var qnt_disp = $(this).attr("disp");
                        var qnt = $(this).attr("qnt");
                        var qnt_anterior = qnt;
                        var id = $(this).attr("id");
                        var codigo_barras = $(this).attr("codigo_barras");
                        var taxa = $("#iva").val();
                        $.ajax({
                            url: '{{url("/lote/preco_custo")}}/'+id
                            , method: "POST"
                            , data: {
                                _token: '{{ csrf_token() }}',
                            }
                            , dataType: 'JSON'
                            , success: function (response) {
                                if(parseFloat(response) <= parseFloat(p_uni)){
                                    // alert('ok')
                                    // if(p_uni != p_uni_anterior){
                                    addToCart(id,qnt,qnt_disp,p_uni,qnt_anterior,0,'direct',taxa)
                                    // }
                                    $(this).hide().prev().show();
                                    // hideLoader()
                                }else {

                                    swal("Ops, o preço unitário deve ser maior que o preço de custo do produto!", {
                                        icon: "error"
                                    });
                                }

                            },
                            error: function (err) {
                                console.log(err)
                                alert('Error')
                            }
                        })
                        // if(p_uni != p_uni_anterior)
                        //     addToCart(id,qnt,qnt_disp,p_uni,qnt_anterior,0,'direct',taxa)
                        // $(this).hide().prev().show();
                    });
                });

                $('#cart_produto_id').change(function(event) {
                    event.preventDefault();
                    // alert($(this).attr("codigo_barras"))
                    // codigo_barras = event.added.codigo_barras;
                    // console.log(event.added.codigo_barras)
                    var produto_id = $(this).val();
                    // produto_id = produto_id.split('_')[0]
                    // $("#codigo_barras_produto").val(codigo_barras)
                    // alert(produto_id)
                    if (produto_id != "") {
                    //     selectLotes(produto_id)
                        if ($("#pacote").val()==1){
                            $("#cart_unit_price").val(event.added.preco);
                            calcularCusto();
                            isScanner = false
                        } else {
                            $.ajax({
                                url: '{{url("/lote/consulta")}}'
                                , type: "post"
                                , data: {
                                    _token: '{{csrf_token()}}'
                                    , produto_id: produto_id
                                }
                                , dataType: "json",
                                success: function(data) {
                                    console.log(data)
                                    $("#iva").val(data.taxa);
                                    preco = parseInt(data.preco_venda);
                                    qnt_disponivel = parseFloat(data.qnt_disponivel);
                                    $("#cart_qnt").attr('max', qnt_disponivel);
                                    $("#cart_qnt_disponivel").val(data.qnt_disponivel);//(0);//

                                    if(preco=="" || isNaN(preco)){
                                        $("#cart_unit_price").val(0);
                                        // $("#cart_unit_price").removeAttr('readonly')
                                    }else{
                                        $("#cart_unit_price").val(preco);
                                        executeAddToCart();
                                        // $("#cart_unit_price").attr('readonly', true)
                                    }

                                    //star para selecionar a proxima opcao

                                    // $("#cart_lote > option:selected")
                                    //     .prop("selected", false)
                                    //     .next()
                                    //     .prop("selected", true).trigger('change');

                                    //end

                                    calcularCusto();
                                    isScanner = false
                                }
                            });
                        }
                    } else {
                        $("#cart_unit_price").val(qnt_disponivel);
                    }
                    // lote
                    // lote_produto(codigo_barras, produto_id)
                });

                $("#addToCart").click(function(event) {
                    event.preventDefault();

                    var produto = $("#cart_produto_id").val();
                    // produto = produto.split('_')[0]
                    // alert(produto)
                    var qnt_disponivel = $("#cart_qnt_disponivel").val();
                    var qnt = $("#cart_qnt").val();
                    var preco = $("#cart_unit_price").val();
                    var custo = $("#cart_coast").val();
                    var desconto = $("#total_desconto").val();
                    var taxa = $("#iva").val();

                    var lote = null;
                    // var codigo_barras = $("#codigo_barras_produto").val()
                    // alert("codigo_barras: "+codigo_barras)
                    // if(lote !="") {
                    //     $(".lote_div").removeClass("has-error");
                        if (preco != 0) {
                            // if (parseInt(qnt_disponivel) >= parseInt(qnt)) {

                            $.ajax({
                                url: '{{url("/lote/preco_custo")}}/' + produto
                                , method: "POST"
                                , data: {
                                    _token: '{{ csrf_token() }}',
                                }
                                , dataType: 'JSON'
                                , success: function (response) {
                                    // alert(response)
                                    if (parseFloat(response) <= parseFloat(preco)) {
                                        $("#unit_price").removeClass('has-error')
                                        addToCart(produto, qnt, qnt_disponivel, preco, 0, desconto, 'manualInput', taxa,lote);
                                        // alert('c')
                                        $("#cart_produto_id").val('').trigger("change");
                                        ;
                                        $("#cart_qnt").val('');
                                        $("#cart_qnt_disponivel").val('');
                                        $("#cart_unit_price").val('');
                                        $("#cart_coast").val('');
                                        hideLoader()
                                    } else {
                                        // alert(response)
                                        $("#unit_price").addClass('has-error')
                                        swal("Ops, o preço unitário deve ser maior que o preço de custo do produto!", {
                                            icon: "error"
                                            // ,confirmButtonText:'Alterar',
                                            // , buttons: true
                                            // , timer: 5000
                                        });
                                    }

                                },
                                error: function (err) {
                                    alert('Error')
                                }
                            })


                            // } else {
                            //     swal(
                            //         'Quantidade não disponivel!'
                            //         , 'Porfavor, introduza uma qunatidade válida.'
                            //         , 'warning'
                            //     );
                            hideLoader()
                            // $("#cart_qnt").val(qnt_disponivel);
                            // }
                        } else {
                            swal("Ops, o preço unitário deve ser maior que zero!", {
                                icon: "warning"
                                // , confirmButtonText: 'Alterar',
                                // , buttons: true
                                // , timer: 5000
                            });
                            hideLoader()
                        }
                    // }else{
                    //     swal("Ops, Por favor selecione o Lote para venda", {
                    //         icon: "warning"
                    //         // , confirmButtonText: 'Alterar',
                    //         // , buttons: true
                    //         // , timer: 5000
                    //     });
                    //     if(lote==""){
                    //         $(".lote_div").addClass("has-error");
                    //     }else{
                    //         $(".lote_div").removeClass("has-error");
                    //     }
                    //     hideLoader()
                    // }

                });
            }else{
                // start sistema com uso de lotes
                // alert("usando lotes")

                $(document).on('dblclick', '.showLabel', function() {
                    var qnt_anterior = $(this).next().val()
                    $(this).hide().next().show().focus().focusout(function () {
                        var qnt = $(this).val();
                        var qnt_disp = $(this).attr("disp");
                        var p_uni = $(this).attr("p_uni");
                        var id = $(this).attr("id");
                        var codigo_barras = $(this).attr("codigo_barras");
                        var taxa = $("#iva").val();
                        var desconto = $(this).attr("desconto");

                        if(qnt_anterior != qnt)
                            addToCart(codigo_barras,qnt,qnt_disp,p_uni,qnt_anterior,desconto,'direct',taxa)
                        $(this).hide().prev().show();
                    });
                });

                $(document).on('dblclick', '.showLabelPreco', function() {
                    var p_uni_anterior = $(this).next().val();
                    $(this).hide().next().show().focus().focusout(function () {
                        var p_uni = $(this).val();
                        var qnt_disp = $(this).attr("disp");
                        var qnt = $(this).attr("qnt");
                        var qnt_anterior = qnt;
                        var id = $(this).attr("id");
                        var codigo_barras = $(this).attr("codigo_barras");
                        var taxa = $("#iva").val();
                        var desconto = $(this).attr("desconto");



                        $.ajax({
                            url: '{{url("/lote/preco_custo")}}/'+id
                            , method: "POST"
                            , data: {
                                _token: '{{ csrf_token() }}',
                            }
                            , dataType: 'JSON'
                            , success: function (response) {
                                if(parseFloat(response) <= parseFloat(p_uni)){
                                    // alert('ok')
                                    // if(p_uni != p_uni_anterior){
                                    addToCart(codigo_barras,qnt,qnt_disp,p_uni,qnt_anterior,desconto,'direct',taxa)
                                    // }
                                    $(this).hide().prev().show();
                                    // hideLoader()
                                }else {

                                    swal("Ops, o preço unitário deve ser maior que o preço de custo do produto!", {
                                        icon: "error"
                                    });
                                }

                            },
                            error: function (err) {
                                console.log(err)
                                alert('Error')
                            }
                        })
                        // if(p_uni != p_uni_anterior)
                        //     addToCart(id,qnt,qnt_disp,p_uni,qnt_anterior,0,'direct',taxa)
                        // $(this).hide().prev().show();
                    });
                });


                $(document).on('dblclick', '.showLabelDesconto', function() {
                    var p_uni_anterior = $(this).next().val();



                    $(this).hide().next().show().focus().focusout(function () {
                        var desconto = $(this).val();
                        var p_uni = $(this).attr("p_uni");
                        var qnt_disp = $(this).attr("disp");
                        var qnt = $(this).attr("qnt");
                        var qnt_anterior = qnt;
                        var id = $(this).attr("id");
                        var codigo_barras = $(this).attr("codigo_barras");
                        var taxa = $("#iva").val();

                        // var valor_desconto = (p_uni * qnt) * (desconto / 100);
                        // var valor_desconto = (p_uni) * (desconto / 100);


                        // var message = "Desconto: " + desconto + "\n" +
                        //             "Preço Unitário: " + p_uni + "\n" +
                        //             "Quantidade Disponível: " + qnt_disp + "\n" +
                        //             "Quantidade: " + qnt + "\n" +
                        //             "Quantidade Anterior: " + qnt_anterior + "\n" +
                        //             "ID: " + id + "\n" +
                        //             "Código de Barras: " + codigo_barras + "\n" +
                        //             "valor_desconto: " + valor_desconto + "\n" +
                        //             "Taxa: " + taxa;

                        // alert(message);


                        $.ajax({
                            url: '{{url("/lote/preco_custo")}}/'+id
                            , method: "POST"
                            , data: {
                                _token: '{{ csrf_token() }}',
                            }
                            , dataType: 'JSON'
                            , success: function (response) {
                                if(parseFloat(response) <= parseFloat(p_uni)){
                                    // alert('ok')
                                    // if(p_uni != p_uni_anterior){
                                    addToCart(codigo_barras,qnt,qnt_disp,p_uni,qnt_anterior,desconto,'direct',taxa)
                                    // }
                                    $(this).hide().prev().show();
                                    // hideLoader()
                                }else {

                                    swal("Ops, o preço unitário deve ser maior que o preço de custo do produto!", {
                                        icon: "error"
                                    });
                                }

                            },
                            error: function (err) {
                                console.log(err)
                                alert('Error')
                            }
                        })
                        // if(p_uni != p_uni_anterior)
                        //     addToCart(id,qnt,qnt_disp,p_uni,qnt_anterior,0,'direct',taxa)
                        // $(this).hide().prev().show();
                    });
                });

                $("#addToCart").click(function(event) {
                    event.preventDefault();

                    var produto = $("#cart_produto_id").val();
                    produto = produto.split('_')[0]
                    // alert(produto)
                    var qnt_disponivel = $("#cart_qnt_disponivel").val();
                    var qnt = $("#cart_qnt").val();
                    var preco = $("#cart_unit_price").val();
                    var custo = $("#cart_coast").val();
                    var desconto = $("#total_desconto").val();
                    var taxa = $("#iva").val();
                    // alert(preco)
                    var lote = $("#cart_lote_id").val();
                    var codigo_barras = $("#cart_lote_id").attr('codigo_barras')
                    var preco_compra = $("#cart_lote_id").attr('preco_compra');
                    if(preco!=0){
                        if(qnt > 0){
                            if(parseFloat(preco_compra) <= parseFloat(preco)){
                                $("#unit_price").removeClass('has-error')
                                addToCart(codigo_barras, qnt, qnt_disponivel, preco,0, desconto,'manualInput',taxa);
                                // alert('c')
                                $("#cart_produto_id").val('').trigger("change");;
                                $("#cart_qnt").val('');
                                $("#cart_unit_price").val('');
                                $("#cart_coast").val('');
                                hideLoader()
                            }else {
                                // alert(response)
                                $("#unit_price").addClass('has-error')
                                swal("Ops, o preço unitário deve ser maior que o preço de custo do produto!", {
                                    icon: "error"
                                    // ,confirmButtonText:'Alterar',
                                    // , buttons: true
                                    // , timer: 5000
                                });
                            }
                        }else{
                            swal("A quantidade deve ser maior que zero!", {
                                title: "ALERTA",
                                icon: "warning",
                                buttons: false,
                                timer: 5000,
                            });
                        }
                    }else{
                        swal("Opps, o preço unitário deve ser maior que zero!", {
                            icon: "warning"
                            , buttons: false
                            , timer: 5000
                        });
                    }

                });

                $('#cart_produto_id').change(function(event) {
                    event.preventDefault();
                    var produto_id = $(this).val();

                    getLotesByProduto(produto_id);
                });

                function getLotesByProduto(produto_id){
                    $.ajax({
                        url: '{{url("lote/getLotesActivosByProduto")}}'
                        , method: 'post'
                        , data:{
                            _token: '{{ csrf_token() }}',
                            produto_id: produto_id
                        }
                        , dataType: 'json'
                        , success: function(response) {
                            function contains(str1, str2) {
                                return new RegExp(str2, "i").test(str1);
                            }
                            $('.mySelect2LotesByProduto').select2({
                                theme: 'bootstrap4',
                                data: response,
                                placeholder: 'search',
                                allowClear: true,
                                multiple: false,
                                // query with pagination
                                query: function(q) {
                                    var pageSize,
                                        results,
                                        that = this;
                                    pageSize = 20; // or whatever pagesize
                                    results = [];
                                    if (q.term && q.term !== '') {
                                        // HEADS UP; for the _.filter function i use underscore (actually lo-dash) here

                                        results = _.filter(that.data, function(e) {
                                            return e.text.toUpperCase().indexOf(q.term.toUpperCase()) >= 0;
                                        });
                                    } else if (q.term === '') {
                                        results = that.data;
                                    }
                                    q.callback({
                                        results: results.slice((q.page - 1) * pageSize, q.page * pageSize),
                                        more: results.length >= q.page * pageSize,
                                    });
                                },
                            });
                        },
                        error: function(err){
                            // alert("Requisição teve um problema! Porfavor, contacte o suporte.")
                            console.log(err);
                        }
                    })
                }

                $(document).on('change', '#cart_lote_id', function(e){
                    e.preventDefault();
                    console.log(e);
                    // Verificar se e.added existe antes de tentar acessá-lo
                    if (e.added) {
                        console.log(e.added);
                        $(this).attr("preco_compra", e.added.preco_compra || null);
                        $(this).attr("preco_venda", e.added.preco_venda);
                        $(this).attr("qnt_disponivel", e.added.qnt_disponivel);
                        $(this).attr("data_validade", e.added.data_validade);
                        $(this).attr("codigo_barras", e.added.codigo_barras);

                        $("#iva").val(e.added.taxa);
                        qnt_disponivel = parseFloat(e.added.qnt_disponivel);
                        $("#cart_qnt").attr('max', qnt_disponivel);
                        $("#cart_qnt_disponivel").val(e.added.qnt_disponivel);

                        if(e.added.preco_venda == "" || isNaN(e.added.preco_venda)){
                            $("#cart_unit_price").val(0);
                        } else {
                            $("#cart_unit_price").val(e.added.preco_venda);
                            executeAddToCart();
                        }
                        calcularCusto();
                        isScanner = false;
                    } else {
                        // Quando e.added não existe, pode estar usando outro método de seleção
                        // Tente obter os valores diretamente do elemento selecionado
                        var selectedOption = $(this).find('option:selected');
                        if (selectedOption.length) {
                            var preco_compra = selectedOption.data('preco_compra') || null;
                            var preco_venda = selectedOption.data('preco_venda');
                            var qnt_disponivel = selectedOption.data('qnt_disponivel');
                            var data_validade = selectedOption.data('data_validade');
                            var codigo_barras = selectedOption.data('codigo_barras');
                            var taxa = selectedOption.data('taxa');

                            $(this).attr("preco_compra", preco_compra);
                            $(this).attr("preco_venda", preco_venda);
                            $(this).attr("qnt_disponivel", qnt_disponivel);
                            $(this).attr("data_validade", data_validade);
                            $(this).attr("codigo_barras", codigo_barras);

                            $("#iva").val(taxa);
                            $("#cart_qnt").attr('max', qnt_disponivel);
                            $("#cart_qnt_disponivel").val(qnt_disponivel);

                            if(preco_venda == "" || isNaN(preco_venda)){
                                $("#cart_unit_price").val(0);
                            } else {
                                $("#cart_unit_price").val(preco_venda);
                                executeAddToCart();
                            }
                            calcularCusto();
                            isScanner = false;
                        }
                    }
                    // $(this).attr("preco_compra",e.added.preco_compra)
                    // $(this).attr("preco_venda",e.added.preco_venda)
                    // $(this).attr("qnt_disponivel",e.added.qnt_disponivel)
                    // $(this).attr("data_validade",e.added.data_validade)
                    // $(this).attr("codigo_barras",e.added.codigo_barras)

                    // $("#iva").val(e.added.taxa);
                    // qnt_disponivel = parseFloat(e.added.qnt_disponivel);
                    // $("#cart_qnt").attr('max', qnt_disponivel);
                    // $("#cart_qnt_disponivel").val(e.added.qnt_disponivel);
                    // // alert(e.added.preco_venda)
                    // if(e.added.preco_venda=="" || isNaN(e.added.preco_venda)){
                    //     $("#cart_unit_price").val(0);
                    // }else{
                    //     $("#cart_unit_price").val(e.added.preco_venda);
                    //     executeAddToCart();
                    // }
                    // calcularCusto();
                    // isScanner = false
                })
            }

            function executeAddToCart(){ // barcode scanner
                if(isScanner)
                    $("#addToCart").click()
            }

            //change product and show name
            // $('#cart_produto_id').change(function(event) {
            //     // alert()
            //     event.preventDefault();
            //     codigo_barras = event.added.codigo_barras;
            //     console.log(event.added.codigo_barras)
            //     var produto_id = $(this).val();
            //     produto_id = produto_id.split('_')[0]
            //     // alert($(this).val())
            //     // var produto_id = $(this).val();
            //     $("#codigo_barras_produto").val(codigo_barras)
            //     // alert(codigo_barras)
            //     if (produto_id != "") {
            //         $.ajax({
            //             url: '{{url("/lote/consultaLote")}}'
            //             , type: "post"
            //             , data: {
            //                 _token: '{{csrf_token()}}'
            //                 , produto_id: produto_id
            //                 , codigo_barras: codigo_barras
            //             }
            //             , dataType: "json",
            //             success: function(data) {
            //                 $("#product_name").val(data.produto_nome_only)
            //                 console.log(data)
            //                 $("#iva").val(data.taxa);
            //                 preco = parseInt(data.preco_venda);
            //                 qnt_disponivel = parseFloat(data.qnt_disponivel);
            //                 $("#cart_qnt").attr('max', qnt_disponivel);
            //                 $("#cart_qnt_disponivel").val(data.qnt_disponivel);
            //                 // preco = parseInt(data.preco_venda);
            //                 // qnt_disponivel = parseInt(data.qnt_total_disponivel);
            //                 // $("#cart_qnt").attr('max', qnt_disponivel);
            //                 // if(data.qnt_total_disponivel>0){
            //                 // $("#cart_qnt_disponivel").val(data.qnt_total_disponivel);
            //                 // calcularAjuste();
            //                 // }else{
            //                 //     $("#cart_qnt_disponivel").val(0);
            //                 //     calcularAjuste();
            //                 // }
            //                 // alert($("#cart_qnt_disponivel").val())
            //                 // alert(preco)

            //                 if(preco=="" || preco=="NaN"){
            //                     $("#cart_unit_price").val(0);

            //                 }else{
            //                     $("#cart_unit_price").val(preco);
            //                 }
            //                 calcularCusto();
            //                 executeAddToCart();
            //                 isScanner = false
            //             },
            //             error: function(err){
            //                 alert("err")
            //             }
            //         });
            //     } else {

            //         // $("#cart_unit_price").val(qnt_disponivel);
            //     }

            //     lote_produto(codigo_barras, produto_id)
            // });

            // function lote_produto(codigo_barras, produto_id){
            //     $.ajax({
            //         url: '{{url("/loteProduto")}}'
            //         , type: "post"
            //         , data: {
            //             _token: '{{csrf_token()}}'
            //             , produto_id: produto_id,
            //             codigo_barras: codigo_barras
            //         }
            //         , dataType: "json",
            //         success: function(data) {
            //             console.log(data)
            //             // alert(data.id)
            //             $("#cart_lote").val(data.id)
            //         }
            //     });
            // }
            //change product and show name

            $(document).on('change', '#forma_pagamento', function() { campos_extras(); });
            $(document).on('click', '#continuar', function() {

                campos_extras();

                flatpickr(".date", {
                    altInput: true,
                    maxDate: "today",
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d"
                });

                $("#forma_pagamento").select2()

                //Scripts de tipo de pagamento
                $(function() {
                    $("#forma_pagamento").select2()
                    listarCarinhoTipoPagamento();
                })

                $('#confirmarSaida').on('shown.bs.modal', function () {
                let progress = document.querySelector(".progress span");
                let steps = document.querySelectorAll(".step");
                let btnNext = document.querySelector(".btn-next");
                let btnPrev = document.querySelector(".btn-prev");
                let contents = document.querySelectorAll(".content-step");
                let countSteps = steps.length;
                let step = 1;

                // Adicionar eventos ao botão Próximo
                btnNext.addEventListener("click", () => {
                    let widthProgress = (step / (countSteps - 1)) * 100;
                    btnPrev.classList.remove("disabled");
                    btnPrev.removeAttribute("disabled");

                    if (step + 1 <= countSteps) {
                        step++;
                        let newStep = document.querySelector(`[data-step="${step}"]`);
                        progress.style.width = `${widthProgress}%`;
                        newStep.classList.add("active");

                        // Atualizar conteúdo exibido
                        updateContent(step);
                    }

                    if (step === countSteps) {
                        btnNext.textContent = "Registar";
                        if (btnNext.getAttribute("ready")) {
                            btnNext.setAttribute("id", "registar_venda");
                        }
                        btnNext.setAttribute("ready", "true");
                    } else {
                        btnNext.textContent = "Próximo";
                        btnNext.removeAttribute("id");
                        btnNext.removeAttribute("ready");
                    }
                });

                // Adicionar eventos ao botão Anterior
                btnPrev.addEventListener("click", () => {
                    btnNext.classList.remove("disabled");
                    btnNext.removeAttribute("disabled");

                    if (step > 1) {
                        let newStep = document.querySelector(`[data-step="${step}"]`);
                        newStep.classList.remove("active");
                        step--;
                        let widthProgress = ((step - 1) / (countSteps - 1)) * 100;
                        progress.style.width = `${widthProgress}%`;

                        // Atualizar conteúdo exibido
                        updateContent(step);
                    }

                    if (step < countSteps) {
                        btnNext.textContent = "Próximo";
                        btnNext.removeAttribute("id");
                        btnNext.removeAttribute("ready");
                    }

                    if (step === 1) {
                        btnPrev.classList.add("disabled");
                        btnPrev.setAttribute("disabled", "disabled");
                    }
                });

                // Função para exibir o conteúdo correto baseado no step atual
                function updateContent(step) {
                    contents.forEach(content => content.classList.remove("active"));
                    document.querySelector(`#content-${step}`).classList.add("active");
                }
                });

            });


            $(document).on('submit', '#clienteSaida', function(event) {
                event.preventDefault();
                $.ajax({
                    url: '{{url("/cliente/addSaida")}}'
                    , method: 'post'
                    , data: $("#clienteSaida").serialize()
                    , dataType: 'json'
                    , success: function(response) {

                        if (response.success) {

                            $('#cliente_id').append('<option value="' + response.id + '">' + response.nome + '</option>');
                            $("#cliente_id").val(response.id).change();
                            // $("#closeModal").click();
                            $('#AddCliente').modal('hide')
                            $.notify(response.nome + " adicionado com sucesso !");

                        } else {
                            $.notify("Opps! Ocorreu um erro ao tentar adiconar cliente.");
                        }

                    }
                    , error: function() {
                        alert('Ocorreu um erro desconhecido');
                    }
                });
                return false
            });

            function campos_extras() {
                pagamento = parseInt($('#forma_pagamento').val());

                if (pagamento == 2 || pagamento == 3 || pagamento == 5) {
                    tp = "<div class='col col-md-6'>" +
                        "<label>Numero</label>" +
                        "<input type='number' name='payment_numero' id='payment_numero' class='form-control'>" +
                        "</div>";

                    if (pagamento == 3) {

                        tp += "<div class='col col-md-6'>" +
                            "<label>Referência</label>" +
                            "<input type='text' name='payment_ref' id='payment_ref' class='form-control'>" +
                            "</div>";
                    }

                } else if (pagamento == 4) {

                    tp = "<div class='col col-md-6'>" +
                        "<label>Numero</label>" +
                        "<input type='number' name='payment_numero' id='payment_numero' class='form-control'>" +
                        "</div>"
                        // +
                        // "<div class='col col-md-6'>" +
                        // "<label >E-mail</label>" +
                        // "<input type='email' name='payment_email' id='payment_email' class='form-control'>" +
                        // "</div>"
                        ;

                } else {

                    tp = '';

                }

                $('.campos_extras').html(tp);
            }

            // $(document).on('click', "#total_desconto", function() {
            //     var ele = parseFloat($(this).val());
            //
            //     if (parseFloat(ele) == 0)
            //         $(this).val('');
            //
            // });


            $(document).on("keyup", "#valor_pago", function(event) {
                if($(this).val() > 0)
                    $(".metodoPagamento").removeAttr('hidden')
                else
                    $(".metodoPagamento").attr('hidden', true)
            })


            $(document).on("click", "#registar_venda", function(event) {
                event.preventDefault();
                // alert("v.c")
                var cliente = $("#cliente_id").val();
                var desconto = $("#total_desconto").val();
                var numero = $('#payment_numero').val();
                var payment_ref = $('#payment_ref').val();
                var email = null;//$('#payment_email').val();
                var tipo_pagamento = $('#forma_pagamento').val();
                var tipo_venda = "credito";
                var valor_pago = $('#valor_pago').val();
                var valor_total = $('#custo_total').val();
                var percentagem = $('#desconto_check').is(':checked');
                var total_carrinho_pagamento = $('#total_carrinho_tipo_pagamento').val();
                var data_venda = $('#data_venda').val();
                var nome_segurado = $("#nome_segurado").val() ?? null;
                var numero_membro = $("#numero_membro").val() ?? null;
                var codigo_autorizacao = $("#codigo_autorizacao").val() ?? null;

                const urlParams = new URLSearchParams(window.location.search);
                const cotacao = urlParams.get('cotacao');
                const cotacao_id = urlParams.get('saida_id');
                // alert(cotacao)
                // alert(cotacao_id)


                if($("#pagarParcial").is(":checked")==true){
                    if(parseFloat(total_carrinho_pagamento) != 0){
                        if(parseFloat(valor_pago) != parseFloat(total_carrinho_pagamento)){
                            $("#valor_pago").closest('.form-group').removeClass('has-success').addClass('has-error');
                            swal(
                                'Error!'
                                , 'O valor por pagar deve ser igual ao somatório de valor por tipo de pagamento!'
                                , 'error'
                            );
                            return false;
                        }else{
                            $("#valor_pago").closest('.form-group').removeClass('has-success').addClass('has-error');
                        }

                        if(cliente == ""){
                            $("#cliente_id").closest('.form-group,.input-group').removeClass('has-success').addClass('has-error');
                            swal(
                                'Campo vazio!'
                                , 'Porfavor, selecione o cliente !'
                                , 'warning'
                            );
                        }else {
                            $("#cliente_id").closest('.form-group,.input-group').removeClass('has-error').addClass('has-success');
                            $("#registar_venda").attr("disabled", true);
                            showLoader();
                            $.ajax({
                                url: '{{url("saida/credito")}}'
                                , method: "post"
                                , data: {
                                    _token: '{{csrf_token()}}'
                                    , cliente: cliente
                                    , desconto: desconto
                                    , tipo_pagamento: tipo_pagamento
                                    , numero: numero
                                    , referencia: payment_ref
                                    , email: email
                                    , tipo_venda: tipo_venda
                                    , valor_pago: valor_pago
                                    , percentagem: percentagem
                                    , data_venda: data_venda
                                    , nome_segurado: nome_segurado
                                    , codigo_autorizacao: codigo_autorizacao
                                    , numero_membro: numero_membro
                                    , cotacao: cotacao
                                    , cotacao_id: cotacao_id
                                }
                                , dataType: "json"
                                , success: function (response) {

                                    if (response.success) {
                                        $('#confirmarSaida').modal('hide');
                                        printRecibo(response.saida_id);
                                        $(".modal-backdrop").removeClass('show')
                                        $(".modal-backdrop").removeClass('fade')
                                        $(".modal-backdrop").removeClass('modal-backdrop')
                                        setTimeout(function() {
                                            listarCarinho();
                                            // window.location.reload();
                                        }, 800);
                                    } else {
                                        hideLoader();
                                        $("#registar_venda").removeAttr("disabled");
                                        $.notify(response.message);
                                    }

                                }
                                , error: function (response) {
                                    console.log(response);
                                    hideLoader();
                                    $("#registar_venda").removeAttr("disabled");
                                }
                            })
                        }

                    }else{
                        swal(
                            'Ops'
                            , "Por favor adicione o metodo de pagamento!"
                            , 'error'
                        );
                        hideLoader();
                    }
                }else{
                    if(parseFloat(valor_pago) != parseFloat(total_carrinho_pagamento)){
                        $("#valor_pago").closest('.form-group').removeClass('has-success').addClass('has-error');
                        swal(
                            'Error!'
                            , 'O valor por pagar deve ser igual ao somatório de valor por tipo de pagamento!'
                            , 'error'
                        );
                        return false;
                    }else{
                        $("#valor_pago").closest('.form-group').removeClass('has-success').addClass('has-error');
                    }

                    if(parseFloat(valor_pago) >= parseFloat(valor_total)){
                        $("#valor_pago").closest('.form-group').removeClass('has-success').addClass('has-error');
                    }else {
                        $("#valor_pago").closest('.form-group').removeClass('has-error').addClass('has-success');
                        if(cliente == ""){
                            $("#cliente_id").closest('.form-group,.input-group').removeClass('has-success').addClass('has-error');
                            swal(
                                'Campo vazio!'
                                , 'Porfavor, selecione o cliente !'
                                , 'warning'
                            );
                        }else {
                            $("#cliente_id").closest('.form-group,.input-group').removeClass('has-error').addClass('has-success');
                            $("#registar_venda").attr("disabled", true);
                            showLoader();
                            $.ajax({
                                url: '{{url("saida/credito")}}'
                                , method: "post"
                                , data: {
                                    _token: '{{csrf_token()}}'
                                    , cliente: cliente
                                    , desconto: desconto
                                    , tipo_pagamento: tipo_pagamento
                                    , numero: numero
                                    , referencia: payment_ref
                                    , email: email
                                    , tipo_venda: tipo_venda
                                    , valor_pago: valor_pago
                                    , percentagem: percentagem
                                    , data_venda: data_venda
                                    , nome_segurado
                                    , codigo_autorizacao
                                    , numero_membro
                                    , cotacao: cotacao
                                    , cotacao_id: cotacao_id

                                }
                                , dataType: "json"
                                , success: function (response) {

                                    if (response.success) {
                                        $('#confirmarSaida').modal('hide');
                                        printRecibo(response.saida_id);
                                        setTimeout(function() {
                                            listarCarinho();
                                            // window.location.reload();
                                        }, 1000);
                                    } else {
                                        hideLoader();
                                        $("#registar_venda").removeAttr("disabled");
                                        $.notify(response.message);
                                    }

                                }
                                , error: function (response) {
                                    console.log(response);
                                    hideLoader();
                                    $("#registar_venda").removeAttr("disabled");
                                }
                            })
                        }
                    }
                }
            });

            // $(document).on("load", function(event) {alert("f")
            //     event.preventDefault();
            //     // showLoader();
            //     $.ajax({
            //         url: '{{url("saida/notificar")}}'
            //         , data: {
            //             _token: '{{csrf_token()}}'
            //         }
            //         , method: "GET"
            //         , dataType: 'JSON'
            //         , success: function(response) {alert("")
            //             $.notify(response.message);
            //             swal(
            //             'Notificação'
            //             , 'response.message'
            //             , 'warning'
            //         );
            //         }
            //         , error: function(response) {
            //             console.log(response);
            //         }
            //     })
            // });
            $("#cart_unit_price").on('keyup change', function() {
                event.preventDefault();
                calcularCusto();
            })
            // $('#cart_produto_id').change(function(event) {
            //     event.preventDefault();
            //     // alert("")
            //     var produto_id = $(this).val();
            //     if (produto_id != "") {
            //         $.ajax({
            //             url: '{{url("/lote/consulta")}}'
            //             , type: "post"
            //             , data: {
            //                 _token: '{{csrf_token()}}'
            //                 , produto_id: produto_id
            //             }
            //             , dataType: "json",

            //             success: function(data) {
            //                 $("#iva").val(data.taxa);
            //                 preco = parseInt(data.preco_venda);
            //                 qnt_disponivel = parseInt(data.qnt_total_disponivel);
            //                 $("#cart_qnt").attr('max', qnt_disponivel);
            //                 $("#cart_qnt_disponivel").val(data.qnt_total_disponivel);
            //                 if(preco=="" || isNaN(preco)){
            //                     $("#cart_unit_price").val(0);
            //                     // $("#cart_unit_price").removeAttr('readonly')
            //                 }else{
            //                     $("#cart_unit_price").val(preco);
            //                     executeAddToCart();
            //                     // $("#cart_unit_price").attr('readonly', true)
            //                 }
            //                 calcularCusto();
            //                 isScanner = false
            //             }
            //         });
            //     } else {

            //         $("#cart_unit_price").val(qnt_disponivel);
            //     }

            // });


            $("#cart_qnt").on('keyup change', function(event) {
                event.preventDefault();
                if (parseInt($(this).val()) != 0) {
                    calcularCusto();
                } else {
                    swal(
                        'Dado inválido!'
                        , 'Porfavor, introduza uma quantidade válida.'
                        , 'warning'
                    );
                    $(this).val(1);
                    calcularCusto();
                }


            });


            // $("#addToCart").click(function(event) {
            //     event.preventDefault();

            //     var produto = $("#cart_produto_id").val();
            //     produto = produto.split('_')[0]
            //     // alert(produto)
            //     var qnt_disponivel = $("#cart_qnt_disponivel").val();
            //     var qnt = $("#cart_qnt").val();
            //     var preco = $("#cart_unit_price").val();
            //     var custo = $("#cart_coast").val();
            //     var desconto = $("#total_desconto").val();
            //     var taxa = $("#iva").val();

            //     var lote = $("#cart_lote").val();
            //     var codigo_barras = $("#codigo_barras_produto").val()
            //     if(preco!=0){
            //         // if (parseInt(qnt_disponivel) >= parseInt(qnt)) {


            //         $.ajax({
            //             url: '{{url("/lote/preco_custo")}}/'+produto
            //             , method: "POST"
            //             , data: {
            //                 _token: '{{ csrf_token() }}',
            //             }
            //             , dataType: 'JSON'
            //             , success: function (response) {
            //                 // alert(response)
            //                 if(parseFloat(response) <= parseFloat(preco)){
            //                     $("#unit_price").removeClass('has-error')
            //                     addToCart(codigo_barras, qnt, qnt_disponivel, preco,0, desconto,'manualInput',taxa);
            //                     // alert('c')
            //                     $("#cart_produto_id").val('').trigger("change");;
            //                     $("#cart_qnt").val('');
            //                     $("#cart_unit_price").val('');
            //                     $("#cart_coast").val('');
            //                     hideLoader()
            //                 }else {
            //                     // alert(response)
            //                     $("#unit_price").addClass('has-error')
            //                     swal("Ops, o preço unitário deve ser maior que o preço de custo do produto!", {
            //                         icon: "error"
            //                         // ,confirmButtonText:'Alterar',
            //                         // , buttons: true
            //                         // , timer: 5000
            //                     });
            //                 }

            //             },
            //             error: function (err) {
            //                 alert('Error')
            //             }
            //         })


            //         // addToCart(produto, qnt, qnt_disponivel, preco,0, desconto,'manualInput',taxa);
            //         // // alert('c')
            //         // $("#cart_produto_id").val('').trigger("change");;
            //         // $("#cart_qnt").val('');
            //         // $("#cart_unit_price").val('');
            //         // $("#cart_coast").val('');
            //         //
            //         // // } else {
            //         // //     swal(
            //         // //         'Quantidade não disponivel!'
            //         // //         , 'Porfavor, introduza uma qunatidade válida.'
            //         // //         , 'warning'
            //         // //     );
            //         // hideLoader()
            //         // $("#cart_qnt").val(qnt_disponivel);
            //         // }
            //     }else{
            //         swal("Opps, o preço unitário deve ser maior que zero!", {
            //             icon: "warning"
            //             , buttons: false
            //             , timer: 5000
            //         });
            //     }

            // });
            $(document).on("click", ".cancelar-cart", function(event) {
                event.preventDefault();
                var ele = $(this);
                swal({
                    title: 'Deseja cancelar a venda?',
                    // text: "Após apagar não será possivel recuperar!",
                    type: 'warning'
                    , buttons: {
                        cancel: {
                            visible: true
                            , text: 'Não, cancelar!'
                            , className: 'btn btn-danger'
                        }
                        , confirm: {
                            text: 'Sim, remover!'
                            , className: 'btn btn-success'
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            url: '{{url("/saida/carinho/cancelar_credito/")}}'
                            , method: "POST"
                            , data: {
                                _token: '{{ csrf_token() }}',
                                tipo: 'cancelamento_venda_credito'
                            }
                            , dataType: 'JSON'
                            , success: function(response) {

                                if (response.success) {
                                    listarCarinho();
                                    $.notify("Venda cancelada com sucesso!", {
                                        icon: "success"
                                        , timer: 1000
                                        , });

                                } else {
                                    swal("Opps, ocorreu um erro!", {
                                        icon: "error"
                                        , buttons: false
                                        , timer: 1000
                                        , });
                                }
                            }

                        });


                        // $.ajax({
                        //   url:'/saida/carinho/remover/'+ele.attr("value"),
                        //   method: "GET",
                        //   success: function(response) {
                        //   if(response.success){
                        //       listarCarinho();
                        //   }
                        //   }
                        // });

                    } else {
                        swal.close();
                    }
                });


            });

            $(document).on("click", ".remove-from-cart", function(event) {
                event.preventDefault();

                var ele = $(this);
                swal({
                    title: 'Deseja remover o produto ?',
                    // text: "Após apagar não será possivel recuperar!",
                    type: 'warning'
                    , buttons: {
                        cancel: {
                            visible: true
                            , text: 'Não, cancelar!'
                            , className: 'btn btn-danger'
                        }
                        , confirm: {
                            text: 'Sim, remover!'
                            , className: 'btn btn-success'
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {

                        $.ajax({
                            url: '{{url("/saida/carinho/remover_credito")}}/' + ele.attr("value")
                            , method: "POST"
                            , data: {
                                _token: '{{ csrf_token() }}',
                                id: ele.attr("value"),
                                tipo: "cancelamento_venda_credito",
                            }
                            , dataType: 'JSON'
                            , success: function(response) {

                                if (response.success) {
                                    listarCarinho();
                                    $.notify("Removido com sucesso!", {
                                        icon: "success"
                                        , timer: 1000
                                        , });

                                } else {
                                    swal("Opps, ocorreu um erro!", {
                                        icon: "error"
                                        , buttons: false
                                        , timer: 1000
                                        , });
                                }
                            }

                        });
                        // $.ajax({
                        //   url:'/saida/carinho/remover/'+ele.attr("value"),
                        //   method: "GET",
                        //   success: function(response) {
                        //   if(response.success){
                        //       listarCarinho();
                        //   }
                        //   }
                        // });

                    } else {
                        swal.close();
                    }
                });


            });
            function notificar(){
                $.ajax({
                    url: '{{url("saida/notificar")}}'
                    // , data: {
                    //     _token: '{{csrf_token()}}'
                    // }
                    , method: "GET"
                    , dataType: 'JSON'
                    , success: function(response) {//alert("")
                        // $.notify(response.message);
                        swal(
                            'Notificação'
                            , response.message
                            , 'warning'
                        );
                    }
                    , error: function(response) {
                        console.log(response);
                    }
                })
            }


        });
        function addToCart(produto, qnt, qnt_disponivel, preco,qntAnterior, desconto, tipo, taxa){
            $.ajax({
                url: '{{url("/saida/carinho/adicionar_credito")}}/' + produto
                , data: {
                    cart_qnt: qnt
                    , unit_price: preco
                    , cart_tax: ''
                    , deconto: desconto
                    , disponivel: qnt_disponivel
                    , qntAnterior: qntAnterior
                    , tipo: tipo
                    , taxa:taxa
                    , _token: '{{csrf_token()}}'
                }
                , method: "POST"
                , dataType: 'JSON'
                , success: function(response) {

                    calcularCusto();
                    console.log(response);
                    if (response.success) {
                        $.notify(response.message);
                        $("#cart_qnt").val(1);
                        $("#cart_qnt_disponivel").val(0)
                        listarCarinho();

                    } else if (response.quantidade) {
                        swal(
                            'Quantidade não disponivel!'
                            , 'Porfavor, introduza uma quantidade válida.'
                            , 'warning'
                        );
                        hideLoader()
                    }else if(response.success == false){
                        swal(
                            response.message
                            , ''
                            , 'warning'
                        );
                        listarCarinho();
                    }
                }
            });
        }
        function listarCarinho() {
            showLoader();
            $.ajax({
                url: '{{url("/saida/carinhoCredito/listarCredito")}}'
                , method: 'post'
                , data: {
                    _token: '{{csrf_token()}}'
                }
                , dataType: 'html'
                , success: function(response) {
                    $('.cart-lista').html(response);
                }

            }).always(function () {
                hideLoader(); //loader
            });


        }

        function clientesPorTipo(tipo_cliente_id) {
            // alert(tipo_cliente_id)
            showLoader();
            $.ajax({
                url: '{{url("/saida/clientesPorTipo")}}'
                , method: 'post'
                , data: {
                    _token: '{{csrf_token()}}',
                    tipo_cliente_id: tipo_cliente_id
                }
                , dataType: 'html'
                , success: function(response) {
                    // alert(response)
                    $('#cliente_id').html(response);
                }

            }).always(function () {
                hideLoader(); //loader
            });


        }


        function addProductToCartBarCode(barcode, qty){
            if($("#usar_lotes").val()=='0'){
                barcode = barcode
            }else{
                barcode = parseInt(barcode)
            }
            $.ajax({
                url: '{{url("/saida/carinho/adicionarVCCodBar")}}/' + barcode
                , data: {
                    barcode: barcode
                    , qty: qty
                    , _token: '{{csrf_token()}}'
                }
                , method: "POST"
                , dataType: 'JSON'
                , success: function(response) {
                    calcularCusto();
                    if (response.success) {
                        $.notify(response.message);
                        $("#cart_qnt").val(1);
                        // $("#cart_qnt_disponivel").val()
                        listarCarinho();
                    } else {
                        swal(
                            response.message
                            , ''
                            , 'warning'
                        );
                        // swal(
                        //     'Código de barras não encontrado!'
                        //     , 'Porfavor, pesquise pela descrição.'
                        //     , 'warning'
                        // );
                        hideLoader()
                    }
                }
            });
        }

        function calcularCusto() {

            var custo = 0;
            var qnt = $('#cart_qnt').val();
            // alert(qnt)
            if (parseInt(qnt) >= 1) {
                var preco_unit = $("#cart_unit_price").val();
                // alert(preco_unit)
                if(preco_unit == "NaN"){
                    preco_unit = 0;
                    $("#cart_unit_price").val(0);
                    // alert(preco_unit)
                    $("#cart_coast").val(0);
                }
                if (preco_unit != 0) {
                    custo = (qnt * preco_unit);
                }else{
                    custo = 0;
                }
                // alert(custo)
            }

            $('#cart_coast').val(custo);
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



        //Scripts de tipo de pagamento
        $(function (){
            $("#forma_pagamento").select2()
            listarCarinhoTipoPagamento();

            $(document).on("click", "#addTipoPagamento", function (){
                var tipo_pagamento = $("#forma_pagamento").val();
                var valor = $("#valor").val();
                var total_carrinho_tipo_pagamento = $("#total_carrinho_tipo_pagamento").val();
                var total_por_pagar = parseFloat($("#custo_total").val());
                var total_por_pagar2 = parseFloat($("#valor_pago").val());
                var numero = $("#payment_numero").val();
                var referencia = $("#payment_ref").val();
                // var email = $("#payment_email").val();
                var sum = parseFloat(valor)+parseFloat(total_carrinho_tipo_pagamento);
                // alert(total_carrinho_tipo_pagamento)

                if(tipo_pagamento != "" && valor != "") {
                    $(".div_form_pagamento").removeClass('has-error');
                    $(".div_valor").removeClass('has-error');
                    if(parseFloat(sum) <= parseFloat(total_por_pagar)) {
                        $.ajax({
                            url: '{{url("/saida/adicionarTipoPagamento2")}}'
                            , method: "POST"
                            , data: {
                                tipo_pagamento: tipo_pagamento
                                , valor: valor
                                , numero: numero
                                , referencia: referencia
                                // , email: email
                                , _token: '{{ csrf_token() }}'
                            }
                            , dataType: 'JSON'
                            , success: function (response) {

                                if (response.success) {
                                    listarCarinhoTipoPagamento();
                                    $("#valor_pago").val(parseFloat(sum))

                                    $.notify(response.message, {
                                        icon: "success"
                                        , timer: 1000
                                        ,
                                    });
                                    // $("#forma_pagamento").val('').trigger("change");
                                    $("#valor").val('');
                                    $("#payment_numero").val('');
                                    $("#payment_ref").val('');
                                    // $("#payment_email").val('');

                                } else {
                                    swal({
                                        title: "Ops",
                                        text: response.message,
                                        icon: "error"
                                        // , buttons: false
                                        // , timer: 1000
                                    });
                                }
                            },
                            error: function (err) {
                                console.log(err)
                                alert("Bug")
                            }

                        });
                    }else{
                        $(".div_valor").addClass('has-error');
                        swal({
                            icon: "error"
                            ,title: "Ops"
                            ,text: "O valor total por pagar, não pode ser menor que o valor total das formas de pagamentos!"
                            // , buttons: false
                            // , timer: 1000
                            ,
                        });
                    }
                }else {
                    if(valor == ""){
                        $(".div_valor").addClass('has-error');
                    }
                    if(tipo_pagamento == ""){
                        $(".div_form_pagamento").addClass('has-error');
                    }
                    swal({
                        icon: "error"
                        ,title: "Ops"
                        ,text: "Selecione a forma de pagamento e preencha o campo valor"
                        // , buttons: false
                        // , timer: 1000
                        ,
                    });
                }
            })


            $(document).on("click", ".remove-from-cart-tipoPagamento", function(event) {
                event.preventDefault();

                var ele = $(this);


                swal({
                    title: 'Deseja remover a forma de pagamento ?',
                    // text: "Após apagar não será possivel recuperar!",
                    type: 'warning'
                    , buttons: {
                        cancel: {
                            visible: true
                            , text: 'Não, cancelar!'
                            , className: 'btn btn-danger'
                        }
                        , confirm: {
                            text: 'Sim, remover!'
                            , className: 'btn btn-success'
                        }
                    }
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: '{{url("/saida/removerTipoPagamento2")}}/' + ele.attr("value")
                            , method: "POST"
                            , data: {
                                _token: '{{ csrf_token() }}',
                                id: ele.attr("value"),
                                // tipo: "cancelamento_venda",
                            }
                            , dataType: 'JSON'
                            , success: function(response) {

                                if (response.success) {
                                    listarCarinhoTipoPagamento();
                                    $.notify("Removido com sucesso!", {
                                        icon: "success"
                                        , timer: 1000
                                        , });

                                } else {
                                    swal("Opps, ocorreu um erro!", {
                                        icon: "error"
                                        , buttons: false
                                        , timer: 1000
                                        , });
                                }
                            },
                            error: function(err) {
                                alert("Erro")
                                console.log(err)
                            }

                        });

                    } else {
                        swal.close();
                    }
                });


            });
        })
        function listarCarinhoTipoPagamento() {
            // showLoader();
            $.ajax({
                url: '{{url("/saida/listarTipoPagamento2")}}'
                , method: 'POST'
                , data: {
                    _token: '{{csrf_token()}}'
                }
                , dataType: 'html'
                , success: function(response) {
                    // alert('cart_s')
                    $('.table-tipoPagamento').html(response);
                },error: function(err) {
                    alert("erro2")
                    console.log(err)
                }

            }).always(function () {
                // hideLoader(); //loader
            });
        }

        function cleanPagamentoList() {
            $.ajax({
                url: '{{url("/saida/limparTipoPagamento")}}'
                , method: 'GET'
                , data: {
                    _token: '{{csrf_token()}}'
                }
                , dataType: 'html'
                , success: function(response) {
                    //
                },error: function(err) {
                    console.log(err)
                }
            })
        }

    </script>
@endsection
