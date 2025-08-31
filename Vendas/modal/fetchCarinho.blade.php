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


?>
@if (!empty($carinho))
<?php
$total = 0;
$total_iva = 0;
$total_sem_iva = 0;
?>

<div class="table-responsive">
    <table  class=" display table table-borderless table-hover">
        <thead class="">
            <tr>
                <th style="text-align: center">Código</th>
                <th style="text-align: center">Produto</th>
                <th style="text-align: center">Quantidade</th>
                <th style="text-align: center">P.Unitário</th>
                 <th style="text-align: center">Iva(16%)</th>
                <th style="text-align: center">Total</th>
                <th style="text-align: center">Acções</th>
            </tr>
        </thead>
        <tbody >
            @foreach ((array) $carinho as $item)
            <?php // print_r($item);

            $total += $item['custo'];
            $total_sem_iva += $item['custo'];
            $valor_iva = 0;
            if($item['valor_iva'])
                $valor_iva = $item['custo']-($item['custo']/$empresa->iva);//$item['custo']*0.16;
            $item['valor_iva']=$valor_iva;
            ?>
            <tr class="table-info">
                <td>{{$item['codigo_barras']}}</td>
                <td>{{$item['nome']}}</td>
                <td style="text-align: right">
                    <div class="text-center">
                        <div class="showLabel w-100">{{$item['quantidade']}}</div>
                        <input min="1" class="form-control showValue w-50 quantidade text-right pull-right"
                           id="{{ $item['id'] }}" codigo_barras="{{ $item['codigo_barras'] }}" type="number" value="{{$item['quantidade']}}"
                           style="display: none; font-size: 2em" p_uni="{{$item['preco_unitario']}}"
                            disp="{{$item['disponivel']}}">
                    </div>
                </td>
                <td style="text-align: right">
{{--                    {{number_format($item['preco_unitario'],2)}}--}}
                  <div class="showLabelPreco">{{number_format($item['preco_unitario'],2)}}</div>
                  <input name="preco_unitarioLabel" class="form-control showValueLabel text-right pull-right"
                  id="{{ $item['id'] }}" codigo_barras="{{ $item['codigo_barras'] }}" type="text" value="{{$item['preco_unitario']}}"
                           style="display: none; font-size: 2em" qnt="{{$item['quantidade']}}"
                            disp="{{$item['disponivel']}}">
                </td>
                <td style="text-align: right">{{number_format($item['valor_iva'],2) ?? '0.00'}}</td>
                @php
                    $total_iva +=$item['valor_iva'];
                @endphp
                <td style="text-align: right">{{number_format($item['custo'],2)}}</td>


                <td style="text-align: center">
                    <div class="form-button-action">
                        <a value="{{ $item['id'] }}" data-toggle="tooltip" title="Remover" class=" btn-link btn-danger remove-from-cart" data-original-title="Remove">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </td>
            </tr>

            @endforeach
            <tr class="table-warning" style="text-align: center;font-size:1.5rem">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><strong>Subtotal</strong></td>
                <td class="text-right"><strong>{{number_format($total_sem_iva,2)}}</strong></td>
                <td></td>
            </tr>

            <tr class="table-warning" style="text-align: center;font-size:1.5rem">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><strong>Total do IVA</strong></td>
                <td class="text-right"><strong>{{number_format($total_iva,2)}}</strong></td>
                <td></td>
            </tr>

            <tr class="table-warning" style="text-align: center;font-size:1.5rem">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><strong>Total</strong></td>
                <td class="text-right"><strong>{{number_format($total,2)}}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

{{-- <div class="row text-right">
    <div class="col-md-12">
        <button id="continuar" data-toggle="modal" data-target="#confirmarSaida" class="btn btn-primary mb-3">Continuar</button>
        <button class="btn btn-danger cancelar-cart mb-3">Cancelar</button>
    </div>
</div> --}}
<div class="row">
    <div class="col-md-12 d-flex">
        @if($empresa->pacote != 1)
        <button id="cotacao_salvar" data-toggle="modal" data-target="#confirmarCotacao" class="btn btn-success mb-3">Guardar rascunho</button>
        @endif
        <button id="continuar" data-toggle="modal" data-target="#confirmarSaida" class="btn btn-primary mb-3 ml-auto mr-2">Continuar</button>
        <button class="btn btn-danger cancelar-cart mb-3">Cancelar</button>
    </div>
</div>


{{-- <input type="hidden" name="custo" value="{{$total ?? '0'}}"> --}}
<div class="modal fade" id="confirmarSaida" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">Pagamento</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="custom-control custom-switch" {{!$forma_pagamento ? "":"hidden" }}>
                <input type="checkbox" class="custom-control-input" id="pagarParcial">
                <label class="custom-control-label" for="pagarParcial">Fazer o pagamento?</label>
            </div>
            <div class="row">

                <div class="form-group col col-md" hidden>
                    <label for="">Taxa</label>
                    <input step="0.01"  readonly type="number" name="total_taxa" value="{{$total_iva}}" id="total_taxa" class="pay form-control text-right">
                </div>

                <div class="form-group col col-md">
                    <label for="">Valor Entregue</label>
                    <input type="number" name="valor_entregue" id="valor_entregue" class="form-control valor_entregue">
                </div>

                <div class="form-group col col-md">
                    <label for="">Trocos</label>
                    <input step="0.01" readonly type="text" name="trocos" value="0" id="trocos" class="form-control text-right trocos">
                </div>

                <div class="form-group col col-md">
                    <label>Desconto</label>
                    <input step="0.01"  type="number" name="total_desconto" value="0" id="total_desconto" class="moeda pay form-control text-right">
                </div>
            </div>
            <div class="row">
                <div class="form-group col col-md">
                    <label for="">Subtotal</label>
                    <input step="0.01" readonly type="text" name="custo" value="{{$total ?? '0'}}" id="custo" class="pay form-control text-right">
                </div>
            <div class="form-group total_pagar col-md {{!$forma_pagamento ? "6":"12" }}">
                <label for="" style="text-align:center;">Total</label>
                <input  readonly type="text" value="{{$total ?? '0'}}" name="custo_total" id="custo_total" class="moeda pay text-danger text-right form-control">
            </div>
            <div class="form-group col col-md pagarParcial" hidden>
                <label for="" style="text-align:center;">Valor a Pagar</label>
                <input {{!$forma_pagamento ? "":"readonly" }} max="{{$total ?? 0}}" type="text" value="{{!$forma_pagamento ? 0:$total }}" name="valor_pago" id="valor_pago" required class="moeda pay text-danger text-right form-control">
            </div>
            </div>
            <div class="row metodoPagamento pagarParcial" {{!$forma_pagamento ? "hidden":"" }}>
            <div class="col col-md-6 div_form_pagamento">
                <label for="">Método de pagamento<span class="text-danger">*</span></label>
                <select name="forma_pagamento" id="forma_pagamento" class="moeda form-control custom-select form-select select2">
                <?php if($empresa->ver_pagamentos_venda==1){?>
                    <option value="">Selecione o tipo de pagamento...</option>
                    @foreach ($pagamentos as $item)
                        <option value="{{$item->id}}">{{$item->designacao}}</option>
                    @endforeach
                <?php }else{?>
                    @foreach ($pagamentos as $item)
                        <option {{!$forma_pagamento ? ($item->id == 1 ? "selected":"") :"" }} value="{{$item->id}}">{{$item->designacao}}</option>
                    @endforeach
                <?php } ?>
                </select>
            </div>
                <div class="col-md-6 div_valor">
                    <label for="">Valor da forma de pagamento<span class="text-danger">*</span></label>
                    <input type="number" name="valor" value="{{$total ?? '0'}}" id="valor" class="form-control">
                </div>

                <div class="col-md-12 campos_extras form-group">
                </div>

                <div class="col-md-12 mt-3 text-right">
                    <button type="button" id="addTipoPagamento" class="btn btn-success">
                        <i class="fa fa-shopping-cart"></i>
                    </button>
                </div>

            <div class="col-md-12 mt-3">
                <div class="table-tipoPagamento"></div>
            </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" id="closeModal" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            <button type="button" id="registar_venda" class="btn btn-primary">Registar</button>
        </div>
        </div>
    </div>
</div>

@else
<div class="alert alert-info text-center">Adicione produtos ao carinho !</div>
@endif

<script>
    $("#forma_pagamento").select2()
    //TIpo de pagamento scripts
    $(function (){
        listarCarinhoTipoPagamento();
    })
    //Aqui não pode adicionar nenhum script para evitar redondância de scripts..........
</script>
