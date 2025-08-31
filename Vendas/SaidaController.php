<?php

namespace App\Http\Controllers\Saida;

use App\Models\Banco;
use App\Models\Cliente;
// use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ProdutoController;
use App\Models\Devolucao;
use App\Models\Empresa;
use App\Models\FormaPagamento;
use App\Models\LogsVenda;
use App\Models\Lote;
use App\Models\Pagamento;
use App\Models\Produto;
use App\Models\Notificar;
use App\Models\DiarioStock;
use App\Models\Saida;
use App\Models\SaidaItem;
use App\Models\TipoCliente;
use App\Models\TipoPagamento;
use App\Models\TipoSaida;
use App\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PDF;
use phpDocumentor\Reflection\Types\Null_;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Redis;
use App\Helpers\RedisHelper;

date_default_timezone_set('Africa/Maputo');


class SaidaController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->a   ll());
        $dataInicio = null;
        $dataFim = null;
        $cliente = null;
        $tipoSaida = null;
        $tipo_saida_id = $_POST['tipo_saida_id'];
        $estado = null;
        $estado_text = null;
        $numero_factura = null;
        $codigo_venda = null;
        $produto = null;
        $cont = 0;
        $desconto = null;

        $limite = $request->limite;

        $data = [];

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



        if (isset($_POST['cliente_id']) && !empty($_POST['cliente_id'])) {
            $cliente = $_POST['cliente_id'];
            $data[$cont++] = ['cliente_id', '=', $cliente];

            $cliente_1 = DB::selectOne("SELECT nome
            FROM `clientes`
            WHERE id={$cliente}");
            $cliente = $cliente_1->nome;
        }
        if (isset($_POST['desconto']) && !empty($_POST['desconto'])) {
            if($_POST['desconto']==1){
                $desconto = $_POST['desconto'];
                $data[$cont++] = ['s.desconto', '>', 0];
            }else{
                $data[$cont++] = ['s.desconto', '=', 0];
            }
        }
        if (isset($_POST['devolvida']) && !empty($_POST['devolvida'])) {
            $devolvida = $_POST['devolvida'];
            if($devolvida=='sim'){
                $data[$cont++] = ['s.activo', '=', '3'];
            }else{
                $data[$cont++] = ['s.activo', '!=', '3'];
            }
        }
        if (isset($_POST['tipo_saida_id']) && !empty($_POST['tipo_saida_id'])) {
            $tipo = $_POST['tipo_saida_id'];
            $tipoSaida_1 = DB::selectOne("SELECT descricao
            FROM `tipo_saidas`
            WHERE id={$tipo}");
            $tipoSaida = $tipoSaida_1->descricao;

            $data[$cont++] = ['tipo_saida_id', '=', $tipo];
        }

//        if (isset($_POST['estado']) && !empty($_POST['estado'])) {
//            $estado = $_POST['estado'];
//            $estado_text = @$_POST['estado_text'];
//            $data[$cont++] = ['s.activo', '=', $estado];
//        } else {
//
//            $data[$cont++] = ['s.activo', '=', '1'];
//        }

        if (isset($_POST['codigo_venda']) && !empty($_POST['codigo_venda'])) {
            $codigo_venda = $_POST['codigo_venda'];
            $numero_factura = $codigo_venda;
            $data[$cont++] = ['s.numero_factura', 'like', "%$codigo_venda%"];
        }

        if (isset($_POST['produto']) && !empty($_POST['produto'])) {
            $produto = $_POST['produto'];
            // $numero_factura = $codigo_venda;
            $data[$cont++] = ['saida_items.produto_id', '=', $produto];
        }

        if (isset($_POST['data2']) && !empty($_POST['data2']) && !empty($_POST['data'])) {
            $date2 = date($_POST['data2']);
            $date = date($_POST['data']);

            $dataInicio = $_POST['data'];
            $dataFim = $_POST['data2'];

            $data[$cont++] = ['data', '>=', "$date%"];
            $data[$cont++] = ['data', '<=', "$date2"];
        }

        if (isset($_POST['data']) && !empty($_POST['data']) && empty($date2)) {
            $date = date($_POST['data']);
            $data[$cont++] = ['data', 'like', "$date%"];
        }
        if((auth()->user()->permissoes()->pluck('nome')->toArray()[0]) == 'vendedor')
            $data[$cont++] = ['s.user_id', '=', auth()->user()->id];

        if($request->limite == ""){

            if($empresa->fonte == "1"){
            $saidas = DB::table('saidas as s')
                ->select(array('s.*', DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente',
                    'tipo_saidas.descricao as tipoSaida','users.name as user',
                    DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id) AS qntProdutos"),
                    DB::raw('CASE WHEN iva>0 THEN SUM((saida_items.quantidade*saida_items.preco_unitario)-(saida_items.quantidade*saida_items.preco_unitario)/'.$empresa->iva.') ELSE 0 END AS total_iva'),
                    DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'),
                    DB::raw('(SUM(saida_items.quantidade*saida_items.preco_unitario)) AS total_venda'),
                ))
                ->where($data)
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->leftJoin('users', 's.user_id', '=', 'users.id')
                ->groupBy('s.id')
                ->orderBy('created_at', 'desc')
                ->paginate(999999999999);
            }else{
                $saidas = DB::table('saidas as s')
                ->select(array('s.*', DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente',
                    'tipo_saidas.descricao as tipoSaida','users.name as user',
                    DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id) AS qntProdutos"),
                    DB::raw('CASE WHEN iva>0 THEN SUM((saida_items.quantidade*saida_items.preco_unitario)-(saida_items.quantidade*saida_items.preco_unitario)/'.$empresa->iva.') ELSE 0 END AS total_iva'),
                    DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'),
                    DB::raw('(SUM(saida_items.quantidade*saida_items.preco_unitario) - saida_items.desconto_valor) AS total_venda'),
                ))
                ->where($data)
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->leftJoin('users', 's.user_id', '=', 'users.id')
                ->groupBy('s.id')
                ->orderBy('created_at', 'desc')
                ->paginate(999999999999);
            }
        }else {
            if($empresa->fonte == "1"){

            $saidas = DB::table('saidas as s')
                ->select(array('s.*', DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente',
                    'tipo_saidas.descricao as tipoSaida','users.name as user',
                    DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id) AS qntProdutos"),
                    DB::raw('CASE WHEN iva>0 THEN SUM((saida_items.quantidade*saida_items.preco_unitario)-(saida_items.quantidade*saida_items.preco_unitario)/'.$empresa->iva.') ELSE 0 END AS total_iva'),
                    DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'),
                    DB::raw('(SUM(saida_items.quantidade*saida_items.preco_unitario)) AS total_venda'),
                ))
                ->where($data)
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->leftJoin('users', 's.user_id', '=', 'users.id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->groupBy('s.id')
                ->orderBy('created_at', 'desc')
                ->paginate((int)$request->limite);
            }else{
                $saidas = DB::table('saidas as s')
                ->select(array('s.*', DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente',
                    'tipo_saidas.descricao as tipoSaida','users.name as user',
                    DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id) AS qntProdutos"),
                    DB::raw('CASE WHEN iva>0 THEN SUM((saida_items.quantidade*saida_items.preco_unitario)-(saida_items.quantidade*saida_items.preco_unitario)/'.$empresa->iva.') ELSE 0 END AS total_iva'),
                    DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'),
                    DB::raw('(SUM(saida_items.quantidade*saida_items.preco_unitario) - saida_items.desconto_valor) AS total_venda'),
                ))
                ->where($data)
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->leftJoin('users', 's.user_id', '=', 'users.id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->groupBy('s.id')
                ->orderBy('created_at', 'desc')
                ->paginate((int)$request->limite);
            }
        }
        return view('saida.fetchSaida', compact('saidas', 'limite','dataInicio','dataFim', 'cliente', 'tipoSaida', 'tipo_saida_id', 'estado', 'numero_factura'));
    }

    public function indexCotacao(Request $request)
    {

        $dataInicio = null;
        $dataFim = null;
        $cliente = null;
        $tipoSaida = null;
        $estado = null;
        $estado_text = null;
        $numero_factura = null;
        $codigo_venda = null;
        $produto = null;
        $cont = 0;

        $data = [];

        if (isset($_POST['cliente_id']) && !empty($_POST['cliente_id'])) {
            $cliente = $_POST['cliente_id'];
            $data[$cont++] = ['c.cliente_id', '=', $cliente];

            $cliente_1 = DB::selectOne("SELECT nome
            FROM `clientes`
            WHERE id={$cliente}");
            $cliente = $cliente_1->nome;
        }
       if (isset($_POST['tipo_saida_id']) && !empty($_POST['tipo_saida_id'])) {
            $tipo = 5;
            $tipoSaida_1 = DB::selectOne("SELECT descricao
            FROM `tipo_saidas`
            WHERE id={$tipo}");
            $tipoSaida = $tipoSaida_1->descricao;

            $data[$cont++] = ['c.tipo_saida_id', '!=', ""];
       }else{
            $data[$cont++] = ['c.tipo_saida_id', '!=', ""];
       }

        if (isset($_POST['estado']) && !empty($_POST['estado'])) {
            $estado = $_POST['estado'];
            $estado_text = @$_POST['estado_text'];
            $data[$cont++] = ['c.activo', '=', $estado];
        } else {

            $data[$cont++] = ['c.activo', '=', '1'];
        }

        if (isset($_POST['codigo_venda']) && !empty($_POST['codigo_venda'])) {
            $codigo_venda = $_POST['codigo_venda'];
            $numero_factura = $codigo_venda;
            $data[$cont++] = ['c.numero_factura', 'like', "%$codigo_venda%"];
        }

        if (isset($_POST['tipo_cotacao']) && !empty($_POST['tipo_cotacao'])) {
            $tipo_cotacao = $_POST['tipo_cotacao'];
            $data[$cont++] = ['c.tipo_cotacao', '=', "$tipo_cotacao"];
        }

        if (isset($_POST['produto']) && !empty($_POST['produto'])) {
            $produto = $_POST['produto'];
            $data[$cont++] = ['cotacao_items.produto_id', '=', $produto];
        }

        if (isset($_POST['data2']) && !empty($_POST['data2']) && !empty($_POST['data'])) {
            $date2 = date($_POST['data2']);
            $date = date($_POST['data']);

            $dataInicio = $_POST['data'];
            $dataFim = $_POST['data2'];

            $data[$cont++] = ['c.data', '>=', "$date%"];
            $data[$cont++] = ['c.data', '<=', "$date2"];
        }

        if (isset($_POST['data']) && !empty($_POST['data']) && empty($date2)) {
            $date = date($_POST['data']);
            $data[$cont++] = ['c.data', 'like', "$date%"];
        }


        $limite = $request->limite;


        // if($limite == "") {
        //     $saidas = DB::table('saidas as s')
        //         ->select(array('s.*', DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente',
        //             'tipo_saidas.descricao as tipoSaida',
        //             DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id AND saida_items.activo != '2') AS qntProdutos"),
        //             DB::raw('SUM(devolucao.quantidade) AS qntDevolucao'),
        //             DB::raw('SUM(devolucao.total) AS totalDevolucao'),
        //         ))
        //         ->where($data)
        //         ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
        //         ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
        //         ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        //         ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
        //         ->leftJoin('devolucao', 'saida_items.id', '=', 'devolucao.saida_item_id')
        //         ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
        //         ->groupBy('s.id')
        //         ->orderBy('created_at', 'desc')
        //         ->paginate(999999999999);
        // }else{
        //     $saidas = DB::table('saidas as s')
        //         ->select(array('s.*', DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente',
        //             'tipo_saidas.descricao as tipoSaida',
        //             DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id AND saida_items.activo != '2') AS qntProdutos"),
        //             DB::raw('SUM(devolucao.quantidade) AS qntDevolucao'), DB::raw('SUM(devolucao.total) AS totalDevolucao'),
        //         ))
        //         ->where($data)
        //         ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
        //         ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
        //         ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        //         ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
        //         ->leftJoin('devolucao', 'saida_items.id', '=', 'devolucao.saida_item_id')
        //         ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
        //         ->groupBy('s.id')
        //         ->orderBy('created_at', 'desc')
        //         ->paginate((int)$request->limite);
        // }


        $saidas=DB::table('cotacao as c')
        ->select([
            'c.*',
            DB::raw('YEAR(c.data) anoVenda'),
            'clientes.nome as cliente',
            'tipo_saidas.descricao as tipoSaida',
            DB::raw("(SELECT COUNT(produto_id) FROM cotacao_items WHERE saida_id = c.id AND cotacao_items.activo != '2') AS qntProdutos")
        ])
        ->where($data)
        ->leftJoin('clientes', 'c.cliente_id', '=', 'clientes.id')
        ->join('tipo_saidas', 'c.tipo_saida_id', '=', 'tipo_saidas.id')
        ->leftJoin('tipo_pagamentos', 'c.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        ->join('cotacao_items', 'c.id', '=', 'cotacao_items.saida_id')
        ->join('produtos', 'cotacao_items.produto_id', '=', 'produtos.id')
        ->groupBy('c.id')
        ->orderBy('c.created_at', 'desc')
        ->paginate($limite ? (int)$request->limite : 999999999999);



        return view('saida.fetchCotacao', compact('saidas', 'limite','dataInicio','dataFim', 'cliente', 'tipoSaida', 'estado', 'numero_factura'));
    }

    public function paymentInfo(Request $request)
    {
        $saida_id = $request->id;
        $saidas = DB::table('saidas as s')
        ->select(array('s.*',DB::raw('YEAR(s.data) anoVenda'),'clientes.nome as cliente',
            'tipo_saidas.descricao as tipoSaida',
            DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id AND saida_items.activo != '2') AS qntProdutos"),
            DB::raw('SUM(devolucao.quantidade) AS qntDevolucao'),DB::raw('SUM(devolucao.total) AS totalDevolucao'),
        ))
        ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
        ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
        ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
        ->leftJoin('devolucao', 'saida_items.id', '=', 'devolucao.saida_item_id')
        ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
        ->where('s.id','=',$saida_id)
        ->where('s.activo','=','1')
        ->where('saida_items.activo','=','1')
        ->groupBy('s.id')
        ->orderBy('created_at', 'desc')
        ->first();
        return response()->json($saidas);
    }
    public function paymentAllInfo(Request $request)
    {
        $cliente_id = $request->cliente_id;
        $data = $request->date;
        $data2 = $request->date2;

        $sqlAdd = null;
        $saidas = array();

        if(!empty($request->date) && !empty($request->date2)){
            $dataInicio = $request->date;
            $dataFim = $request->date2;
            $sqlAdd .= " AND data BETWEEN '{$dataInicio}' AND '{$dataFim}' ";
        }
        if(!empty($request->cliente_id)){
            $cliente_id = $request->cliente_id;
            $sqlAdd .= " AND saidas.cliente_id = {$cliente_id} ";
        }

        $divida = DB::selectOne("
            SELECT saidas.cliente_id as cliente_id, clientes.nome, SUM(saidas.valor_remanescente) valor_remanescente,
            COUNT(saidas.id) AS movimentos
            FROM saidas
            INNER JOIN clientes ON clientes.id=saidas.cliente_id
            WHERE 1=1 AND saidas.activo='1' $sqlAdd
            GROUP BY saidas.cliente_id
        ");

        $devolucao = DB::selectOne('SELECT SUM(devolucao.preco_unitario*devolucao.quantidade) AS total_devolucao
        FROM devolucao
        INNER JOIN saida_items ON saida_items.id=devolucao.saida_item_id
        INNER JOIN saidas ON saida_items.saida_id=saidas.id
        WHERE 1=1 '.$sqlAdd);

        $saidas['valor_remanescente'] = (double)$divida->valor_remanescente-(double)$devolucao->total_devolucao;

        // $saidas = DB::table('saidas as s')
        // ->select(array('s.cliente_id',DB::raw('YEAR(s.data) anoVenda'),'clientes.nome as cliente',
        //     'tipo_saidas.descricao as tipoSaida',DB::raw("SUM(s.valor_remanescente) AS valor_remanescente"),
        //     DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id AND saida_items.activo != '2') AS qntProdutos"),
        //     DB::raw('SUM(devolucao.quantidade) AS qntDevolucao'),DB::raw('SUM(devolucao.total) AS totalDevolucao'),
        // ))
        // ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
        // ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
        // ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        // ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
        // ->leftJoin('devolucao', 'saida_items.id', '=', 'devolucao.saida_item_id')
        // ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
        // ->where('s.cliente_id','=',$cliente_id)
        // ->where('s.activo','=','1')
        // ->where('saida_items.activo','=','1')
        // ->whereBetween('s.data',[$data, $data2])
        // ->groupBy('s.cliente_id')
        // ->first();



        return response()->json($saidas);
    }

    public function index2(Request $request)
    {
        $cont = 0;

        $data = [];

        if (isset($_POST['cliente_id']) && !empty($_POST['cliente_id'])) {
            $cliente = $_POST['cliente_id'];
            $data[$cont++] = ['cliente_id', '=', $cliente];
        }
        // if (isset($_POST['tipo_saida_id']) && !empty($_POST['tipo_saida_id'])) {
        //     $tipo = $_POST['tipo_saida_id'];
        $data[$cont++] = ['saida_items.activo', '!=', '0'];


            $data[$cont++] = ['tipo_saida_id', '=', 2];
//            $data[$cont++] = ['saida_items.tipo_motivo', '=', 'outro'];
        // }
        $estado_text = null;
        $dataInicio = null;
        $dataFim = null;

        if (isset($_POST['estado']) && !empty($_POST['estado'])) {
            $estado = $_POST['estado'];
            $estado_text = $_POST['estado_text'];
            $data[$cont++] = ['s.activo', '=', $estado];
        } else {

            $data[$cont++] = ['s.activo', '=', '1'];
        }

        if (isset($_POST['data2']) && !empty($_POST['data2']) && isset($_POST['data']) && !empty($_POST['data'])) {
            $date2 = date($_POST['data2']);
            $date = date($_POST['data']);

            $data[$cont++] = ['data', '>=', "$date%"];
            $data[$cont++] = ['data', '<=', "$date2"];

            $dataInicio = $_POST['data'];
            $dataFim = $_POST['data2'];
        }

        if (isset($_POST['data']) && !empty($_POST['data']) && empty($date2)) {
            $date = date($_POST['data']);
            $data[$cont++] = ['data', 'like', "$date%"];
        }

        $limite = $request->limite;


        if($limite == "") {
            $saidas = DB::table('saidas as s')
                ->select(array('s.*',
                    DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente', 'tipo_saidas.descricao as tipoSaida',
                    DB::raw('(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id) AS qntProdutos'),
                    DB::raw('SUM(preco_compra * quantidade) AS total'),
                    DB::raw('EXTRACT(YEAR_MONTH FROM s.created_at) AS yearMonth'),
                    "tipo_motivo"))
                ->where($data)
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->groupBy('s.id')
                ->orderBy('created_at', 'desc')
                ->paginate(999999999999);
        }else{
            $saidas = DB::table('saidas as s')
                ->select(array('s.*',
                    DB::raw('YEAR(s.data) anoVenda'), 'clientes.nome as cliente', 'tipo_saidas.descricao as tipoSaida',
                    DB::raw('(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id) AS qntProdutos'),
                    DB::raw('SUM(preco_compra * quantidade) AS total'),
                    DB::raw('EXTRACT(YEAR_MONTH FROM s.created_at) AS yearMonth'),
                    "tipo_motivo"))
                ->where($data)
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->groupBy('s.id')
                ->orderBy('created_at', 'desc')
                ->paginate((int)$request->limite);
        }
        return view('saida.fetchAbate', compact('saidas', 'limite','dataInicio', 'dataFim', 'estado_text'));
    }


    public function params()
    {

        $clientes = Cliente::where('activo', '=', '1')->get();
        $tipo_saidas = TipoSaida::all();
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();
        $produto = Produto::all();
        return view('saida.index', compact('clientes', 'produto', 'tipo_saidas','bancos', 'tipo_pagamentos'));
    }

    public function paramsCotacao()
    {

        $clientes = Cliente::where('activo', '=', '1')->get();
        $tipo_saidas = TipoSaida::all();
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();
        $produto = Produto::all();
        return view('saida.indexCotacao', compact('clientes', 'produto', 'tipo_saidas','bancos', 'tipo_pagamentos'));
    }

    public function indexAbate()
    {

        $clientes = Cliente::where('activo', '=', '1')->get();
        $tipo_saidas = TipoSaida::all();
        return view('saida.indexAbate', compact('clientes', 'tipo_saidas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        session()->forget('carinhoTipoPagamento'); //Reset do carrinho

        $produtos = DB::select("SELECT produtos.id,produtos.descricao, produtos.nome,
            produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
            SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        WHERE lt.activo != '0'
        GROUP BY produtos.id");

        // $produtos = DB::select("SELECT produtos.id, produtos.descricao, produtos.nome,
        // produtos.nome_generico, produtos.tipo_produto_id, produtos.codigo
        // FROM produtos");

        $clientes =  Cliente::where('activo', '=', '1')->get();
        $tipoClientes =  TipoCliente::all();

        // $notificar = $this->notificar();

        return view('saida.create', compact('produtos', 'clientes','tipoClientes'));
    }
    public function create_credito()
    {
        session()->forget('carinhoTipoPagamento2'); //Reset do carrinho

        $produtos = DB::select("SELECT produtos.id,produtos.descricao, produtos.nome,
            produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
            SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        GROUP BY produtos.id");
        $clientes =  Cliente::where('activo', '=', '1')->get();
        $tipoClientes =  TipoCliente::where('id', '!=', '1')->get();
        // $notificar = $this->notificar();

        return view('saida.create_credito', compact('produtos', 'clientes','tipoClientes'));
    }

    public  function clientesPorTipo(Request $request){
//        dd($request);
        $tipo_cliente_id = $request->tipo_cliente_id;
        if(empty($tipo_cliente_id))
            $sql = " AND tipo_cliente_id != {$tipo_cliente_id}";
        else
            $sql = " AND tipo_cliente_id = {$tipo_cliente_id}";

        $clientes = DB::select("SELECT * FROM clientes WHERE activo='1' {$sql}");
        return view('saida.cliente_select', compact('clientes'));
    }

    public function createAbate()
    {

        $i = 0;
        $lista = array();

        $loteController = new LoteController();

        // $produtos = DB::select("SELECT produtos.id,produtos.descricao,produtos.nome,
        //     produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
        //     SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        // FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        // WHERE lt.activo='1'
        // GROUP BY produtos.id");

        $produtos = DB::select("SELECT produtos.id,produtos.descricao, produtos.nome,
        produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
        SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        WHERE lt.activo != '0'
        GROUP BY produtos.id");

        // produtos com quantidade maior que 0

        // $produtos = DB::select("SELECT produtos.id,produtos.descricao,produtos.nome,
        //     produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
        //     SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        // FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        // WHERE (SELECT SUM(qnt_disponivel) FROM lotes WHERE produto_id=lt.produto_id
        //     AND activo='1')>0 AND lt.activo='1'
        // GROUP BY produtos.id");


        // $produtos_fora_prazo = DB::select("SELECT produtos.id,produtos.descricao,produtos.nome,
        // produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
        // SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        // FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        // WHERE (SELECT SUM(qnt_disponivel) FROM lotes WHERE produto_id=lt.produto_id
        // AND activo='1')>0 AND lt.activo='1' AND DATEDIFF(data_validade,CURDATE())<=5
        // GROUP BY produtos.id");

        $clientes =  Cliente::where([
            ['activo', '=', '1'],
            ['tipo_cliente_id','=','3']
        ])->get();
        $tipoClientes =  TipoCliente::all();

        $ultima_venda = Saida::latest()->first();
        $numero_factura = @$ultima_venda->id+1 ."/".date("Y");
        return view('saida.abate', compact('produtos', 'clientes', 'numero_factura', 'tipoClientes'));
    }

    public function create_cotacao()
    {
        $produtos = DB::select("SELECT produtos.id,produtos.descricao, produtos.nome,
            produtos.nome_generico,produtos.tipo_produto_id, produtos.codigo,
            SUM(lt.qnt_disponivel) qnt_actual, MAX(lt.preco_venda) preco_actual
        FROM produtos INNER JOIN lotes lt ON lt.produto_id=produtos.id
        WHERE lt.activo != '0'
        GROUP BY produtos.id");

        // $produtos = DB::select("SELECT produtos.id, produtos.descricao, produtos.nome,
        // produtos.nome_generico, produtos.tipo_produto_id, produtos.codigo
        // FROM produtos");

        $clientes =  Cliente::where('activo', '=', '1')->get();
        $tipoClientes =  TipoCliente::all();

        // $notificar = $this->notificar();

        return view('saida.create_cotacao', compact('produtos', 'clientes','tipoClientes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $produtoController = new ProdutoController();
        $loteModel = new LoteController();

        $cart = session()->get('carinho');
        $valor_total = 0;
        $valor_total_iva = 0;
        $dataSaida = array();
        $carinho = array();
        $lotes = array();
        $json['success'] = false;
        $json['message'] = "Erro no registo de vendas!";
        $cont = 0;
        $tipo_venda=null;
        $estado_pagamento = 'pago';
        $tipo_saida_id = 1;
        $percentagem = $request->percentagem;


        $cliente_id =$request->cliente;
        $desconto = (double) $request->desconto;
        $valorEntregue = $request->valor_entregue;
        $trocos = (float) $request->trocos;
        $valor_pago = $request->valor_pago;

        $cotacao = null;
        $cotacao_id = null;

        $cotacao = $request->cotacao ?? null;
        $cotacao_id = $request->cotacao_id ?? null;


        $referencia = Null;
        if (!empty($request->referencia)) {
            $referencia = $request->referencia;
        }

        $numero = NULL;
        if (!empty($request->numero)) {
            $numero = $request->numero;
        }
        $tipo_pagamento =$request->tipo_pagamento;
        $tipo_pa = array();


        $email = NULL;
        if (!empty($request->email)) {
            $email = $request->email;
        }

        if(isset($request->tipo_venda)) {
            $tipo_venda = $request->tipo_venda;
            if($tipo_venda == 'credito') {
                $tipo_saida_id = 3;
                $estado_pagamento = 'nao_pago';
            }
        }

        try{
            $saida = null;
            if (isset($cart)) {

                if (count($cart) > 0) {

                    foreach ($cart as $item) {
                        $valor_total += $item['custo'];
                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                    }

                    if(($valor_total - $desconto) == $valor_pago){
                        $estado_pagamento = 'pago';
                    }

                    DB::beginTransaction();

                    $ano = date("Y");
                    $tipo = 'venda_dinheiro';
                    $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
                    $numero_factura = "VD {$numeracao}/{$ano}";
                    $remanescente = ($valor_total - $desconto) - $valor_pago;
                    if(empty($valorEntregue))
                    {
                        $valorEntregue = $valor_total - $desconto;
                    }

                    $dataSaida = [
                        'cliente_id' => $cliente_id,
                        'data' => date('y-m-d'),
                        'tipo_saida_id' => $tipo_saida_id,
                        'desconto' => $desconto,
                        'valor_entregue' => $valorEntregue,
                        'trocos' => $trocos,
                        'tipo_pagamento_id' => 1,
                        'valor_total' => ($valor_total - $desconto),
                        'valor_pago' => $valor_pago,
                        'valor_remanescente' => $remanescente,
                        'valor_total_iva' => $valor_total_iva,
                        'numero' => $numero,
                        'numero_factura' => $numero_factura,
                        'slip' => "$referencia",
                        'email' => "$email",
                        'activo' => 1,
                        'estado_pagamento' => $estado_pagamento,
                        'percentagem' => $percentagem,
                        'user_id' => Auth::user()->id,
                        'sessao_id' => $empresa->sessao_id ?? NULL,
                    ];

                    @session_start();
                    if(!empty($_SESSION['saida_id'])){
                        $saida_id = $_SESSION['saida_id'];
                        DB::table('saidas')->where('id',$saida_id)->update(['activo'=>'2']);
                        $numero_factura = DB::selectOne("SELECT numero_factura FROM saidas WHERE saidas.id={$saida_id}");
                        $dataSaida['numero_cotacao'] = $numero_factura->numero_factura;
                        $_SESSION['cliente_id'] = "";
                        $_SESSION['saida_id'] = "";
                    }

                    $carinhoTipoPagamento = session()->get('carinhoTipoPagamento');
                    if(!empty($carinhoTipoPagamento)) {
                        if ($saida = DB::table('saidas')->insertGetId($dataSaida)) {

                            $this->updateInvoiceNumber($ano, $tipo);

                            foreach ($carinhoTipoPagamento as $item) {
                                $tipo_pa[] = [
                                    'saida_id' => $saida,
                                    'tipo_pagamento_id' => $item['tipo_pagamento_id'],
                                    'valor' => $item['valor'],
                                    'numero' => $item['numero'],
                                    'referencia' => $item['referencia'],
                                    //'email' => $item['email'],
                                    'activo' => '1'
                                ];
                            }
                            DB::table('saida_tipo_pagamentos')->insert($tipo_pa);
                        }
                    }else{
                        $json['success'] = false;
                        $json['message'] = 'Por favor preencha os dados de Método de pagamento e valor do mesmo!.';
                    }

                    //carrinho
                    $cesto = "";
                    $qtdSaidaItems = 0;
                    // dd($cart);
                    if($empresa->pacote==1){
                        foreach ($cart as $item) {
                            if ($item['quantidade'] != 0) {
                                $carinho =
                                    [
                                        'saida_id' => $saida,
                                        'produto_id' => $item['produto_id'],
                                        'quantidade' => $item['quantidade'],
                                        'preco_unitario' => $item['preco_unitario'],
                                        'preco_compra' => 0,
                                        'custo' => $item['custo'],
                                        'iva' => (float) $item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];

                                    $valor_total += $item['custo'];
                                    $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;

                                    $user_id = auth()->user()->id;

                                    if(DB::table('saida_items')->insert($carinho)){
                                        $qtdSaidaItems++;
                                    }
                            }
                        }
                    }else{
                        if($usar_lotes == '1'){
                            foreach ($cart as $item) {
                                // dd($item['quantidade']);
                                $item['custo'] = (int)$item['quantidade'] * (double)$item['preco_unitario'];

                                $carinho =
                                    [
                                        'saida_id' => $saida,
                                        'produto_id' => $item['produto_id'],
                                        'lote_id' => $item['id'],
                                        'quantidade' => $item['quantidade'],
                                        'preco_unitario' => $item['preco_unitario'],
                                        'preco_compra' => $item['preco_compra'],
                                        'custo' => $item['custo'],
                                        'iva' => (float) $item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];
                                $valor_total += $item['custo'];
                                $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;

                                $loteModel->abater($item['id'], $item['quantidade']);

                                // $quantidade_actual = $loteModel->qnt_disponivel_produto($lote->produto_id);
                                $user_id = auth()->user()->id;
                                // $qntBalancoAnterior = $produtoController->productBalanceStock($lote->produto_id);


                                if(DB::table('saida_items')->insert($carinho)){
                                    $qtdSaidaItems++;
                                    // $produtoController->checkProductBalance($lote->produto_id, 'venda_dinheiro', $qntLote, $quantidade_actual,$qntBalancoAnterior, $user_id);
                                }
                            }
                        }else{
                            foreach ($cart as $item) {
                                $lotes = $loteModel->retornaLoteByProdutoVenda($item['id']);
                                if (count($lotes) > 1) {

                                    $menorData = strtotime($lotes[0]->data_aquisicao);


                                    $controlaQnt = 0;
                                    $ultimo_lote = 0;
                                    $contagem=1;
                                    foreach ($lotes as $lote) {

                                        $dataAquisicao = strtotime($lote->data_aquisicao);

                                        if ($menorData > $dataAquisicao) {
                                            $menorData = $dataAquisicao;
                                        }

                                        if ($item['quantidade'] != 0) {

                                            if ($lote->qnt_disponivel > 0 || $contagem == count($lotes)) {
                                                if ((($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel )) && $lote->qnt_disponivel > 0 && $contagem != count($lotes)) {
                                                    $controlaQnt = (double) ($qntItem - $qntLote);

                                                    if ($controlaQnt < 0) {
                                                        $controlaQnt = (double) ($controlaQnt * (-1));
                                                    }

                                                    $item['quantidade'] -= $qntLote;

                                                    $item['custo'] = $qntLote * $item['preco_unitario'];

                                                    $carinho =
                                                        [
                                                            'saida_id' => $saida,
                                                            'produto_id' => $lote->produto_id,
                                                            'lote_id' => $lote->id,
                                                            'quantidade' => $qntLote,
                                                            'preco_unitario' => $item['preco_unitario'],
                                                            'preco_compra' => $lote->preco_compra,
                                                            'custo' => $item['custo'],
                                                            'iva' => (float) $item['taxa'],
                                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                        ];
                                                    $valor_total += $item['custo'];
                                                    $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                                    $loteModel->abater($lote->id, $qntLote);

                                                    $quantidade_actual = $loteModel->qnt_disponivel_produto($lote->produto_id);
                                                    $user_id = auth()->user()->id;
                                                    $qntBalancoAnterior = $produtoController->productBalanceStock($lote->produto_id);


                                                    if(DB::table('saida_items')->insert($carinho)){
                                                        $qtdSaidaItems++;
                                                        $produtoController->checkProductBalance($lote->produto_id, 'venda_dinheiro', $qntLote, $quantidade_actual,$qntBalancoAnterior, $user_id);
                                                    }
                                                } else {

                                                    $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                                    $carinho =
                                                        [
                                                            'saida_id' => $saida,
                                                            'produto_id' => $lote->produto_id,
                                                            'lote_id' => $lote->id,
                                                            'quantidade' => $item['quantidade'],
                                                            'preco_unitario' => $item['preco_unitario'],
                                                            'preco_compra' => $lote->preco_compra,
                                                            'custo' => $item['custo'],
                                                            'iva' => (float) $item['taxa'],
                                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                        ];

                                                    $item['quantidade'] -= $item['quantidade'];
                                                    $valor_total += $item['custo'];
                                                    $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                                    $loteModel->abater($lote->id, $qntItem);

                                                    $quantidade_actual = $loteModel->qnt_disponivel_produto($lote->produto_id);
                                                    $user_id = auth()->user()->id;
                                                    $qntBalancoAnterior = $produtoController->productBalanceStock($lote->produto_id);

                                                    if(DB::table('saida_items')->insert($carinho)){
                                                        $qtdSaidaItems++;
                                                        $produtoController->checkProductBalance($lote->produto_id, 'venda_dinheiro', $item['quantidade'], $quantidade_actual,$qntBalancoAnterior, $user_id);
                                                    }
                                                }
                                            }
                                        }
                                        $contagem++;
                                    }
                                } else {
                                    if(count($lotes)==0){
                                        $lotes = $loteModel->retornaUltimoLote($item['id']);
                                        $carinho =
                                        [
                                            'saida_id' => $saida,
                                            'produto_id' => $lotes->produto_id,
                                            'lote_id' => $lotes->id,
                                            'quantidade' => $item['quantidade'],
                                            'preco_compra' => $lotes->preco_compra,
                                            'preco_unitario' => $item['preco_unitario'],
                                            'custo' => $item['custo'],
                                            'iva' => (float) $item['taxa'],
                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                        ];

                                        $valor_total += $item['custo'];
                                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                        $loteModel->abater($lotes->id, $item['quantidade']);

                                        $quantidade_actual = $loteModel->qnt_disponivel_produto($lotes->produto_id);
                                        $user_id = auth()->user()->id;
                                        $qntBalancoAnterior = $produtoController->productBalanceStock($lotes->produto_id);

                                        if(DB::table('saida_items')->insert($carinho)){
                                            $qtdSaidaItems++;
                                            $produtoController->checkProductBalance($lotes->produto_id, 'venda_dinheiro', $item['quantidade'], $quantidade_actual,$qntBalancoAnterior, $user_id);
                                        }
                                    }else{
                                        $carinho =
                                        [
                                            'saida_id' => $saida,
                                            'produto_id' => $lotes[0]->produto_id,
                                            'lote_id' => $lotes[0]->id,
                                            'quantidade' => $item['quantidade'],
                                            'preco_unitario' => $item['preco_unitario'],
                                            'preco_compra' => $lotes[0]->preco_compra,
                                            'custo' => $item['custo'],
                                            'iva' => (float) $item['taxa'],
                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                        ];

                                        $valor_total += $item['custo'];
                                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                        // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
                                        $loteModel->abater($lotes[0]->id, $item['quantidade']);

                                        $quantidade_actual = $loteModel->qnt_disponivel_produto($lotes[0]->produto_id);
                                        $user_id = auth()->user()->id;
                                        $qntBalancoAnterior = $produtoController->productBalanceStock($lotes[0]->produto_id);


                                        if(DB::table('saida_items')->insert($carinho)){
                                            $qtdSaidaItems++;
                                            $produtoController->checkProductBalance($lotes[0]->produto_id, 'venda_dinheiro', $item['quantidade'], $quantidade_actual,$qntBalancoAnterior, $user_id);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    DB::commit();

                    if ($qtdSaidaItems != "") {
    //
    //
    //                            foreach ($cart as $item) {
    //                                $produto_id = $item['id'];
    //                                $quantidade = $item['quantidade'];
    //                                $quantidade_actual = $produtoController->qnt_disponivel_produto($produto_id);
    //                                $user_id = auth()->user()->id;
    //                                $produtoController->checkProductBalance($produto_id, 'venda_dinheiro', $quantidade, $quantidade_actual,0, $user_id);
    //                            }
    //

                        session()->pull('carinho', $cart);

                        session()->pull('carinhoTipoPagamento', $carinhoTipoPagamento);

                        $json['saida_id'] = $saida;
                        $json['success'] = true;
                        $json['qtdSaidaItem'] =  $qtdSaidaItems;
                        $json['message'] = 'Saida registada com sucesso.';

                        if($cotacao == 1){
                            DB::table('cotacao')->where('id',$cotacao_id)->update(['activo'=> '2']);
                        }

                        $request->session()->flash('success', $json['message']);

    //                            foreach ($carinho as $key => $cabecudo) {
    //                                DiarioStock::where([['produto_id', '=', $cabecudo['produto_id']]])->decrement('qnt_disponivel', $cabecudo['quantidade']);
    //
    //
    //                            }

    //                        session_start();
    //                        $_SESSION['cliente_id'] = "";
                    }
                        // }

    //                $produtoModel->actualizarDiarioProduto();
                }
            }
        } catch (Exception $e) {
            //throw $th;
            DB::rollBack();
            $json['success'] = false;
            $json['message'] = "Error ".$e;
        }
        // print_r($dataSaida);
        echo json_encode($json);
    }


    public function storeCotacao1(Request $request)
    {
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



        $usar_lotes = $empresa->usar_lotes;

        $produtoModel = new ProdutoController();
        $loteModel = new LoteController();


        // $cart = session()->get('carinhoCotacao');
        // $cart = session()->get('carinho');
        // $cart = session()->get('carinho_credito');

        $valor_total = 0;
        $valor_total_iva = 0;
        $dataSaida = array();
        $carinho = array();
        $lotes = array();
        $json['success'] = false;
        $json['message'] = "Erro no registo de cotação!";
        $cont = 0;
        $tipo_venda=null;
        $estado_pagamento = 'nao_pago';
        // $tipo_saida_id = 1;
        $tipo_saida_id = null;
        $percentagem = $request->percentagem;
        $validade_cotacao = $request->validade_cotacao ?? date('Y-m-d');
        $descricao_rascunho = $request->descricao_rascunho ?? null;

        $cliente_id =$request->cliente;
        $tipo_cotacao = $request->tipo_cotacao;
        $desconto = (double) $request->desconto;
        $valor_pago = $request->valor_pago;


        if (!empty($request->tipo_venda) && ($request->tipo_venda == "normal")) {
            $cart = session()->get('carinho');
            $tipo_saida_id = 1;
        }else if (!empty($request->tipo_venda) && ($request->tipo_venda == "credito")) {
            $cart = session()->get('carinho_credito');
            $tipo_saida_id = 3;
        }

        $referencia = Null;
        if (!empty($request->referencia)) {
            $referencia = $request->referencia;
        }

        $numero = NULL;
        if (!empty($request->numero)) {
            $numero = $request->numero;
        }
        $tipo_pagamento = null;

        $email = NULL;
        if (!empty($request->email)) {
            $email = $request->email;
        }


        if (isset($cart)) {

            if (count($cart) > 0) {

                if($usar_lotes=='0'){
                    foreach ($cart as $item) {
                        $lotes = $loteModel->retornaLoteByProdutoVenda($item['id']);

                        if (count($lotes) > 1) {
                            $contagem = 1;
                            $menorData = strtotime($lotes[0]->data_aquisicao);


                            $controlaQnt = 0;
                            $ultimo_lote = 0;
                            foreach ($lotes as $lote) {

                                $dataAquisicao = strtotime($lote->data_aquisicao);

                                if ($menorData > $dataAquisicao) {
                                    $menorData = $dataAquisicao;
                                }

                                if ($item['quantidade'] != 0) {

                                    if ($lote->qnt_disponivel > 0 || $contagem == count($lotes) ) {

                                        if ((($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel )) && $lote->qnt_disponivel > 0) {

                                            $controlaQnt = (double) ($qntItem - $qntLote);

                                            if ($controlaQnt < 0) {
                                                $controlaQnt = (double) ($controlaQnt * (-1));
                                            }

                                            $item['quantidade'] -= $qntLote;

                                            $item['custo'] = $qntLote * $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $item['produto_id'],
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $qntLote,
                                                    'preco_unitario' => $item['preco_unitario'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'iva' => (float) $item['taxa'],
                                                    'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                ];
                                            $valor_total += $item['custo'];
                                            $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
        //                                        $loteModel->abater($lote->id, $qntLote);
                                        } else {

                                            $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $item['produto_id'],
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $item['quantidade'],
                                                    'preco_unitario' => $item['preco_unitario'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'iva' => (float)$item['taxa'],
                                                    'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                ];

                                            $item['quantidade'] -= $item['quantidade'];
                                            $valor_total += $item['custo'];
                                            $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
        //                                        $loteModel->abater($lote->id, $qntItem);
                                        }
                                    }
                                }
                                $contagem ++;
                            }
                        } else {
                            if(count($lotes)==0){
                                $lotes = $loteModel->retornaUltimoLote($item['id']);
                                // dd($item['id']);
                                $carinho[$cont++] =
                                    [
                                        'produto_id' => $item['produto_id'],
                                        'lote_id' => $lotes->id,
                                        'quantidade' => $item['quantidade'],
                                        'preco_compra' => $lotes->preco_compra,
                                        'preco_unitario' => $item['preco_unitario'],
                                        'custo' => $item['custo'],
                                        'iva' =>(float)$item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];

                                $valor_total += $item['custo'];
                                $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
        //                            $loteModel->abater($lotes->id, $item['quantidade']);
                            }else{
                                $carinho[$cont++] =
                                    [
                                        'produto_id' => $item['produto_id'],
                                        'lote_id' => $lotes[0]->id,
                                        'quantidade' => $item['quantidade'],
                                        'preco_unitario' => $item['preco_unitario'],
                                        'preco_compra' => $lotes[0]->preco_compra,
                                        'custo' => $item['custo'],
                                        'iva' => (float)$item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];

                                $valor_total += $item['custo'];
                                $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
        //                            $loteModel->abater($lotes[0]->id, $item['quantidade']);
                            }

                        }
                    }
                }else{
                    foreach ($cart as $item) {
                        $carinho[$cont++] =
                        [
                            'produto_id' => $item['produto_id'],
                            'lote_id' => $item['id'],
                            'quantidade' => $item['quantidade'],
                            'preco_unitario' => $item['preco_unitario'],
                            'preco_compra' => $item['preco_compra'],
                            'custo' => $item['custo'],
                            'iva' => (float)$item['taxa'],
                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                        ];

                        $valor_total += $item['custo'];
                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                    }
                    // dd($carinho);
                }

                // dd($carinho);

//                if(($valor_total - $desconto) == $valor_pago)
//                    $estado_pagamento = 'pago';

                // $ultima_venda = Saida::latest()->first();
                // $numero_factura = @$ultima_venda->id+1 ."/".date("Y");

                $ano = date("Y");
                $tipo = 'cotacao';
                $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
                $numero_factura = "FP {$numeracao}/{$ano}";

//                if($tipo_venda != 'credito') {
//                    $valor_pago = ($valor_total - $desconto) - $valor_pago;
//                }
                $remanescente = ($valor_total - $desconto) - $valor_pago;
                $dataSaida = [
                    'cliente_id' => $cliente_id,
                    'data' => date('y-m-d'),
                    'tipo_saida_id' => $tipo_saida_id,
                    'desconto' => $desconto,
                    'tipo_pagamento_id' => $tipo_pagamento,
                    'valor_total' => ($valor_total - $desconto),
                    'valor_pago' => $valor_pago,
                    'valor_remanescente' => $remanescente,
                    'valor_total_iva' => $valor_total_iva,
                    'numero' => $numero,
                    'numero_factura' => $numero_factura,
                    'slip' => "$referencia",
                    'email' => "$email",
                    'estado_pagamento' => $estado_pagamento,
                    'activo' => 1,
                    'tipo_cotacao' => $tipo_cotacao,
                    'percentagem' => $percentagem,
                    'validade_cotacao' => $validade_cotacao,
                    'descricao_rascunho' => $descricao_rascunho ?? null,
                    'user_id' => Auth::user()->id
                ];

                // print_r($dataSaida);


                // Insert data into saidas table

                if ($insertGetId = DB::table('cotacao')->insertGetId($dataSaida)) {
                    // Retrieve the last inserted ID
                    // $saida_id = DB::getPdo()->lastInsertId();
                    // $saida = DB::table('cotacao')->where('id', $saida_id)->first();

                    // Call your method to update the invoice number
                    $this->updateInvoiceNumber($ano, $tipo);

                    // Prepare the data for the cotacao_items table
                    foreach ($carinho as &$item) {
                        $item['saida_id'] = $insertGetId;
                    }

                    // Insert multiple records into cotacao_items table
                    $cesto = DB::table('cotacao_items')->insert($carinho);

                    if ($cesto) {
                        // Clear the cart session
                        session()->pull('carinho', $cart);

                        $json['saida_id'] = $insertGetId;
                        $json['success'] = true;
                        $json['message'] = 'Cotação registada com sucesso.';

                        $request->session()->flash('success', $json['message']);

                    }
                }
            }
        }
        echo json_encode($json);
    }
    public function storeCotacao(Request $request)
    {
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



        $usar_lotes = $empresa->usar_lotes;

        $produtoModel = new ProdutoController();
        $loteModel = new LoteController();

        $cart = session()->get('carinhoCotacao');
        $valor_total = 0;
        $valor_total_iva = 0;
        $dataSaida = array();
        $carinho = array();
        $lotes = array();
        $json['success'] = false;
        $json['message'] = "Erro no registo de cotação!";
        $cont = 0;
        $tipo_venda=null;
        $estado_pagamento = 'pago';
        $tipo_saida_id = 1;
        $percentagem = $request->percentagem;
        $validade_cotacao = $request->validade_cotacao;

        $cliente_id =$request->cliente;
        $desconto = (double) $request->desconto;
        $valor_pago = $request->valor_pago;


        $referencia = Null;
        if (!empty($request->referencia)) {
            $referencia = $request->referencia;
        }

        $numero = NULL;
        if (!empty($request->numero)) {
            $numero = $request->numero;
        }
        $tipo_pagamento = null;

        $email = NULL;
        if (!empty($request->email)) {
            $email = $request->email;
        }

        if(isset($request->tipo_venda)) {
            $tipo_venda = $request->tipo_venda;
            if($tipo_venda == 'cotacao') {
                $tipo_saida_id = 5;
                $estado_pagamento = 'nao_pago';
            }
        }



        if (isset($cart)) {

            if (count($cart) > 0) {

                if($usar_lotes=='0'){
                    foreach ($cart as $item) {
                        $lotes = $loteModel->retornaLoteByProdutoVenda($item['id']);

                        if (count($lotes) > 1) {
                            $contagem = 1;
                            $menorData = strtotime($lotes[0]->data_aquisicao);


                            $controlaQnt = 0;
                            $ultimo_lote = 0;
                            foreach ($lotes as $lote) {

                                $dataAquisicao = strtotime($lote->data_aquisicao);

                                if ($menorData > $dataAquisicao) {
                                    $menorData = $dataAquisicao;
                                }

                                if ($item['quantidade'] != 0) {

                                    if ($lote->qnt_disponivel > 0 || $contagem == count($lotes) ) {

                                        if ((($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel )) && $lote->qnt_disponivel > 0) {

                                            $controlaQnt = (double) ($qntItem - $qntLote);

                                            if ($controlaQnt < 0) {
                                                $controlaQnt = (double) ($controlaQnt * (-1));
                                            }

                                            $item['quantidade'] -= $qntLote;

                                            $item['custo'] = $qntLote * $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $item['produto_id'],
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $qntLote,
                                                    'preco_unitario' => $item['preco_unitario'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'iva' => (float) $item['taxa'],
                                                    'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                ];
                                            $valor_total += $item['custo'];
                                            $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
        //                                        $loteModel->abater($lote->id, $qntLote);
                                        } else {

                                            $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $item['produto_id'],
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $item['quantidade'],
                                                    'preco_unitario' => $item['preco_unitario'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'iva' => (float)$item['taxa'],
                                                    'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                ];

                                            $item['quantidade'] -= $item['quantidade'];
                                            $valor_total += $item['custo'];
                                            $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
        //                                        $loteModel->abater($lote->id, $qntItem);
                                        }
                                    }
                                }
                                $contagem ++;
                            }
                        } else {
                            if(count($lotes)==0){
                                $lotes = $loteModel->retornaUltimoLote($item['id']);
                                // dd($item['id']);
                                $carinho[$cont++] =
                                    [
                                        'produto_id' => $item['produto_id'],
                                        'lote_id' => $lotes->id,
                                        'quantidade' => $item['quantidade'],
                                        'preco_compra' => $lotes->preco_compra,
                                        'preco_unitario' => $item['preco_unitario'],
                                        'custo' => $item['custo'],
                                        'iva' =>(float)$item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];

                                $valor_total += $item['custo'];
                                $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
        //                            $loteModel->abater($lotes->id, $item['quantidade']);
                            }else{
                                $carinho[$cont++] =
                                    [
                                        'produto_id' => $item['produto_id'],
                                        'lote_id' => $lotes[0]->id,
                                        'quantidade' => $item['quantidade'],
                                        'preco_unitario' => $item['preco_unitario'],
                                        'preco_compra' => $lotes[0]->preco_compra,
                                        'custo' => $item['custo'],
                                        'iva' => (float)$item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];

                                $valor_total += $item['custo'];
                                $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
        //                            $loteModel->abater($lotes[0]->id, $item['quantidade']);
                            }

                        }
                    }
                }else{
                    foreach ($cart as $item) {
                        $carinho[$cont++] =
                        [
                            'produto_id' => $item['produto_id'],
                            'lote_id' => $item['id'],
                            'quantidade' => $item['quantidade'],
                            'preco_unitario' => $item['preco_unitario'],
                            'preco_compra' => $item['preco_compra'],
                            'custo' => $item['custo'],
                            'iva' => (float)$item['taxa'],
                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                        ];

                        $valor_total += $item['custo'];
                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                    }
                    // dd($carinho);
                }

//                if(($valor_total - $desconto) == $valor_pago)
//                    $estado_pagamento = 'pago';

                // $ultima_venda = Saida::latest()->first();
                // $numero_factura = @$ultima_venda->id+1 ."/".date("Y");

                $ano = date("Y");
                $tipo = 'cotacao';
                $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
                $numero_factura = "FP {$numeracao}/{$ano}";

//                if($tipo_venda != 'credito') {
//                    $valor_pago = ($valor_total - $desconto) - $valor_pago;
//                }
                $remanescente = ($valor_total - $desconto) - $valor_pago;
                $dataSaida = [
                    'cliente_id' => $cliente_id,
                    'data' => date('y-m-d'),
                    'tipo_saida_id' => $tipo_saida_id,
                    'desconto' => $desconto,
                    'tipo_pagamento_id' => $tipo_pagamento,
                    'valor_total' => ($valor_total - $desconto),
                    'valor_pago' => $valor_pago,
                    'valor_remanescente' => $remanescente,
                    'valor_total_iva' => $valor_total_iva,
                    'numero' => $numero,
                    'numero_factura' => $numero_factura,
                    'slip' => "$referencia",
                    'email' => "$email",
                    'activo' => 1,
                    'estado_pagamento' => $estado_pagamento,
                    'percentagem' => $percentagem,
                    'validade_cotacao' => $validade_cotacao,
                    'tipo_cotacao' => 'cotacao',
                    'descricao_rascunho' => null,
                    'user_id' => Auth::user()->id
                ];


                if ($insertGetId = DB::table('cotacao')->insertGetId($dataSaida)) {

                    // Call your method to update the invoice number
                    $this->updateInvoiceNumber($ano, $tipo);

                    // Prepare the data for the cotacao_items table
                    foreach ($carinho as &$item) {
                        $item['saida_id'] = $insertGetId;
                    }

                    // Insert multiple records into cotacao_items table
                    $cesto = DB::table('cotacao_items')->insert($carinho);

                    if ($cesto) {
                        // Clear the cart session
                        session()->pull('carinhoCotacao', $cart);

                        $json['saida_id'] = $insertGetId;
                        $json['success'] = true;
                        $json['message'] = 'Cotação registada com sucesso.';

                        $request->session()->flash('success', $json['message']);

                    }
                }

                // if (($saida = Auth::user()->saidas()->create($dataSaida))) {

                //     $this->updateInvoiceNumber($ano,$tipo);

                //     if (($cesto = $saida->saidaItems()->createMany($carinho))) {


                //         session()->pull('carinhoCotacao', $cart);

                //         $json['saida_id'] = $saida->id;
                //         $json['success'] = true;
                //         $json['message'] = 'Cotação registada com sucesso.';

                //         $request->session()->flash('success',$json['message']);


                //     }
                // }
//                $produtoModel->actualizarDiarioProduto();
            }
        }
        // print_r($dataSaida);
        echo json_encode($json);
    }


    public function updateCotacao($saida_id,Request $request)
    {
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



        $usar_lotes = $empresa->usar_lotes;

        $produtoModel = new ProdutoController();
        $loteModel = new LoteController();

        $cart = session()->get('carinhoCotacaoUpdate');
        $valor_total = 0;
        $valor_total_iva = 0;
        $dataSaida = array();
        $carinho = array();
        $lotes = array();
        $json['success'] = false;
        $json['message'] = "Erro no registo de cotação!";
        $cont = 0;
        $tipo_venda=null;
        $estado_pagamento = 'pago';
        $tipo_saida_id = 1;
        $percentagem = $request->percentagem;
        $validade_cotacao = $request->validade_cotacao;

        $cliente_id =$request->cliente;
        $desconto = (double) $request->desconto;
        $valor_pago = $request->valor_pago;


        $referencia = Null;
        if (!empty($request->referencia)) {
            $referencia = $request->referencia;
        }

        $numero = NULL;
        if (!empty($request->numero)) {
            $numero = $request->numero;
        }
        $tipo_pagamento = null;

        $email = NULL;
        if (!empty($request->email)) {
            $email = $request->email;
        }

        if(isset($request->tipo_venda)) {
            $tipo_venda = $request->tipo_venda;
            if($tipo_venda == 'cotacao') {
                $tipo_saida_id = 5;
                $estado_pagamento = 'nao_pago';
            }
        }



        if (isset($cart)) {

            if (count($cart) > 0) {

                if($usar_lotes=='0'){
                    foreach ($cart as $item) {
                        $lotes = $loteModel->retornaLoteByProdutoVenda($item['id']);

                        if (count($lotes) > 1) {
                            $contagem = 1;
                            $menorData = strtotime($lotes[0]->data_aquisicao);


                            $controlaQnt = 0;
                            $ultimo_lote = 0;
                            foreach ($lotes as $lote) {

                                $dataAquisicao = strtotime($lote->data_aquisicao);

                                if ($menorData > $dataAquisicao) {
                                    $menorData = $dataAquisicao;
                                }

                                if ($item['quantidade'] != 0) {

                                    if ($lote->qnt_disponivel > 0 || $contagem == count($lotes) ) {

                                        if ((($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel )) && $lote->qnt_disponivel > 0) {

                                            $controlaQnt = (double) ($qntItem - $qntLote);

                                            if ($controlaQnt < 0) {
                                                $controlaQnt = (double) ($controlaQnt * (-1));
                                            }

                                            $item['quantidade'] -= $qntLote;

                                            $item['custo'] = $qntLote * $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $lote->produto_id,
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $qntLote,
                                                    'preco_unitario' => $item['preco_unitario'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'iva' =>(float)$item['taxa'],
                                                    'valor_iva' => $item['valor_iva'],
                                                ];
                                            $valor_total += $item['custo'];
    //                                        $loteModel->abater($lote->id, $qntLote);
                                        } else {

                                            $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $lote->produto_id,
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $item['quantidade'],
                                                    'preco_unitario' => $item['preco_unitario'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'iva' =>(float)$item['taxa'],
                                                    'valor_iva' => $item['valor_iva'],
                                                ];

                                            $item['quantidade'] -= $item['quantidade'];
                                            $valor_total += $item['custo'];
    //                                        $loteModel->abater($lote->id, $qntItem);
                                        }
                                    }
                                }
                                $contagem ++;
                            }
                        } else {
                            if(count($lotes)==0){
                                $lotes = $loteModel->retornaUltimoLote($item['id']);
                                $carinho[$cont++] =
                                    [
                                        'produto_id' => $lotes->produto_id,
                                        'lote_id' => $lotes->id,
                                        'quantidade' => $item['quantidade'],
                                        'preco_compra' => $lotes->preco_compra,
                                        'preco_unitario' => $item['preco_unitario'],
                                        'custo' => $item['custo'],
                                        'iva' =>(float)$item['taxa'],
                                        'valor_iva' => $item['valor_iva'],
                                    ];

                                $valor_total += $item['custo'];
    //                            $loteModel->abater($lotes->id, $item['quantidade']);
                            }else{
                                $carinho[$cont++] =
                                    [
                                        'produto_id' => $lotes[0]->produto_id,
                                        'lote_id' => $lotes[0]->id,
                                        'quantidade' => $item['quantidade'],
                                        'preco_unitario' => $item['preco_unitario'],
                                        'preco_compra' => $lotes[0]->preco_compra,
                                        'custo' => $item['custo'],
                                        'iva' =>(float)$item['taxa'],
                                        'valor_iva' => $item['valor_iva'],
                                    ];

                                $valor_total += $item['custo'];
                                // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
    //                            $loteModel->abater($lotes[0]->id, $item['quantidade']);
                            }

                        }
                    }
                }else{
                    foreach ($cart as $item) {
                        $carinho[$cont++] =
                        [
                            'produto_id' => $item['produto_id'],
                            'lote_id' => $item['id'],
                            'quantidade' => $item['quantidade'],
                            'preco_unitario' => $item['preco_unitario'],
                            'preco_compra' => $item['preco_compra'],
                            'custo' => $item['custo'],
                            'iva' =>(float)$item['taxa'],
                            'valor_iva' => $item['valor_iva'],
                        ];

                        $valor_total += $item['custo'];
                    }
                }

//                if(($valor_total - $desconto) == $valor_pago)
//                    $estado_pagamento = 'pago';

                // $ultima_venda = Saida::latest()->first();
                // $numero_factura = @$ultima_venda->id+1 ."/".date("Y");

//                $ano = date("Y");
//                $tipo = 'cotacao';
//                $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
//                $numero_factura = "FP {$numeracao}/{$ano}";

//                if($tipo_venda != 'credito') {
//                    $valor_pago = ($valor_total - $desconto) - $valor_pago;
//                }
                $remanescente = ($valor_total - $desconto) - $valor_pago;
                $dataSaida = [
                    'cliente_id' => $cliente_id,
                    'data' => date('y-m-d'),
//                    'tipo_saida_id' => $tipo_saida_id,
                    'desconto' => $desconto,
                    'tipo_pagamento_id' => $tipo_pagamento,
                    'valor_total' => ($valor_total - $desconto),
                    'valor_pago' => $valor_pago,
                    'valor_remanescente' => $remanescente,
                    'numero' => $numero,
//                    'numero_factura' => $numero_factura,
//                    'slip' => "$referencia",
//                    'email' => "$email",
                    'activo' => 1,
//                    'estado_pagamento' => $estado_pagamento,
//                    'percentagem' => $percentagem,
                    'validade_cotacao' => $validade_cotacao,
                ];



                foreach ($carinho as $item){
//                    dd($item['produto_id']);
                    $produto = $item['produto_id'];
//                    dd($produto);
                    $val = DB::selectOne("SELECT produto_id FROM saida_items  WHERE saida_items.saida_id = {$saida_id} AND produto_id = {$produto} AND activo='1'");
//                    dd($val->produto_id);
                    if(isset($val->produto_id) && !empty($val->produto_id) && $val->produto_id==$produto){
                        //update
                        DB::table('saida_items')->where(['saida_id'=>$saida_id,'produto_id'=>$val->produto_id])->update(
                            [   'produto_id'=>$item['produto_id'],
                                'saida_id'=>$saida_id,
                                'lote_id'=>$item['lote_id'],
                                'quantidade'=>$item['quantidade'],
                                'preco_unitario'=>$item['preco_unitario'],
                                'preco_compra'=>$item['preco_compra'],
                                'custo'=>$item['custo'],
                                'valor_iva' => $item['valor_iva'],
                            ]);
                    }else{
                        //insert line
                        DB::table('saida_items')->insert(
                            [   'produto_id'=>$item['produto_id'],
                                'lote_id'=>$item['lote_id'],
                                'saida_id'=>$saida_id,
                                'quantidade'=>$item['quantidade'],
                                'preco_unitario'=>$item['preco_unitario'],
                                'preco_compra'=>$item['preco_compra'],
                                'custo'=>$item['custo'],
                                'iva' =>(float)$item['iva'],
                                'valor_iva' => $item['valor_iva'],
                            ]);
                    }
                }
                DB::table('saidas')->where(['id'=>$saida_id])->update($dataSaida);

                $json['saida_id'] = $saida_id;
                $json['success'] = true;
                $json['message'] = 'Cotação registada com sucesso.';

                $request->session()->flash('success',$json['message']);

//                if (($saida = Auth::user()->saidas()->create($dataSaida))) {
//
////                    $this->updateInvoiceNumber($ano,$tipo);
//
//                    if (($cesto = $saida->saidaItems()->createMany($carinho))) {
//
//
//                        session()->pull('carinhoCotacaoUpdate', $cart);
//
//                        $json['saida_id'] = $saida->id;
//                        $json['success'] = true;
//                        $json['message'] = 'Cotação registada com sucesso.';
//
//                        $request->session()->flash('success',$json['message']);
//
////                        foreach ($carinho as $key=>$cabecudo) {
////                            DiarioStock::where([['produto_id','=',$cabecudo['produto_id'] ]])->decrement('qnt_disponivel',$cabecudo['quantidade']);
////                        }
//
//                    }
//                }
//                $produtoModel->actualizarDiarioProduto();
            }
        }
        // print_r($dataSaida);
        echo json_encode($json);
    }


    function getInvoiceNumber($ano, $tipo){
        $numero = 0;
        $numeros = DB::table('numeracao')
        ->select("numero")

        ->where(['ano'=>$ano,'tipo' => "$tipo"])->get();

        foreach($numeros as $numero2){
            $numero=$numero2->numero;
        }
        // print_r($numero);
        return $numero;
    }

    function updateInvoiceNumber($ano, $tipo){
        $numero = $this->getInvoiceNumber($ano, $tipo);
        if(!empty($numero)){
            $numeracao_factura = DB::table('numeracao')->where('tipo',$tipo)->update(['numero'=> $numero+1]);
        }else {
            $numeracao_factura = DB::table('numeracao')->insertGetId(['numero'=> 1,'ano'=> $ano, 'tipo' => $tipo]);
        }
        return $numeracao_factura;
    }


    function store_credito(Request $request)
    {
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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $produtoController = new ProdutoController();
        $loteModel = new LoteController();

        $cart = session()->get('carinho_credito');
        // print_r($cart);
        // exit();
        $valor_total = 0;
        $valor_total_iva = 0;
        $dataSaida = array();
        $carinho = array();
        $lotes = array();
        $json['success'] = false;
        $json['message'] = "Erro no registo de vendas!";
        $cont = 0;
        $tipo_venda=null;
        $estado_pagamento = 'pago';
        $tipo_saida_id = 1;
        $percentagem = $request->percentagem;
        $data_venda = $request->data_venda ?? date('y-m-d');


        $cliente_id =$request->cliente;
        $desconto = (int) $request->desconto;
        $valorEntregue = $request->valor_entregue;
        $trocos = (float) $request->trocos;
        $valor_pago = $request->valor_pago;

        $nome_segurado = $request->nome_segurado ?? null;
        $codigo_autorizacao = $request->codigo_autorizacao ?? null;
        $numero_membro= $request->numero_membro ?? null;


        $referencia = Null;
        if (!empty($request->referencia)) {
            $referencia = $request->referencia;
        }

        $numero = NULL;
        if (!empty($request->numero)) {
            $numero = $request->numero;
        }
        $tipo_pagamento =$request->tipo_pagamento;

        $email = NULL;
        if (!empty($request->email)) {
            $email = $request->email;
        }

        if(isset($request->tipo_venda)) {
            $tipo_venda = $request->tipo_venda;
            if($tipo_venda == 'credito') {
                $tipo_saida_id = 3;
                $estado_pagamento = 'nao_pago';
            }
        }

        $cotacao = null;
        $cotacao_id = null;

        $cotacao = $request->cotacao ?? null;
        $cotacao_id = $request->cotacao_id ?? null;

        try {

            if (isset($cart)) {

                if (count($cart) > 0) {

                    $saida = "";
                    foreach ($cart as $item) {
                        $valor_total += $item['custo'];
                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;

                        if($empresa->fonte == '1'){
                            $desconto += $item['desconto_valor'];
                        }
                    }

                    if(($valor_total - $desconto) == $valor_pago){
                        $estado_pagamento = 'pago';
                    }

                    DB::beginTransaction();

                    $ano = date("Y");
                    $tipo = 'venda_credito';
                    $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
                    $numero_factura = "FT {$numeracao}/{$ano}";
                    //$numero_factura=0;
                    //if($tipo_venda != 'credito') {
                        //$valor_pago = ($valor_total - $desconto) - $valor_pago;
                    //}

                    $remanescente = ($valor_total - $desconto) - $valor_pago;
                    if(empty($valorEntregue))
                    {
                        $valorEntregue = $valor_total - $desconto;
                    }

                    $dataSaida = [
                        'cliente_id' => $cliente_id,
                        'data' => $empresa->data_venda == '1' ? $data_venda : date('y-m-d'),
                        'tipo_saida_id' => $tipo_saida_id,
                        'desconto' => $desconto,
                        'valor_entregue' => $valorEntregue,
                        'trocos' => $trocos,
                        'tipo_pagamento_id' => 1,
                        'valor_total' => ($valor_total - $desconto),
                        'valor_pago' => $valor_pago,
                        'valor_remanescente' => $remanescente,
                        'valor_total_iva' => $valor_total_iva,
                        'numero' => $numero,
                        'numero_factura' => $numero_factura,
                        'slip' => "$referencia",
                        'email' => "$email",
                        'activo' => 1,
                        'estado_pagamento' => $estado_pagamento,
                        'percentagem' => $percentagem,
                        'user_id' => Auth::user()->id,
                        'sessao_id' => $empresa->sessao_id ?? NULL,
                        'numero_membro' => $numero_membro ?? NULL,
                        'codigo_autorizacao' => $codigo_autorizacao ?? NULL,
                        'nome_segurado' => $nome_segurado ?? NULL,
                    ];

                    if ($empresa->data_venda == '1') {
                        $dateTime = new \DateTime($data_venda);
                        $horaActual = (new \DateTime())->format('H:i:s');
                        $dateTime->setTime(...explode(':', $horaActual));
                        $dataSaida['created_at'] = $dateTime->format('Y-m-d H:i:s');
                    }

                    session_start();
                    if(!empty($_SESSION['saida_id'])){
                        $saida_id = $_SESSION['saida_id'];
                        DB::table('saidas')->where('id',$saida_id)->update(['activo'=>'2']);
                        $numero_factura = DB::selectOne("SELECT numero_factura FROM saidas WHERE saidas.id={$saida_id}");
                        $dataSaida['numero_cotacao'] = $numero_factura->numero_factura;
                        $_SESSION['cliente_id'] = "";
                        $_SESSION['saida_id'] = "";
                    }

                    $carinhoTipoPagamento = session()->get('carinhoTipoPagamento2');
                    if ($saida = DB::table('saidas')->insertGetId($dataSaida)) {

                        $this->updateInvoiceNumber($ano, $tipo);

                        if(!empty($carinhoTipoPagamento)) {

                            foreach ($carinhoTipoPagamento as $item) {
                                $tipo_pa[] = [
                                    'saida_id' => $saida,
                                    'tipo_pagamento_id' => $item['tipo_pagamento_id'],
                                    'valor' => $item['valor'],
                                    'numero' => $item['numero'],
                                    'referencia' => $item['referencia'],
                                    //'email' => $item['email'],
                                    'activo' => '1'
                                ];
                            }
                            DB::table('saida_tipo_pagamentos')->insert($tipo_pa);
                        }else{
                            $json['success'] = false;
                            $json['message'] = 'Por favor preencha os dados de Método de pagamento e valor do mesmo!.';
                        }
                    }


                    //carrinho
                    $cesto = "";
                    $qtdSaidaItems = 0;
                    if($empresa->pacote==1){
                        foreach ($cart as $item) {
                            if ($item['quantidade'] != 0) {
                                $carinho =
                                    [
                                        'saida_id' => $saida,
                                        'produto_id' => $item['produto_id'],
                                        'quantidade' => $item['quantidade'],
                                        'preco_unitario' => $item['preco_unitario'],
                                        'preco_compra' => 0,
                                        'custo' => $item['custo'],
                                        'iva' => (float) $item['taxa'],
                                        'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                    ];

                                    $valor_total += $item['custo'];
                                    $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;

                                    $user_id = auth()->user()->id;

                                    if(DB::table('saida_items')->insert($carinho)){
                                        $qtdSaidaItems++;
                                    }
                            }
                        }
                    }else{
                        if($usar_lotes == '1'){
                            foreach ($cart as $item) {

                                $item['custo'] = (int)$item['quantidade'] * (double)$item['preco_unitario'];
                                if($empresa->fonte == '1'){
                                    $carinho =
                                        [
                                            'saida_id' => $saida,
                                            'produto_id' => $item['produto_id'],
                                            'lote_id' => $item['id'],
                                            'quantidade' => $item['quantidade'],
                                            'preco_unitario' => $item['preco_unitario'],
                                            'preco_compra' => $item['preco_compra'],
                                            'custo' => $item['custo'] - $item['desconto_valor'],
                                            'iva' => (float) $item['taxa'],
                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                            'desconto_percentual' => (double)$item['desconto'],
                                            'desconto_valor' => (double)$item['desconto_valor'],

                                        ];
                                }else{
                                    $carinho =
                                        [
                                            'saida_id' => $saida,
                                            'produto_id' => $item['produto_id'],
                                            'lote_id' => $item['id'],
                                            'quantidade' => $item['quantidade'],
                                            'preco_unitario' => $item['preco_unitario'],
                                            'preco_compra' => $item['preco_compra'],
                                            'custo' => $item['custo'],
                                            'iva' => (float) $item['taxa'],
                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                        ];
                                }
                                $valor_total += $item['custo'];
                                $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;

                                $loteModel->abater($item['id'], $item['quantidade']);

                                // $quantidade_actual = $loteModel->qnt_disponivel_produto($lote->produto_id);
                                $user_id = auth()->user()->id;
                                // $qntBalancoAnterior = $produtoController->productBalanceStock($lote->produto_id);


                                if(DB::table('saida_items')->insert($carinho)){
                                    $qtdSaidaItems++;
                                    // $produtoController->checkProductBalance($lote->produto_id, 'venda_dinheiro', $qntLote, $quantidade_actual,$qntBalancoAnterior, $user_id);
                                }
                            }
                        }else{
                            foreach ($cart as $item) {
                                $lotes = $loteModel->retornaLoteByProdutoVenda($item['id']);
                                if (count($lotes) > 1) {

                                    $menorData = strtotime($lotes[0]->data_aquisicao);


                                    $controlaQnt = 0;
                                    $ultimo_lote = 0;
                                    $contagem=1;
                                    foreach ($lotes as $lote) {

                                        $dataAquisicao = strtotime($lote->data_aquisicao);

                                        if ($menorData > $dataAquisicao) {
                                            $menorData = $dataAquisicao;
                                        }

                                        if ($item['quantidade'] != 0) {

                                            if ($lote->qnt_disponivel > 0 || $contagem == count($lotes)) {
                                                if ((($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel )) && $lote->qnt_disponivel > 0 && $contagem != count($lotes)) {
                                                    $controlaQnt = (double) ($qntItem - $qntLote);

                                                    if ($controlaQnt < 0) {
                                                        $controlaQnt = (double) ($controlaQnt * (-1));
                                                    }

                                                    $item['quantidade'] -= $qntLote;

                                                    $item['custo'] = $qntLote * $item['preco_unitario'];

                                                    $carinho =
                                                        [
                                                            'saida_id' => $saida,
                                                            'produto_id' => $lote->produto_id,
                                                            'lote_id' => $lote->id,
                                                            'quantidade' => $qntLote,
                                                            'preco_unitario' => $item['preco_unitario'],
                                                            'preco_compra' => $lote->preco_compra,
                                                            'custo' => $item['custo'],
                                                            'iva' => (float) $item['taxa'],
                                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                        ];
                                                    $valor_total += $item['custo'];
                                                    $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                                    $loteModel->abater($lote->id, $qntLote);

                                                    $quantidade_actual = $loteModel->qnt_disponivel_produto($lote->produto_id);
                                                    $user_id = auth()->user()->id;
                                                    $qntBalancoAnterior = $produtoController->productBalanceStock($lote->produto_id);


                                                    if(DB::table('saida_items')->insert($carinho)){
                                                        $qtdSaidaItems++;
                                                        $produtoController->checkProductBalance($lote->produto_id, 'venda_credito', $qntLote, $quantidade_actual,$qntBalancoAnterior, $user_id);
                                                    }
                                                } else {

                                                    $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                                    $carinho =
                                                        [
                                                            'saida_id' => $saida,
                                                            'produto_id' => $lote->produto_id,
                                                            'lote_id' => $lote->id,
                                                            'quantidade' => $item['quantidade'],
                                                            'preco_unitario' => $item['preco_unitario'],
                                                            'preco_compra' => $lote->preco_compra,
                                                            'custo' => $item['custo'],
                                                            'iva' => (float) $item['taxa'],
                                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                                        ];

                                                    $item['quantidade'] -= $item['quantidade'];
                                                    $valor_total += $item['custo'];
                                                    $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                                    $loteModel->abater($lote->id, $qntItem);

                                                    $quantidade_actual = $loteModel->qnt_disponivel_produto($lote->produto_id);
                                                    $user_id = auth()->user()->id;
                                                    $qntBalancoAnterior = $produtoController->productBalanceStock($lote->produto_id);

                                                    if(DB::table('saida_items')->insert($carinho)){
                                                        $qtdSaidaItems++;
                                                        $produtoController->checkProductBalance($lote->produto_id, 'venda_credito', $item['quantidade'], $quantidade_actual,$qntBalancoAnterior, $user_id);
                                                    }
                                                }
                                            }
                                        }
                                        $contagem++;
                                    }
                                } else {
                                    if(count($lotes)==0){
                                        $lotes = $loteModel->retornaUltimoLote($item['id']);
                                        $carinho =
                                        [
                                            'saida_id' => $saida,
                                            'produto_id' => $lotes->produto_id,
                                            'lote_id' => $lotes->id,
                                            'quantidade' => $item['quantidade'],
                                            'preco_compra' => $lotes->preco_compra,
                                            'preco_unitario' => $item['preco_unitario'],
                                            'custo' => $item['custo'],
                                            'iva' => (float) $item['taxa'],
                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                        ];

                                        $valor_total += $item['custo'];
                                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                        $loteModel->abater($lotes->id, $item['quantidade']);

                                        $quantidade_actual = $loteModel->qnt_disponivel_produto($lotes->produto_id);
                                        $user_id = auth()->user()->id;
                                        $qntBalancoAnterior = $produtoController->productBalanceStock($lotes->produto_id);

                                        if(DB::table('saida_items')->insert($carinho)){
                                            $qtdSaidaItems++;
                                            $produtoController->checkProductBalance($lotes->produto_id, 'venda_credito', $item['quantidade'], $quantidade_actual,$qntBalancoAnterior, $user_id);
                                        }
                                    }else{
                                        $carinho =
                                        [
                                            'saida_id' => $saida,
                                            'produto_id' => $lotes[0]->produto_id,
                                            'lote_id' => $lotes[0]->id,
                                            'quantidade' => $item['quantidade'],
                                            'preco_unitario' => $item['preco_unitario'],
                                            'preco_compra' => $lotes[0]->preco_compra,
                                            'custo' => $item['custo'],
                                            'iva' => (float) $item['taxa'],
                                            'valor_iva' => $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0,
                                        ];

                                        $valor_total += $item['custo'];
                                        $valor_total_iva += $item['valor_iva'] != 0 || $item['valor_iva'] != "" ? (double)$item['custo']-((double)$item['custo']/$empresa->iva) : 0;
                                        // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
                                        $loteModel->abater($lotes[0]->id, $item['quantidade']);

                                        $quantidade_actual = $loteModel->qnt_disponivel_produto($lotes[0]->produto_id);
                                        $user_id = auth()->user()->id;
                                        $qntBalancoAnterior = $produtoController->productBalanceStock($lotes[0]->produto_id);

                                        if(DB::table('saida_items')->insert($carinho)){
                                            $qtdSaidaItems++;
                                            $produtoController->checkProductBalance($lotes[0]->produto_id, 'venda_credito', $item['quantidade'], $quantidade_actual,$qntBalancoAnterior, $user_id);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    DB::commit();

                    if ($qtdSaidaItems != "") {
    //
    //
    //                            foreach ($cart as $item) {
    //                                $produto_id = $item['id'];
    //                                $quantidade = $item['quantidade'];
    //                                $quantidade_actual = $produtoController->qnt_disponivel_produto($produto_id);
    //                                $user_id = auth()->user()->id;
    //                                $produtoController->checkProductBalance($produto_id, 'venda_dinheiro', $quantidade, $quantidade_actual,0, $user_id);
    //                            }
    //

                        session()->pull('carinho_credito', $cart);

                        session()->pull('carinhoTipoPagamento2', $carinhoTipoPagamento);

                        $json['saida_id'] = $saida;
                        $json['success'] = true;
                        $json['qtdSaidaItem'] =  $qtdSaidaItems;
                        $json['message'] = 'Saida registada com sucesso.';

                        if($cotacao == 1){
                            DB::table('cotacao')->where('id',$cotacao_id)->update(['activo'=> '2']);
                        }

                        $request->session()->flash('success', $json['message']);

    //                            foreach ($carinho as $key => $cabecudo) {
    //                                DiarioStock::where([['produto_id', '=', $cabecudo['produto_id']]])->decrement('qnt_disponivel', $cabecudo['quantidade']);
    //
    //
    //                            }

    //                        session_start();
    //                        $_SESSION['cliente_id'] = "";
                    }
                        // }

    //                $produtoModel->actualizarDiarioProduto();
                }
            }
        } catch (Exception $e) {
            //throw $th;
            DB::rollBack();
            $json['success'] = false;
            $json['message'] = "Error ".$e;
        }
        // print_r($dataSaida);
        echo json_encode($json);
    }


    public function storePayment(Request $request)
    {
        $continue=true;
        $remanescente = (double)$request->valor_remanescente-(double)$request->valor_pagar;
        $saida = Saida::find($request->saida_id);

        $payments = new Pagamento(
            array(
                'valor_pago' => (double)$request->valor_pagar,
                'numero_recibo' => $request->numero_recibo,
                'data_pagamento' => date('y-m-d H:i:s'),
                'banco_id' => $request->banco_id,
                'numero' => '',
                'tipo_pagamento_id' => $request->tipo_pagamento_id,
                'cliente_id' => $request->cliente_id,
                'saida_id' => $request->saida_id,
                'user_id' => Auth::user()->id,
                'valor_pago2' => (double)$request->valor_pagar2 ?? NULL,
                'cambio' => $request->cambio ?? NULL,
            )
        );
        if($remanescente == 0){
            $saida->estado_pagamento = 'pago';
        }else if($remanescente<0){
            $json['success'] = false;
            $continue = false;
            $json['message'] = 'A valor pago é superior com o previsto.';
        }else{
            $saida->estado_pagamento = 'parcial';
        }
        if($continue) {
            if ($payments->save()) {
                $saida->valor_remanescente = (double)$remanescente;
                $saida->valor_pago = (double)$saida->valor_pago+(double)$request->valor_pagar;
                if($saida->save()){
                    $json['success'] = true;
                    $json['message'] = 'Saida Paga com Sucesso.';
                }else{
                    $json['success'] = false;
                    $json['message'] = 'Falha ao Actualizar a Saída.';
                }
            }else{
                $json['success'] = false;
                $json['message'] = 'Falha ao Actualizar o Pagamento.';
            }
        }else{
            $json['success'] = false;
        }
        return response()->json($json);
    }
    public function storePaymentAll(Request $request)
    {
        DB::beginTransaction();
        try{
            $json['success'] = false;
            $json['message'] = null;
            // $continue=true;
            $cliente_id=$request->cliente_id;
            $data=$request->first_date;
            $data2=$request->last_date;
            $pago = $request->valor_pagar;
            $remanescente2 = $request->valor_remanescente-$request->valor_pagar;
            $sobra = $request->valor_pagar;
            $usado = 0;

            $saidas = DB::table('saidas as s')
                ->select(array('s.cliente_id','s.id',DB::raw('YEAR(s.data) anoVenda'),'clientes.nome as cliente',
                    'tipo_saidas.descricao as tipoSaida',DB::raw("s.valor_remanescente AS valor_remanescente"), DB::raw("s.valor_pago AS valor_pago"),
                    DB::raw("(SELECT COUNT(produto_id) total FROM `saida_items` WHERE saida_id = s.id AND saida_items.activo != '2') AS qntProdutos"),
                    DB::raw('SUM(devolucao.quantidade) AS qntDevolucao'),DB::raw('SUM(devolucao.total) AS totalDevolucao'),
                ))
                ->leftJoin('clientes', 's.cliente_id', '=', 'clientes.id')
                ->join('tipo_saidas', 's.tipo_saida_id', '=', 'tipo_saidas.id')
                ->leftJoin('tipo_pagamentos', 's.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
                ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
                ->leftJoin('devolucao', 'saida_items.id', '=', 'devolucao.saida_item_id')
                ->join('produtos', 'saida_items.produto_id', '=', 'produtos.id')
                ->where('s.cliente_id','=',$cliente_id)
                ->where('s.activo','=','1')
                ->where('saida_items.activo','=','1')
                ->whereBetween('s.data',[$data, $data2])
                ->groupBy('s.id')
                ->get();
            $pagar = $pago;
            foreach ($saidas AS $saida) {
                if($pagar > 0){
                    $sobra -= $saida->valor_remanescente;
                    $usado += $saida->valor_remanescente;
                    $saida_array = Saida::find($saida->id);
                    $valor_pagar = $saida->valor_remanescente;
                    $valor_pago_anterior =  $saida->valor_pago;
                    $remanescente = $saida->valor_remanescente-$valor_pagar;
                    if($saida->valor_remanescente > 0){
                        if((double)$pagar > (double)$saida->valor_remanescente){
                            $pagar = (double)$pagar - (double)$saida->valor_remanescente;
                            $payments = new Pagamento(
                                array(
                                    'valor_pago' => (double)$saida->valor_remanescente,
                                    'numero_recibo' => $request->numero_recibo,
                                    'data_pagamento' => date('y-m-d H:i:s'),
                                    'banco_id' => $request->banco_id,
                                    'numero' => '',
                                    'tipo_pagamento_id' => $request->tipo_pagamento_id,
                                    'cliente_id' => $request->cliente_id,
                                    'saida_id' => $saida->id,
                                    'user_id' => Auth::user()->id,
                                )
                            );
                            $payments->save();
                            $saida_array->estado_pagamento = 'pago';
                            $saida_array->valor_remanescente = 0;
                            $saida_array->valor_pago = (double)$saida->valor_remanescente+(double)$valor_pago_anterior;
                            $saida_array->save();
                        }elseif((double)$pagar == (double)$saida->valor_remanescente){
                            $pagar = (double)$pagar - (double)$saida->valor_remanescente;
                            $payments = new Pagamento(
                                array(
                                    'valor_pago' => (double)$saida->valor_remanescente,
                                    'numero_recibo' => $request->numero_recibo,
                                    'data_pagamento' => date('y-m-d H:i:s'),
                                    'banco_id' => $request->banco_id,
                                    'numero' => '',
                                    'tipo_pagamento_id' => $request->tipo_pagamento_id,
                                    'cliente_id' => $request->cliente_id,
                                    'saida_id' => $saida->id,
                                    'user_id' => Auth::user()->id,
                                )
                            );
                            $payments->save();
                            $saida_array->estado_pagamento = 'pago';
                            $saida_array->valor_remanescente = 0;
                            $saida_array->valor_pago = (double)$saida->valor_remanescente+(double)$valor_pago_anterior;
                            $saida_array->save();

                        }elseif((double)$pagar != 0 && (double)$pagar < (double)$saida->valor_remanescente){
                            $payments = new Pagamento(
                                array(
                                    'valor_pago' => (double)$pagar,
                                    'numero_recibo' => $request->numero_recibo,
                                    'data_pagamento' => date('y-m-d H:i:s'),
                                    'banco_id' => $request->banco_id,
                                    'numero' => '',
                                    'tipo_pagamento_id' => $request->tipo_pagamento_id,
                                    'cliente_id' => $request->cliente_id,
                                    'saida_id' => $saida->id,
                                    'user_id' => Auth::user()->id,
                                )
                            );
                            $payments->save();
                            $saida_array->estado_pagamento = 'parcial';
                            $saida_array->valor_remanescente = (double)$saida->valor_remanescente-(double)$pagar;
                            $saida_array->valor_pago = (double)$pagar+(double)$valor_pago_anterior;
                            $saida_array->save();
                            $pagar = ((double)$pagar-(double)$saida->valor_remanescente) < 0 ? 0 : (double)$pagar-(double)$saida->valor_remanescente;
                        }
                    }
                }

            }
            // if ($remanescente == 0) {
            //     $saida_array->estado_pagamento = 'pago';
            // } else if ($remanescente < 0) {
            //     $json['success'] = false;
            //     $continue = false;
            //     $json['message'] = 'A valor pago é superior com o previsto.';
            // } else {
            //     $saida_array->estado_pagamento = 'parcial';
            // }
            // if ($continue) {
            //     if ($payments->save()) {
            //         $saida_array->valor_remanescente = $remanescente;
            //         if ($saida_array->save()) {
            //             $json['success'] = true;
            //             $json['message'] = 'Saida Paga com Sucesso.';
            //         } else {
            //             $json['success'] = false;
            //             $json['message'] = 'Falha ao Actualizar a Saída.';
            //         }
            //     } else {
            //         $json['success'] = false;
            //         $json['message'] = 'Falha ao Actualizar o Pagamento.';
            //     }
            // } else {
            //     $json['success'] = false;
            // }
            // print_r($pagar);
            // dd($payments);
            if((double)$remanescente2 > 0){
                $json['success'] = true;
                $json['message'] = 'Dívida paga de forma parcial com Sucesso.';
            }else{
                $json['success'] = true;
                $json['message'] = 'Dívida paga na totalidade com Sucesso.';
            }
            DB::commit();
            return response()->json($json);
        }catch(Exception $ex){
            DB::rollback();
            $json['success'] = false;
            $json['message'] = "Houve um erro de execução.\n".$ex->getMessage();
            return response()->json($json);
        }
    }
    public function getNotify(){
        $json['warning'] = false;
        $json['id'] = null;
        $json['message'] = null;
        $json['tipo_notificacao'] = null;
        $get = Notificar::all();
        $descricao = '';
        $tipo = '';
        $id = '';
        foreach($get as $item){
            if(empty($item['dataVisualizacao'])){
                $id=$item['id'];
                $descricao=$item['descricao'];
                $tipo=$item['tipo_notificacao'];
            }
        }
        $json['id'] = $id;
        $json['warning'] = true;
        $json['tipo_notificacao'] = $tipo;
        $json['message'] = $descricao;
        echo json_encode($json);
    }


    public function storeAbate(Request $request)
    {
        $produtoModel = new ProdutoController();
        $loteModel = new LoteController();

        $cart = session()->get('carinhoAbate');

        $valor_total = 0;
        $dataSaida = array();
        $carinho = array();
        $lotes = array();
        $json['success'] = false;
        $json['message'] = null;
        $cont = 0;

        $motivo = $request->motivo;
        $tipo_motivo = $request->tipo_motivo;
        $cliente_id = $request->cliente;
        $desconto = $request->desconto;
        $numero_factura = $request->numero_factura;
        $qntItem=0;


        if (isset($cart)) {

            if (count($cart) > 0) {

                foreach ($cart as $item) {
                    // if ($produtoModel->quantidadeDisponivel($item['id'])->qnt_disponivel >= $item['quantidade']) {
                        // print_r("ei");
                        $lotes = $loteModel->retornaLoteByProdutoVenda($item['id']);

                        if (count($lotes) > 1) {

                            $menorData = strtotime($lotes[0]->data_aquisicao);


                            $controlaQnt = 0;
                            $contagem=1;

                            foreach ($lotes as $lote) {

                                $dataAquisicao = strtotime($lote->data_aquisicao);

                                if ($menorData > $dataAquisicao) {
                                    $menorData = $dataAquisicao;
                                }

                                if ($item['quantidade'] != 0) {

                                    if ($lote->qnt_disponivel > 0 || $contagem == count($lotes)) {

                                        if ((($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel )) && $lote->qnt_disponivel > 0 && $contagem != count($lotes)) {

                                            $controlaQnt = (double) ($qntItem - $qntLote);

                                            if ($controlaQnt < 0) {
                                                $controlaQnt = (double) ($controlaQnt * (-1));
                                            }

                                            $item['quantidade'] -= $qntLote;

                                            $item['custo'] = $qntLote * $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $lote->produto_id,
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $qntLote,
                                                    'preco_unitario' => $item['preco_venda'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $qntLote*$item['preco_unitario'],
                                                    'tipo_motivo' => $tipo_motivo,
                                                    'motivo' => $motivo

                                                ];
                                            $valor_total += $qntLote*$item['preco_unitario'];
                                            // print_r("entrou");
                                            $loteModel->abater($lote->id, $qntLote);
                                        } else {

                                            $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                            $carinho[$cont++] =
                                                [
                                                    'produto_id' => $lote->produto_id,
                                                    'lote_id' => $lote->id,
                                                    'quantidade' => $item['quantidade'],
                                                    'preco_unitario' => $item['preco_venda'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'tipo_motivo' => $tipo_motivo,
                                                    'motivo' => $motivo
                                                ];

                                            $item['quantidade'] -= $item['quantidade'];
                                            $valor_total += $item['quantidade']*$item['preco_unitario'];
                                            $loteModel->abater($lote->id, $qntItem);
                                        }
                                    }
                                }
                                $contagem++;
                            }
                        } else {

                            if(count($lotes)==0){
                                $lotes = $loteModel->retornaUltimoLote($item['id']);
                                $carinho[$cont++] =
                                [
                                    'produto_id' => $lotes->produto_id,
                                    'lote_id' => $lotes->id,
                                    'quantidade' => $item['quantidade'],
                                    'preco_compra' => $lotes->preco_compra,
                                    'preco_unitario' => $item['preco_venda'],//$item['preco_unitario'],
                                    'custo' => $item['quantidade']*$item['preco_unitario'],
                                    'tipo_motivo' => $item['tipo_motivo'],
                                    'motivo' => $item['motivo'],
                                ];

                                $valor_total += $item['quantidade']*$item['preco_unitario'];
                                $loteModel->abater($lotes->id, $item['quantidade']);
                            }else{
                                $carinho[$cont++] =
                                [
                                    'produto_id' => $lotes[0]->produto_id,
                                    'lote_id' => $lotes[0]->id,
                                    'quantidade' => $item['quantidade'],
                                    'preco_unitario' => $item['preco_venda'],//$item['preco_unitario'],
                                    'preco_compra' => $lotes[0]->preco_compra,
                                    'custo' => $item['quantidade']*$item['preco_unitario'],
                                    'tipo_motivo' => $item['tipo_motivo'],
                                    'motivo' => $item['motivo'],
                                ];

                                $valor_total += $item['quantidade']*$item['preco_unitario'];
                                // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
                                $loteModel->abater($lotes[0]->id, $item['quantidade']);
                            }

                            // $carinho[$cont++] =
                            //     [
                            //         'produto_id' => $lotes[0]->produto_id,
                            //         'lote_id' => $lotes[0]->id,
                            //         'quantidade' => $item['quantidade'],
                            //         'preco_unitario' => $lotes[0]->preco_compra,
                            //         'preco_compra' => $lotes[0]->preco_compra,
                            //         'custo' => $item['quantidade']*$lotes[0]->preco_compra,
                            //         'tipo_motivo' => $item['tipo_motivo'],
                            //         'motivo' => $item['motivo'],
                            //     ];

                            // $valor_total += $item['quantidade']*$lotes[0]->preco_compra;
                            // $loteModel->abater($lotes[0]->id, $item['quantidade']);
                        }
                    // } else {

                    //     $json['success'] = false;
                    //     $json['message'] = 'Produto não possui quantidade suficiente !';
                    // }
                }

                $ultima_venda = Saida::latest()->first();
                $numero_factura = @$ultima_venda->id+1 ."/".date("Y");
                $dataSaida = [
                    'cliente_id' => $cliente_id,
                    'numero_factura' => $numero_factura,
                    'data' => date('y-m-d'),
                    'tipo_saida_id' => 2,
                    'desconto' => $desconto,
                    'valor_total' => ($valor_total - $desconto),
                    'activo' => 1
                ];

                if (($saida = Auth::user()->saidas()->create($dataSaida))) {



                    if (($cesto = $saida->saidaItems()->createMany($carinho))) {


                        session()->pull('carinhoAbate', $cart);

                        $json['saida_id'] = $saida->id;
                        $json['success'] = true;
                        $json['message'] = 'Abate registado com sucesso.';

                        $request->session()->flash('success',$json['message']);

//                        foreach ($carinho as $key=>$cabecudo) {
//                        DiarioStock::where([['produto_id','=',$cabecudo['produto_id'] ]])->decrement('qnt_disponivel',$cabecudo['quantidade']);
//
//
//                        }

                    }
                }
//                $produtoModel->actualizarDiarioProduto();
            }
        }
        echo json_encode($json);
    }


    public function  cancelarVenda(Request $request)
    {

        $cart = session()->get('carinho');

        if (!empty($cart)) {
            foreach ($cart AS $item){
                //Log de cancelamento de vendas
                $logsVenda = new LogsVenda(
                    array(
                        "quantidade" => $item['quantidade'],
                        "preco_venda" => $item['preco_unitario'],
                        "preco_compra" => 0,
                        "tipo_log" => $request->tipo,
                        "produto_id" => $item['produto_id'],
                        "user_id" => Auth::user()->id,
                    )
                );
                $logsVenda->save();
                //Logs End ///
            }
            session()->pull('carinho', $cart);
        }

        session_start();
        $_SESSION['cliente_id'] = "";
        $_SESSION['saida_id'] = "";

        echo json_encode(['success' => true]);


    }

    public function  cancelarCredito(Request $request)
    {

        $cart = session()->get('carinho_credito');

        if (!empty($cart)) {
            foreach ($cart AS $item){
                //Log de cancelamento de vendas
                $logsVenda = new LogsVenda(
                    array(
                        "quantidade" => $item['quantidade'],
                        "preco_venda" => $item['preco_unitario'],
                        "preco_compra" => 0,
                        "tipo_log" => $request->tipo,
                        "produto_id" => $item['produto_id'],
                        "user_id" => Auth::user()->id,
                    )
                );
                $logsVenda->save();
                //Logs End ///
            }
            session()->pull('carinho_credito', $cart);
        }

        session_start();
        $_SESSION['cliente_id2'] = "";
        $_SESSION['saida_id2'] = "";

        echo json_encode(['success' => true]);


    }

    public function  cancelarAbate()
    {

        $cart = session()->get('carinhoAbate');

        if (!empty($cart))
            session()->pull('carinhoAbate', $cart);

        echo json_encode(['success' => true]);


    }

    //Cancelar cotacao
    public function  cancelarCotacao()
    {

        $cart = session()->get('carinhoCotacao');

        if (!empty($cart))
            session()->pull('carinhoCotacao', $cart);

        echo json_encode(['success' => true]);


    }

    public function  cancelarCotacao2()
    {

        $cart = session()->get('carinhoCotacaoUpdate');

        if (!empty($cart))
            session()->pull('carinhoCotacaoUpdate', $cart);

        echo json_encode(['success' => true]);


    }
    //end cancelar cotacao


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Saida\Saida  $saida
     * @return \Illuminate\Http\Response
     */
    public function show(Saida $saida)
    {
        $tipos_pagamento = DB::select("SELECT designacao,valor FROM saida_tipo_pagamentos
        INNER JOIN tipo_pagamentos ON tipo_pagamentos.id = saida_tipo_pagamentos.tipo_pagamento_id
        WHERE saida_id = {$saida->id}");

        $devolucao_valor = DB::selectOne("SELECT SUM(devolucao.total) AS totalDevolucao FROM saidas as s
        INNER JOIN saida_items ON saida_items.saida_id = s.id
        LEFT JOIN devolucao ON saida_items.id = devolucao.saida_item_id
        WHERE s.id={$saida->id}");

        return view('saida.show', compact('saida','tipos_pagamento', 'devolucao_valor'));
    }
    public function devolucaoVenda(Request $request)
    {
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



        if($empresa->pacote==1){
            $saidaItems = DB::table('saida_items AS si')
                ->select('si.*', 'produtos.descricao AS produto_descricao', 'devolucao.quantidade AS qntDevolucao')
                // ->join('lotes', 'lotes.id', '=', 'lote_id')
                ->join('produtos', 'produtos.id', '=', 'si.produto_id')
                ->leftjoin('devolucao', 'devolucao.saida_item_id', '=', 'si.id')
                ->where('saida_id', '=', $request->id)
                ->where('si.activo', '!=', '2')->get();
        }else{
            $saidaItems = DB::table('saida_items AS si')
                ->select('si.*', 'produtos.descricao AS produto_descricao', 'devolucao.quantidade AS qntDevolucao')
                ->join('lotes', 'lotes.id', '=', 'lote_id')
                ->join('produtos', 'produtos.id', '=', 'si.produto_id')
                ->leftjoin('devolucao', 'devolucao.saida_item_id', '=', 'si.id')
                ->where('saida_id', '=', $request->id)
                ->where('si.activo', '!=', '2')->get();
        }
            // ->where('devolucao.estado', '=', '1')
        return view('saida.devolucao', compact('saidaItems'));
    }

    public function storedevolucaoVenda(Request $request)
    {
        // dd($request);
        DB::beginTransaction();
        $produtoModel = new ProdutoController();
        $lotes = new LoteController();
        $json['success'] = false;
        $qntProdutos = 0;
        $qntProdutosDevolvidos = 0;
        $valor_total = 0;

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




        $saidaID = DB::selectOne("SELECT saida_id FROM saida_items WHERE id={$request->saida_item_id[0]}")->saida_id;

        $saidax = DB::selectOne("SELECT * FROM saidas WHERE id={$saidaID}");

        $contar = 1;
        $total_dev = 0;
        $ano = date("Y");
        $tipo = $saidax->tipo_saida_id==3 ? 'nota_credito' : 'devolucao';
        $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
        $numero_factura = $saidax->tipo_saida_id==3 ? "NC {$numeracao}/{$ano}" : "DEV {$numeracao}/{$ano}";
        $total_content = count(array_filter($request->quantidade));
        $devol_agregada_id = null;
        $dv_items = array();


        $devol_agregada = array();

        $total_dev = 0;
        $saida_id = 0;
        foreach ($request->quantidade AS $key => $value) {

            if($value > 0){
                $preco_unitario = $request->preco_unitario2[$key];
                $total_dev += (double)$preco_unitario * (double)$request->quantidade[$key];
                $saida_item_id = $request->saida_item_id[$key];
                $saidaItem = SaidaItem::find($saida_item_id);
                $saida_id = $saidaItem->saida_id;
            }

        }
        $devol_agregada = [
            "saida_id" => $saida_id,
            "numero" => $numero_factura,
            "total_devolucao" => $total_dev,
            "user_id" => Auth::user()->id,
            "sessao_id" => $empresa->sessao_id ?? NULL,
            // "created_at" => $saidaItem->created_at,//comentar depois
        ];

        try{
            $devol_agregada_id = DB::table('devolucao_agregada')->insertGetId($devol_agregada);

            foreach ($request->quantidade AS $key => $value) {

                if($value > 0){
                    if($empresa->pacote==1){
                        // $lote_id = $request->lote_id[$key];
                        // $saida_item_id = $request->saida_item_id[$key];
                        // $saidaItem = SaidaItem::find($saida_item_id);
                        $quantidade = (double)$request->quantidade[$key];
                        // $saida_id = $saidaItem->saida_id;
                        $saidaItem = SaidaItem::find($request->saida_item_id[$key]);
                        $saida_id = $saidaItem->saida_id;

                        $devol = [
                            "devolucao_agregada_id" => $devol_agregada_id,
                            "saida_item_id" => $request->saida_item_id[$key],
                            "quantidade" => (double)$request->quantidade[$key],
                            "preco_unitario" => str_replace(',', '', $request->preco_unitario[$key]),
                            "preco_compra" => 0,//$lotes->precoCompraLote($lote_id),
                            "total" => $preco_unitario * (double)$request->quantidade[$key],
                            "produto_id" => $request->produto_id[$key],
                            "data_hora" => $saidaItem->created_at,
                            "user_id" => Auth::user()->id,
                            "motivo_devolucao" => $request->motivo_devolucao,
                            // "created_at" => $saidaItem->created_at,//comentar depois
                        ];

                        $json['devolucao_agregada_id'] = $devol_agregada_id;
                        $json['saida_id'] = $saida_id;

                        if(DB::table('devolucao')->insert($devol)){
                            if ($saidaItem->quantidade == $quantidade) {
                                $qntProdutosDevolvidos++;
                            }

                            // $lote = Lote::find($lote_id);
                            // $quantidade_devol = (double)$lote->qnt_disponivel+(double)$request->quantidade[$key];

                            // DB::table('lotes')->where('id',$lote_id)->update(['qnt_disponivel'=>$quantidade_devol,'activo'=>'1']);

                            $json['success'] = true;
                        }
                        $valor_total += str_replace(',', '', $request->preco_unitario[$key]) * ((double)$request->quantidade_total[$key] - (double)$request->quantidade[$key]);
                        $qntProdutos++;
                        $contar++;
                    }else{
                        $lote_id = $request->lote_id[$key];
                        // $saida_item_id = $request->saida_item_id[$key];
                        // $saidaItem = SaidaItem::find($saida_item_id);
                        $quantidade = (double)$request->quantidade[$key];
                        // $saida_id = $saidaItem->saida_id;
                        $saidaItem = SaidaItem::find($request->saida_item_id[$key]);
                        $saida_id = $saidaItem->saida_id;

                        $devol = [
                            "devolucao_agregada_id" => $devol_agregada_id,
                            "saida_item_id" => $request->saida_item_id[$key],
                            "quantidade" => (double)$request->quantidade[$key],
                            "preco_unitario" => str_replace(',', '', $request->preco_unitario[$key]),
                            "preco_compra" => $lotes->precoCompraLote($lote_id),
                            "total" => $preco_unitario * (double)$request->quantidade[$key],
                            "produto_id" => $request->produto_id[$key],
                            "data_hora" => $saidaItem->created_at,
                            "user_id" => Auth::user()->id,
                            "motivo_devolucao" => $request->motivo_devolucao,
                            // "created_at" => $saidaItem->created_at,//comentar depois
                        ];

                        $json['devolucao_agregada_id'] = $devol_agregada_id;
                        $json['saida_id'] = $saida_id;

                        if(DB::table('devolucao')->insert($devol)){
                            if ($saidaItem->quantidade == $quantidade) {
                                $qntProdutosDevolvidos++;
                            }

                            $lote = Lote::find($lote_id);
                            $quantidade_devol = (double)$lote->qnt_disponivel+(double)$request->quantidade[$key];

                            DB::table('lotes')->where('id',$lote_id)->update(['qnt_disponivel'=>$quantidade_devol,'activo'=>'1']);

                            $json['success'] = true;
                        }
                        $valor_total += str_replace(',', '', $request->preco_unitario[$key]) * ((double)$request->quantidade_total[$key] - (double)$request->quantidade[$key]);
                        $qntProdutos++;
                        $contar++;
                    }

                }
            }
            DB::table('saidas')->where('id',$saida_id)->update(['activo'=>'3']);
            $this->updateInvoiceNumber($ano, $tipo);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            //throw $th;
            $json['success'] = false;
            $json['message'] = "Error ".$e;
        }
//        print_r($contar);
        $this->updateInvoiceNumber($ano, $tipo);

        echo json_encode($json);
    }


    // public function index_devolucao(){
    //     $clientes =  Cliente::where('activo', '=', '1')->get();
    //     $users =  User::where('activo', '=', '1')->get();
    //     return view('devolucao.index',compact('users','clientes'));
    // }

    // public function fetch_devolucao()
    // {
    //     $limite = $_POST['limite'];
    //     $cont = 0;
    //     $data_inicio = $_POST['data_inicio'];
    //     $data_fim = $_POST['data_fim'];

    //     $dataParams[$cont++] = ['devolucao_agregada.created_at', '>=', $data_inicio . " 00:00:00"];
    //     $dataParams[$cont++] = ['devolucao_agregada.created_at', '<=', $data_fim . " 23:59:59"];
    //     if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
    //         $codigo = $_POST['codigo'];
    //         $dataParams[$cont++] = ['devolucao_agregada.numero', 'like', "%$codigo%"];
    //     }
    //     if (isset($_POST['user']) && !empty($_POST['user'])) {
    //         $user = $_POST['user'];
    //         $dataParams[$cont++] = ['devolucao_agregada.user_id', '=', $user];
    //     }
    //     if (isset($_POST['produto_id']) && !empty($_POST['produto_id'])) {
    //         $produto_id = $_POST['produto_id'];
    //         $dataParams[$cont++] = ['devolucao.produto_id', '=', $produto_id];
    //     }
    //     if (isset($_POST['cliente_id']) && !empty($_POST['cliente_id'])) {
    //         $cliente_id = $_POST['cliente_id'];
    //         $dataParams[$cont++] = ['devolucao.cliente_id', '=', $cliente_id];
    //     }

    //     if($limite == ""){
    //         $devolucoes = DB::table('devolucao_agregada')
    //             ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
    //             ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
    //             ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
    //             ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
    //             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
    //             ->select('devolucao_agregada.id as id', 'devolucao_agregada.saida_id as saida_id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
    //                 'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente', 'devolucao_agregada.created_at as data_registo',
    //                 DB::raw('COUNT(produto_id) as total_produtos'))
    //             ->where($dataParams)
    //             ->groupBy('devolucao_agregada.id')
    //             ->orderBy('devolucao_agregada.created_at', 'DESC')
    //             ->paginate(999999999999);
    //     }elseif($limite != ""){
    //         $devolucoes = DB::table('devolucao_agregada')
    //             ->leftJoin('devolucao', 'devolucao_agregada.id',  '=', 'devolucao.devolucao_agregada_id')
    //             ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
    //             ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
    //             ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
    //             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
    //             ->select('devolucao_agregada.id as id', 'devolucao_agregada.saida_id as saida_id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
    //                 'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente', 'devolucao_agregada.created_at as data_registo',
    //                 DB::raw('COUNT(produto_id) as total_produtos'))
    //             ->where($dataParams)
    //             ->groupBy('devolucao_agregada.id')
    //             ->orderBy('devolucao_agregada.created_at', 'DESC')
    //             ->paginate((int)$limite);
    //     }

    //     return view('devolucao.table', compact('devolucoes','limite'));
    // }

    // public function show_devolucao($devolucao_agregada_id){
    //     $dev = DB::table('devolucao_agregada')->select('saida_id','numero')->where(['id'=>$devolucao_agregada_id])->first();


    //     $numero = $dev->numero;
    //     return view('devolucao.show',compact('devolucao_agregada_id','numero','dev'));
    // }

//     public function fetch_show(){
//         $devolucao_agregada_id = $_POST['id'];
//         $dev = DB::table('devolucao_agregada')->select('saida_id','numero')->where(['id'=>$devolucao_agregada_id])->first();

// //        $numero = $dev->numero;

//         $saidas = DB::table('saidas')
//             ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
//             ->join('produtos', 'produtos.id', '=', 'saida_items.produto_id')
//             ->join('users', 'users.id', '=', 'saidas.user_id')
//             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
//             ->select('saidas.id as id', 'saida_items.id AS saida_items_id', 'saidas.numero_factura as numero_factura', 'users.name as user',
//                 'saidas.created_at as data_criacao',
//                 'produtos.descricao as produto', DB::raw('SUM(saida_items.quantidade) as quantidade'), 'saida_items.preco_unitario as preco',
//                 DB::raw('SUM(quantidade*preco_unitario) as total'))
//             ->where(['saidas.id'=>$dev->saida_id])
//             ->groupBy('produto_id')
//             ->get();

//         $saida = DB::table('saidas')
//             ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
//             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
//             ->join('users', 'users.id', '=', 'saidas.user_id')
//             ->select('users.name as user','numero_factura','clientes.nome as cliente',DB::raw('SUM(quantidade*preco_unitario) as total'),DB::raw('COUNT(produto_id) as total_produtos'))
//             ->where(['saidas.id'=>$dev->saida_id])->first();



//         $devolucao = DB::table('devolucao_agregada')
//             ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
//             ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
//             ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
//             ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
//             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
//             ->select('devolucao.saida_item_id as saida_item_id','devolucao_agregada.id as id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
//                 'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente',
//                 'produtos.descricao as produto', 'devolucao.preco_unitario as preco',
//                 DB::raw('SUM(devolucao.quantidade) as quantidade'),DB::raw('SUM(devolucao.preco_unitario*devolucao.quantidade) as total'),'motivo_devolucao')
//             ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
//             ->groupBy('devolucao.produto_id')
//             ->get();

//         $devol = DB::table('devolucao_agregada')
//             ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
//             ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
//             ->select('users.name as user','numero',DB::raw('SUM(quantidade*preco_unitario) as total'),DB::raw('COUNT(produto_id) as total_produtos'),'motivo_devolucao')
//             ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
//             ->first();

//         $saidaItemsId = array();
//         foreach ($devolucao as $item){
//             array_push($saidaItemsId, $item->saida_item_id);
//         }

//         return view('devolucao.table-show',compact('saidas', 'saidaItemsId', 'saida','devolucao', 'devol'));
//     }

//     public function recibo_devolucao(){
//         $json['success'] = true;
//         $empresa = DB::table('empresa')->first();

//         $devolucao_agregada_id = $_POST['id'];
//         $saida_id = $_POST['saida_id'];
//         $dev = DB::table('devolucao_agregada')->select('saida_id','numero')->first();

// //        $numero = $dev->numero;

//         $saidas = DB::table('saidas')
//             ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
//             ->join('produtos', 'produtos.id', '=', 'saida_items.produto_id')
//             ->join('users', 'users.id', '=', 'saidas.user_id')
//             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
//             ->select('saidas.id as id', 'saida_items.id AS saida_items_id', 'saidas.numero_factura as numero_factura', 'users.name as user',
//                 'saidas.created_at as data_criacao',
//                 'produtos.descricao as produto', DB::raw('SUM(saida_items.quantidade) as quantidade'), 'saida_items.preco_unitario as preco',
//                 DB::raw('SUM(quantidade*preco_unitario) as total'))
//             ->where(['saidas.id'=>$saida_id])
//             ->groupBy('produto_id')
//             ->get();

//         $saida = DB::table('saidas')
//             ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
//             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
//             ->join('users', 'users.id', '=', 'saidas.user_id')
//             ->select('users.name as user','numero_factura','clientes.nome as cliente_nome','clientes.nuit as cliente_nuit'
//                 ,'clientes.endereco as cliente_endereco','clientes.contacto as cliente_contacto', 'tipo_saida_id',
//                 DB::raw('SUM(quantidade*preco_unitario) as total'),
//                 DB::raw('COUNT(produto_id) as total_produtos'))
//             ->where(['saidas.id'=>$saida_id])->first();



//         $devolucao = DB::table('devolucao_agregada')
//             ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
//             ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
//             ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
//             ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
//             ->join('saida_items', 'saida_items.id', '=', 'devolucao.saida_item_id')
//             ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
//             ->select('devolucao.saida_item_id as saida_item_id','devolucao_agregada.id as id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
//                 'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente',
//                 'produtos.descricao as produto', 'devolucao.preco_unitario as preco', 'saida_items.iva as iva',
//                 DB::raw('SUM(devolucao.quantidade) as quantidade'),DB::raw('SUM(devolucao.preco_unitario*devolucao.quantidade) as total'))
//             ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
//             ->groupBy('devolucao.produto_id')
//             ->get();

//         $saidaItemsId = array();
//         foreach ($devolucao as $item){
//             array_push($saidaItemsId, $item->saida_item_id);
//         }

//         $devol = DB::table('devolucao_agregada')
//             ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
//             ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
//             ->select('users.name as user','numero',DB::raw('SUM(quantidade*preco_unitario) as total')
//                 ,'devolucao_agregada.created_at as created_at',DB::raw('COUNT(produto_id) as total_produtos'))
//             ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
//             ->first();


//         session_start();
//         $_COOKIE['empresa'] = json_encode($empresa);
//         $_SESSION['saida'] = json_encode($saida);
//         $_SESSION['saidaItems'] = json_encode($saidas);
//         $_SESSION['devolucao'] = json_encode($devol);
//         $_SESSION['devolucaoItems'] = json_encode($devolucao);
//         $_SESSION['user'] = json_encode(auth()->user()->name);
//         $_SESSION['saidaItemsId'] = json_encode($saidaItemsId);


//         echo json_encode($json);

// //        return view('devolucao.table-show',compact('saidas', 'saida','devolucao', 'devol'));
//     }

    public function index_devolucao(){
        $clientes =  Cliente::where('activo', '=', '1')->get();
        $tipo_saida = TipoSaida::where('activo', '=', '1')->get();
        $users =  User::where('activo', '=', '1')->get();
        return view('devolucao.index',compact('users','clientes', 'tipo_saida'));
    }

    public function fetch_devolucao()
    {
        $limite = $_POST['limite'];
        $cont = 0;
        $data_inicio = $_POST['data_inicio'];
        $data_fim = $_POST['data_fim'];

        $dataParams[$cont++] = ['devolucao_agregada.created_at', '>=', $data_inicio . " 00:00:00"];
        $dataParams[$cont++] = ['devolucao_agregada.created_at', '<=', $data_fim . " 23:59:59"];
        if (isset($_POST['codigo']) && !empty($_POST['codigo'])) {
            $codigo = $_POST['codigo'];
            $dataParams[$cont++] = ['devolucao_agregada.numero', 'like', "%$codigo%"];
        }
        if (isset($_POST['user']) && !empty($_POST['user'])) {
            $user = $_POST['user'];
            $dataParams[$cont++] = ['devolucao_agregada.user_id', '=', $user];
        }
        if (isset($_POST['produto_id']) && !empty($_POST['produto_id'])) {
            $produto_id = $_POST['produto_id'];
            $dataParams[$cont++] = ['devolucao.produto_id', '=', $produto_id];
        }
        if (isset($_POST['cliente_id']) && !empty($_POST['cliente_id'])) {
            $cliente_id = $_POST['cliente_id'];
            $dataParams[$cont++] = ['saidas.cliente_id', '=', $cliente_id];
        }
        if (isset($_POST['tipo_saida_id']) && !empty($_POST['tipo_saida_id'])) {
            $tipo_saida_id = $_POST['tipo_saida_id'];
            $dataParams[$cont++] = ['saidas.tipo_saida_id', '=', $tipo_saida_id];
        }
        $dataParams[$cont++] = ['devolucao_agregada.estado', '=', '1'];
        $dataParams[$cont++] = ['devolucao.estado', '=', '1'];


        if($limite == ""){
            $devolucoes = DB::table('devolucao_agregada')
                ->leftJoin('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
                ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
                ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
                ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
                ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
                ->select('devolucao_agregada.id as id', 'devolucao_agregada.saida_id as saida_id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
                    'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente', 'devolucao_agregada.created_at as data_registo',
                    DB::raw('COUNT(produto_id) as total_produtos'))
                ->where($dataParams)
                ->groupBy('devolucao_agregada.id')
                ->orderBy('devolucao_agregada.created_at', 'DESC')
                ->paginate(999999999999);
        }elseif($limite != ""){
            $devolucoes = DB::table('devolucao_agregada')
                ->leftJoin('devolucao', 'devolucao_agregada.id',  '=', 'devolucao.devolucao_agregada_id')
                ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
                ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
                ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
                ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
                ->select('devolucao_agregada.id as id', 'devolucao_agregada.saida_id as saida_id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
                    'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente', 'devolucao_agregada.created_at as data_registo',
                    DB::raw('COUNT(produto_id) as total_produtos'))
                ->where($dataParams)
                ->groupBy('devolucao_agregada.id')
                ->orderBy('devolucao_agregada.created_at', 'DESC')
                ->paginate((int)$limite);
        }

        return view('devolucao.table', compact('devolucoes','limite'));
    }

    public function show_devolucao($devolucao_agregada_id){
        $dev = DB::table('devolucao_agregada')->select('saida_id','numero')->where(['id'=>$devolucao_agregada_id])->first();


        $numero = $dev->numero;
        return view('devolucao.show',compact('devolucao_agregada_id','numero','dev'));
    }

    public function fetch_show(){
        $devolucao_agregada_id = $_POST['id'];
        $dev = DB::table('devolucao_agregada')->select('saida_id','numero')->where(['id'=>$devolucao_agregada_id])->first();

//        $numero = $dev->numero;

        $saidas = DB::table('saidas')
            ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
            ->join('produtos', 'produtos.id', '=', 'saida_items.produto_id')
            ->join('users', 'users.id', '=', 'saidas.user_id')
            ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
            ->select('saidas.id as id', 'saida_items.id AS saida_items_id', 'saidas.numero_factura as numero_factura', 'users.name as user',
                'saidas.created_at as data_criacao',
                'produtos.descricao as produto', 'saida_items.quantidade as quantidade', 'saida_items.preco_unitario as preco',
                DB::raw('SUM(quantidade*preco_unitario) as total'))
            ->where(['saidas.id'=>$dev->saida_id])
            ->groupBy('produto_id')
            ->get();

        $saida = DB::table('saidas')
            ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
            ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
            ->join('users', 'users.id', '=', 'saidas.user_id')
            ->select('users.name as user','numero_factura','clientes.nome as cliente',DB::raw('SUM(quantidade*preco_unitario) as total'),DB::raw('COUNT(produto_id) as total_produtos'))
            ->where(['saidas.id'=>$dev->saida_id])->first();



        $devolucao = DB::table('devolucao_agregada')
            ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
            ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
            ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
            ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
            ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
            ->select('devolucao.saida_item_id as saida_item_id','devolucao_agregada.id as id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
                'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente',
                'produtos.descricao as produto', 'devolucao.preco_unitario as preco',
                'devolucao.quantidade as quantidade',DB::raw('SUM(devolucao.preco_unitario*devolucao.quantidade) as total'),'motivo_devolucao')
            ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
            ->groupBy('devolucao.produto_id')
            ->get();

        $devol = DB::table('devolucao_agregada')
            ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
            ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
            ->select('users.name as user','numero',DB::raw('SUM(quantidade*preco_unitario) as total'),DB::raw('COUNT(produto_id) as total_produtos'),'motivo_devolucao')
            ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
            ->first();

        $saidaItemsId = array();
        foreach ($devolucao as $item){
            array_push($saidaItemsId, $item->saida_item_id);
        }

        return view('devolucao.table-show',compact('saidas', 'saidaItemsId', 'saida','devolucao', 'devol'));
    }

    public function recibo_devolucao(){
        $json['success'] = true;
        $empresa = DB::table('empresa')->first();

        $devolucao_agregada_id = $_POST['id'];
        $saida_id = $_POST['saida_id'];
        $dev = DB::table('devolucao_agregada')->select('saida_id','numero')->first();

//        $numero = $dev->numero;

        $saidas = DB::table('saidas')
            ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
            ->join('produtos', 'produtos.id', '=', 'saida_items.produto_id')
            ->join('users', 'users.id', '=', 'saidas.user_id')
            ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
            ->select('saidas.id as id', 'saida_items.id AS saida_items_id', 'saidas.numero_factura as numero_factura', 'users.name as user',
                'saidas.created_at as data_criacao',
                'produtos.descricao as produto', 'saida_items.quantidade as quantidade', 'saida_items.preco_unitario as preco',
                DB::raw('SUM(quantidade*preco_unitario) as total'))
            ->where(['saidas.id'=>$saida_id])
            ->groupBy('produto_id')
            ->get();

        $saida = DB::table('saidas')
            ->join('saida_items', 'saida_items.saida_id', '=', 'saidas.id')
            ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
            ->join('users', 'users.id', '=', 'saidas.user_id')
            ->select('users.name as user','numero_factura','clientes.nome as cliente_nome','clientes.nuit as cliente_nuit'
                ,'clientes.endereco as cliente_endereco','clientes.contacto as cliente_contacto', 'tipo_saida_id',
                DB::raw('SUM(quantidade*preco_unitario) as total'),
                DB::raw('COUNT(produto_id) as total_produtos'))
            ->where(['saidas.id'=>$saida_id])->first();



        $devolucao = DB::table('devolucao_agregada')
            ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
            ->join('produtos', 'produtos.id', '=', 'devolucao.produto_id')
            ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
            ->join('saidas', 'saidas.id', '=', 'devolucao_agregada.saida_id')
            ->join('saida_items', 'saida_items.id', '=', 'devolucao.saida_item_id')
            ->join('clientes', 'clientes.id', '=', 'saidas.cliente_id')
            ->select('devolucao.saida_item_id as saida_item_id','devolucao_agregada.id as id', 'devolucao.id AS devolucao_id', 'devolucao_agregada.numero as codigo', 'users.name as user',
                'total_devolucao', 'devolucao_agregada.created_at as data_criacao', 'clientes.nome as cliente',
                'produtos.descricao as produto', 'devolucao.preco_unitario as preco', 'saida_items.iva as iva',
                'devolucao.quantidade as quantidade',DB::raw('SUM(devolucao.preco_unitario*devolucao.quantidade) as total'))
            ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
            ->groupBy('devolucao.produto_id')
            ->get();

        $saidaItemsId = array();
        foreach ($devolucao as $item){
            array_push($saidaItemsId, $item->saida_item_id);
        }

        $devol = DB::table('devolucao_agregada')
            ->join('devolucao', 'devolucao_agregada.id', '=', 'devolucao.devolucao_agregada_id')
            ->join('users', 'users.id', '=', 'devolucao_agregada.user_id')
            ->select('users.name as user','numero',DB::raw('SUM(quantidade*preco_unitario) as total')
                ,'devolucao_agregada.created_at as created_at',DB::raw('COUNT(produto_id) as total_produtos'))
            ->where(['devolucao_agregada.id'=>$devolucao_agregada_id])
            ->first();


        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['saida'] = json_encode($saida);
        $_SESSION['saidaItems'] = json_encode($saidas);
        $_SESSION['devolucao'] = json_encode($devol);
        $_SESSION['devolucaoItems'] = json_encode($devolucao);
        $_SESSION['user'] = json_encode(auth()->user()->name);
        $_SESSION['saidaItemsId'] = json_encode($saidaItemsId);


        echo json_encode($json);

//        return view('devolucao.table-show',compact('saidas', 'saida','devolucao', 'devol'));
    }


    public function show2(Saida $saida)
    {
        $id = $saida->id;
        $motivo = DB::selectOne("SELECT tipo_motivo FROM saida_items WHERE saida_id={$id} AND activo!='2'");

        $valor_total = DB::selectOne("SELECT SUM(preco_compra * quantidade) as valor_total
        FROM saidas
        INNER JOIN saida_items ON saida_items.saida_id=saidas.id
        INNER JOIN produtos ON produtos.id=saida_items.produto_id
        WHERE tipo_saida_id = 2 AND saida_items.saida_id = {$id}
        AND saidas.activo = '1' AND saida_items.activo = '1' ");

        $saidaItems = DB::select("SELECT saida_items.id as id,SUM(saida_items.quantidade) AS quantidade,saida_items.produto_id as produto_id,
        produtos.descricao as produto_descricao, saida_items.preco_compra as preco_unitario, saida_items.preco_unitario as preco_venda, SUM(custo) AS custo,
        SUM(saida_items.quantidade * saida_items.preco_compra) AS total, motivo,
        tipo_motivo, motivo, saida_items.lote_id as lote_id, saida_items.activo as activo, produtos.codigo,
        entradas.fornecedor_ref as fornecedor_ref, fornecedors.nome as fornecedor, entradas.data_aquisicao as data_aquisicao
        FROM saidas
        INNER JOIN saida_items ON saida_items.saida_id=saidas.id
        INNER JOIN produtos ON produtos.id=saida_items.produto_id
        INNER JOIN lotes ON lotes.id=saida_items.lote_id
        INNER JOIN entradas ON entradas.id=lotes.entrada_id
        INNER JOIN fornecedors ON fornecedors.id=entradas.fornecedor_id
        WHERE tipo_saida_id = 2 AND saida_items.saida_id = {$id}
        AND saidas.activo = '1' AND saida_items.activo = '1' GROUP BY entradas.fornecedor_id, saida_items.id ");

        // dd($saidaItems);
        return view('saida.showAbate', compact('saida','saidaItems','motivo','valor_total'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Saida\Saida  $saida
     * @return \Illuminate\Http\Response
     */
    public function edit(Saida $saida)
    {

        // dd($saida);

        return view('saida.edit', compact('outgoing'));
    }

    public function editCotacao($saida)
    {
        $tipoClientes = TipoCliente::all();
        $clientes = Cliente::all();
        $cotacao = DB::selectOne("SELECT saidas.id as saida_id, clientes.nome as cliente, cliente_id,
        validade_cotacao
        FROM saidas
        INNER JOIN clientes ON clientes.id = saidas.cliente_id
        INNER JOIN saida_items ON saida_items.saida_id = saidas.id
        WHERE saidas.id = {$saida}
        ");
        return view('saida.update_cotacao', compact('cotacao','tipoClientes','clientes'));
    }

    public function apagarCotacao($saida)
    {
        $json['success'] = false;

        // if(DB::table('saidas')->where('id',$saida)->update(['activo'=>'0'])){
        //     $json['success'] = true;
        // }
        if(DB::table('cotacao')->where('id',$saida)->update(['activo'=>'0'])){
            $json['success'] = true;
        }
        echo json_encode($json);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Saida\Saida  $saida
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Saida $saida)
    {
        $produtoModel = new ProdutoController();
        $data = $request->validate([]);
        $saida->update($data);
//        $produtoModel->actualizarDiarioProduto();
        return redirect("saida/" . $saida->id);
    }

    public function listarCarinho()
    {
        if(session()->get('carinho')>0){
            $carinho = array_reverse(session()->get('carinho'));
        }else{
            $carinho = session()->get('carinho');
        }
        $pagamentos = TipoPagamento::where('is_active', '=', 1)->get();
        $forma_pagamento = true;
        return view('saida.fetchCarinho', compact('carinho', 'pagamentos','forma_pagamento'));
    }

    public function listarCarinhoCredito()
    {
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



        if(session()->get('carinho_credito')>0){
            $carinho_credito = array_reverse(session()->get('carinho_credito'));
        }else{
            $carinho_credito = session()->get('carinho_credito');
        }
        $pagamentos = TipoPagamento::where('is_active', '=', 1)->get();
        $forma_pagamento = false;

        if($empresa->fonte == 1){
            return view('saida.fetchCarinhoCreditoImportadora', compact('carinho_credito', 'pagamentos', 'forma_pagamento'));
        }else{
            return view('saida.fetchCarinhoCredito', compact('carinho_credito', 'pagamentos', 'forma_pagamento'));
        }


    }

    public function listarCarinhoAbate()
    {
        $saida_id = $_POST['saida_id'];

        $carinhoAbate = DB::select("SELECT saida_items.id as saida_items_id, saida_items.lote_id as lote_id, produtos.id as id,
        produtos.descricao as nome, quantidade, preco_unitario, saida_items.preco_compra, tipo_motivo, motivo,
        entradas.fornecedor_ref as fornecedor_ref, lotes.id as lote, entradas.data_aquisicao as data_aquisicao, lotes.data_validade, saida_items.created_at as data_criacao
        FROM saida_items
        INNER JOIN saidas ON saidas.id = saida_items.saida_id
        INNER JOIN produtos ON produtos.id = saida_items.produto_id
        INNER JOIN lotes ON lotes.id=saida_items.lote_id
        INNER JOIN entradas ON entradas.id=lotes.entrada_id
        WHERE saida_items.saida_id = {$saida_id} AND saida_items.activo = '1' ORDER BY saida_items.created_at DESC
        ");

//        $carinhoAbate = session()->get('carinhoAbate');
        $pagamentos = TipoPagamento::all();
        return view('saida.fetchCarinhoAbate', compact('carinhoAbate', 'pagamentos'));
    }

    //carrinho cotacao
    public function listarCarinhoCotacao()
    {
        if(session()->get('carinhoCotacao')>0){
            $carinhoCotacao = array_reverse(session()->get('carinhoCotacao'));
        }else{
            $carinhoCotacao = session()->get('carinhoCotacao');
        }
        $pagamentos = TipoPagamento::all();
        $forma_pagamento = true;
        return view('saida.fetchCarinhoCotacao', compact('carinhoCotacao', 'pagamentos','forma_pagamento'));
    }

    public function listarCarinhoCotacaoUpdate()
    {
        if(session()->get('carinhoCotacaoUpdate')>0){
            $carinhoCotacao = array_reverse(session()->get('carinhoCotacaoUpdate'));
        }else{
            $carinhoCotacao = session()->get('carinhoCotacaoUpdate');
        }
        $pagamentos = TipoPagamento::all();
        $forma_pagamento = true;
        return view('saida.fetchCarinhoCotacaoUpdate', compact('carinhoCotacao', 'pagamentos','forma_pagamento'));
    }

    public function listarDividaClientes(Request $request)
    {
//        $dataInicio = null;
//        $dataFim = null;

        $sqlAdd = null;
        if(!empty($request->data) && !empty($request->data2)){
            $dataInicio = $request->data;
            $dataFim = $request->data2;
            $sqlAdd .= " AND data BETWEEN '{$dataInicio}' AND '{$dataFim}' ";
        }
        if(!empty($request->cliente_id)){
            $cliente_id = $request->cliente_id;
            $sqlAdd .= " AND s.cliente_id = {$cliente_id} ";
        }
        $sql = "
        SELECT s.id as saida_id, s.cliente_id as cliente_id, clientes.nome,
        SUM(s.valor_total) AS total,
        SUM(s.valor_pago) AS total_pago,
        SUM(s.valor_remanescente) AS total_remanescente,
        SUM(s.desconto) AS desconto,
        COUNT(s.id) AS movimentos
        FROM saidas as s
        INNER JOIN clientes ON clientes.id=s.cliente_id
        WHERE s.tipo_saida_id=3
        AND s.activo='1' $sqlAdd
        GROUP BY s.cliente_id
        ";
        // $sql = "
        // SELECT s.id as saida_id, s.cliente_id as cliente_id, clientes.nome,
        // SUM((quantidade * preco_unitario)-IFNULL(s.desconto,0)) AS total,
        // COUNT(s.id) AS movimentos
        // FROM saidas as s
        // INNER JOIN clientes ON clientes.id=s.cliente_id
        // INNER JOIN saida_items ON saida_items.saida_id=s.id
        // WHERE s.tipo_saida_id=3
        // AND s.activo='1' $sqlAdd
        // GROUP BY s.cliente_id
        // ";
        //SUM(saidas.valor_remanescente) totalDivida,
        $clientesDi = DB::select($sql);

        $i = 0;
        $clientesDivida = array();
        foreach ($clientesDi as $divida){

            $total_pago_cliente = DB::selectOne("SELECT IF((devolucao.quantidade*devolucao.preco_unitario)>0,SUM(DISTINCT s.valor_pago),0) as total_pago FROM saidas as s
            INNER JOIN saida_items ON saida_items.saida_id = s.id
            LEFT JOIN devolucao ON saida_items.id = devolucao.saida_item_id
            WHERE s.cliente_id={$divida->cliente_id} AND s.tipo_saida_id=3 AND s.activo='1' $sqlAdd");

            // print_r("SELECT SUM(DISTINCT s.valor_pago) as total_pago FROM saidas as s
            // INNER JOIN saida_items ON saida_items.saida_id = s.id
            // LEFT JOIN devolucao ON saida_items.id = devolucao.saida_item_id
            // WHERE s.cliente_id={$divida->cliente_id} AND s.tipo_saida_id=3 AND s.activo='1' $sqlAdd");

            // $saida_items = DB::select("SELECT saida_items.*
            // FROM saida_items
            // WHERE saida_items.saida_id ='.$divida->saida_id.' AND saida_items.activo='1'");

            // $total_ = 0;
            // foreach ($saida_items as $item){
            //     $devolucao = DB::selectOne("SELECT IFNULL(SUM(devolucao.preco_unitario*devolucao.quantidade),0) AS total_
            //     FROM devolucao
            //     WHERE devolucao.saida_item_id ={$item->id} AND devolucao.estado='1'");
            //     $total_
            //     // $devolucao = DB::selectOne("SELECT SUM(devolucao.preco_unitario*devolucao.quantidade) AS total_devolucao
            //     // FROM devolucao
            //     // WHERE devolucao.saida_item_id ={$item->id} AND devolucao.estado='1'");
            // $devolucao = DB::selectOne('SELECT SUM(devolucao.preco_unitario*devolucao.quantidade) AS total_devolucao
            // FROM devolucao
            // INNER JOIN saida_items ON saida_items.id=devolucao.saida_item_id
            // INNER JOIN saidas as s ON saida_items.saida_id=s.id
            // WHERE s.cliente_id ='.$divida->cliente_id. ' '.$sqlAdd);

            //     $total_devolucao += $devolucao->total_devolucao;
            // }
        //     print_r('SELECT SUM(devolucao.preco_unitario*devolucao.quantidade) AS total_devolucao
        //     FROM devolucao
        //     INNER JOIN saida_items ON saida_items.id=devolucao.saida_item_id
        //    INNER JOIN saidas as s ON saida_items.saida_id=s.id
        //    WHERE s.cliente_id ='.$divida->cliente_id. ' '.$sqlAdd);

            // $devolucao = DB::selectOne('SELECT IFNULL(SUM(devolucao.preco_unitario*devolucao.quantidade),0) AS total_devolucao
         	// FROM devolucao
         	// INNER JOIN saida_items ON saida_items.id=devolucao.saida_item_id
            // INNER JOIN saidas as s ON saida_items.saida_id=s.id
            // WHERE s.cliente_id ='.$divida->cliente_id. ' '.$sqlAdd);

        //     print_r('SELECT IFNULL(SUM(devolucao.preco_unitario*devolucao.quantidade),0) AS total_devolucao
        //     FROM devolucao
        //     INNER JOIN saida_items ON saida_items.id=devolucao.saida_item_id
        //    INNER JOIN saidas as s ON saida_items.saida_id=s.id
        //    WHERE s.cliente_id ='.$divida->cliente_id. ' '.$sqlAdd);

            // if(($divida->total - $devolucao->total_devolucao) > 0){
            //     $clientesDivida[$i]['cliente_id'] = $divida->cliente_id;
            //     $clientesDivida[$i]['nome'] = $divida->nome;
            //     $clientesDivida[$i]['movimentos'] = $divida->movimentos;
            //     $clientesDivida[$i]['total'] = ((double)$divida->total-(double)$devolucao->total_devolucao)-$divida->desconto;
            //     $clientesDivida[$i]['total_pago'] = (double)$devolucao->total_devolucao>0 ? (double)$divida->total_pago-(double)$total_pago_cliente->total_pago : (double)$divida->total_pago;
            //     $clientesDivida[$i]['totalDivida'] = ((double)$divida->total-(double)$devolucao->total_devolucao)-((double)$divida->total_pago-(double)$total_pago_cliente->total_pago);
            //     $clientesDivida[$i]['total_devolucao'] = (double)$devolucao->total_devolucao-$divida->desconto;
            //     $clientesDivida[$i]['devolvida'] = (double)$divida->total==(double)$devolucao->total_devolucao ? '1' : '0';
            //     $i++;
            // }
            // if(($divida->total - $devolucao->total_devolucao) > 0){
                $clientesDivida[$i]['cliente_id'] = $divida->cliente_id;
                $clientesDivida[$i]['nome'] = $divida->nome;
                $clientesDivida[$i]['movimentos'] = $divida->movimentos;
                $clientesDivida[$i]['total'] = (double)$divida->total-(double)$divida->desconto;
                $clientesDivida[$i]['total_pago'] = (double)$divida->total_pago;
                $clientesDivida[$i]['totalDivida'] = (double)$divida->total-(double)$divida->total_pago;
                $clientesDivida[$i]['total_devolucao'] = 0;
                $clientesDivida[$i]['devolvida'] = '0';
                $i++;
            // }
        }

        // dd($clientesDivida);
        $dataInicio = $request->data;
        $dataFim = $request->data2;
        $cliente_id = $request->cliente_id;
        return view('saida.fetchClientesDivida', compact('clientesDivida', 'dataInicio','dataFim'));
    }

    public function detalhesDividaCliente(Request $request){
        $cliente_id = $request->cliente_id;
        // print_r($cliente_id);
        $sqlAdd = null;
        if(!empty($request->data) && !empty($request->data2)){
            $dataInicio = $request->data;
            $dataFim = $request->data2;
            $sqlAdd .= " AND data BETWEEN '{$dataInicio}' AND '{$dataFim}' ";
        }
        if(!empty($request->cliente_id)){
            $cliente_id = $request->cliente_id;
            $sqlAdd .= " AND saidas.cliente_id = {$cliente_id} ";
        }

        $cliente = DB::selectOne("SELECT * FROM clientes WHERE id = {$cliente_id}");
        $clientesDividaDetalhes = DB::select("
        SELECT produtos.codigo as produto_codigo ,produtos.descricao as produto_descricao,
        saida_items.preco_unitario as preco_unitario, saida_items.custo as custo, saida_items.quantidade as quantidade
        ,lotes.fornecedor_ref as fornecedor_ref, saida_items.valor_iva as iva
        FROM saidas
        INNER JOIN clientes ON clientes.id=saidas.cliente_id
        INNER JOIN saida_items ON saidas.id = saida_items.saida_id
        INNER JOIN produtos ON produtos.id = saida_items.produto_id
        INNER JOIN lotes ON lotes.id = saida_items.lote_id
        WHERE saidas.tipo_saida_id=3
        AND saidas.activo='1' $sqlAdd GROUP BY produtos.id
        ");
        return view('saida.detalhesDividas', compact('clientesDividaDetalhes', 'cliente'));
    }
    public function listarDividas()
    {
        $bancos = Banco::all();
        $clientes = Cliente::all();
        $tipo_pagamentos = TipoPagamento::all();
        return view('saida.dividas', compact('bancos','clientes','tipo_pagamentos'));
    }

    public function adicionar($produto_)
    {
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
        // dd($empresa);
        $usar_lotes = $empresa->usar_lotes;

        $json['success'] = false;
        $json['message'] = null;



        $loteController = new LoteController();
        $qnt = abs($_POST['cart_qnt']);
        $taxa = $_POST['taxa'];
        $disponivel = isset($_POST['disponivel']) ? $_POST['disponivel']:0;
        // $custo=$_POST['cart_coast'];
        // dd("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
        // lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
        // FROM lotes
        // INNER JOIN produtos ON produtos.id=lotes.produto_id
        // WHERE lotes.codigo_barras='{$produto}' AND produtos.activo='1' AND lotes.activo != '0'");
//        $lote = $_POST['lote'];
        $preco = $_POST['unit_price'];
        $type = $_POST['tipo'];
        $valor_iva = 0;
        $custo = (double)$preco*(double)$qnt;
        if($taxa != 0){
            $valor_iva = $custo-($custo/$empresa->iva);//($preco*$qnt)*($taxa/100);
//            dd($valor_iva);
        }elseif($taxa == 0) {
            $valor_iva = 0;
        }

        $cart = session()->get('carinho');

        if($usar_lotes=='1'){
            // $produto = Produto::find($produto);
            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");

            $preco_compra = $produto->preco_compra;

            if (!empty($cart[$produto->id])) {
                if($empresa->vender_negativo == 2){
                    if ($produto->qnt_disponivel >= $qnt){
                        if($type == 'direct'){
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] = $qnt;
                            $cart[$produto->id]['custo'] = ($preco * $qnt);
                        }else{
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] += $qnt;
                            $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                        }
                        session()->put('carinho', $cart);
                        $json['success'] = true;
                        $json['message'] = "$produto->descricao!";
                    }else{
                        $json['success'] = false;
                        $json['message'] = "O lote $produto->codigo_barras tem quantidade disponível $produto->qnt_disponivel!";
                    }
                }else{
                    if($type == 'direct'){
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] = $qnt;
                        $cart[$produto->id]['custo'] = ($preco * $qnt);
                    }else{
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += $qnt;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                    }
                    session()->put('carinho', $cart);
                    $json['success'] = true;
                    $json['message'] = "$produto->descricao!";
                }
            } else {
                if($empresa->vender_negativo == 2){
                    if ($produto->qnt_disponivel >= $qnt){
                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->produto_id,
                            'codigo_barras' => $produto->codigo_barras,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            //'lote' => $lote,
                            'valor_iva' => (double)$valor_iva,
                        ];
                        session()->put('carinho', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                    }else{
                        $json['success'] = false;
                        $json['message'] = "O lote $produto->codigo_barras tem quantidade disponível $produto->qnt_disponivel!";
                    }
                }else{
                    $cart[$produto->id] = [
                        'id' => $produto->id,
                        'produto_id' => $produto->produto_id,
                        'codigo_barras' => $produto->codigo_barras,
                        'nome' => $produto->descricao,
                        'preco_unitario' => $preco,
                        'preco_compra' => $preco_compra,
                        'quantidade' => $qnt,
                        'disponivel' => $disponivel,
                        'custo' => ($preco * $qnt),
                        'taxa' => $taxa,
                        //'lote' => $lote,
                        'valor_iva' => (double)$valor_iva,
                    ];
                    session()->put('carinho', $cart);
                    $json['success'] = true;
                    $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                }
            }
        }else{
            $produto = Produto::find($produto_);

            $preco_compra = $loteController->preco_custo($produto_);

            if (!empty($cart[$produto->id])) {
                if($type == 'direct'){
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] = $qnt;
                    $cart[$produto->id]['custo'] = ($preco * $qnt);
                }else{
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] += $qnt;
                    $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                }
                    session()->put('carinho', $cart);
                    $json['success'] = true;
                    $json['message'] = "$produto->descricao actualizado !";

            } else {
                $cart[$produto->id] = [
                    'id' => $produto->id,
                    'produto_id' => $produto->id,
                    'codigo_barras' => $produto->codigo,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $preco,
                    'preco_compra' => $preco_compra,
                    'quantidade' => $qnt,
                    'disponivel' => $disponivel,
                    'custo' => ($preco * $qnt),
                    'taxa' => $taxa,
    //                'lote' => $lote,
                    'valor_iva' => (double)$valor_iva,
                ];
                session()->put('carinho', $cart);
                $json['success'] = true;
                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
            }
        }
        echo json_encode($json);
    }

    public function adicionarTipoPagamento(Request $request)
    {
        $json['success'] = false;
        $json['message'] = null;

        $tipoPagamento_id = (int) $_POST['tipo_pagamento'];
        if(!empty($tipoPagamento_id)) {
            $NometipoPagamento = DB::selectOne("SELECT * FROM tipo_pagamentos WHERE id = {$tipoPagamento_id}");
            if (!empty($NometipoPagamento))
                $NometipoPagamento = $NometipoPagamento->designacao;
            else
                $NometipoPagamento = null;

            $valor = $_POST['valor'];
            $numero = $_POST['numero'] ?? null;
            $referencia = $_POST['referencia'] ?? null;
//        $email = $_POST['email'] ?? null;

            $cart = session()->get('carinhoTipoPagamento');

            if (!empty($cart[$tipoPagamento_id])) {
//            if($type == 'direct'){
//                $cart[$produto->id]['preco_unitario'] = $preco;
//                $cart[$produto->id]['quantidade'] = $qnt;
//                $cart[$produto->id]['custo'] = ($preco * $qnt);
//            }else{
//                $cart[$produto->id]['preco_unitario'] = $preco;
//                $cart[$produto->id]['quantidade'] += $qnt;
//                $cart[$produto->id]['custo'] += ($preco * $qnt);
//            }
//            session()->put('carinhoTipoPagamento', $cart);
                $json['success'] = false;
                $json['message'] = "Já foi registado no carrinho";

            } else {
                $cart[$tipoPagamento_id] = [
                    'tipo_pagamento_id' => $tipoPagamento_id,
                    'nome' => $NometipoPagamento,
                    'valor' => $valor,
                    'numero' => $numero,
                    'referencia' => $referencia,
//                'email' => $email,
                ];
                session()->put('carinhoTipoPagamento', $cart);
                $json['success'] = true;
                $json['message'] = "{$NometipoPagamento} adicionado no carinho !";
            }
        }else{
            $json['success'] = false;
            $json['message'] = "Nenhum tipo de pagamento selecionado!";
        }
        echo   json_encode($json);
    }

    public function listarCarinhoTipoPagamento()
    {
        $carinhoTipoPagamento = session()->get('carinhoTipoPagamento');
        return view('saida.fetchCarinhoTipoPagamento', compact('carinhoTipoPagamento'));
    }

    public function removerTipoPagamento($id)
    {

        $json['success'] = false;
        $cart = session()->get('carinhoAbateUpdate');

        session()->forget('carinhoTipoPagamento.' . $id);
        $json['success'] = true;
        echo  json_encode($json);
    }

    public function adicionarTipoPagamento2(Request $request)
    {
        $json['success'] = false;
        $json['message'] = null;
        $tipoPagamento_id = (int) $_POST['tipo_pagamento'];
        if(!empty($tipoPagamento_id)) {
            $NometipoPagamento = DB::selectOne("SELECT * FROM tipo_pagamentos WHERE id = {$tipoPagamento_id}");
            if (!empty($NometipoPagamento))
                $NometipoPagamento = $NometipoPagamento->designacao;
            else
                $NometipoPagamento = null;
            $valor = $_POST['valor'];
            $numero = $_POST['numero'] ?? null;
            $referencia = $_POST['referencia'] ?? null;
//        $email = $_POST['email'] ?? null;

            $cart = session()->get('carinhoTipoPagamento2');

            if (!empty($cart[$tipoPagamento_id])) {
//            if($type == 'direct'){
//                $cart[$produto->id]['preco_unitario'] = $preco;
//                $cart[$produto->id]['quantidade'] = $qnt;
//                $cart[$produto->id]['custo'] = ($preco * $qnt);
//            }else{
//                $cart[$produto->id]['preco_unitario'] = $preco;
//                $cart[$produto->id]['quantidade'] += $qnt;
//                $cart[$produto->id]['custo'] += ($preco * $qnt);
//            }
//            session()->put('carinhoTipoPagamento', $cart);
                $json['success'] = false;
                $json['message'] = "Já foi registado no carrinho";

            } else {
                $cart[$tipoPagamento_id] = [
                    'tipo_pagamento_id' => $tipoPagamento_id,
                    'nome' => $NometipoPagamento,
                    'valor' => $valor,
                    'numero' => $numero,
                    'referencia' => $referencia,
//                'email' => $email,
                ];
                session()->put('carinhoTipoPagamento2', $cart);
                $json['success'] = true;
                $json['message'] = "{$NometipoPagamento} adicionado no carinho !";
            }
        }else{
            $json['success'] = false;
            $json['message'] = "Nenhum tipo de pagamento selecionado!";
        }
        echo   json_encode($json);
    }

    public function listarCarinhoTipoPagamento2()
    {
        $carinhoTipoPagamento = session()->get('carinhoTipoPagamento2');
        return view('saida.fetchCarinhoTipoPagamento2', compact('carinhoTipoPagamento'));
    }

    public function removerTipoPagamento2($id)
    {

        $json['success'] = false;
        $cart = session()->get('carinhoAbateUpdate');

        session()->forget('carinhoTipoPagamento2.' . $id);
        $json['success'] = true;
        echo  json_encode($json);
    }
    public function limparTipoPagamento()
    {
//        session()->forget('carinhoTipoPagamento');
        session()->forget('carinhoTipoPagamento2');
        $json['success'] = true;
        echo  json_encode($json);
    }

    public function adicionarVendaCotacao($saida_id)
    {
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

        $json['success'] = false;
        $json['message'] = null;

        $cart = session()->get('carinho');

        $dados = DB::select("SELECT cotacao_items.produto_id, produtos.descricao as descricao, cotacao_items.lote_id, cotacao_items.preco_unitario, cotacao_items.quantidade, disponivel, cotacao_items.iva as taxa,
            valor_iva, valor_total_iva, lotes.codigo_barras, produtos.codigo, cotacao_items.preco_compra
            FROM cotacao_items
            INNER JOIN cotacao ON cotacao.id=cotacao_items.saida_id
            INNER JOIN produtos ON produtos.id=cotacao_items.produto_id
            INNER JOIN lotes ON cotacao_items.lote_id=lotes.id
            WHERE cotacao.id={$saida_id}
        ");

        if($usar_lotes=='1'){
            foreach ($dados as $produto) {
                $cart[$produto->produto_id] = [
                    'id' => $produto->lote_id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo_barras,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $produto->preco_unitario,
                    'preco_compra' => $produto->preco_compra,
                    'quantidade' => $produto->quantidade,
                    'disponivel' => $produto->disponivel,
                    'custo' => ($produto->preco_unitario * $produto->quantidade),
                    'taxa' => $produto->taxa,
                    'valor_iva' => $produto->valor_iva,
                ];
            }
        }else{
            foreach ($dados as $produto) {
                $cart[$produto->produto_id] = [
                    // 'id' => $produto->lote_id,
                    'id' => $produto->produto_id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $produto->preco_unitario,
                    'preco_compra' => $produto->preco_compra,
                    'quantidade' => $produto->quantidade,
                    'disponivel' => $produto->disponivel,
                    'custo' => ($produto->preco_unitario * $produto->quantidade),
                    'taxa' => $produto->taxa,
                    'valor_iva' => $produto->valor_iva,
                ];
            }
        }
        session()->put('carinho', $cart);
        $json['success'] = true;
//            $json['message'] = $produto->quantidade . "x $produto->descricao adicionado no carinho !";

        echo   json_encode($json);
    }

    public function adicionarVendaCotacao2($saida_id)
    {
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

        $json['success'] = false;
        $json['message'] = null;

        $cart = session()->get('carinho_credito');

        $dados = DB::select("SELECT cotacao_items.produto_id, produtos.descricao as descricao, cotacao_items.lote_id, cotacao_items.preco_unitario, cotacao_items.quantidade,
        disponivel, valor_iva, cotacao_items.iva as taxa, lotes.codigo_barras, produtos.codigo, cotacao_items.preco_compra
            FROM cotacao_items
            INNER JOIN cotacao ON cotacao.id=cotacao_items.saida_id
            INNER JOIN produtos ON produtos.id=cotacao_items.produto_id
            INNER JOIN lotes ON cotacao_items.lote_id=lotes.id
            WHERE cotacao.id={$saida_id}
        ");

        if($usar_lotes=='1'){
            foreach ($dados as $produto) {
                $cart[$produto->produto_id] = [
                    'id' => $produto->lote_id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo_barras,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $produto->preco_unitario,
                    'preco_compra' => $produto->preco_compra,
                    'quantidade' => $produto->quantidade,
                    'disponivel' => $produto->disponivel,
                    'custo' => ($produto->preco_unitario * $produto->quantidade),
                    'taxa' => $produto->taxa,
                    'valor_iva' => $produto->valor_iva,
                ];
            }
        }else{
            foreach ($dados as $produto) {
                $cart[$produto->produto_id] = [
                    // 'id' => $produto->lote_id,
                    'id' => $produto->produto_id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo_barras,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $produto->preco_unitario,
                    'preco_compra' => $produto->preco_compra,
                    'quantidade' => $produto->quantidade,
                    'disponivel' => $produto->disponivel,
                    'custo' => ($produto->preco_unitario * $produto->quantidade),
                    'taxa' => $produto->taxa,
                    'valor_iva' => $produto->valor_iva,
                ];
            }
        }
        session()->put('carinho_credito', $cart);
        $json['success'] = true;
//            $json['message'] = $produto->quantidade . "x $produto->descricao adicionado no carinho !";

        echo   json_encode($json);
    }

    public function adicionarCodigo($produto_) {
        $json['success'] = false;
        $json['message'] = null;

        $loteController = new LoteController();

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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        // echo "SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
        // lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
        // FROM lotes
        // INNER JOIN produtos ON produtos.id=lotes.produto_id
        // WHERE lotes.codigo_barras='{$produto}' AND produtos.activo='1' AND lotes.activo != '0'";
        // $produto = Produto::where('codigo', '=', $produto)->where('activo', '=', '1')->first();

        $cart = session()->get('carinho');
        // Para sistema usando lotes
        if($usar_lotes == '1'){
            $lote = DB::selectOne("SELECT * FROM lotes
            WHERE lotes.codigo_barras='{$produto_}' AND lotes.activo != '0'");

            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");

            // dd($produto);

            // dd($cart[$produto->id]);
            if(!empty($produto)) {
                if($empresa->vender_negativo == 2){
                    if($produto->qnt_disponivel >= (isset($cart[$produto->id]) ? (int)$cart[$produto->id]['quantidade']+1 : 0)){
                        if($lote->data_validade > date('Y-m-d')){
                            $loteController = new LoteController();
                            // $produto_id = $produto->id;

                            // if($loteController->is_lote($produto_id)) {
                            //     $loteInfo = $loteController->consultaProdutoStock($produto_id);
                            //     $qnt = 1;
                            $qnt = 1;
                            $disponivel = $produto->qnt_disponivel;//$loteInfo['qnt_total_disponivel'];
                            // $custo=$_POST['cart_coast'];
                            $preco = $produto->preco_venda;//$loteInfo['preco_venda'];
                            $preco_compra = $produto->preco_compra;//$loteInfo['preco_venda'];
                            $taxa = $produto->taxa;//$loteInfo['taxa'];
                            $valor_iva = 0;
                            if($taxa != 0){
                                $valor_iva = ($preco*$qnt)*($taxa/100);
                //            dd($valor_iva);
                            }elseif($taxa == 0) {
                                $valor_iva = 0;
                            }

                            if (!empty($cart[$produto->id])) {
                                $qnt = $cart[$produto->id]['quantidade'];
                                $cart[$produto->id]['preco_unitario'] = $preco;
                                $cart[$produto->id]['quantidade'] += 1;
                                $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                                session()->put('carinho', $cart);
                                $json['success'] = true;
                                $json['message'] = "Quantidade de $produto->descricao actualizada !";

                            } else {
                                $cart[$produto->id] = [
                                    'id' => $produto->id,
                                    'produto_id' => $produto->produto_id,
                                    'codigo_barras' => $produto->codigo_barras,
                                    'nome' => $produto->descricao,
                                    'preco_unitario' => $preco,
                                    'preco_compra' => $preco_compra,
                                    'quantidade' => $qnt,
                                    'disponivel' => $disponivel,
                                    'custo' => ($preco * $qnt),
                                    'taxa' => $taxa,
                                    'valor_iva' => $valor_iva,
                                ];
                                session()->put('carinho', $cart);
                                $json['success'] = true;
                                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                            }
                            // } else{
                            //     $json['success'] = false;
                            //     $json['message'] = "Produto Sem nenhum stock!";
                            // }
                        } else{
                            $json['success'] = false;
                            $json['message'] = "Produto Expirado!";
                        }
                    } else{
                        $json['success'] = false;
                        $json['message'] = "Quantidade do produto esgotada!";
                    }
                }else{
                    if($lote->data_validade > date('Y-m-d')){
                        $loteController = new LoteController();
                        // $produto_id = $produto->id;

                        // if($loteController->is_lote($produto_id)) {
                        //     $loteInfo = $loteController->consultaProdutoStock($produto_id);
                        //     $qnt = 1;
                        $qnt = 1;
                        $disponivel = $produto->qnt_disponivel;//$loteInfo['qnt_total_disponivel'];
                        // $custo=$_POST['cart_coast'];
                        $preco = $produto->preco_venda;//$loteInfo['preco_venda'];
                        $preco_compra = $produto->preco_compra;//$loteInfo['preco_venda'];
                        $taxa = $produto->taxa;//$loteInfo['taxa'];
                        $valor_iva = 0;
                        if($taxa != 0){
                            $valor_iva = ($preco*$qnt)*($taxa/100);
            //            dd($valor_iva);
                        }elseif($taxa == 0) {
                            $valor_iva = 0;
                        }

                        if (!empty($cart[$produto->id])) {
                            $qnt = $cart[$produto->id]['quantidade'];
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] += 1;
                            $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                            session()->put('carinho', $cart);
                            $json['success'] = true;
                            $json['message'] = "Quantidade de $produto->descricao actualizada !";

                        } else {
                            $cart[$produto->id] = [
                                'id' => $produto->id,
                                'produto_id' => $produto->produto_id,
                                'codigo_barras' => $produto->codigo_barras,
                                'nome' => $produto->descricao,
                                'preco_unitario' => $preco,
                                'preco_compra' => $preco_compra,
                                'quantidade' => $qnt,
                                'disponivel' => $disponivel,
                                'custo' => ($preco * $qnt),
                                'taxa' => $taxa,
                                'valor_iva' => $valor_iva,
                            ];
                            session()->put('carinho', $cart);
                            $json['success'] = true;
                            $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                        }
                        // } else{
                        //     $json['success'] = false;
                        //     $json['message'] = "Produto Sem nenhum stock!";
                        // }
                    } else{
                        $json['success'] = false;
                        $json['message'] = "Produto Expirado!";
                    }
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }else{
            // print_r($produto_);
            $produto = Produto::where('codigo', '=', $produto_)->where('activo', '=', '1')->first();
            if(!empty($produto)) {
                if($loteController->is_lote($produto->id)) {
                    $loteInfo = $loteController->consultaProdutoStock($produto->id);
                    $qnt = 1;
                    $disponivel = $loteInfo['qnt_total_disponivel'];
                    // $custo=$_POST['cart_coast'];
                    $preco = $loteInfo['preco_venda'];
                    $preco_compra = $loteController->preco_custo($produto->id); //$loteInfo['preco_venda'];
                    $taxa = $loteInfo['taxa'];

                    if($taxa != 0){
                        $valor_iva = ($preco*$qnt)*($taxa/100);
                    }elseif($taxa == 0) {
                        $valor_iva = 0;
                    }

                    if (!empty($cart[$produto->id])) {
                        $qnt = $cart[$produto->id]['quantidade'];
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += 1;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                        session()->put('carinho', $cart);
                        $json['success'] = true;
                        $json['message'] = "Quantidade de $produto->descricao actualizada !";

                    } else {
                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->id,
                            'codigo_barras' => $produto->codigo,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            'valor_iva' => $valor_iva,
                        ];
                        session()->put('carinho', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                    }

                } else{
                    $json['success'] = false;
                    $json['message'] = "Produto Sem nenhum stock!";
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }

        echo json_encode($json);
    }
    public function adicionarCodigoVC($produto_) {
        $json['success'] = false;
        $json['message'] = null;

        $loteController = new LoteController();

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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $cart = session()->get('carinho_credito');

        // Para sistema usando lotes
        if($usar_lotes == '1'){
            $lote = DB::selectOne("SELECT * FROM lotes
            WHERE lotes.codigo_barras='{$produto_}' AND lotes.activo != '0'");
            // $produto = Produto::where('codigo', '=', $produto)->where('activo', '=', '1')->first();
            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");


            if(!empty($produto)) {
                if($empresa->vender_negativo == 2){
                    if($produto->qnt_disponivel >= (isset($cart[$produto->id]) ? (int)$cart[$produto->id]['quantidade']+1 : 0)){
                        if($lote->data_validade > date('Y-m-d')){
                            $produto_id = $produto->id;

                            // if($loteController->is_lote($produto_id)) {
                            // $loteInfo = $loteController->consultaProdutoStock($produto_id);
                            $qnt = 1;

                            $disponivel = $produto->qnt_disponivel;
                            // $custo=$_POST['cart_coast'];
                            $preco = $produto->preco_venda;
                            $preco_compra = $produto->preco_compra;
                            $taxa = $produto->taxa;
                            $valor_iva = 0;
                            if($taxa != 0){
                                $valor_iva = ($preco*$qnt)*($taxa/100);
                            // dd($valor_iva);
                            }elseif($taxa == 0) {
                                $valor_iva = 0;
                            }


                            if (!empty($cart[$produto->id])) {
                                $qnt = $cart[$produto->id]['quantidade'];
                                $cart[$produto->id]['preco_unitario'] = $preco;
                                $cart[$produto->id]['quantidade'] += 1;
                                $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                                session()->put('carinho_credito', $cart);
                                $json['success'] = true;
                                $json['message'] = "Quantidade de $produto->descricao actualizada !";

                            } else {
                                $cart[$produto->id] = [
                                    'id' => $produto->id,
                                    'produto_id' => $produto->produto_id,
                                    'codigo_barras' => $produto->codigo_barras,
                                    'nome' => $produto->descricao,
                                    'preco_unitario' => $preco,
                                    'preco_compra' => $preco_compra,
                                    'quantidade' => $qnt,
                                    'disponivel' => $disponivel,
                                    'custo' => ($preco * $qnt),
                                    'taxa' => $taxa,
                                    'valor_iva' => $valor_iva,
                                ];
                                session()->put('carinho_credito', $cart);
                                $json['success'] = true;
                                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                            }
                            // } else{
                            //     $json['success'] = false;
                            //     $json['message'] = "Produto sem nenhum stock!";
                            // }
                        } else{
                            $json['success'] = false;
                            $json['message'] = "Produto Expirado!";
                        }
                    }else{
                        $json['success'] = false;
                        $json['message'] = "Quantidade do produto esgotada!";
                    }
                }else{
                    if($lote->data_validade > date('Y-m-d')){
                        $produto_id = $produto->id;

                    // if($loteController->is_lote($produto_id)) {
                        // $loteInfo = $loteController->consultaProdutoStock($produto_id);
                        $qnt = 1;

                        $disponivel = $produto->qnt_disponivel;
                        // $custo=$_POST['cart_coast'];
                        $preco = $produto->preco_venda;
                        $preco_compra = $produto->preco_compra;
                        $taxa = $produto->taxa;
                        $valor_iva = 0;
                        if($taxa != 0){
                            $valor_iva = ($preco*$qnt)*($taxa/100);
                        // dd($valor_iva);
                        }elseif($taxa == 0) {
                            $valor_iva = 0;
                        }


                        if (!empty($cart[$produto->id])) {
                            $qnt = $cart[$produto->id]['quantidade'];
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] += 1;
                            $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                            session()->put('carinho_credito', $cart);
                            $json['success'] = true;
                            $json['message'] = "Quantidade de $produto->descricao actualizada !";

                        } else {
                            $cart[$produto->id] = [
                                'id' => $produto->id,
                                'produto_id' => $produto->produto_id,
                                'codigo_barras' => $produto->codigo_barras,
                                'nome' => $produto->descricao,
                                'preco_unitario' => $preco,
                                'preco_compra' => $preco_compra,
                                'quantidade' => $qnt,
                                'disponivel' => $disponivel,
                                'custo' => ($preco * $qnt),
                                'taxa' => $taxa,
                                'valor_iva' => $valor_iva,
                            ];
                            session()->put('carinho_credito', $cart);
                            $json['success'] = true;
                            $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                        }
                    // } else{
                    //     $json['success'] = false;
                    //     $json['message'] = "Produto sem nenhum stock!";
                    // }
                    } else{
                        $json['success'] = false;
                        $json['message'] = "Produto Expirado!";
                    }
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }else{
            // print_r($produto_);
            $produto = Produto::where('codigo', '=', $produto_)->where('activo', '=', '1')->first();
            if(!empty($produto)) {
                if($loteController->is_lote($produto->id)) {
                    $loteInfo = $loteController->consultaProdutoStock($produto->id);
                    $qnt = 1;
                    $disponivel = $loteInfo['qnt_total_disponivel'];
                    // $custo=$_POST['cart_coast'];
                    $preco = $loteInfo['preco_venda'];
                    $preco_compra = $loteController->preco_custo($produto->id); //$loteInfo['preco_venda'];
                    $taxa = $loteInfo['taxa'];

                    if($taxa != 0){
                        $valor_iva = ($preco*$qnt)*($taxa/100);
                    }elseif($taxa == 0) {
                        $valor_iva = 0;
                    }

                    if (!empty($cart[$produto->id])) {
                        $qnt = $cart[$produto->id]['quantidade'];
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += 1;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                        session()->put('carinho_credito', $cart);
                        $json['success'] = true;
                        $json['message'] = "Quantidade de $produto->descricao actualizada !";

                    } else {
                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->id,
                            'codigo_barras' => $produto->codigo,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            'valor_iva' => $valor_iva,
                        ];
                        session()->put('carinho_credito', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                    }

                } else{
                    $json['success'] = false;
                    $json['message'] = "Produto Sem nenhum stock!";
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }

        echo json_encode($json);
    }

    public function adicionarCodigo2($produto_) {
        $json['success'] = false;
        $json['message'] = null;

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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $cart = session()->get('carinhoCotacao');

        if($usar_lotes=='0'){

            $produto = Produto::where('codigo', '=', $produto_)->where('activo', '=', '1')->first();

            if($produto) {
                $loteController = new LoteController();
                $produto_id = $produto->id;

                if($loteController->is_lote($produto_id)) {
                    $loteInfo = $loteController->consultaProdutoStock($produto_id);
                    $qnt = 1;

                    $disponivel = $loteInfo['qnt_total_disponivel'];
                    // $custo=$_POST['cart_coast'];
                    $preco_compra = $loteController->preco_custo($produto->id);
                    $preco = $loteInfo['preco_venda'];

                    $taxa = $loteInfo['taxa'];

                    if($taxa != 0){
                        $valor_iva = ($preco*$qnt)*($taxa/100);
                    }elseif($taxa == 0) {
                        $valor_iva = 0;
                    }

                    if (!empty($cart[$produto->id])) {
                        $qnt = $cart[$produto->id]['quantidade'];
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += 1;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                        session()->put('carinhoCotacao', $cart);
                        $json['success'] = true;
                        $json['message'] = "Quantidade de $produto->descricao actualizada !";

                    } else {
                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->id,
                            'codigo_barras' => $produto->codigo,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            'valor_iva' => $valor_iva,
                        ];
                        session()->put('carinhoCotacao', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                    }
                } else{
                    $json['success'] = false;
                    $json['message'] = "Produto sem nenhum stock!";
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }else{
            $lote = DB::selectOne("SELECT * FROM lotes
            WHERE lotes.codigo_barras='{$produto_}' AND lotes.activo != '0'");
            // $produto = Produto::where('codigo', '=', $produto)->where('activo', '=', '1')->first();
            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");

            if(!empty($produto) && $produto->qnt_disponivel) {
                if($lote->data_validade > date('Y-m-d')){
                    $produto_id = $produto->id;

                    // if($loteController->is_lote($produto_id)) {
                        // $loteInfo = $loteController->consultaProdutoStock($produto_id);
                        $qnt = 1;

                        $disponivel = $produto->qnt_disponivel;
                        // $custo=$_POST['cart_coast'];
                        $preco = $produto->preco_venda;
                        $preco_compra = $produto->preco_compra;
                        $taxa = $produto->taxa;
                        $valor_iva = 0;
                        if($taxa != 0){
                            $valor_iva = ($preco*$qnt)*($taxa/100);
                        // dd($valor_iva);
                        }elseif($taxa == 0) {
                            $valor_iva = 0;
                        }


                        if (!empty($cart[$produto->id])) {
                            $qnt = $cart[$produto->id]['quantidade'];
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] += 1;
                            $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                            session()->put('carinho_credito', $cart);
                            $json['success'] = true;
                            $json['message'] = "Quantidade de $produto->descricao actualizada !";

                        } else {
                            $cart[$produto->id] = [
                                'id' => $produto->id,
                                'produto_id' => $produto->produto_id,
                                'codigo_barras' => $produto->codigo_barras,
                                'nome' => $produto->descricao,
                                'preco_unitario' => $preco,
                                'preco_compra' => $preco_compra,
                                'quantidade' => $qnt,
                                'disponivel' => $disponivel,
                                'custo' => ($preco * $qnt),
                                'taxa' => $taxa,
                                'valor_iva' => $valor_iva,
                            ];
                            session()->put('carinho_credito', $cart);
                            $json['success'] = true;
                            $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                        }
                    // } else{
                    //     $json['success'] = false;
                    //     $json['message'] = "Produto sem nenhum stock!";
                    // }
                } else{
                    $json['success'] = false;
                    $json['message'] = "Produto Expirado!";
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }
        echo json_encode($json);
    }

    public function adicionarCotacaoCodigo2($produto_) {
        $json['success'] = false;
        $json['message'] = null;

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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $cart = session()->get('carinhoCotacaoUpdate');

        if($usar_lotes=='0'){

            $produto = Produto::where('codigo', '=', $produto_)->where('activo', '=', '1')->first();

            if($produto) {
                $loteController = new LoteController();
                $produto_id = $produto->id;

                if($loteController->is_lote($produto_id)) {
                    $loteInfo = $loteController->consultaProdutoStock($produto_id);
                    $qnt = 1;

                    $preco_compra = $loteController->preco_custo($produto->id);

                    $disponivel = $loteInfo['qnt_total_disponivel'];
                    // $custo=$_POST['cart_coast'];
                    $preco = $loteInfo['preco_venda'];

                    $taxa = $loteInfo['taxa'];

                    if($taxa != 0){
                        $valor_iva = ($preco*$qnt)*($taxa/100);
                    }elseif($taxa == 0) {
                        $valor_iva = 0;
                    }


                    if (!empty($cart[$produto->id])) {
                        $qnt = $cart[$produto->id]['quantidade'];
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += 1;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                        session()->put('carinhoCotacaoUpdate', $cart);
                        $json['success'] = true;
                        $json['message'] = "Quantidade de $produto->descricao actualizada !";

                    } else {
                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->id,
                            'codigo_barras' => $produto->codigo,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            'valor_iva' => $valor_iva,
                        ];
                        session()->put('carinhoCotacaoUpdate', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                    }
                } else{
                    $json['success'] = false;
                    $json['message'] = "Produto sem nenhum stock!";
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }else{
            $lote = DB::selectOne("SELECT * FROM lotes
            WHERE lotes.codigo_barras='{$produto_}' AND lotes.activo != '0'");
            // $produto = Produto::where('codigo', '=', $produto)->where('activo', '=', '1')->first();
            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");

            if(!empty($produto)) {
                if($lote->data_validade > date('Y-m-d')){
                    $produto_id = $produto->id;

                    // if($loteController->is_lote($produto_id)) {
                        // $loteInfo = $loteController->consultaProdutoStock($produto_id);
                    $qnt = 1;

                    $disponivel = $produto->qnt_disponivel;
                    // $custo=$_POST['cart_coast'];
                    $preco = $produto->preco_venda;
                    $preco_compra = $produto->preco_compra;
                    $taxa = $produto->taxa;
                    $valor_iva = 0;
                    if($taxa != 0){
                        $valor_iva = ($preco*$qnt)*($taxa/100);
                    // dd($valor_iva);
                    }elseif($taxa == 0) {
                        $valor_iva = 0;
                    }


                    if (!empty($cart[$produto->id])) {
                        $qnt = $cart[$produto->id]['quantidade'];
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += 1;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                        session()->put('carinho_credito', $cart);
                        $json['success'] = true;
                        $json['message'] = "Quantidade de $produto->descricao actualizada !";

                    } else {
                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->produto_id,
                            'codigo_barras' => $produto->codigo_barras,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            'valor_iva' => $valor_iva,
                        ];
                        session()->put('carinho_credito', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                    }
                    // } else{
                    //     $json['success'] = false;
                    //     $json['message'] = "Produto sem nenhum stock!";
                    // }
                } else{
                    $json['success'] = false;
                    $json['message'] = "Produto Expirado!";
                }
            }else{
                $json['success'] = false;
                $json['message'] = "Código de barras não encontrado!";
            }
        }
        echo json_encode($json);
    }

    public function adicionar_credito($produto_)
    {
        $json['success'] = false;
        $json['message'] = null;
        // dd($produto);
        // $produto = Produto::find($produto);

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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $loteController = new LoteController();

        $cart = session()->get('carinho_credito');

        $qnt = abs($_POST['cart_qnt']);
        $disponivel = isset($_POST['disponivel']) ? $_POST['disponivel']:0;
        // $custo=$_POST['cart_coast'];
        $taxa = $_POST['taxa'];
        $preco = $_POST['unit_price'];
        $type = $_POST['tipo'];
        $valor_iva = 0;
        $desconto= 0;
        $desconto = isset($_POST['deconto']) && !empty($_POST['deconto']) ? (float)abs($_POST['deconto']) : 0;


        $desconto_total = $preco * $qnt *  ($desconto/100);


        $custo = ($preco*$qnt);
        if($taxa != 0){
            $valor_iva = $custo-($custo/$empresa->iva);//($preco*$qnt)*($taxa/100);
        }elseif($taxa == 0) {
            $valor_iva = 0;
        }
        if($usar_lotes=='1'){

            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
                lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
                FROM lotes
                INNER JOIN produtos ON produtos.id=lotes.produto_id
                WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");


            $preco_compra = $produto->preco_compra;

            if (!empty($cart[$produto->id])) {
                if($empresa->vender_negativo == 2){
                    if ($produto->qnt_disponivel >= $qnt){
                        if($type == 'direct'){
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] = $qnt;
                            $cart[$produto->id]['custo'] = ($preco * $qnt);
                            $cart[$produto->id]['desconto'] = isset($desconto) && !empty($desconto) ? $desconto : 0;
                            $cart[$produto->id]['desconto_valor'] = isset($desconto_total) && !empty($desconto_total) ? $desconto_total : 0;
                        }else{
                            $cart[$produto->id]['preco_unitario'] = $preco;
                            $cart[$produto->id]['quantidade'] += $qnt;
                            $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                            $cart[$produto->id]['desconto'] = isset($desconto) && !empty($desconto) ? $desconto : 0;
                            $cart[$produto->id]['desconto_valor'] = isset($desconto_total) && !empty($desconto_total) ? $desconto_total : 0;
                        }
                        session()->put('carinho_credito', $cart);
                        $json['success'] = true;
                        $json['message'] = "$produto->descricao actualizado !";

                    }else{
                        $json['success'] = false;
                        $json['message'] = "O lote $produto->codigo_barras tem quantidade disponível $produto->qnt_disponivel!";
                    }
                }else{
                    if($type == 'direct'){
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] = $qnt;
                        $cart[$produto->id]['custo'] = ($preco * $qnt);
                        $cart[$produto->id]['desconto'] = isset($desconto) && !empty($desconto) ? $desconto : 0;
                        $cart[$produto->id]['desconto_valor'] = isset($desconto_total) && !empty($desconto_total) ? $desconto_total : 0;
                    }else{
                        $cart[$produto->id]['preco_unitario'] = $preco;
                        $cart[$produto->id]['quantidade'] += $qnt;
                        $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                        $cart[$produto->id]['desconto'] = isset($desconto) && !empty($desconto) ? $desconto : 0;
                        $cart[$produto->id]['desconto_valor'] = isset($desconto_total) && !empty($desconto_total) ? $desconto_total : 0;
                    }
                    session()->put('carinho_credito', $cart);
                    $json['success'] = true;
                    $json['message'] = "$produto->descricao actualizado !";
                }
            } else {

                if($empresa->vender_negativo == 2){
                    if ($produto->qnt_disponivel >= $qnt){

                        $cart[$produto->id] = [
                            'id' => $produto->id,
                            'produto_id' => $produto->produto_id,
                            'codigo_barras' => $produto->codigo_barras,
                            'nome' => $produto->descricao,
                            'preco_unitario' => $preco,
                            'preco_compra' => $preco_compra,
                            'quantidade' => $qnt,
                            'disponivel' => $disponivel,
                            'custo' => ($preco * $qnt),
                            'taxa' => $taxa,
                            'valor_iva' => $valor_iva,
                            'desconto' => isset($desconto) && !empty($desconto) ? $desconto : 0,
                            'desconto_valor' =>  $desconto_total,

                        ];
                        session()->put('carinho_credito', $cart);
                        $json['success'] = true;
                        $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";

                    }else{
                        $json['success'] = false;
                        $json['message'] = "O lote $produto->codigo_barras tem quantidade disponível $produto->qnt_disponivel!";
                    }
                }else{
                    $cart[$produto->id] = [
                        'id' => $produto->id,
                        'produto_id' => $produto->produto_id,
                        'codigo_barras' => $produto->codigo_barras,
                        'nome' => $produto->descricao,
                        'preco_unitario' => $preco,
                        'preco_compra' => $preco_compra,
                        'quantidade' => $qnt,
                        'disponivel' => $disponivel,
                        'custo' => ($preco * $qnt),
                        'taxa' => $taxa,
                        'valor_iva' => $valor_iva,
                        'desconto' => isset($desconto) && !empty($desconto) ? $desconto : 0,
                        'desconto_valor' =>  $desconto_total,

                    ];
                    session()->put('carinho_credito', $cart);
                    $json['success'] = true;
                    $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
                }
            }
        }else{
            $produto = Produto::find($produto_);

            $preco_compra = $loteController->preco_custo($produto_);

            if (!empty($cart[$produto->id])) {
                if($type == 'direct'){
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] = $qnt;
                    $cart[$produto->id]['custo'] = ($preco * $qnt);
                }else{
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] += $qnt;
                    $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                }
                    session()->put('carinho_credito', $cart);
                    $json['success'] = true;
                    $json['message'] = "$produto->descricao actualizado !";

            } else {
                $cart[$produto->id] = [
                    'id' => $produto->id,
                    'produto_id' => $produto->id,
                    'codigo_barras' => $produto->codigo,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $preco,
                    'preco_compra' => $preco_compra,
                    'quantidade' => $qnt,
                    'disponivel' => $disponivel,
                    'custo' => ($preco * $qnt),
                    'taxa' => $taxa,
    //                'lote' => $lote,
                    'valor_iva' => (double)$valor_iva,
                ];
                session()->put('carinho_credito', $cart);
                $json['success'] = true;
                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
            }
        }
        echo json_encode($json);
    }


    public function dados_abate(Request $request)
    {
        $json['saida_id'] = "";
        $json['success'] = false;
        $json['message'] = null;

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



        $cliente_id = $request->cliente;
        $tipo_motivo = $request->tipo_motivo;
//        $numero_factura = $request->numero_factura;
        $motivo = $request->motivo;
        $saida_id = $request->saida_id;
        $text_button = $request->text_button;

        // $ultima_venda = Saida::latest()->first();
        // $numero_factura = @$ultima_venda->id+1 ."/".date("Y");
        $ano = date("Y");
        $tipo = 'abate';
        $numeracao = $this->getInvoiceNumber($ano, $tipo)+1;
        $numero_factura = "SA {$numeracao}/{$ano}";

        $dataSaida = [
            'cliente_id' => $cliente_id,
            'numero_factura' => $numero_factura,
            'data' => date('y-m-d'),
            'tipo_saida_id' => 2,
            'desconto' => 0,
            'valor_total' => 0,
            'activo' => 1,
            'sessao_id' => $empresa->sessao_id ?? NULL,
        ];

        $dataSaidaUpdate = [
            'cliente_id' => $cliente_id,
            'desconto' => 0,
            'valor_total' => 0,
            'activo' => 1
        ];

        if($text_button == "Registar") {
            if (($saida = Auth::user()->saidas()->create($dataSaida))) {
                $this->updateInvoiceNumber($ano, $tipo);
                $json['success'] = true;
                $json['saida_id'] = $saida->id;
                $json['message'] = "Registado com sucesso.";
            } else {
                $json['message'] = "Houve um erro ao tentar registar";
            }
        }else{
            if (DB::table("saidas")->where(['id'=>$saida_id])->update($dataSaidaUpdate)){
                $json['success'] = true;
                $json['saida_id'] = $saida_id;
                $json['message'] = "Actualizado com sucesso.";
            }else{
////                print_r($dataSaidaUpdate);
//                $json['saida_id'] = $saida_id;
//                $json['message'] = "Houve um erro ao tentar Actualizar";
            }
        }

        echo json_encode($json);
    }

    public function actualizarDadosAbate(){
        $saida_id = $_POST['saida_id'];
        $cliente_id = $_POST['cliente_id'];

        $json['success'] = false;
        $json['message'] = null;

        if(DB::table('saidas')->where('id',$saida_id)->update(['cliente_id'=>$cliente_id])){
            $json['success'] = true;
            $json['message'] = "Actualizado com sucesso";
        }else{
            $json['message'] = "Houve um erro ao tentar actualizar os dados";
        }
        echo  json_encode($json);
    }

    public function adicionarAbate($produto)
    {
        $produtoController = new ProdutoController();
        $loteModel = new LoteController();

        $json['success'] = false;
        $json['message'] = null;
        $produto = Produto::find($produto);

        $saida_id = $_POST['saida_id'];
        $tipo_motivo = $_POST['tipo_motivo'];
        $motivo = $_POST['motivo'];
        $qnt = $_POST['cart_qnt'];
        $lote = $_POST['lote'];


        $verifica = DB::selectOne("SELECT COUNT(*) AS total FROM saida_items WHERE saida_id = {$saida_id} AND lote_id = {$lote} AND produto_id = {$produto->id} AND activo = '1'")->total;

        $lol = DB::selectOne("SELECT qnt_disponivel, preco_venda, preco_compra FROM lotes WHERE id={$lote}");

        if((double)$qnt <= (double)$lol->qnt_disponivel && (double)$qnt > 0) {
            if ($verifica > 0) {

                $json['success'] = false;
                $json['message'] = "O lote para este produto já foi registado o abate";//"O produto $produto->nome  nesse já foi registado!";

            } else {
                $quantidade = (float) $qnt;
                $quantidade_actual = $loteModel->qnt_disponivel_produto($produto->id);
                $user_id = auth()->user()->id;
                $carinho = [
                        'saida_id' => $saida_id,
                        'produto_id' => $produto->id,
                        'lote_id' => $lote,
                        'quantidade' => $quantidade,
                        'preco_unitario' => (float) $lol->preco_venda,//$preco,
                        'preco_compra' => (float) $lol->preco_compra,//$lotes[0]->preco_compra,
                        'custo' => (float) ($qnt * $lol->preco_compra),
                        'tipo_motivo' => $tipo_motivo,
                        'motivo' => $motivo
                    ];
                    $qntBalancoAnterior = $produtoController->productBalanceStock($produto->id);
                if (DB::table("saida_items")->insert($carinho)) {
                    $loteModel->abater($lote, $qnt);

                    $produtoController->checkProductBalance($produto->id, 'saida_create', $quantidade, $quantidade_actual,$qntBalancoAnterior, $user_id);

                    $json['success'] = true;
                    $json['message'] = 'Abate registado com sucesso.';

                    session()->flash('success', $json['message']);
                }
            }
        }else{
            $json['message'] = 'A quantidade de abate, não pode ser superior a quantidade disponível e nem inferior ou igual a 0.';
        }
        echo   json_encode($json);
    }

    public function replaceAbate(Request $request, $produto){
        $json['success'] = false;
        $json['message'] = null;
        // $tipo_motivo = $_POST['tipo_motivo'];
        // $motivo = $_POST['motivo'];
        $qnt = (double)$_POST['qnt']; // Nova quantidade por abater
        $preco_unitario = $_POST['preco_unitario'];
        $abate = $_POST['abate'];
        $qnt_anterior = $_POST['qnt_anterior'];

        $saida_items_id = $_POST['saida_items_id'];
        $lote_id = $_POST['lote_id'];

        $loteModel = new LoteController();
        $produtoController = new ProdutoController();



        $sel = DB::selectOne("SELECT * FROM saida_items WHERE id = $saida_items_id");

        $lol = DB::selectOne("SELECT qnt_disponivel FROM lotes WHERE id={$sel->lote_id}");

        $res = (double)$sel->quantidade + (double)$lol->qnt_disponivel;


        if((double)$qnt <= (double)$res && (double)$qnt > 0) {
            $quantidade = (float) $qnt;
            $quantidade_actual = $loteModel->qnt_disponivel_produto($sel->produto_id);
            $user_id = auth()->user()->id;
            $carinho =
                [
                    'saida_id' => $sel->saida_id,
                    'produto_id' => $sel->produto_id,
                    'lote_id' => $lote_id,
                    'quantidade' => $qnt,
                    'quantidade_anterior' => $qnt_anterior,
                    'preco_unitario' => (float) $preco_unitario,//$preco,
                    'preco_compra' => (float) $sel->preco_compra,//$lotes[0]->preco_compra,
                    'custo' => (float) ($qnt * $sel->preco_compra),
                    'tipo_motivo' => $sel->tipo_motivo,
                    'motivo' => $sel->motivo
                ];
            $qntBalancoAnterior = $produtoController->productBalanceStock($sel->produto_id);

            if (DB::table('saida_items')->where('id', $saida_items_id)->update(['activo' => '0'])) {
                if ($res >= 0) {
                    DB::table('lotes')->where('id', $sel->lote_id)->update(['qnt_disponivel' => $res, 'activo' => '1']);
                } else {
                    DB::table('lotes')->where('id', $sel->lote_id)->update(['qnt_disponivel' => $res]);
                }


                $json['qntStockDB'] = (double)$lol->qnt_disponivel;
                $json['qntSaidaAnteriorDB'] = (double)$sel->quantidade;
                $json['qntReposicaoProduto'] = $res;
                $json['qntPorAbaterProduto'] = $qnt;

                if(DB::table('saida_items')->insert($carinho)) {
                    $loteModel->abater($lote_id, $qnt);

                    $produtoController->checkProductBalance($sel->produto_id, 'saida_update', $quantidade, $quantidade_actual,$qntBalancoAnterior, $user_id);

                    $json['success'] = true;
                    $json['message'] = "Quantidade actualizada com sucesso";
                }
            }

        }else{
            $json['message'] = 'A quantidade de abate, não pode ser superior a quantidade disponível de "'.$res.'" nem inferior ou igual a 0';;
        }
        // print_r($cart);
        echo json_encode($json);
    }

    public function adicionarCotacao($produto_)
    {
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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $loteController = new LoteController();

        $json['success'] = false;
        $json['message'] = null;

        $qnt = $_POST['cart_qnt'];
        $taxa = $_POST['taxa'];
        $disponivel = isset($_POST['disponivel']) ? $_POST['disponivel']:0;
        // $custo=$_POST['cart_coast'];
//        $tax = $_POST['cart_tax'];
        $preco = $_POST['unit_price'];
        $type = $_POST['tipo'];
        $valor_iva = 0;
        $custo = (double)$preco*(double)$qnt;
        if($taxa != 0){
            $valor_iva = $custo-($custo/$empresa->iva);//$valor_iva = ($preco*$qnt)*($taxa/100);
//            dd($valor_iva);
        }elseif($taxa == 0) {
            $valor_iva = 0;
        }

        $cart = session()->get('carinhoCotacao');

        if($usar_lotes=='0'){
            $produto = Produto::find($produto_);
            $preco_compra = $loteController->preco_custo($produto->id);
            if (!empty($cart[$produto->id])) {
                if($type == 'direct'){
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] = $qnt;
                    $cart[$produto->id]['custo'] = ($preco * $qnt);
                }else{
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] += $qnt;
                    $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                }
                session()->put('carinhoCotacao', $cart);
                $json['success'] = true;
                $json['message'] = "Quantidade de $produto->descricao actualizada !";

            } else {
                $cart[$produto->id] = [
                    'id' => $produto->id,
                    'produto_id' => $produto->id,
                    'codigo_barras' => $produto->codigo,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $preco,
                    'preco_compra' => $preco_compra,
                    'quantidade' => $qnt,
                    'disponivel' => $disponivel,
                    'taxa' => $taxa,
                    'custo' => ($preco * $qnt),
                    'valor_iva' => $valor_iva,
                ];
                session()->put('carinhoCotacao', $cart);
                $json['success'] = true;
                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
            }
        }else{
            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");

            $preco_compra = $produto->preco_compra;

            if (!empty($cart[$produto->id])) {
                if($type == 'direct'){
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] = $qnt;
                    $cart[$produto->id]['custo'] = ($preco * $qnt);
                }else{
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] += $qnt;
                    $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];

                }
                session()->put('carinhoCotacao', $cart);
                $json['success'] = true;
                $json['message'] = "Quantidade de $produto->descricao actualizada !";

            } else {
                $cart[$produto->id] = [
                    'id' => $produto->id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo_barras,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $preco,
                    'preco_compra' => $preco_compra,
                    'quantidade' => $qnt,
                    'disponivel' => $disponivel,
                    'taxa' => $taxa,
                    'custo' => ($preco * $qnt),
                    'valor_iva' => $valor_iva,
                ];
                session()->put('carinhoCotacao', $cart);
                $json['success'] = true;
                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
            }
        }
        echo   json_encode($json);
    }

    public function adicionarCotacaoBD($saida)
    {
        $json['success'] = false;
        $json['message'] = null;
//        $produto = Produto::find($produto);

//        $qnt = $_POST['cart_qnt'];
//        $disponivel = isset($_POST['disponivel']) ? $_POST['disponivel']:0;
        // $custo=$_POST['cart_coast'];
//        $tax = $_POST['cart_tax'];
//        $preco = $_POST['unit_price'];
//        $type = $_POST['tipo'];
        $cart = session()->get('carinhoCotacaoUpdate');
        session()->pull('carinhoCotacaoUpdate', $cart);
        $cart = session()->get('carinhoCotacaoUpdate');

        $data = DB::select("SELECT saida_items.produto_id as produto_id, produtos.descricao as descricao, produtos.codigo, lotes.id as lote_id, lotes.codigo_barras as codigo_barras,
            saida_items.preco_unitario as preco_unitario, lotes.preco_compra, saida_items.quantidade as qnt, lotes.taxa as taxa, saida_items.valor_iva as valor_iva
            FROM saidas
            INNER JOIN saida_items ON saida_items.saida_id = saidas.id
            INNER JOIN produtos ON saida_items.produto_id = produtos.id
            INNER JOIN lotes ON saida_items.lote_id = lotes.id
            WHERE saidas.id = {$saida}");

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
        if($usar_lotes=='0'){
            foreach ($data as $produto) {
                $cart[$produto->produto_id] = [
                    'id' => $produto->produto_id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $produto->preco_unitario,
                    'preco_compra' => $produto->preco_compra,
                    'quantidade' => $produto->qnt,
                    'disponivel' => "",
                    'taxa'=> $produto->taxa,
                    'custo' => ($produto->preco_unitario * $produto->qnt),
                    'valor_iva' => $produto->valor_iva,
                ];
            }
        }else{
            foreach ($data as $produto) {
                $cart[$produto->produto_id] = [
                    'id' => $produto->lote_id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo_barras,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $produto->preco_unitario,
                    'preco_compra' => $produto->preco_compra,
                    'quantidade' => $produto->qnt,
                    'disponivel' => "",
                    'taxa'=> $produto->taxa,
                    'custo' => ($produto->preco_unitario * $produto->qnt),
                    'valor_iva' => $produto->valor_iva,
                ];
            }
        }

        session()->put('carinhoCotacaoUpdate', $cart);
        $json['success'] = true;
//            $json['message'] = "x $produto->descricao adicionado no carinho !";

        echo   json_encode($json);
    }

    public function adicionarCotacao2($produto_)
    {
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



        // print_r($empresa->usar_lotes);
        $usar_lotes = $empresa->usar_lotes;

        $loteController = new LoteController();

        $json['success'] = false;
        $json['message'] = null;

        $qnt = $_POST['cart_qnt'];
        $taxa = $_POST['taxa'];
        $disponivel = isset($_POST['disponivel']) ? $_POST['disponivel']:0;
        // $custo=$_POST['cart_coast'];
        $tax = $_POST['cart_tax'];
        $preco = $_POST['unit_price'];
        $type = $_POST['tipo'];
        $valor_iva = 0;
        $custo = (double)$preco*(double)$qnt;

        if($taxa != 0){
            $valor_iva = $custo-($custo/$empresa->iva);//$valor_iva = ($preco*$qnt)*($taxa/100);
            //            dd($valor_iva);
        }elseif($taxa == 0) {
            $valor_iva = 0;
        }
        $cart = session()->get('carinhoCotacaoUpdate');

        if($usar_lotes=='0'){

            $produto = Produto::find($produto_);
            $preco_compra = $loteController->preco_custo($produto->id);
            if (!empty($cart[$produto->id])) {
                if($type == 'direct'){
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] = $qnt;
                    $cart[$produto->id]['custo'] = ($preco * $qnt);
                }else{
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] += $qnt;
                    $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                }
                session()->put('carinhoCotacaoUpdate', $cart);
                $json['success'] = true;
                $json['message'] = "Quantidade de $produto->descricao actualizada !";

            } else {
                $cart[$produto->id] = [

                    'id' => $produto->id,
                    'produto_id' => $produto->id,
                    'codigo_barras' => $produto->codigo,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $preco,
                    'preco_compra' => $preco_compra,
                    'quantidade' => $qnt,
                    'disponivel' => $disponivel,
                    'taxa' => $taxa,
                    'custo' => ($preco * $qnt),
                    'valor_iva' => $valor_iva,
                ];
                session()->put('carinhoCotacaoUpdate', $cart);
                $json['success'] = true;
                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
            }
        }else{
            $produto = DB::selectOne("SELECT produtos.id as produto_id, lotes.codigo_barras as codigo_barras, lotes.id as id, produtos.descricao as descricao,
            lotes.preco_compra as preco_compra, lotes.preco_venda as preco_venda, lotes.taxa, lotes.qnt_disponivel
            FROM lotes
            INNER JOIN produtos ON produtos.id=lotes.produto_id
            WHERE lotes.codigo_barras='{$produto_}' AND produtos.activo='1' AND lotes.activo != '0'");

            $preco_compra = $produto->preco_compra;

            if (!empty($cart[$produto->id])) {
                if($type == 'direct'){
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] = $qnt;
                    $cart[$produto->id]['custo'] = ($preco * $qnt);
                }else{
                    $cart[$produto->id]['preco_unitario'] = $preco;
                    $cart[$produto->id]['quantidade'] += $qnt;
                    $cart[$produto->id]['custo'] = $cart[$produto->id]['preco_unitario']*$cart[$produto->id]['quantidade'];
                }
                session()->put('carinhoCotacaoUpdate', $cart);
                $json['success'] = true;
                $json['message'] = "Quantidade de $produto->descricao actualizada !";

            } else {
                $cart[$produto->id] = [

                    'id' => $produto->id,
                    'produto_id' => $produto->produto_id,
                    'codigo_barras' => $produto->codigo_barras,
                    'nome' => $produto->descricao,
                    'preco_unitario' => $preco,
                    'preco_compra' => $preco_compra,
                    'quantidade' => $qnt,
                    'disponivel' => $disponivel,
                    'taxa' => $taxa,
                    'custo' => ($preco * $qnt),
                    'valor_iva' => $valor_iva,
                ];
                session()->put('carinhoCotacaoUpdate', $cart);
                $json['success'] = true;
                $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
            }
        }
        echo   json_encode($json);
    }

    public function adicionarAbateUpdate($produto)
    {
        $produtoModel = new ProdutoController();
        // $loteController = new LoteController();
        $json['success'] = false;
        $json['message'] = null;
        $produto = Produto::find($produto);
        $loteController = new LoteController();

        $tipo_motivo = $_POST['tipo_motivo'];
        $motivo = $_POST['motivo'];
        $qnt = $_POST['cart_qnt'];
        $saida_id=$_POST['saida_id'];
        $preco = $_POST['unit_price'];
        $precoVenda = $_POST['unit_price_venda'];
        $cart = session()->get('carinhoAbateUpdate');

        // print_r(["produto"=>$produto,"preço = "=>$preco, "qnt="=>$qnt, "vezes="=>$preco]);

        if (empty($cart[$produto->id])) {

            $cart[$produto->id] = [
                'id' => $produto->id,
                'nome' => $produto->descricao,
                'preco_unitario' => (float) $preco,
                'preco_venda' => (float) $precoVenda,
                'quantidade' => (float) $qnt,
                // 'taxa' => $loteController->consulta($produto->id)['taxa'],
                'custo' => (float) ($preco * $qnt),
                'tipo_motivo' => $tipo_motivo,
                'motivo' => $motivo,
                'saida_id' => $saida_id,
                'saida_items' => null,
                'abate' => null,
                'activo' => '1',

            ];
            session()->put('carinhoAbateUpdate', $cart);
            $json['success'] = true;
            $json['message'] = $qnt . "x $produto->descricao adicionado no carinho !";
        }
//        $produtoModel->actualizarDiarioProduto();
        echo   json_encode($json);
    }


    public function AddCarinhoShowAbate(Request $request){
        $json['success'] = false;
        $json['message'] = null;
        $cart = session()->get('carinhoAbateUpdate');
        session()->pull('carinhoAbateUpdate', $cart);

        $cart = session()->get('carinhoAbateUpdate');

        $carinhoAbateUpdate = DB::select("SELECT saida_items.id as id,SUM(saida_items.quantidade) as quantidade,
       SUM(saida_items.quantidade * saida_items.preco_compra) AS total,
        saida_items.produto_id as produto_id,produtos.descricao as produto_descricao,
        saida_items.preco_unitario as preco_unitario, saida_items.preco_compra as preco_compra, custo,
        tipo_motivo, motivo, saida_items.lote_id as lote_id
        FROM saidas
        INNER JOIN saida_items ON saida_items.saida_id=saidas.id
        INNER JOIN produtos ON produtos.id=saida_items.produto_id
        WHERE tipo_saida_id = 2 AND saida_items.saida_id = $request->saida_id
        AND saidas.activo = '1' AND saida_items.activo = '1' GROUP BY produtos.id ");

        foreach($carinhoAbateUpdate as $abate){
            $preco_custo = number_format($abate->total/$abate->quantidade,2,'.','');
            $cart[$abate->produto_id] = [
                'id' => $abate->produto_id,
                'nome' => $abate->produto_descricao,
                'preco_unitario' => $preco_custo,
                'preco_compra' => $preco_custo,
                'quantidade' => $abate->quantidade,
                'custo' => ($abate->total),
                'tipo_motivo' => $abate->tipo_motivo,
                'motivo' => $abate->motivo,
                'saida_id' => $request->saida_id,
                'saida_items' => $abate->id,
                'abate' => '',
                'qnt_anterior' => '',
                'lote_id' => $abate->lote_id,
                'activo' => '1',
            ];
        }
        session()->put('carinhoAbateUpdate', $cart);
        $carinhoAbateUpdate = session()->get('carinhoAbateUpdate');

        $json['success'] = true;
        $json['message'] = "Carinho preenchido.";
        echo   json_encode($json);
    }

    public function selectLotes(Request $request){
        $produto_id = $request->produto_id;
        $tipo_motivo = (int)$request->tipo_motivo;

        $lotes = DB::select("SELECT lotes.id as id, lotes.codigo_barras, entradas.fornecedor_ref as fornecedor_ref, qnt_disponivel, entradas.data_aquisicao as data_aquisicao, data_validade FROM lotes
            INNER JOIN entradas ON entradas.id=lotes.entrada_id
            WHERE produto_id = {$produto_id} AND entradas.activo != '0' AND qnt_disponivel > 0 AND lotes.activo IN ('1','2')");

        // if($tipo_motivo == 2) {
        //     // dd("SELECT lotes.id as id, lotes.codigo_barras, entradas.fornecedor_ref as fornecedor_ref, qnt_disponivel, entradas.data_aquisicao as data_aquisicao, data_validade FROM lotes
        //     //     INNER JOIN entradas ON entradas.id=lotes.entrada_id
        //     //     WHERE produto_id = {$produto_id} AND entradas.activo != '0' AND qnt_disponivel > 0 AND lotes.activo != '0' AND DATEDIFF(data_validade,CURDATE()) > 0 AND DATEDIFF(data_validade,CURDATE()) <= 5");
        //     $lotes = DB::select("SELECT lotes.id as id, lotes.codigo_barras, entradas.fornecedor_ref as fornecedor_ref, qnt_disponivel, entradas.data_aquisicao as data_aquisicao, data_validade FROM lotes
        //         INNER JOIN entradas ON entradas.id=lotes.entrada_id
        //         WHERE produto_id = {$produto_id} AND entradas.activo != '0' AND qnt_disponivel > 0 AND lotes.activo != '0' AND DATEDIFF(data_validade,CURDATE()) <= 0");
        // }else{
        //     $lotes = DB::select("SELECT lotes.id as id, lotes.codigo_barras, entradas.fornecedor_ref as fornecedor_ref, qnt_disponivel, entradas.data_aquisicao as data_aquisicao, data_validade FROM lotes
        //         INNER JOIN entradas ON entradas.id=lotes.entrada_id
        //         WHERE produto_id = {$produto_id} AND entradas.activo != '0' AND qnt_disponivel > 0 AND lotes.activo != '0' AND DATEDIFF(data_validade,CURDATE()) > 0");
        // }

        return view("saida.selectLotes", compact("lotes"));
    }

    public function viewUpdateAbate($saida_id){
        $saida = DB::selectOne("SELECT saidas.id as id, numero_factura,tipo_motivo,motivo, cliente_id FROM saidas
        INNER JOIN saida_items ON saida_items.saida_id=saidas.id
        WHERE saidas.id = {$saida_id}");

        $clientes =  Cliente::where('activo', '=', '1')->get();
        $tipoClientes =  TipoCliente::all();

        $ultima_venda = Saida::latest()->first();
        $numero_factura = @$ultima_venda->id+1 ."/".date("Y");

        return view('saida.updateAbate', compact('saida', 'saida_id', 'clientes','numero_factura','tipoClientes'));
    }


    public function listarCarinhoAbateUpdate()
    {
        $saida_id = $_POST['saida_id'];
        $cart = array();
        $carinhoAbateUpdate = DB::select("SELECT saida_items.id as id,SUM(saida_items.quantidade) as quantidade,
        SUM(saida_items.quantidade * saida_items.preco_compra) AS total,
        saida_items.produto_id as produto_id,produtos.descricao as produto_descricao,
        saida_items.preco_unitario as preco_unitario, saida_items.preco_compra as preco_compra, custo,
        tipo_motivo, motivo, saida_items.lote_id as lote_id,
        entradas.fornecedor_ref as fornecedor_ref, lotes.id as lote, entradas.data_aquisicao as data_aquisicao, lotes.data_validade,
        DATE(saida_items.created_at) AS data_criacao
        FROM saidas
        INNER JOIN saida_items ON saida_items.saida_id=saidas.id
        INNER JOIN produtos ON produtos.id=saida_items.produto_id
        INNER JOIN lotes ON lotes.id=saida_items.lote_id
        INNER JOIN entradas ON entradas.id=lotes.entrada_id
        WHERE tipo_saida_id = 2 AND saida_items.saida_id = {$saida_id}
        AND saidas.activo = '1' AND saida_items.activo = '1' GROUP BY saida_items.id");

        foreach($carinhoAbateUpdate as $abate){
            $preco_custo = number_format($abate->total/$abate->quantidade,2,'.','');
            $cart[$abate->id] = [
                'id' => $abate->produto_id,
                'lote' => $abate->lote,
                'nome' => $abate->produto_descricao,
                'preco_unitario' => $preco_custo,
                'preco_compra' => $preco_custo,
                'quantidade' => $abate->quantidade,
                'custo' => ($abate->total),
                'tipo_motivo' => $abate->tipo_motivo,
                'motivo' => $abate->motivo,
                'saida_id' => $saida_id,
                'saida_items' => $abate->id,
                'abate' => '',
                'qnt_anterior' => '',
                'lote_id' => $abate->lote_id,
                'fornecedor_ref' => $abate->fornecedor_ref,
                'data_aquisicao' => $abate->data_aquisicao,
                'data_validade' => $abate->data_validade,
                'data_criacao' => $abate->data_criacao,
                'activo' => '1',
            ];
        }
        $carinhoAbateUpdate = $cart;//session()->get('carinhoAbateUpdate');
        return view('saida.fetchCarinhoAbateUpdate', compact('carinhoAbateUpdate'));
    }

    public function removerCarinhoAbate(Produto $produto)
    {

        $json['success'] = false;
        $cart = session()->get('carinhoAbateUpdate');

        if (isset($cart[$produto->id])) {
            // DB::table('saida_items')->where([''])->update()
            $cart[$produto->id]['activo'] = '0';
            session()->put('carinhoAbateUpdate', $cart);
            // session()->forget('carinhoAbateUpdate.' . $produto->id);
            $json['success'] = true;
        }
        echo  json_encode($json);
    }

    public function storeUpdateAbate(){
        $json['success'] = false;
        $json['message'] = "";
        $cart = session()->get('carinhoAbateUpdate');

        $carinhoInsert = array();
        $carinhoUpdate = array();
        $cont = 0;
        $saida_id = null;
        $update = null;
        $valor_total = null;

//        dd($cart);

        $loteModel = new LoteController();

            if (isset($cart) AND count($cart) > 0) {
                foreach ($cart as $item) {
                    if(!isset($item['saida_items']) || empty($item['saida_items'])){ //Adicionar novo item na base de dados
                        $lotes = $loteModel->retornaLoteByProduto($item['id']);
                        if (count($lotes) > 1) {

                            $menorData = strtotime($lotes[0]->data_aquisicao);


                            $controlaQnt = 0;

                            foreach ($lotes as $lote) {

                                $dataAquisicao = strtotime($lote->data_aquisicao);

                                if ($menorData > $dataAquisicao) {
                                    $menorData = $dataAquisicao;
                                }

                                if ($item['quantidade'] != 0) {

                                    if ($lote->qnt_disponivel > 0) {

                                        if (($qntItem = $item['quantidade']) > ($qntLote = $lote->qnt_disponivel)) {

                                            $controlaQnt = (double) ($qntItem - $qntLote);

                                            if ($controlaQnt < 0) {
                                                $controlaQnt = (double) ($controlaQnt * (-1));
                                            }

                                            $item['quantidade'] -= $qntLote;

                                            $item['custo'] = $qntLote * $item['preco_unitario'];

                                            $carinhoInsert[$cont++] =
                                                [
                                                    'produto_id' => $lote->produto_id,
                                                    'lote_id' => $lote->id,
                                                    'saida_id' => $item['saida_id'],
                                                    'quantidade' => $qntLote,
                                                    'preco_unitario' => $item['preco_venda'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $qntLote*$lote->preco_compra,
                                                    'tipo_motivo' => $item['tipo_motivo'],
                                                    'motivo' => $item['motivo']

                                                ];
                                            $valor_total += $qntLote*$item['preco_unitario'];
                                            // print_r("entrou");
                                            $loteModel->abater($lote->id, $qntLote);
                                        } else {

                                            $item['custo'] = $item['quantidade'] *  $item['preco_unitario'];

                                            $carinhoInsert =
                                                [
                                                    'produto_id' => $lote->produto_id,
                                                    'lote_id' => $lote->id,
                                                    'saida_id' => $item['saida_id'],
                                                    'quantidade' => $item['quantidade'],
                                                    'preco_unitario' => $item['preco_venda'],
                                                    'preco_compra' => $lote->preco_compra,
                                                    'custo' => $item['custo'],
                                                    'tipo_motivo' => $item['tipo_motivo'],
                                                    'motivo' => $item['motivo']
                                                ];

                                            $item['quantidade'] -= $item['quantidade'];
                                            $valor_total += $item['quantidade']*$item['preco_unitario'];
                                            $loteModel->abater($lote->id, $qntItem);
                                        }
                                    }
                                }
                            }
                        } else {

                            if(count($lotes)==0){
                                $lotes = $loteModel->retornaUltimoLote($item['id']);
                                $carinhoInsert =
                                [
                                    'produto_id' => $lotes->produto_id,
                                    'lote_id' => $lotes->id,
                                    'saida_id' => $item['saida_id'],
                                    'quantidade' => $item['quantidade'],
                                    'preco_compra' => $lotes[0]->preco_compra,
                                    'preco_unitario' => $item['preco_venda'],//$item['preco_unitario'],
                                    'custo' => $item['quantidade']*$lotes[0]->preco_compra,
                                    'tipo_motivo' => $item['tipo_motivo'],
                                    'motivo' => $item['motivo'],
                                ];

                                $valor_total += $item['quantidade']*$item['preco_unitario'];
                                $loteModel->abater($lotes->id, $item['quantidade']);
                            }else{
                                $carinhoInsert =
                                [
                                    'produto_id' => $lotes[0]->produto_id,
                                    'lote_id' => $lotes[0]->id,
                                    'saida_id' => $item['saida_id'],
                                    'quantidade' => $item['quantidade'],
                                    'preco_unitario' => $item['preco_venda'],//$item['preco_unitario'],
                                    'preco_compra' => $lotes[0]->preco_compra,
                                    'custo' => $item['quantidade']*$item['preco_unitario'],
                                    'tipo_motivo' => $item['tipo_motivo'],
                                    'motivo' => $item['motivo'],
                                ];

                                $valor_total += $item['quantidade']*$item['preco_unitario'];
                                // print_r(['lote_id'=>$lotes[0]->id, 'qnt'=>$item['quantidade']]);
                                $loteModel->abater($lotes[0]->id, $item['quantidade']);
                            }
                        }


                        if (DB::table('saida_items')->insert($carinhoInsert)) {

                            // foreach ($carinhoInsert as $key=>$cabecudo) {
//                                DiarioStock::where([['produto_id','=',$carinhoInsert['produto_id'] ]])->decrement('qnt_disponivel',$carinhoInsert['quantidade']);
                            // }

                        }else{
                            $json['success'] =false;
                            $json['message'] = "Erro ao registar!.";

                            // print_r($carinhoInsert);
                        }
                    }else { // Actualizar as quantidades do produto referente ao abate no sistema
                        $produto_id = $item['id'];
                        $saida_id = $item['saida_id'];

                        //Pesquisa pela quantidade actual do produto na saída
                        $qtdActual = DB::selectOne("SELECT SUM(quantidade) AS total FROM saida_items
                        WHERE saida_id = '{$saida_id}' AND produto_id='{$produto_id}' AND activo='1' ")->total;

                        $item['abate'] = $qtdActual - $item['quantidade'];
                        $quantidadeRemanescente = $item['abate'];
//dd($quantidadeRemanescente);
                        $saida_items_list = SaidaItem::where([ // Consulta de todos os itens do produto da saída.
                                'saida_id'=> $saida_id,
                                'produto_id'=> $produto_id,
                                'activo' => '1'
                        ])->orderByDesc('id')->get();
                        if ($quantidadeRemanescente > 0) { // Condição para a devolução
                            foreach ($saida_items_list as $saida_item) {
                                if ($quantidadeRemanescente > 0) { // Caso tenham mais quantidades por devolver
                                    $qtdItem = $saida_item->quantidade;
                                    $itemId = $saida_item->id;
                                    if ($qtdItem <= $quantidadeRemanescente) {
                                        $carinhoUpdate = [
                                                'activo' => '0',
                                            ];
                                        SaidaItem::where('id', $itemId)->update($carinhoUpdate);
                                    }
                                    else{
                                        $qtdActualizada = $qtdItem-$quantidadeRemanescente;
                                        $carinhoUpdate =
                                            [
                                                'quantidade' => $qtdActualizada,
//                                                'preco_unitario' => $item['preco_unitario'],
//                                                'preco_compra' => $item['preco_unitario'],
                                                'custo' => $qtdActualizada*$item['preco_unitario'],
                                                'activo' => $item['activo'],

                                            ];
                                        SaidaItem::where('id', $itemId)->update($carinhoUpdate);
                                    }
                                    $quantidadeRemanescente -= $qtdItem;
                                } else
                                    break;
                            }
                        }else{
//                            dd($item['abate'] *=-1);
                            $item['abate'] *=-1;
                            $carinhoUpdate =
                                [
                                    'quantidade' => $item['quantidade'],
//                                    'preco_unitario' => $item['preco_unitario'],
//                                    'preco_compra' => $item['preco_unitario'],
                                    'custo' => $item['quantidade']*$item['preco_unitario'],
                                    'activo' => $item['activo'],

                                ];
//                            dd($carinhoUpdate);
                            SaidaItem::where('id', $item['saida_items'])->update($carinhoUpdate);
                        }

                        $valor_total += $item['quantidade']*$item['preco_unitario'];
                        $saida_id = $item['saida_id'];

                        if($qtdActual<$item['quantidade']){
                            $loteModel->abater((int)$item['lote_id'], (int)$item['abate']);
                        }else {
                            $loteModel->devolverQnt((int)$item['lote_id'],(int)$item['abate']);
                        }
                    }

                    if($item['activo']=="0"){
                        $loteModel->devolverQnt($item['lote_id'], $item['quantidade']);
                    }

                }
                DB::table('saidas')->where('id',$saida_id)->update(['valor_total'=>$valor_total]);
            }

        // if(){
           $json['success'] = true;
           $json['message'] = "Registado com sucesso.";
        // }

        echo  json_encode($json);
    }

    public function removerAbate($saida_items_id)
    {
        $json['success'] = false;
        $cart = session()->get('carinhoAbate');
        $loteModel = new LoteController();
        $produtoController = new ProdutoController();


//        $saida_items_id = $_POST['saida_items_id'];

        $sel = DB::selectOne("SELECT quantidade, lote_id,produto_id  FROM saida_items WHERE id = $saida_items_id");

        $lol = DB::selectOne("SELECT qnt_disponivel FROM lotes WHERE id={$sel->lote_id}");

        $res = (double)$sel->quantidade + (double)$lol->qnt_disponivel;


        $quantidade = (float) $sel->quantidade;
        $quantidade_actual = $loteModel->qnt_disponivel_produto($sel->produto_id);
        $user_id = auth()->user()->id;
        $qntBalancoAnterior = $produtoController->productBalanceStock($sel->produto_id);


//        if (isset($cart[$produto->id])) {

        if(DB::table('saida_items')->where(['id'=>$saida_items_id])->update(['activo'=>'0'])){
            if($res >= 0) {
                DB::table('lotes')->where('id', $sel->lote_id)->update(['qnt_disponivel' => $res,'activo'=>'1']);
            }else{
                DB::table('lotes')->where('id', $sel->lote_id)->update(['qnt_disponivel' => $res]);
            }
            $produtoController->checkProductBalance($sel->produto_id, 'saida_cancelar', $quantidade, $quantidade_actual,$qntBalancoAnterior, $user_id);
            $json['success'] = true;

        }
//            session()->forget('carinhoAbate.' . $produto->id);

//        }
        echo  json_encode($json);
    }


    public function remover(Request $request)
    {
        $json['success'] = false;
        $cart = session()->get('carinho');
        $quantidadeRemover = $cart[$request->id]['quantidade'];

        if (isset($cart[$request->id])) {
            $produto = $cart[$request->id]['produto_id'];
            $lotes = DB::select("
                SELECT lotes.id, preco_venda, preco_compra, SUM(qnt_disponivel) qnt_disponivel
                FROM lotes
                WHERE qnt_disponivel > 0 AND activo = 1 AND produto_id={$produto}
                GROUP BY produto_id, preco_venda
                ORDER BY data_factura
            ");
            session()->forget('carinho.' . $request->id);

            //Log de cancelamento de vendas
            $logsVenda = new LogsVenda(
                array(
                    "quantidade" => $cart[$request->id]['quantidade'],
                    "preco_venda" => $cart[$request->id]['preco_unitario'],
                    // "preco_compra" => $cart[$request->id]['preco_compra'],
                    "tipo_log" => $request->tipo,
                    "produto_id" => $produto,
                    // "entrada_item_id" => $cart[$request->id]['lote_id'],
                    "user_id" => Auth::user()->id,
                )
            );
            $logsVenda->save();
            //Logs End ///

            foreach ($lotes AS $lote){ // quantidade total
                if($lote->id != $request->id){
                    if (isset($cart[$lote->id])) {
                        $cart = session()->get('carinho');
                        $cart[$lote->id]['quantidade_total'] -= $quantidadeRemover;
                        session()->put('carinho', $cart);
                    }
                }
            }
            $json['success'] = true;
        }
        echo  json_encode($json);
    }

    //Remover cotacao
//    public function removerCotacao(Request $request)
//    {
//        $json['success'] = false;
//        $cart = session()->get('carinhoCotacao');
//        $quantidadeRemover = $cart[$request->id]['quantidade'];
//
//        if (isset($cart[$request->id])) {
//            $produto = $cart[$request->id]['id'];
//            $lotes = DB::select("
//                SELECT lotes.id, preco_venda, preco_compra, SUM(qnt_disponivel) qnt_disponivel
//                FROM lotes
//                WHERE qnt_disponivel > 0 AND activo = 1 AND produto_id={$produto}
//                GROUP BY produto_id, preco_venda
//                ORDER BY data_factura
//            ");
//            session()->forget('carinhoCotacao.' . $request->id);
//
//            //Log de cancelamento de vendas
//            $logsVenda = new LogsVenda(
//                array(
//                    "quantidade" => $cart[$request->id]['quantidade'],
//                    "preco_venda" => $cart[$request->id]['preco_unitario'],
//                    // "preco_compra" => $cart[$request->id]['preco_compra'],
//                    "tipo_log" => $request->tipo,
//                    "produto_id" => $produto,
//                    // "entrada_item_id" => $cart[$request->id]['lote_id'],
//                    "user_id" => Auth::user()->id,
//                )
//            );
//            $logsVenda->save();
//            //Logs End ///
//
//            foreach ($lotes AS $lote){ // quantidade total
//                if($lote->id != $request->id){
//                    if (isset($cart[$lote->id])) {
//                        $cart = session()->get('carinhoCotacao');
//                        $cart[$lote->id]['quantidade_total'] -= $quantidadeRemover;
//                        session()->put('carinhoCotacao', $cart);
//                    }
//                }
//            }
//            $json['success'] = true;
//        }
//        echo  json_encode($json);
//    }
    //End remover cotacao
    public function removerCotacao(Produto $produto)
    {

        $json['success'] = false;
        $cart = session()->get('carinhoCotacao');

        if (isset($cart[$produto->id])) {

            session()->forget('carinhoCotacao.' . $produto->id);
            $json['success'] = true;
        }
        echo  json_encode($json);
    }

    public function removerCotacao2(Produto $produto)
    {

        $json['success'] = false;
        $cart = session()->get('carinhoCotacaoUpdate');

        if (isset($cart[$produto->id])) {

            session()->forget('carinhoCotacaoUpdate.' . $produto->id);
            $json['success'] = true;
        }
        echo  json_encode($json);
    }


    public function remover_credito(Request $request)
    {
        $json['success'] = false;
        $cart = session()->get('carinho_credito');
        // dd($request->all());
        $quantidadeRemover = $cart[$request->id]['quantidade'];

        if (isset($cart[$request->id])) {
            $produto = $cart[$request->id]['produto_id'];
            $lotes = DB::select("
                SELECT lotes.id, preco_venda, preco_compra, SUM(qnt_disponivel) qnt_disponivel
                FROM lotes
                WHERE qnt_disponivel > 0 AND activo = 1 AND produto_id={$produto}
                GROUP BY produto_id, preco_venda
                ORDER BY data_factura
            ");
            session()->forget('carinho_credito.' . $request->id);

            //Log de cancelamento de vendas
            $logsVenda = new LogsVenda(
                array(
                    "quantidade" => $cart[$request->id]['quantidade'],
                    "preco_venda" => $cart[$request->id]['preco_unitario'],
                    // "preco_compra" => $cart[$request->id]['preco_compra'],
                    "tipo_log" => $request->tipo,
                    "produto_id" => $produto,
                    // "entrada_item_id" => $cart[$request->id]['lote_id'],
                    "user_id" => Auth::user()->id,
                )
            );
            $logsVenda->save();
            //Logs End ///

            foreach ($lotes AS $lote){ // quantidade total
                if($lote->id != $request->id){
                    if (isset($cart[$lote->id])) {
                        $cart = session()->get('carinho_credito');
                        $cart[$lote->id]['quantidade_total'] -= $quantidadeRemover;
                        session()->put('carinho_credito', $cart);
                    }
                }
            }
            $json['success'] = true;
        }
        echo  json_encode($json);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Saida\Saida  $saida
     * @return \Illuminate\Http\Response
     */
    public function destroy(Saida $saida)
    {
        //
    }
    public function gerapdf()
    {
        $saida = Saida::where('activo', '=', '1')->get();;
        $pdf = PDF::loadView('saida.pdf', compact('saida'));
        return $pdf->setPaper('A4')->stream('Todas as Saidas');
    }
    public function gerashowpdf(Saida $saida)
    {
        //   dd($saida)
        $pdf = PDF::loadView('saida.showPdf', compact('saida'));
        $pdf->setPaper('A4')->stream('Saida');
    }

    public function cancelar(Saida $saida)
    {
        $produtoModel = new ProdutoController();
        $json['success'] = false;
        $data = [
            'activo' => '2',
            'user_id' => Auth::user()->id
        ];

        if ($saida->update($data)) {
            foreach ($saida->saidaItems as  $item) {
                $lote = Lote::find($item->lote_id);
                $data['activo'] = '1';
                if ($item->lote()->increment("qnt_disponivel", $item->quantidade)) {
                    $lote->update($data);
                    $json['success'] += true;
                }
            }
        }
//        $produtoModel->actualizarDiarioProduto();
        echo json_encode($json);
    }



    public function recibo(Request $request)
    {
        $json['success'] = true;
        $empresa = DB::table('empresa')->first();
        $formaPagamentos = FormaPagamento::all();
        $saida = DB::table('saidas AS s')
            ->select('s.*', 'clientes.nome AS cliente_nome', 'clientes.endereco AS cliente_endereco',
            'clientes.nuit AS cliente_nuit', 'clientes.contacto AS cliente_contacto','name AS userName',
            DB::raw('SUM(DISTINCT s.desconto) AS desconto'),
            DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'))
            ->join('clientes', 'clientes.id', '=', 's.cliente_id')
            ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
            ->leftJoin('users', 's.user_id', '=', 'users.id')
            ->where('s.id', '=', $request->saida)->first();

        $tipo_pagamento = DB::select("SELECT saida_tipo_pagamentos.tipo_pagamento_id, designacao, valor FROM tipo_pagamentos, saida_tipo_pagamentos WHERE saida_tipo_pagamentos.saida_id = {$request->saida} AND saida_tipo_pagamentos.tipo_pagamento_id = tipo_pagamentos.id");

        $validate = DB::selectOne("SELECT validade_cotacao FROM saidas WHERE saidas.id={$request->saida}");
        if($empresa->fonte == '1') {
             $saidaItems = DB::table('saida_items AS si')
            ->select('si.preco_unitario','si.tipo_motivo','si.motivo','si.user_id','si.activo','si.desconto_percentual', 'si.desconto_valor',
                DB::raw('SUM(si.quantidade) AS quantidade'),DB::raw('SUM(si.custo) AS custo'),
                'produtos.descricao AS produto_descricao', 'produtos.codigo', 'valor_iva as taxa')
            ->join('lotes', 'lotes.id', '=', 'lote_id')
            ->join('produtos', 'produtos.id', '=', 'si.produto_id')
            ->where('saida_id', '=', $request->saida)
            ->where('si.activo', '!=', '2')
            ->groupBy('si.lote_id')->get();

        }else{
            if($empresa->pacote==1){
                $saidaItems = DB::table('saida_items AS si')
                    ->select('si.preco_unitario','si.tipo_motivo','si.motivo','si.user_id','si.activo',
                        DB::raw('SUM(si.quantidade) AS quantidade'),DB::raw('SUM(si.custo) AS custo'),
                        'produtos.descricao AS produto_descricao', 'produtos.codigo', 'valor_iva as taxa')
                    ->join('produtos', 'produtos.id', '=', 'si.produto_id')
                    ->where('saida_id', '=', $request->saida)
                    ->where('si.activo', '!=', '2')
                    ->groupBy('si.produto_id')->get();
            }else{
                $saidaItems = DB::table('saida_items AS si')
                    ->select('si.preco_unitario','si.tipo_motivo','si.motivo','si.user_id','si.activo',
                        DB::raw('SUM(si.quantidade) AS quantidade'),DB::raw('SUM(si.custo) AS custo'),
                        'produtos.descricao AS produto_descricao', 'produtos.codigo', 'valor_iva as taxa')
                    ->join('lotes', 'lotes.id', '=', 'lote_id')
                    ->join('produtos', 'produtos.id', '=', 'si.produto_id')
                    ->where('saida_id', '=', $request->saida)
                    ->where('si.activo', '!=', '2')
                    ->groupBy('si.lote_id')->get();
            }
        }

        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['formaPagamentos'] = json_encode($formaPagamentos);
        $_SESSION['saida'] = json_encode($saida);
        $_SESSION['saidaItems'] = json_encode($saidaItems);
        $_SESSION['tipoPagamento'] = json_encode($tipo_pagamento);
        $_SESSION['user'] = json_encode(auth()->user()->name);
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();

//        dd($tipo_pagamento);
        echo json_encode($json);
        // return view('saida.invoice', compact('empresa','formaPagamentos', 'saida','saidaItems','bancos','tipo_pagamentos'));
    }

    public function recibo_cotacao(Request $request)
    {
        $json['success'] = true;
        $empresa = DB::table('empresa')->first();
        $formaPagamentos = FormaPagamento::all();
        $saida = DB::table('cotacao AS s')
            ->select('s.*', 'clientes.nome AS cliente_nome', 'clientes.endereco AS cliente_endereco',
            'clientes.nuit AS cliente_nuit', 'clientes.contacto AS cliente_contacto','name AS userName',
            DB::raw('SUM(DISTINCT s.desconto) AS desconto'),
            DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'))
            ->join('clientes', 'clientes.id', '=', 's.cliente_id')
            ->join('cotacao_items', 's.id', '=', 'cotacao_items.saida_id')
            ->leftJoin('users', 's.user_id', '=', 'users.id')
            ->where('s.id', '=', $request->saida)->first();

        $tipo_pagamento = DB::select("SELECT saida_tipo_pagamentos.tipo_pagamento_id, designacao, valor FROM tipo_pagamentos, saida_tipo_pagamentos WHERE saida_tipo_pagamentos.saida_id = {$request->saida} AND saida_tipo_pagamentos.tipo_pagamento_id = tipo_pagamentos.id");

        $validate = DB::selectOne("SELECT validade_cotacao FROM saidas WHERE saidas.id={$request->saida}");
        if($empresa->fonte == '1') {
             $saidaItems = DB::table('cotacao_items AS si')
            ->select('si.preco_unitario','si.tipo_motivo','si.motivo','si.user_id','si.activo','si.desconto_percentual', 'si.desconto_valor',
                DB::raw('SUM(si.quantidade) AS quantidade'),DB::raw('SUM(si.custo) AS custo'),
                'produtos.descricao AS produto_descricao', 'produtos.codigo', 'valor_iva as taxa')
            ->join('lotes', 'lotes.id', '=', 'lote_id')
            ->join('produtos', 'produtos.id', '=', 'si.produto_id')
            ->where('saida_id', '=', $request->saida)
            ->where('si.activo', '!=', '2')
            ->groupBy('si.lote_id')->get();

        }else{
            $saidaItems = DB::table('cotacao_items AS si')
                ->select('si.preco_unitario','si.tipo_motivo','si.motivo','si.user_id','si.activo',
                    DB::raw('SUM(si.quantidade) AS quantidade'),DB::raw('SUM(si.custo) AS custo'),
                    'produtos.descricao AS produto_descricao', 'produtos.codigo', 'valor_iva as taxa')
                ->join('lotes', 'lotes.id', '=', 'lote_id')
                ->join('produtos', 'produtos.id', '=', 'si.produto_id')
                ->where('saida_id', '=', $request->saida)
                ->where('si.activo', '!=', '2')
                ->groupBy('si.lote_id')->get();
        }

        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['formaPagamentos'] = json_encode($formaPagamentos);
        $_SESSION['saida'] = json_encode($saida);
        $_SESSION['saidaItems'] = json_encode($saidaItems);
        $_SESSION['tipoPagamento'] = json_encode($tipo_pagamento);
        $_SESSION['user'] = json_encode(auth()->user()->name);
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();

//        dd($tipo_pagamento);
        echo json_encode($json);
        // return view('saida.invoice', compact('empresa','formaPagamentos', 'saida','saidaItems','bancos','tipo_pagamentos'));
    }


    public function invoice($saida_id,Request $request)
    {
        $empresa = DB::table('empresa')->first();
        $formaPagamentos = FormaPagamento::all();
        $saida = DB::table('saidas AS s')
            ->select('s.*', 'clientes.nome AS cliente_nome', 'clientes.endereco AS cliente_endereco',
            'clientes.nuit AS cliente_nuit', 'clientes.contacto AS cliente_contacto', 'valor_total_iva',
            DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'))
            ->join('clientes', 'clientes.id', '=', 's.cliente_id')
            ->join('saida_items', 's.id', '=', 'saida_items.saida_id')
            ->where('s.id', '=', $saida_id)->first();

        $saidaItems = DB::table('saidas AS si')
            ->select('si.*', 'produtos.descricao AS produto_descricao', 'produtos.codigo', 'saida_items.preco_unitario as preco_unitario', 'saida_items.quantidade as quantidade', 'valor_iva as taxa')
            ->join('saida_items', 'si.id', '=', 'saida_items.saida_id')
            ->join('lotes', 'lotes.id', '=', 'lote_id')
            ->join('produtos', 'produtos.id', '=', 'saida_items.produto_id')
            ->where('saida_id', '=', $saida_id)
            ->where('si.activo', '!=', '2')->get();

        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['formaPagamentos'] = json_encode($formaPagamentos);
        $_SESSION['saida'] = json_encode($saida);
        $_SESSION['saidaItems'] = json_encode($saidaItems);
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();
        return view('saida.invoice', compact('empresa','formaPagamentos', 'saida','saidaItems','bancos','tipo_pagamentos'));
    }

    public function invoiceProforma($saida_id,Request $request)
    {
        $empresa = DB::table('empresa')->first();
        $formaPagamentos = FormaPagamento::all();
        $saida = DB::table('cotacao AS s')
            ->select('s.*', 'clientes.nome AS cliente_nome', 'clientes.endereco AS cliente_endereco',
                'clientes.nuit AS cliente_nuit', 'clientes.contacto AS cliente_contacto', 'valor_total_iva',
                DB::raw('(SELECT total_devolucao FROM devolucao_agregada WHERE saida_id = s.id) AS totalDevolucao'))
            ->join('clientes', 'clientes.id', '=', 's.cliente_id')
            ->join('cotacao_items', 's.id', '=', 'cotacao_items.saida_id')
            ->where('s.id', '=', $saida_id)->first();

        $saidaItems = DB::table('saidas AS si')
            ->select('si.*', 'produtos.descricao AS produto_descricao', 'produtos.codigo', 'cotacao_items.preco_unitario as preco_unitario', 'cotacao_items.quantidade as quantidade', 'valor_iva as taxa')
            ->join('cotacao_items', 'si.id', '=', 'cotacao_items.saida_id')
            ->join('lotes', 'lotes.id', '=', 'lote_id')
            ->join('produtos', 'produtos.id', '=', 'cotacao_items.produto_id')
            ->where('saida_id', '=', $saida_id)
            ->where('si.activo', '!=', '2')->get();

        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['formaPagamentos'] = json_encode($formaPagamentos);
        $_SESSION['saida'] = json_encode($saida);
        $_SESSION['saidaItems'] = json_encode($saidaItems);
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();
        return view('saida.invoiceProforma', compact('empresa','formaPagamentos', 'saida','saidaItems','bancos','tipo_pagamentos'));
    }

    public function invoice_listar()
    {
        $invoice = DB::select("SELECT produtos.codigo as produtocodigo, produtos.nome as produtonome, saida_items.quantidade as quantidade, preco_unitario as precounitario
        FROM saida_items
        INNER JOIN produtos ON produtos.id=saida_items.produto_id
        INNER JOIN lotes ON lotes.id=saida_items.lote_id
        WHERE saida_items.activo = 1 GROUP BY produtos.codigo
        ");
        // print_r($invoice);
        return view('saida.invoiceTable', compact('invoice'));
    }


    public function devolucoes()
    {
        $data_inicio = "";
        $data_fim = "";
        $produto = "";
        $produto_text = "";

        $cont = 0;
        $data = '';

        if (isset($_POST['produto_id']) && !empty($_POST['produto_id'])) {
            $produto = (int) $_POST['produto_id'];
            $produto_dados = DB::selectOne("SELECT * FROM produtos WHERE id = {$produto}");
            $produto_text = $produto_dados->codigo." || ".$produto_dados->descricao;
            $data .= " AND produtos.id = {$produto} ";
        }

        if (!empty($_POST['data_inicio']) && !empty($_POST['data_fim'])) {
            $data_inicio = date($_POST['data_inicio']);
            $data_fim = date($_POST['data_fim']);
            $data .= " AND DATE(devolucao.created_at) BETWEEN '{$data_inicio}' AND '{$data_fim}' ";
        }

        //paginação pedente
        $devolucoes = DB::select("SELECT produtos.codigo AS produto_codigo, produtos.descricao AS produto_descricao,
        SUM(devolucao.quantidade) AS qntDevolucao, devolucao.preco_unitario AS preco_unitario, SUM(devolucao.preco_unitario*devolucao.quantidade) AS total
        FROM saida_items AS si
        INNER JOIN saidas s on si.saida_id = s.id
        INNER JOIN produtos ON produtos.id=si.produto_id
        INNER JOIN devolucao ON devolucao.saida_item_id=si.id
        WHERE s.tipo_saida_id = '1' {$data} GROUP BY si.produto_id
        ");

        return view('relatorios/devolucao.table', compact('devolucoes', 'data_inicio', 'data_fim', 'produto_text'));
    }

    public function devolucoes_index()
    {
        $produto = Produto::all();
        $clientes = Cliente::where('activo', '=', '1')->get();
        $tipo_saidas = TipoSaida::all();
        $bancos = Banco::all();
        $tipo_pagamentos = TipoPagamento::all();
        return view('relatorios/devolucao.index', compact('produto','clientes', 'tipo_saidas','bancos', 'tipo_pagamentos'));
    }


    // Pagamentos
    public function indexPagamentos()
    {
        $tipo_pagamentos = TipoPagamento::all();
        $clientes = Cliente::where('activo', '=', '1')->get();
        return view('documentos/pagamentos.index',compact('tipo_pagamentos','clientes'));
    }

    public function pagamentos(Request $request)
    {

        $tipo_pagamento_id = null;
        $tipo_pagamento = null;
        $cliente_id = null;
        $cliente_nome = null;
        $dataInicio = null;
        $dataFim = null;
        $cont = 0;
        $data = [];

        if (isset($request->cliente_id) && !empty($request->cliente_id)) {
            $cliente_id = $request->cliente_id;

            $data[$cont++] = ['pagamentos.cliente_id', '=', $cliente_id];

            $cliente_1 = DB::selectOne("SELECT nome
            FROM `clientes`
            WHERE id={$cliente_id}");
            $cliente_nome = $cliente_1->nome;
        }
        if (isset($request->tipo_pagamento_id) && !empty($request->tipo_pagamento_id)) {
            $tipo_pagamento_id = $request->tipo_pagamento_id;

            $data[$cont++] = ['pagamentos.tipo_pagamento_id', '=', $tipo_pagamento_id];

            $tp = DB::selectOne("SELECT designacao
            FROM `tipo_pagamentos`
            WHERE id={$tipo_pagamento_id}");
            $tipo_pagamento = $tp->designacao;
        }

        if (isset($request->dataInicio) && !empty($request->dataInicio) && !empty($request->dataFim)) {

            $dataInicio = $request->dataInicio;
            $dataFim = $request->dataFim;

            $data[$cont++] = ['data_pagamento', '>=', $dataInicio." 00:00:00"];
            $data[$cont++] = ['data_pagamento', '<=', $dataFim." 23:59:59"];
        }

        $pagamentos = DB::table('pagamentos')->select('pagamentos.id as id', 'pagamentos.cliente_id as cliente_id','clientes.nome as cliente',
        'data_pagamento',DB::raw('COUNT(DISTINCT numero_factura) as qntFactura'),'pagamentos.valor_pago as valorPago','tipo_pagamentos.designacao as tipoPagamento')
        ->join('tipo_pagamentos', 'pagamentos.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        ->join('saidas','pagamentos.saida_id','=','saidas.id')
        ->join('clientes','pagamentos.cliente_id','=','clientes.id')
        ->where($data)
        ->groupBy('pagamentos.cliente_id')
        ->orderBy('pagamentos.created_at', 'desc')
        ->paginate((int) $request->limite);

        return view('documentos/pagamentos.table',compact('pagamentos','tipo_pagamento','cliente_nome', 'dataInicio', 'dataFim'));
    }

    public function detailsPagamentos($cliente_id,$dataInicio,$dataFim){




        $pagamento = DB::selectOne("SELECT pagamentos.id as id,
        CAST(data_pagamento AS date) as data_pagamento,pagamentos.valor_pago as valorPago
        FROM pagamentos
        WHERE pagamentos.cliente_id = $cliente_id AND (CAST(data_pagamento AS date) BETWEEN '$dataInicio' AND '$dataFim')");

        // print_r($pagamento);

        // $pagamentos = DB::select("SELECT DISTINCT numero_factura,pagamentos.id as id,clientes.nome as cliente,
        // data_pagamento,pagamentos.valor_pago as valorPago,tipo_pagamentos.designacao as tipoPagamento
        // FROM pagamentos
        // INNER JOIN tipo_pagamentos ON pagamentos.tipo_pagamento_id = tipo_pagamentos.id
        // INNER JOIN saidas ON pagamentos.saida_id = saidas.id
        // INNER JOIN clientes ON pagamentos.cliente_id = clientes.id
        // WHERE pagamentos.id = $cliente_id");

        $pagamentos = DB::table('pagamentos')->select('saidas.data as dataSaida','pagamentos.id as id', 'pagamentos.cliente_id as cliente_id','clientes.nome as cliente',
        'data_pagamento','numero_factura','pagamentos.valor_pago as valorPago','tipo_pagamentos.designacao as tipoPagamento')
        ->join('tipo_pagamentos', 'pagamentos.tipo_pagamento_id', '=', 'tipo_pagamentos.id')
        ->join('saidas','pagamentos.saida_id','=','saidas.id')
        ->join('clientes','pagamentos.cliente_id','=','clientes.id')
        ->where('pagamentos.cliente_id','=',$cliente_id)->get();
        // ->groupBy('pagamentos.cliente_id')
        // ->orderBy('pagamentos.created_at', 'desc')
        // ->paginate((int) $request->limite);


        $empresa = DB::table('empresa')->first();

        $cliente = DB::selectOne("SELECT * FROM clientes WHERE id=$cliente_id");
        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['cliente'] = json_encode($cliente);
        $_SESSION['pagamento'] = json_encode($pagamento);
        $_SESSION['pagamentos'] = json_encode($pagamentos);

        return view('documentos/pagamentos.details',compact('pagamento', 'pagamentos', 'empresa', 'cliente'));
    }



    // Nota de crédito
    public function indexNotaCredito()
    {
        $tipo_pagamentos = TipoPagamento::all();
        $clientes = Cliente::where('activo', '=', '1')->get();
        return view('documentos/nota_credito.index',compact('tipo_pagamentos','clientes'));
    }

    public function nota_credito(Request $request)
    {

        $tipo_pagamento_id = null;
        $tipo_pagamento = null;
        $cliente_id = null;
        $cliente_nome = null;
        $dataInicio = null;
        $dataFim = null;
        $cont = 0;
        $data = [];

        if (isset($request->cliente_id) && !empty($request->cliente_id)) {
            $cliente_id = $request->cliente_id;

            $data[$cont++] = ['saidas.cliente_id', '=', $cliente_id];

            $cliente_1 = DB::selectOne("SELECT nome
            FROM `clientes`
            WHERE id={$cliente_id}");
            $cliente_nome = $cliente_1->nome;
        }
        // if (isset($request->tipo_pagamento_id) && !empty($request->tipo_pagamento_id)) {
        //     $tipo_pagamento_id = $request->tipo_pagamento_id;

        //     $data[$cont++] = ['pagamentos.tipo_pagamento_id', '=', $tipo_pagamento_id];

        //     $tp = DB::selectOne("SELECT designacao
        //     FROM `tipo_pagamentos`
        //     WHERE id={$tipo_pagamento_id}");
        //     $tipo_pagamento = $tp->designacao;
        // }

        if (isset($request->dataInicio) && !empty($request->dataInicio) && !empty($request->dataFim)) {

            $dataInicio = $request->dataInicio;
            $dataFim = $request->dataFim;

            $data[$cont++] = ['devolucao.data_hora', '>=', $dataInicio." 00:00:00"];
            $data[$cont++] = ['devolucao.data_hora', '<=', $dataFim." 23:59:59"];
            $data[$cont++] = ['saidas.tipo_saida_id', '=', 3];
        }

        $nota_credito = DB::table('devolucao')->select('devolucao.id as id','saidas.id as saida_id','saidas.cliente_id as cliente_id','clientes.nome as cliente',
        'data_hora','saidas.numero_factura as numero_factura',DB::raw('SUM(devolucao.total) as valorPago'),DB::raw('SUM(devolucao.quantidade) as quantidade')
        ,'produtos.codigo','produtos.descricao as produto')
        ->join('saida_items','devolucao.saida_item_id','=','saida_items.id')
        ->join('saidas','saida_items.saida_id','=','saidas.id')
        ->join('produtos', 'devolucao.produto_id', '=', 'produtos.id')
        ->join('clientes','saidas.cliente_id','=','clientes.id')
        ->where($data)
        ->groupBy('saidas.id')
        ->orderBy('devolucao.created_at', 'desc')
        ->paginate((int) $request->limite);

        return view('documentos/nota_credito.table',compact('nota_credito','tipo_pagamento','cliente_nome', 'dataInicio', 'dataFim'));
    }

    public function detailsNotaCredito($cliente_id,$saida_id,$dataInicio,$dataFim){

        $nota = DB::selectOne("SELECT devolucao.id as id,
        CAST(data_hora AS date) as data, saidas.data as data_factura,saidas.numero_factura as numero_factura
        FROM devolucao
        INNER JOIN saida_items ON devolucao.saida_item_id = saida_items.id
        INNER JOIN saidas ON saida_items.saida_id = saidas.id
        WHERE saidas.id = $saida_id AND saidas.cliente_id=$cliente_id AND (CAST(devolucao.data_hora AS date) BETWEEN '$dataInicio' AND '$dataFim')");


        $nota_credito = DB::table('devolucao')->select('devolucao.id as id','saidas.cliente_id as cliente_id','clientes.nome as cliente',
        'data_hora','saidas.numero_factura as numero_factura','devolucao.preco_unitario as preco_unitario','devolucao.total as valorPago','devolucao.quantidade as quantidade'
        ,'produtos.codigo as produto_codigo','produtos.descricao as produto')
        ->join('saida_items','devolucao.saida_item_id','=','saida_items.id')
        ->join('saidas','saida_items.saida_id','=','saidas.id')
        ->join('produtos', 'devolucao.produto_id', '=', 'produtos.id')
        ->join('clientes','saidas.cliente_id','=','clientes.id')
        ->where('saidas.id','=',$saida_id)
        // ->where()
        ->get();


        $empresa = DB::table('empresa')->first();
        $bancos = Banco::all();
        $formaPagamentos = FormaPagamento::all();
        $cliente = DB::selectOne("SELECT * FROM clientes WHERE id=$cliente_id");
        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        $_SESSION['cliente'] = json_encode($cliente);
        $_SESSION['bancos'] = json_encode($bancos);
        $_SESSION['formaPagamentos'] = json_encode($formaPagamentos);
        $_SESSION['nota'] = json_encode($nota);
        $_SESSION['nota_credito'] = json_encode($nota_credito);

        return view('documentos/nota_credito.details',compact('nota', 'nota_credito', 'formaPagamentos', 'bancos','empresa', 'cliente'));
    }

    public function recibo_fecho_dia(Request $request){
        // dd($request->sessao_id);
        $json['success'] = true;
        $empresa = DB::table('empresa')->first();
        // $formaPagamentos = FormaPagamento::all();
        $total_vendas = [];

        // if(!empty($request->sessao_id)){
        $saidas_agrupadas = DB::select("SELECT tipo_saidas.descricao as tipo_saida, SUM(saida_items.preco_unitario*saida_items.quantidade) as total
        FROM `saidas`
        INNER JOIN saida_items ON saida_items.saida_id=saidas.id
        INNER JOIN tipo_saidas ON tipo_saidas.id=saidas.tipo_saida_id
        WHERE saidas.sessao_id = {$request->sessao_id}
        GROUP BY saidas.tipo_saida_id");

        $saidas_tipo_pagamento = DB::select("SELECT tipo_pagamentos.designacao as tipo_pagamento, SUM(saida_tipo_pagamentos.valor) as total
        FROM `saida_tipo_pagamentos`
        INNER JOIN saidas ON saidas.id=saida_tipo_pagamentos.saida_id
        INNER JOIN tipo_pagamentos ON tipo_pagamentos.id=saida_tipo_pagamentos.tipo_pagamento_id
        WHERE saidas.sessao_id = {$request->sessao_id}
        GROUP BY tipo_pagamentos.id;");

        $saidas_usuario = DB::select("SELECT users.name as usuario, SUM(saida_items.quantidade*saida_items.preco_unitario) as total FROM `saidas`
        INNER JOIN saida_items ON saidas.id=saida_items.saida_id
        INNER JOIN users ON users.id=saidas.user_id
        WHERE saidas.sessao_id = {$request->sessao_id}
        GROUP BY users.id");

        $total_venda_dinheiro  = DB::select("SELECT COUNT(*) as total, SUM(valor_pago) as total_vendido FROM saidas WHERE tipo_saida_id = 1 AND sessao_id = {$request->sessao_id}");
        $total_venda_credito = DB::select("SELECT COUNT(*) as total, SUM(valor_pago) as total_vendido FROM saidas WHERE tipo_saida_id = 3 AND sessao_id = {$request->sessao_id}");

        $total_vendas = [
            "total_vd" => $total_venda_dinheiro[0]->total ?? 0,
            "total_venda_dinheiro" => $total_venda_dinheiro[0]->total_vendido ?? 0,
            "total_vc" => $total_venda_credito[0]->total ?? 0,
            "total_venda_credito" => $total_venda_credito[0]->total_vendido ?? 0,

        ];

        $sessao = DB::select("SELECT sessao.*,  abertura.name AS usuario_abertura, fechamento.name AS usuario_fecho
        FROM sessao
        INNER JOIN users AS abertura ON sessao.user_id_abertura = abertura.id
        INNER JOIN users AS fechamento ON sessao.user_id_fecho = fechamento.id
        WHERE sessao.id = {$request->sessao_id}");



        $saidaItems = DB::table('saida_items AS si')
            ->select('si.preco_unitario','si.tipo_motivo','si.motivo','si.user_id','si.activo',
                DB::raw('SUM(si.quantidade) AS quantidade'),DB::raw('SUM(si.custo) AS custo'),
                'produtos.descricao AS produto_descricao', 'produtos.codigo', 'valor_iva as taxa')
            ->join('saidas', 'saidas.id', '=', 'si.saida_id')
            ->join('lotes', 'lotes.id', '=', 'lote_id')
            ->join('produtos', 'produtos.id', '=', 'si.produto_id')
            ->where('sessao_id', '=', $request->sessao_id)
            ->where('si.activo', '!=', '2')
            ->groupBy('si.lote_id')->get();


        $devolucoes = DB::select("SELECT da.id, da.saida_id, da.numero, p.descricao, d.quantidade, d.quantidade * d.preco_unitario as total_devolvido, u.name as nome FROM devolucao_agregada da
        INNER JOIN devolucao d ON d.devolucao_agregada_id = da.id
        INNER JOIN saida_items si ON si.id = d.saida_item_id
        INNER JOIN produtos p ON p.id = d.produto_id
        INNER JOIN users u ON u.id = da.user_id
        WHERE da.sessao_id = {$request->sessao_id}");

        $abates = DB::select("SELECT saidas.id, produtos.descricao as produto, saida_items.quantidade, tipo_motivo.descricao, saida_items.motivo ,saida_items.preco_unitario * saida_items.quantidade as total_abatido, users.name as nome
        FROM saidas
        INNER JOIN saida_items on saidas.id = saida_items.saida_id
        INNER JOIN produtos on produtos.id = saida_items.produto_id
        INNER JOIN tipo_motivo on tipo_motivo.id = saida_items.tipo_motivo
        INNER JOIN users on users.id = saidas.user_id  WHERE saidas.sessao_id = {$request->sessao_id}
        GROUP BY saidas.id");


        session_start();
        $_COOKIE['empresa'] = json_encode($empresa);
        // $_SESSION['formaPagamentos'] = json_encode($formaPagamentos);
        $_SESSION['saida_fecho'] = json_encode($saidas_agrupadas);
        $_SESSION['saidaItems_fecho'] = json_encode($saidaItems);
        $_SESSION['tipoPagamento_fecho'] = json_encode($saidas_tipo_pagamento);
        $_SESSION['saidas_usuario_fecho'] = json_encode($saidas_usuario);
        $_SESSION['user'] = json_encode(auth()->user()->name);
        $_SESSION['sessao_dados'] = json_encode($sessao);
        $_SESSION['total_vendas'] = json_encode($total_vendas);
        $_SESSION['devolucoes'] = json_encode($devolucoes);
        $_SESSION['abates'] = json_encode($abates);





        // $bancos = Banco::all();
        // $tipo_pagamentos = TipoPagamento::all();

//        dd($tipo_pagamento);
        echo json_encode($json);
        // return view('saida.invoice', compact('empresa','formaPagamentos', 'saida','saidaItems','bancos','tipo_pagamentos'));
    }




    // public function sincronizar_vendas()
    // {

    //     use App\Helpers\RedisHelper;

        // $codigoEncriptado = $_COOKIE['codigo_loja'] ?? null;
        // // Obter dados da empresa e lidar com diferentes tipos de retorno
        // $empresaData = RedisHelper::getValue2($codigoEncriptado);
        // // dd($empresaData);

        // // Verificar se o valor já é um array ou objeto
        // if (is_array($empresaData)) {
        //     $empresa = (object)$empresaData;
        // } elseif (is_object($empresaData)) {
        //     $empresa = $empresaData;
        //     // dd($empresa);
        // } else {
        //     // Tentar decodificar a string JSON
        //     $empresa = json_decode($empresaData);

        //     // Se a decodificação falhar, criar um objeto padrão
        //     if (!$empresa) {
        //         $empresa = (object)[
        //             'nome' => 'SISTEMA DE GESTÃO DE STOCK',
        //             'endereco' => '',
        //             'cidade' => '',
        //             'telefone' => '',
        //             'email' => '',
        //             'codigo' => 'default',
        //             'pacote' => 2 // Padrão para pacote standard
        //         ];
        //     }
        // }

        // // Garantir que o pacote existe
        // if (!isset($empresa->pacote)) {
        //     $empresa->pacote = 2; // Valor padrão se não existir
        // }
    //     $link = isset($empresa) && !empty($empresa) ? $empresa->link : "";

    //     $json['success'] = false;
    //     $json['message'] = null;


    //     try {
    //         $vendas = DB::select("SELECT * FROM saidas WHERE estado_envio = '0' AND tipo_saida_id != '3' AND estado_pagamento='nao_pago' AND activo IN ('1')");

    //         if (empty($vendas)) {
    //             $json['message'] = "Nenhuma venda encontrada, para a sincronização.";
    //             $json['success'] = false;

    //         }else{

    //                 $count_success = 0;
    //                 $count_error = 0;

    //                 foreach ($vendas as $item) {
    //                     try {
    //                         $response = Http::post($link . "/vendas/create.php", [
    //                             "nr_factura" => $item->numero_factura,
    //                             "valor_total" => $item->valor_total,
    //                             "valor_pago" => $item->valor_pago,
    //                             "valor_total_iva" => $item->valor_total_iva,
    //                             "desconto" => $item->desconto,
    //                             "data" => $item->data,
    //                             "nr_slip" => $item->slip ?? "NULL",
    //                             "cliente_id" => $item->cliente_id,
    //                             "valor_remanescente" => $item->valor_remanescente,
    //                             "proveniencia" => $empresa->codigo,
    //                             "user_id" => auth()->user()->id
    //                         ]);



    //                         if(isset($response['status_text']) && $response['status_text']=="success"){
    //                             $count_success++;
    //                             DB::table('saidas')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                         }else{
    //                             $count_error++;
    //                             Log::error('Erro ao sincronizar venda', ['item_id' => $item->id, 'response' => $response->json()]);
    //                         }


    //                     } catch (Exception $e) {
    //                         $count_error++;
    //                         Log::error('Exceção ao sincronizar venda', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                     }
    //                 }



    //                 if ($count_success > 0) {
    //                     $json['success'] = true;
    //                     $json['message'] = "Vendas sincronizadas com sucesso.";
    //                 } else {
    //                     $json['success'] = false;
    //                     $json['message'] = "Erro ao efectuar a sincronização de vendas. ".$count_error;
    //                 }


    //         }




    //     } catch (Exception $e) {
    //         Log::error('Erro ao sincronizar vendas', ['error' => $e->getMessage()]);
    //         $json['success'] = false;
    //         $json['message'] = "Erro inesperado ao sincronizar vendas.";

    //     }

    //     echo json_encode($json);

    // }

    // public function sincronizar_vendas_importadoras()
    // {

    //     use App\Helpers\RedisHelper;

        // $codigoEncriptado = $_COOKIE['codigo_loja'] ?? null;

        // // Obter dados da empresa e lidar com diferentes tipos de retorno
        // $empresaData = RedisHelper::getValue2($codigoEncriptado);
        // // dd($empresaData);

        // // Verificar se o valor já é um array ou objeto
        // if (is_array($empresaData)) {
        //     $empresa = (object)$empresaData;
        // } elseif (is_object($empresaData)) {
        //     $empresa = $empresaData;
        //     // dd($empresa);
        // } else {
        //     // Tentar decodificar a string JSON
        //     $empresa = json_decode($empresaData);

        //     // Se a decodificação falhar, criar um objeto padrão
        //     if (!$empresa) {
        //         $empresa = (object)[
        //             'nome' => 'SISTEMA DE GESTÃO DE STOCK',
        //             'endereco' => '',
        //             'cidade' => '',
        //             'telefone' => '',
        //             'email' => '',
        //             'codigo' => 'default',
        //             'pacote' => 2 // Padrão para pacote standard
        //         ];
        //     }
        // }

        // // Garantir que o pacote existe
        // if (!isset($empresa->pacote)) {
        //     $empresa->pacote = 2; // Valor padrão se não existir
        // }
    //     $link = isset($empresa) && !empty($empresa) ? $empresa->link : "";

    //     $json['success'] = false;
    //     $json['message'] = null;

    //     try {
    //         $vendas = DB::select("SELECT * FROM saidas WHERE estado_envio = '0' AND tipo_saida_id = '3' AND estado_pagamento='nao_pago' AND activo IN ('1')");
    //         if (empty($vendas)) {
    //             $json['message'] = "Nenhuma venda encontrada, para a sincronização.";
    //             $json['success'] = false;

    //         }else{

    //                 $count_success = 0;
    //                 $count_error = 0;

    //                 foreach ($vendas as $item) {
    //                     try {
    //                         $response = Http::post($link. "/factura_importadora/create.php", [
    //                             "nr_factura" => $item->numero_factura,
    //                             "valor" => $item->valor_total,
    //                             "valor_pago" => $item->valor_pago,
    //                             "valor_total_iva" => $item->valor_total_iva ?? 0,
    //                             "desconto" => $item->desconto ?? 0,
    //                             "data_factura" => $item->data,
    //                             "nr_slip" => $item->slip ?? "NULL",
    //                             "cliente_id" => $item->cliente_id,
    //                             "valor_remanescente" => $item->valor_remanescente,
    //                             "proveniencia" => $empresa->codigo ?? "NULL",
    //                             "tipo_factura_id" => 1 ,
    //                             "user_id" => auth()->user()->id
    //                         ]);




    //                         if(isset($response['status_text']) && $response['status_text']=="success"){
    //                             $count_success++;
    //                             DB::table('saidas')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                         }else{
    //                             $count_error++;
    //                             Log::error('Erro ao sincronizar venda', ['item_id' => $item->id, 'response' => $response->json()]);
    //                         }


    //                     } catch (Exception $e) {
    //                         $count_error++;
    //                         Log::error('Exceção ao sincronizar venda', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                     }
    //                 }



    //                 if ($count_success > 0) {
    //                     $json['success'] = true;
    //                     $json['message'] = "Vendas sincronizadas com sucesso.";
    //                 } else {
    //                     $json['success'] = false;
    //                     $json['message'] = "Erro ao efectuar a sincronização de vendas. ".$count_error;
    //                 }


    //         }




    //     } catch (Exception $e) {
    //         Log::error('Erro ao sincronizar vendas', ['error' => $e->getMessage()]);
    //         $json['success'] = false;
    //         $json['message'] = "Erro inesperado ao sincronizar vendas.";

    //     }
    //     // echo ($response);


    //     echo json_encode($json);

    // }



    // public function sincronizar_vendas()
    // {

    //     //Sincronizar notas de credito


    //     use App\Helpers\RedisHelper;

        // $codigoEncriptado = $_COOKIE['codigo_loja'] ?? null;

        // // Obter dados da empresa e lidar com diferentes tipos de retorno
        // $empresaData = RedisHelper::getValue2($codigoEncriptado);
        // // dd($empresaData);

        // // Verificar se o valor já é um array ou objeto
        // if (is_array($empresaData)) {
        //     $empresa = (object)$empresaData;
        // } elseif (is_object($empresaData)) {
        //     $empresa = $empresaData;
        //     // dd($empresa);
        // } else {
        //     // Tentar decodificar a string JSON
        //     $empresa = json_decode($empresaData);

        //     // Se a decodificação falhar, criar um objeto padrão
        //     if (!$empresa) {
        //         $empresa = (object)[
        //             'nome' => 'SISTEMA DE GESTÃO DE STOCK',
        //             'endereco' => '',
        //             'cidade' => '',
        //             'telefone' => '',
        //             'email' => '',
        //             'codigo' => 'default',
        //             'pacote' => 2 // Padrão para pacote standard
        //         ];
        //     }
        // }

        // // Garantir que o pacote existe
        // if (!isset($empresa->pacote)) {
        //     $empresa->pacote = 2; // Valor padrão se não existir
        // }
    //     $link = isset($empresa) && !empty($empresa) ? $empresa->link : "";

    //     $json['success'] = false;
    //     $json['message'] = null;


    //     if($empresa->fonte == "1"){

    //         try {
    //             $nota_credito = DB::select("SELECT da.id, s.numero_factura, s.valor_pago, s.valor_total_iva, s.desconto, s.slip, s.cliente_id, s.valor_remanescente, da.total_devolucao, d.data_hora FROM `devolucao_agregada` da INNER JOIN saidas s ON s.id = da.saida_id INNER JOIN devolucao d ON
    //                                     d.devolucao_agregada_id = da.id  WHERE s.tipo_saida_id = '3' AND s.estado_pagamento = 'nao_pago' AND da.estado_envio = '0'");

    //             if (empty($nota_credito)) {
    //                 $json['message'] = "Nenhuma nota de crédito encontrada, para a sincronização.";
    //                 $json['success'] = false;

    //             }else{

    //                     $count_success = 0;
    //                     $count_error = 0;

    //                     foreach ($nota_credito as $item) {
    //                         try {
    //                             $response = Http::post($link . "/factura_importadora/create.php", [
    //                                 "nr_factura" => $item->numero_factura,
    //                                 "valor" => $item->total_devolucao,
    //                                 "valor_pago" => $item->valor_pago,
    //                                 "valor_total_iva" => $item->valor_total_iva ?? 0,
    //                                 "desconto" => $item->desconto ?? 0,
    //                                 "data_factura" => $item->data_hora,
    //                                 "nr_slip" => $item->slip ?? "NULL",
    //                                 "cliente_id" => $item->cliente_id,
    //                                 "valor_remanescente" => $item->valor_remanescente,
    //                                 "proveniencia" => $empresa->codigo ?? "NULL",
    //                                 "tipo_factura_id" => 2 ,
    //                                 "user_id" => auth()->user()->id
    //                             ]);




    //                             if(isset($response['status_text']) && $response['status_text']=="success"){
    //                                 $count_success++;
    //                                 DB::table('devolucao_agregada')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                             }else{
    //                                 $count_error++;
    //                                 Log::error('Erro ao sincronizar a nota de cédito', ['item_id' => $item->id, 'response' => $response->json()]);
    //                             }


    //                         } catch (Exception $e) {
    //                             $count_error++;
    //                             Log::error('Exceção ao sincronizar a nota de crédito', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                         }
    //                     }



    //                     if ($count_success > 0) {
    //                         $json['success'] = true;
    //                         $json['message'] = "Notas de crédito sincronizadas com sucesso.";
    //                     } else {
    //                         $json['success'] = false;
    //                         $json['message'] = "Erro ao efectuar a sincronização da nota de crédito. ".$count_error;
    //                     }


    //             }




    //         } catch (Exception $e) {
    //             Log::error('Erro ao sincronizar a nota de crédito', ['error' => $e->getMessage()]);
    //             $json['success'] = false;
    //             $json['message'] = "Erro inesperado ao sincronizar as notas de crédito.";

    //         }



    //         //Sincronizar facturas de importadoras clientes

    //         try {
    //             $vendas = DB::select("SELECT * FROM saidas WHERE estado_envio = '0' AND tipo_saida_id = '3' AND estado_pagamento='nao_pago' AND activo IN ('1')");
    //             if (empty($vendas)) {
    //                 $json['message'] = "Nenhuma venda encontrada, para a sincronização.";
    //                 $json['success'] = false;

    //             }else{

    //                     $count_success = 0;
    //                     $count_error = 0;

    //                     foreach ($vendas as $item) {
    //                         try {
    //                             $response = Http::post($link. "/factura_importadora/create.php", [
    //                                 "nr_factura" => $item->numero_factura,
    //                                 "valor" => $item->valor_total,
    //                                 "valor_pago" => $item->valor_pago,
    //                                 "valor_total_iva" => $item->valor_total_iva ?? 0,
    //                                 "desconto" => $item->desconto ?? 0,
    //                                 "data_factura" => $item->data,
    //                                 "nr_slip" => $item->slip ?? "NULL",
    //                                 "cliente_id" => $item->cliente_id,
    //                                 "valor_remanescente" => $item->valor_remanescente,
    //                                 "proveniencia" => $empresa->codigo ?? "NULL",
    //                                 "tipo_factura_id" => 1 ,
    //                                 "user_id" => auth()->user()->id
    //                             ]);




    //                             if(isset($response['status_text']) && $response['status_text']=="success"){
    //                                 $count_success++;
    //                                 DB::table('saidas')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                             }else{
    //                                 $count_error++;
    //                                 Log::error('Erro ao sincronizar venda', ['item_id' => $item->id, 'response' => $response->json()]);
    //                             }


    //                         } catch (Exception $e) {
    //                             $count_error++;
    //                             Log::error('Exceção ao sincronizar venda', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                         }
    //                     }



    //                     if ($count_success > 0) {
    //                         $json['success'] = true;
    //                         $json['message'] = "Vendas sincronizadas com sucesso.";
    //                     } else {
    //                         $json['success'] = false;
    //                         $json['message'] = "Erro ao efectuar a sincronização de vendas. ".$count_error;
    //                     }


    //             }




    //         } catch (Exception $e) {
    //             Log::error('Erro ao sincronizar vendas', ['error' => $e->getMessage()]);
    //             $json['success'] = false;
    //             $json['message'] = "Erro inesperado ao sincronizar vendas.";

    //         }



    //         //Sincronizar facturas de importadoras fornecedores


    //         try {
    //             $entradas = DB::select("SELECT id, numero_factura, fornecedor_id, data_factura, total_factura, valor_remanescente FROM `entradas` WHERE estado = 'terminado' AND estado_envio = '0' AND activo = '1'");


    //             if (empty($entradas)) {
    //                 $json['message'] = "Nenhuma entrada/factura do fornecedor encontrada, para a sincronização.";
    //                 $json['success'] = false;

    //             }else{

    //                     $count_success = 0;
    //                     $count_error = 0;

    //                     foreach ($entradas as $item) {
    //                         try {
    //                             $response = Http::post($link . "/factura_importadora_fornecedor/create.php", [
    //                                 "nr_factura" => $item->numero_factura,
    //                                 "valor" => $item->total_factura,
    //                                 "valor_pago" => $item->valor_pago ?? 0,
    //                                 "valor_total_iva" => $item->valor_total_iva ?? 0,
    //                                 "desconto" => $item->desconto ?? 0,
    //                                 "data_factura" => $item->data_factura,
    //                                 "nr_slip" => $item->slip ?? "NULL",
    //                                 "fornecedor_id" => $item->fornecedor_id,
    //                                 "valor_remanescente" => $item->valor_remanescente,
    //                                 "proveniencia" => $empresa->codigo ?? "NULL",
    //                                 "tipo_factura_id" => 1 ,
    //                                 "user_id" => auth()->user()->id
    //                             ]);




    //                             if(isset($response['status_text']) && $response['status_text']=="success"){
    //                                 $count_success++;
    //                                 DB::table('entradas')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                             }else{
    //                                 $count_error++;
    //                                 Log::error('Erro ao sincronizar as compras', ['item_id' => $item->id, 'response' => $response->json()]);
    //                             }


    //                         } catch (Exception $e) {
    //                             $count_error++;
    //                             Log::error('Exceção ao sincronizar as compras', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                         }
    //                     }



    //                     if ($count_success > 0) {
    //                         $json['success'] = true;
    //                         $json['message'] = "Compras/Entradas sincronizadas com sucesso.";
    //                     } else {
    //                         $json['success'] = false;
    //                         $json['message'] = "Erro ao efectuar a sincronização das compras/entradas. ".$count_error;
    //                     }


    //             }




    //         } catch (Exception $e) {
    //             Log::error('Erro ao sincronizar as entradas', ['error' => $e->getMessage()]);
    //             $json['success'] = false;
    //             $json['message'] = "Erro inesperado ao sincronizar as entradas.";

    //         }






    //         //Sincronizar notas de crédito das importadoras - fornecedores


    //         try {
    //             $nota_credito_fornecedor = DB::select("SELECT n.id, e.numero_factura, e.fornecedor_id, n.data_aquisicao as data_factura, n.valor FROM `nota_credito_fornecedor` n INNER JOIN
    //                                                     entradas e ON e.id = n.entrada_id WHERE n.activo = '1' AND e.estado = 'terminado' AND n.estado_envio = '0'");


    //             if (empty($nota_credito_fornecedor)) {
    //                 $json['message'] = "Nenhuma nota de crédito do fornecedor encontrada, para a sincronização.";
    //                 $json['success'] = false;

    //             }else{

    //                     $count_success = 0;
    //                     $count_error = 0;

    //                     foreach ($nota_credito_fornecedor as $item) {
    //                         try {
    //                             $response = Http::post($link . "/factura_importadora_fornecedor/create.php", [
    //                                 "nr_factura" => $item->numero_factura,
    //                                 "valor" => $item->valor,
    //                                 "valor_pago" => $item->valor_pago ?? 0,
    //                                 "valor_total_iva" => $item->valor_total_iva ?? 0,
    //                                 "desconto" => $item->desconto ?? 0,
    //                                 "data_factura" => $item->data_factura,
    //                                 "nr_slip" => $item->slip ?? "NULL",
    //                                 "fornecedor_id" => $item->fornecedor_id,
    //                                 "valor_remanescente" => $item->valor_remanescente ?? 0,
    //                                 "proveniencia" => $empresa->codigo ?? "NULL",
    //                                 "tipo_factura_id" => 2 ,
    //                                 "user_id" => auth()->user()->id
    //                             ]);




    //                             if(isset($response['status_text']) && $response['status_text']=="success"){
    //                                 $count_success++;
    //                                 DB::table('nota_credito_fornecedor')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                             }else{
    //                                 $count_error++;
    //                                 Log::error('Erro ao sincronizar as notas de crédito do fornecedor', ['item_id' => $item->id, 'response' => $response->json()]);
    //                             }


    //                         } catch (Exception $e) {
    //                             $count_error++;
    //                             Log::error('Exceção ao sincronizar as notas de crédito do fornecedor', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                         }
    //                     }



    //                     if ($count_success > 0) {
    //                         $json['success'] = true;
    //                         $json['message'] = "Nota(s) de crédito do fornecedor sincronizadas com sucesso.";
    //                     } else {
    //                         $json['success'] = false;
    //                         $json['message'] = "Erro ao efectuar a sincronização das notas de crédito do fornecedor. ".$count_error;
    //                     }


    //             }




    //         } catch (Exception $e) {
    //             Log::error('Erro ao sincronizar as entradas', ['error' => $e->getMessage()]);
    //             $json['success'] = false;
    //             $json['message'] = "Erro inesperado ao sincronizar as notas de crédito do fornecedor.";

    //         }







    //     }else{





    //     //Sincronizar vendas seguradoras e empresas


    //     try {
    //         $vendas = DB::select("SELECT * FROM saidas WHERE estado_envio = '0' AND tipo_saida_id = '1' OR tipo_saida_id = '3' AND estado_pagamento='nao_pago' AND activo IN ('1')");

    //         if (empty($vendas)) {
    //             $json['message'] = "Nenhuma venda encontrada, para a sincronização.";
    //             $json['success'] = false;

    //         }else{

    //                     $count_success = 0;
    //                     $count_error = 0;

    //                     foreach ($vendas as $item) {
    //                         try {
    //                             $response = Http::post($link . "/vendas/create.php", [
    //                                 "nr_factura" => $item->numero_factura,
    //                                 "valor_total" => $item->valor_total,
    //                                 "valor_pago" => $item->valor_pago,
    //                                 "valor_total_iva" => $item->valor_total_iva,
    //                                 "desconto" => $item->desconto,
    //                                 "data" => $item->data,
    //                                 "nr_slip" => $item->slip ?? "NULL",
    //                                 "cliente_id" => $item->cliente_id,
    //                                 "valor_remanescente" => $item->valor_remanescente,
    //                                 "proveniencia" => $empresa->codigo,
    //                                 "user_id" => auth()->user()->id
    //                             ]);



    //                             if(isset($response['status_text']) && $response['status_text']=="success"){
    //                                 $count_success++;
    //                                 DB::table('saidas')->where('id', $item->id)->update(['estado_envio' => 1]);
    //                             }else{
    //                                 $count_error++;
    //                                 Log::error('Erro ao sincronizar venda', ['item_id' => $item->id, 'response' => $response->json()]);
    //                             }


    //                         } catch (Exception $e) {
    //                             $count_error++;
    //                             Log::error('Exceção ao sincronizar venda', ['item_id' => $item->id, 'error' => $e->getMessage()]);
    //                         }
    //                     }



    //                     if ($count_success > 0) {
    //                         $json['success'] = true;
    //                         $json['message'] = "Vendas sincronizadas com sucesso.";
    //                     } else {
    //                         $json['success'] = false;
    //                         $json['message'] = "Erro ao efectuar a sincronização de vendas. ".$count_error;
    //                     }


    //             }




    //         } catch (Exception $e) {
    //             Log::error('Erro ao sincronizar vendas', ['error' => $e->getMessage()]);
    //             $json['success'] = false;
    //             $json['message'] = "Erro inesperado ao sincronizar vendas.";

    //         }


    //     }



    //     echo json_encode($json);

    // }




    public function sincronizar_vendas()
    {
        // Sincronizar notas de crédito

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

        // Garantir que o pacote existe
        if (!isset($empresa->pacote)) {
            $empresa->pacote = 2; // Valor padrão se não existir
        }

        $link = isset($empresa) && !empty($empresa) ? $empresa->link : "";

        $json['success'] = false;
        $json['message'] = null;

        $valor_bruto = 0;

        $sincronizacoes = [
            'notas_credito' => ['erros' => 0, 'sucessos' => 0],
            'vendas' => ['erros' => 0, 'sucessos' => 0],
            'entradas' => ['erros' => 0, 'sucessos' => 0],
            'vendas_credito' => ['erros' => 0, 'sucessos' => 0],
            'notas_credito_fornecedor' => ['erros' => 0, 'sucessos' => 0]
        ];

        if ($empresa->fonte == "1") {
            try {
                $nota_credito = DB::select("SELECT da.id, s.numero_factura, s.valor_pago, s.valor_total_iva, s.desconto, s.slip, s.cliente_id, s.valor_remanescente, da.total_devolucao, d.data_hora FROM `devolucao_agregada` da INNER JOIN saidas s ON s.id = da.saida_id INNER JOIN devolucao d ON d.devolucao_agregada_id = da.id WHERE s.tipo_saida_id = '3' AND s.estado_pagamento = 'nao_pago' AND da.estado_envio = '0'");

                if (!empty($nota_credito)) {
                    foreach ($nota_credito as $item) {
                        try {
                            $response = Http::post($link . "/factura_importadora/create.php", [
                                "nr_factura" => $item->numero_factura,
                                "valor" => $item->total_devolucao,
                                "valor_pago" => $item->valor_pago,
                                "valor_total_iva" => $item->valor_total_iva ?? 0,
                                "desconto" => $item->desconto ?? 0,
                                "data_factura" => $item->data_hora,
                                "nr_slip" => $item->slip ?? "NULL",
                                "cliente_id" => $item->cliente_id,
                                "valor_remanescente" => $item->valor_remanescente,
                                "proveniencia" => $empresa->codigo ?? "NULL",
                                "tipo_factura_id" => 2,
                                "fonte_id" => $empresa->fonte,
                                "user_id" => auth()->user()->id
                            ]);

                            if (isset($response['status_text']) && $response['status_text'] == "success") {
                                $sincronizacoes['notas_credito']['sucessos']++;
                                DB::table('devolucao_agregada')->where('id', $item->id)->update(['estado_envio' => 1]);
                            } else {
                                $sincronizacoes['notas_credito']['erros']++;
                                Log::error('Erro ao sincronizar a nota de crédito', ['item_id' => $item->id, 'response' => $response->json()]);
                            }
                        } catch (Exception $e) {
                            $sincronizacoes['notas_credito']['erros']++;
                            Log::error('Exceção ao sincronizar a nota de crédito', ['item_id' => $item->id, 'error' => $e->getMessage()]);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error('Erro ao sincronizar a nota de crédito', ['error' => $e->getMessage()]);
            }

            // Sincronizar facturas de importadoras clientes

            try {
                $vendas = DB::select("SELECT * FROM saidas  WHERE saidas.estado_envio = '0' AND saidas.tipo_saida_id = '3' AND saidas.estado_pagamento='nao_pago' AND saidas.activo IN ('1')");
                if (!empty($vendas)) {
                    foreach ($vendas as $item) {
                        $valor_bruto = (float)$item->valor_total + (float)$item->desconto;

                        try {
                            $response = Http::post($link . "/factura_importadora/create.php", [
                                "nr_factura" => $item->numero_factura,
                                "valor" => $item->valor_total,
                                "valor_bruto" => $valor_bruto,
                                "valor_pago" => $item->valor_pago,
                                "valor_total_iva" => $item->valor_total_iva ?? 0,
                                "desconto" => $item->desconto ?? 0,
                                "data_factura" => $item->data,
                                "nr_slip" => $item->slip ?? "NULL",
                                "cliente_id" => $item->cliente_id,
                                "valor_remanescente" => $item->valor_remanescente,
                                "proveniencia" => $empresa->codigo ?? "NULL",
                                "tipo_factura_id" => 1,
                                "fonte_id" => $empresa->fonte,
                                "user_id" => auth()->user()->id
                            ]);

                            if (isset($response['status_text']) && $response['status_text'] == "success") {
                                $sincronizacoes['vendas']['sucessos']++;
                                DB::table('saidas')->where('id', $item->id)->update(['estado_envio' => 1]);
                            } else {
                                $sincronizacoes['vendas']['erros']++;
                                Log::error('Erro ao sincronizar venda', ['item_id' => $item->id, 'response' => $response->json()]);
                            }
                        } catch (Exception $e) {
                            $sincronizacoes['vendas']['erros']++;
                            Log::error('Exceção ao sincronizar venda', ['item_id' => $item->id, 'error' => $e->getMessage()]);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error('Erro ao sincronizar vendas', ['error' => $e->getMessage()]);
            }

            // Sincronizar facturas de importadoras fornecedores

            try {
                $entradas = DB::select("SELECT id, numero_factura, fornecedor_id, data_factura, total_factura, total_factura2, cambio, valor_remanescente FROM `entradas` WHERE valor_remanescente = '0' AND estado_envio = '0' AND activo = '1'");
                if (!empty($entradas)) {
                    foreach ($entradas as $item) {
                        try {
                            $response = Http::post($link . "/factura_importadora_fornecedor/create.php", [
                                "nr_factura" => $item->numero_factura,
                                "valor" => $item->total_factura2 ?? 0,
                                "valor2" => $item->total_factura ?? 0,
                                "cambio" => $item->cambio ?? 0,
                                "valor_total_iva" => $item->valor_total_iva ?? 0,
                                "desconto" => $item->desconto ?? 0,
                                "data_factura" => $item->data_factura,
                                "nr_slip" => $item->slip ?? "NULL",
                                "fornecedor_id" => $item->fornecedor_id,
                                "valor_remanescente" => $item->valor_remanescente,
                                "proveniencia" => $empresa->codigo ?? "NULL",
                                "tipo_factura_id" => 1,
                                "fonte_id" => $empresa->fonte,
                                "user_id" => auth()->user()->id
                            ]);

                            if (isset($response['status_text']) && $response['status_text'] == "success") {
                                $sincronizacoes['entradas']['sucessos']++;
                                DB::table('entradas')->where('id', $item->id)->update(['estado_envio' => 1]);
                            } else {
                                $sincronizacoes['entradas']['erros']++;
                                Log::error('Erro ao sincronizar as compras', ['item_id' => $item->id, 'response' => $response->json()]);
                            }
                        } catch (Exception $e) {
                            $sincronizacoes['entradas']['erros']++;
                            Log::error('Exceção ao sincronizar as compras', ['item_id' => $item->id, 'error' => $e->getMessage()]);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error('Erro ao sincronizar as entradas', ['error' => $e->getMessage()]);
            }

            // Sincronizar notas de crédito das importadoras - fornecedores

            try {
                $nota_credito_fornecedor = DB::select("SELECT n.id, e.numero_factura, e.fornecedor_id, n.data_aquisicao as data_factura, n.valor FROM `nota_credito_fornecedor` n INNER JOIN entradas e ON e.id = n.entrada_id WHERE n.activo = '1' AND e.estado = 'terminado' AND n.estado_envio = '0'");
                if (!empty($nota_credito_fornecedor)) {
                    foreach ($nota_credito_fornecedor as $item) {
                        try {
                            $response = Http::post($link . "/factura_importadora_fornecedor/create.php", [
                                "nr_factura" => $item->numero_factura,
                                "valor" => $item->valor,
                                "valor_pago" => $item->valor_pago ?? 0,
                                "valor_total_iva" => $item->valor_total_iva ?? 0,
                                "desconto" => $item->desconto ?? 0,
                                "data_factura" => $item->data_factura,
                                "nr_slip" => $item->slip ?? "NULL",
                                "fornecedor_id" => $item->fornecedor_id,
                                "valor_remanescente" => $item->valor_remanescente ?? 0,
                                "proveniencia" => $empresa->codigo ?? "NULL",
                                "tipo_factura_id" => 2,
                                "fonte_id" => $empresa->fonte,
                                "user_id" => auth()->user()->id
                            ]);

                            if (isset($response['status_text']) && $response['status_text'] == "success") {
                                $sincronizacoes['notas_credito_fornecedor']['sucessos']++;
                                DB::table('nota_credito_fornecedor')->where('id', $item->id)->update(['estado_envio' => 1]);
                            } else {
                                $sincronizacoes['notas_credito_fornecedor']['erros']++;
                                Log::error('Erro ao sincronizar a nota de crédito do fornecedor', ['item_id' => $item->id, 'response' => $response->json()]);
                            }
                        } catch (Exception $e) {
                            $sincronizacoes['notas_credito_fornecedor']['erros']++;
                            Log::error('Exceção ao sincronizar a nota de crédito do fornecedor', ['item_id' => $item->id, 'error' => $e->getMessage()]);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::error('Erro ao sincronizar a nota de crédito do fornecedor', ['error' => $e->getMessage()]);
            }
        }





        //  // Sincronizar vendas a credito para gestor de despesas

         try {
            $vendas_credito = DB::select("SELECT * FROM saidas  WHERE saidas.estado_envio = '0' AND saidas.tipo_saida_id = '3' AND saidas.estado_pagamento='nao_pago' AND saidas.activo IN ('1')");
            if (!empty($vendas_credito)) {
                foreach ($vendas_credito as $item) {

                    try {

                        $response = Http::post($link . "/vendas/create.php", [
                            "nr_factura" => $item->numero_factura,
                            "nome_segurado" => $item->nome_segurado,
                            "codigo_autorizacao" => $item->codigo_autorizacao,
                            "numero_membro" => $item->numero_membro,
                            "valor_total" => $item->valor_total,
                            "valor_total_iva" => $item->valor_total_iva,
                            "valor_pago" => $item->valor_pago,
                            "valor_remanescente" => $item->valor_remanescente,
                            "desconto" => $item->desconto,
                            "data" => $item->data,
                            "nr_slip" => $item->slip ?? "NULL",
                            "cliente_id" => $item->cliente_id,
                            "proveniencia" => $empresa->codigo,
                            "user_id" => auth()->user()->id
                        ]);

                        if (isset($response['status_text']) && $response['status_text'] == "success") {
                            $sincronizacoes['vendas_credito']['sucessos']++;
                            DB::table('saidas')->where('id', $item->id)->update(['estado_envio' => 1]);
                        } else {
                            $sincronizacoes['vendas_credito']['erros']++;
                            Log::error('Erro ao sincronizar venda', ['item_id' => $item->id, 'response' => $response->json()]);
                        }
                    } catch (Exception $e) {
                        $sincronizacoes['vendas_credito']['erros']++;
                        Log::error('Exceção ao sincronizar venda', ['item_id' => $item->id, 'error' => $e->getMessage()]);
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Erro ao sincronizar vendas', ['error' => $e->getMessage()]);
        }

        $json['sincronizacoes'] = $sincronizacoes;
        $json['message'] = 'Sincronização concluída com os seguintes resultados: <br>' . PHP_EOL .
                           'Notas de Crédito: <br> ' . $sincronizacoes['notas_credito']['sucessos'] . ' sucessos e ' . $sincronizacoes['notas_credito']['erros'] . ' erros.<br>' . PHP_EOL .
                           'Vendas: <br>' . $sincronizacoes['vendas']['sucessos'] . ' sucessos e ' . $sincronizacoes['vendas']['erros'] . ' erros.<br>' . PHP_EOL .
                           'Entradas: <br>' . $sincronizacoes['entradas']['sucessos'] . ' sucessos e ' . $sincronizacoes['entradas']['erros'] . ' erros.<br>' . PHP_EOL .
                           'Vendas a Crédito: <br>' . $sincronizacoes['vendas_credito']['sucessos'] . ' sucessos e ' . $sincronizacoes['vendas_credito']['erros'] . ' erros.<br>' . PHP_EOL .
                           'Notas de Crédito de Fornecedores: <br>' . $sincronizacoes['notas_credito_fornecedor']['sucessos'] . ' sucessos e ' . $sincronizacoes['notas_credito_fornecedor']['erros'] . ' erros.';
        $json['success'] = true;

        return response()->json($json);

    }




}
