<?php session_start();?>
<?php $_SESSION['html'] = null;?>
<?php $_SESSION['title'] = null; ?>
<?php $_SESSION['nome_utilizador'] = null; ?>
<?php $content=null; ?>

@if(count($produtos)>0)
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
                <th style="width: 1%;">#</th>
                <th class="text-center col-5"  >Descricao</th>
                <th class="text-center col-1"  >Quantidade</th>
                <th class="text-center col-2"  >Data Criação</th>
                <th class="text-center col-1"  >Estado</th>
    @php
        $content .= ob_get_contents();
    @endphp
                <th class="col-3 text-center">Acções</th>
   @php
        ob_start();
   @endphp

            </tr>
        </thead>
        <tbody>
            @php
            $cont = 1;
            @endphp
            @foreach ($produtos as $item)
                <tr>
                    <th scope="row">{{ $cont++ }}</th>
                    <td class="text-center">{{ $item->descricao }}</td>
                    <td class="text-center">
                        @php
                        if($item->quantidade == 0) :
                       @endphp
                        <div class="badge bg-danger text-white">{{ $item->quantidade }}</div>
                       @php
                        elseif($item->quantidade >= 10 && $item->quantidade <= 50) :
                       @endphp
                        <div class="badge bg-warning text-white">{{ $item->quantidade }}</div>
                       @php
                        elseif($item->quantidade > 50) :
                       @endphp
                        <div class="badge bg-success text-white">{{ $item->quantidade }}</div>
                       @php
                        endif
                       @endphp

                    </td>
                    <td class="text-center" >{{ $item->created_at }}</td>
                    <td class="text-center">
                        @php
                        if($item->estado == '1') :
                        @endphp
                        <div class="badge bg-success text-white">Activo</div>
                        @php
                        elseif($item->estado == '2'):
                        @endphp
                        <div class="badge bg-danger text-white">Inactivo</div>
                        @php
                        endif
                        @endphp

                    </td>
    @php
        $content .= ob_get_contents();
    @endphp
                    <td class="text-center">
                        <a href="{{ route('detalhes', ['id' => $item->id]) }}"  class="btn btn-primary"><i class="fa fa-eye text-white"></i> </a>

                        @if($item->estado == '1')
                        <button class="btn btn-warning" nome="{{ $item->nome }}" stock_minimo="{{ $item->stock_minimo }}" quantidade="{{ $item->quantidade }}" descricao="{{ $item->descricao }}" value="{{ $item->id }}"   data-toggle="modal" data-target="#edit_produto" id="btn_edit"><i class="fa fa-pencil text-white"></i> </button>
                        <button title="Remover a produto" class="btn btn-danger" value="{{ $item->id }}" id="btn_delete"><i class="fa fa-trash"></i> </button>
                        @endif

                        @if($item->estado == '2')
                        <button title="Activar a produto" class="btn btn-success" value="{{ $item->id }}" id="btn_active"><i class="fa fa-check"></i> </button>
                        @endif

                        @if($item->estado == '1')
                        <button title="Solictar o produto" class="btn btn-success" descricao="{{ $item->descricao }}"  value="{{ $item->id }}" id="btn_requisitar"><i class="fa fa-book"></i> Requisitar </button>
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
            {{ $produtos->links() }}
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info" role="alert">
        <strong class="text-capitalize">Alerta!</strong>
        Nenhum produto encontrado.
    </div>
@endif



<?php $_SESSION['title'] = "Lista de produtos"; ?>
<?php   $_SESSION['html'] = $content; ?>
<?php   $_SESSION['nome_utilizador'] = session('nome_utilizador'); ?>




