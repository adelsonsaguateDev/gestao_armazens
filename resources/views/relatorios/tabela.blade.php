<?php session_start();?>
<?php $_SESSION['html'] = null;?>
<?php $_SESSION['title'] = null; ?>
<?php $_SESSION['nome_utilizador'] = null; ?>
<?php $content=null; ?>

@if(count($requisicoes)>0)
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
                <th class="text-center" style="width: 30%;" >Produto</th>
                <th class="text-center" style="width: 10%;" >Quantidade</th>
                <th class="text-center" style="width: 10%;" >Funcionario</th>
                <th class="text-center" style="width: 10%;" >Data da requisição</th>
                <th class="text-center" style="width: 10%;" >Estado requisição</th>

    @php
        $content .= ob_get_contents();
    @endphp

   @php
        ob_start();
   @endphp

            </tr>
        </thead>
        <tbody>
            @php
            $cont = 1;
            @endphp
            @foreach ($requisicoes as $item)
                <tr>
                    <th scope="row">{{ $cont++ }}</th>
                    <td class="text-center">{{ $item->produtos->descricao }}</td>
                    <td class="text-center">{{ $item->quantidade }}</td>
                    <td class="text-center">{{ $item->users->name }}</td>
                    <td class="text-center" >{{ $item->created_at }}</td>
                    <td class="text-center" >

                        @php
                        if($item->estado_requisicao == '1') :
                        @endphp
                        <div class="badge bg-warning text-white">PENDENTE</div>
                        @php
                        elseif($item->estado_requisicao == '2'):
                        @endphp
                        <div class="badge bg-success text-white">APROVADA</div>
                        @php
                        elseif($item->estado_requisicao == '3'):
                        @endphp
                        <div class="badge bg-danger text-white">REPROVADA</div>
                        @php
                        endif
                        @endphp

                    </td>


    @php
        $content .= ob_get_contents();
    @endphp


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
            {{ $requisicoes->links() }}
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info" role="alert">
        <strong class="text-capitalize">Alerta!</strong>
        Nenhum relatório encontrado.
    </div>
@endif



<?php $_SESSION['title'] = "Lista de requisicoes"; ?>
<?php   $_SESSION['html'] = $content; ?>
<?php   $_SESSION['nome_utilizador'] = session('nome_utilizador'); ?>




