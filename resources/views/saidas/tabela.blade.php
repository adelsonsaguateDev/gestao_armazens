<?php session_start();?>
<?php $_SESSION['html'] = null;?>
<?php $_SESSION['title'] = null; ?>
<?php $_SESSION['nome_utilizador'] = null; ?>
<?php $content=null; ?>

@if(count($saidas)>0)
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
                <th class="text-center col-2">Nº Factura</th>
                <th class="text-center col-2">Cliente</th>
                <th class="text-center col-1">Tipo Saída</th>
                <th class="text-center col-1">Data Saída</th>
                <th class="text-center col-1">Valor Total</th>
                <th class="text-center col-2">Estado</th>
    @php
        $content .= ob_get_contents();
    @endphp
                <th class="text-center col-2">Acções</th>
   @php
        ob_start();
   @endphp

            </tr>
        </thead>
        <tbody>
            @php
            $cont = 1;
            @endphp
            @foreach ($saidas as $item)
                <tr>
                    <th scope="row">{{ $cont++ }}</th>
                    <td class="text-center">{{ $item->numero_factura ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->cliente->nome ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->tipoSaida->nome ?? 'N/A' }}</td>
                    <td class="text-center">{{ $item->data }}</td>
                    <td class="text-center">{{ number_format($item->valor_total, 2, ',', '.') }}</td>
                    <td class="text-center">
                        @php
                        if($item->activo == '1') :
                        @endphp
                        <div class="badge bg-success text-white">Activo</div>
                        @php
                        elseif($item->activo == '0'):
                        @endphp
                        <div class="badge bg-danger text-white">Eliminado</div>
                        @php
                        elseif($item->activo == '2'):
                        @endphp
                        <div class="badge bg-warning text-white">...</div>
                        @php
                        elseif($item->activo == '3'):
                        @endphp
                        <div class="badge bg-info text-white">Devolvida</div>
                        @php
                        endif
                        @endphp
                    </td>
                    @php
                        $content .= ob_get_contents();
                    @endphp
                    <td class="text-center">
                        <a href="{{ route('saida.detalhes', ['id' => $item->id]) }}"  class="btn btn-primary"><i class="fa fa-eye text-white"></i> </a>

                        @if($item->activo == '1')
                        <button class="btn btn-warning" value="{{ $item->id }}"   data-toggle="modal" data-target="#edit_saida" id="btn_edit"><i class="fa fa-pencil text-white"></i> </button>
                        <button title="Remover a saida" class="btn btn-danger" value="{{ $item->id }}" id="btn_delete"><i class="fa fa-trash"></i> </button>
                        <button title="Imprimir Recibo" class="btn btn-info recibo" value="{{ $item->id }}"><i class="fa fa-print"></i> </button>
                        <input type="hidden" class="rota{{ $item->id }}" value="{{ route('saida.recibo', ['id' => $item->id]) }}">
                        @endif

                        @if($item->activo == '0')
                        <button title="Activar a saida" class="btn btn-success" value="{{ $item->id }}" id="btn_active"><i class="fa fa-check"></i> </button>
                        @endif
                    </td>
                    @php
                        ob_start();
                    @endphp

                </tr>
            @endforeach
        </tbody>
        <tfoot></tfoot>
    </table>
    @php
        $content .= ob_get_contents();
    @endphp
     <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">

            <div class="pagination justify-content-end">
            {{ $saidas->links() }}
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
