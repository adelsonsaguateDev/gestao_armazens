<?php session_start();?>
<?php $_SESSION['html'] = null;?>
<?php $_SESSION['title'] = null; ?>
<?php $_SESSION['nome_utilizador'] = null; ?>

<?php $content=null; ?>

@if(count($utilizadores)>0)
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
                <th class="text-center" style="width: 30%;" >Nome</th>
                <th class="text-center" style="width: 10%;" >Função</th>
                <th class="text-center" style="width: 16.67%;" >E-mail</th>
                <th class="text-center" style="width: 16.67%;" >Contacto</th>
                <th class="text-center" style="width: 10%;" >Estado</th>
    @php
        $content .= ob_get_contents();
    @endphp
                <th class="col-2">Acções</th>
   @php
        ob_start();
   @endphp

            </tr>
        </thead>
        <tbody>
            @php
            $cont = 1;
            @endphp
            @foreach ($utilizadores as $item)
                <tr>
                    <th scope="row">{{ $cont++ }}</th>
                    <td class="text-center">{{ $item->name }}</td>
                    <td class="text-center">{{ $item->permissoes[0]->nome }}</td>
                    <td class="text-center">{{ $item->email }}</td>
                    <td class="text-center" >{{ $item->contacto ?? " Sem contacto" }}</td>
                    <td class="text-center"> @php
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
                    <td>
                        <a href="{{ route('utilizador.detalhes', ['id' => $item->id]) }}"  class="btn btn-primary"><i class="fa fa-eye text-white"></i> </a>

                        @if($item->estado == '1')
                        <button class="btn btn-warning" name="{{ $item->name }}" value="{{ $item->id }}" id="btn_edit" ><i class="fa fa-pencil text-white"></i> </button>
                        <button title="Remover a funcionario." class="btn btn-danger" value="{{ $item->id }}" id="btn_delete"><i class="fa fa-trash"></i> </button>
                        @endif

                        @if($item->estado == '2')
                        <button title="Activar o funcionario." class="btn btn-success" value="{{ $item->id }}" id="btn_active"><i class="fa fa-check"></i> </button>
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
            {{ $utilizadores->links() }}
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info" role="alert">
        <strong class="text-capitalize">Alerta!</strong>
        Nenhum funcionario encontrado.
    </div>
@endif



<?php $_SESSION['title'] = "Lista de utilizadores"; ?>
<?php   $_SESSION['html'] = $content; ?>
<?php   $_SESSION['nome_utilizador'] = session('nome_utilizador'); ?>




