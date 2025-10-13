<?php session_start();?>
<?php $_SESSION['html'] = null;?>
<?php $_SESSION['title'] = null; ?>
<?php $_SESSION['nome_utilizador'] = null; ?>
<?php $content=null; ?>

@if(count($pagamentos)>0)
    <div class="row">
        <div class="col-md-12">
            <h4><strong>Total:  </strong>{{ $total }}</h4>
        </div>
    </div>
    @php
        ob_start();
    @endphp

    <table class="display table table-hover" width="100%">
        <thead>
            <tr style="font-weight: bold; color:black">
                <th style="width: 1%">#</th>
                <th class="text-center ">Nº Pagamento</th>
                <th class="text-center ">Nº Factura</th>
                <th class="text-center">Cliente</th>
                <th class="text-center ">Data do Pagamento</th>
                <th class="text-center ">Forma de Pagamento</th>
                <th class="text-center ">Valor Pago</th>
                <th class="text-center">Utilizador</th>
    @php
        $content .= ob_get_contents();
    @endphp
                <th class="text-center">Acções</th>
   @php
        ob_start();
   @endphp

            </tr>
        </thead>
        <tbody>
            @php
            $cont = 1;
            $total = 0;
            @endphp
            @foreach ($pagamentos as $item)
            @php $total += $item->valor_total @endphp
              
                <tr>
                    <th scope="row">{{ $cont++ }}</th>
                    <td class="text-center">{{ $item->numero ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->numero_recibo ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->cliente->nome ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->data_pagamento ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->tipoPagamento->designacao ?? 'N/A' }}</td>
                    <td class="text-center">{{ number_format($item->valor_pago, 2, ',', '.') }}</td>
                    <td class="text-center">{{ $item->user->name }}</td>
                    @php
                        $content .= ob_get_contents();
                    @endphp
                    <td class="text-center">
                        <a href="{{ route('saida.detalhes', ['id' => $item->id]) }}"  class="btn btn-primary"><i class="fa fa-eye text-white"></i> </a>

                        {{-- @if($item->activo == '1') --}}
                        {{-- <button class="btn btn-warning" value="{{ $item->id }}"   data-toggle="modal" data-target="#edit_saida" id="btn_edit"><i class="fa fa-pencil text-white"></i> </button> --}}
                        <button title="Remover a pagamento" class="btn btn-danger" value="{{ $item->id }}" id="btn_delete"><i class="fa fa-trash"></i> </button>
                        <button title="Imprimir Recibo" class="btn btn-info recibo" value="{{ $item->saida_id }}"><i class="fa fa-print"></i> </button>
                        <input type="hidden" class="rota{{ $item->saida_id }}" value="{{ route('saida.recibo', ['id' => $item->saida_id]) }}">
                        {{-- @endif --}}

                        {{-- @if($item->activo == '0')
                        <button title="Activar a saida" class="btn btn-success" value="{{ $item->id }}" id="btn_active"><i class="fa fa-check"></i> </button>
                        @endif --}}
                    </td>
                    @php
                        ob_start();
                    @endphp

                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <td colspan="5"><b>Total</b></td>
            <td class="text-right"><b>{{number_format($total, 2, ',', '.')}}</b></td>
        </tfoot>
    </table>
    @php
        $content .= ob_get_contents();
    @endphp
     <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="pagination justify-content-end">
            {{ $pagamentos->links() }}
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info" role="alert">
        <strong class="text-capitalize">Alerta!</strong>
        Nenhuma saída encontrada.
    </div>
@endif



<?php $_SESSION['title'] = "Lista de Saídas"; ?>
<?php   $_SESSION['html'] = $content; ?>
<?php   $_SESSION['nome_utilizador'] = session('nome_utilizador'); ?>
