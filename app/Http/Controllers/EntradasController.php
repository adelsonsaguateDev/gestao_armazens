<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\EntradaItem;
use App\Models\Produto;
use App\Models\Fornecedor;
use App\Models\TipoEntrada;
use App\Models\Historico;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class EntradasController extends Controller
{
    public function index()
    {
        $produtos = Produto::where('estado', 1)->get();
        $fornecedores = Fornecedor::where('estado', 1)->get();
        $tipos_entrada = TipoEntrada::where('estado', 1)->get();
        return view('entradas.index', compact('produtos', 'fornecedores', 'tipos_entrada'));
    }

    public function create()
    {
        $produtos = Produto::where('estado', 1)->get();
        $fornecedores = Fornecedor::where('estado', 1)->get();
        $tipos_entrada = TipoEntrada::where('estado', 1)->get();
        return view('entradas.create', compact('produtos', 'fornecedores', 'tipos_entrada'));
    }

    public function list(Request $request)
    {
        $query = Entrada::with(['tipoEntrada', 'fornecedor', 'user', 'estadoObj']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->input('numero_factura') . '%');
        }

        if ($request->filled('fornecedor_id')) {
            $query->where('fornecedor_id', $request->input('fornecedor_id'));
        }

        if ($request->filled('tipo_entrada_id')) {
            $query->where('tipo_entrada_id', $request->input('tipo_entrada_id'));
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_aquisicao', [$request->input('data_inicio'), $request->input('data_fim')]);
        }

        $total = $query->count();

        $itensPorPagina = $request->input('limite', 10);
        $entradas = $query->paginate($itensPorPagina);
        $entradas->appends($request->query());

        return view('entradas.tabela', compact('entradas', 'total'));
    }

    public function add(Request $request)
    {
        DB::beginTransaction();
        try {
            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $dataEntrada = $request->validate([
                'tipo_entrada_id' => 'required|exists:tipos_entradas,id',
                'fornecedor_id' => 'nullable|exists:fornecedores,id',
                'fornecedor_ref' => 'nullable|string|max:100',
                'numero_factura' => 'nullable|string|max:45',
                'data_aquisicao' => 'required|date',
                'data_factura' => 'required|date',
                'total' => 'required|numeric|min:0',
                'total_factura' => 'required|numeric|min:0',
                'total_desconto' => 'nullable|numeric|min:0',
                'total_iva' => 'nullable|numeric|min:0',
                'valor_remanescente' => 'nullable|numeric|min:0',
                'ficheiro_entrada' => 'nullable|string|max:255', // Assuming file path is stored as string
            ]);

            $dataEntrada['user_id'] = auth()->user()->id;
            $dataEntrada['estado'] = 1;

            $entrada = Entrada::create($dataEntrada);

            // Handle Entrada Items
            $itens = json_decode($request->input('itens'), true); // Assuming items come as a JSON string
            if (empty($itens)) {
                throw new \Exception('Nenhum item de entrada foi fornecido.');
            }

            foreach ($itens as $itemData) {
                $itemData['entrada_id'] = $entrada->id;
                $itemData['user_id'] = auth()->user()->id;
                $itemData['estado'] = 1;
                EntradaItem::create($itemData);
            }

            $descricao = 'Registou a entrada Nº ' . $entrada->id . ' com ' . count($itens) . ' itens.';
            $historico->insert($entrada->getTable(), $entrada->id, $descricao);

            DB::commit();
            $json['success'] = true;
            $json['message'] = 'Entrada registada com sucesso.';
            $json['code'] = 200;

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();
            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422;
        } catch (\Exception $e) {
            DB::rollBack();
            $json['success'] = false;
            $json['message'] = $e->getMessage();
            $json['code'] = 500;
        }

        echo json_encode($json);
    }

    public function show_details($id)
    {
        $entrada = Entrada::with(['tipoEntrada', 'fornecedor', 'user', 'estadoObj', 'itens.produto'])->find($id);

        if (!$entrada) {
            return response()->json(['error' => 'Entrada não encontrada'], 404);
        }

        $historico = Historico::where('row_id', $id)
            ->where('tabela', 'entradas')->with('users')->get();

        return response()->view('entradas.detalhes', compact('entrada', 'historico'));
    }

    public function show($id)
    {
        $entrada = Entrada::with('itens')->find($id);
        $produtos = Produto::where('estado', 1)->get();
        $fornecedores = Fornecedor::where('estado', 1)->get();
        $tipos_entrada = TipoEntrada::where('estado', 1)->get();

        if (!$entrada) {
            return response()->json(['error' => 'Entrada não encontrada'], 404);
        }

        return view('entradas.form_edit', compact('entrada', 'produtos', 'fornecedores', 'tipos_entrada'));
    }

    public function edit(Request $request)
    {
        DB::beginTransaction();
        try {
            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $entrada = Entrada::find($request->input('id'));
            if (!$entrada) {
                throw new \Exception('Entrada não encontrada para edição.');
            }

            $dataEntrada = $request->validate([
                'tipo_entrada_id' => 'required|exists:tipos_entradas,id',
                'fornecedor_id' => 'nullable|exists:fornecedores,id',
                'fornecedor_ref' => 'nullable|string|max:100',
                'numero_factura' => 'nullable|string|max:45',
                'data_aquisicao' => 'required|date',
                'data_factura' => 'required|date',
                'total' => 'required|numeric|min:0',
                'total_factura' => 'required|numeric|min:0',
                'total_desconto' => 'nullable|numeric|min:0',
                'total_iva' => 'nullable|numeric|min:0',
                'valor_remanescente' => 'nullable|numeric|min:0',
                'ficheiro_entrada' => 'nullable|string|max:255',
            ]);

            $dataEntrada['user_id'] = auth()->user()->id;
            $dataEntrada['estado'] = 1;

            $entrada->update($dataEntrada);

            // Handle Entrada Items - Delete existing and re-create
            EntradaItem::where('entrada_id', $entrada->id)->delete();

            $itens = json_decode($request->input('itens'), true);
            if (empty($itens)) {
                throw new \Exception('Nenhum item de entrada foi fornecido.');
            }

            foreach ($itens as $itemData) {
                $itemData['entrada_id'] = $entrada->id;
                $itemData['user_id'] = auth()->user()->id;
                $itemData['estado'] = 1;
                EntradaItem::create($itemData);
            }

            $descricao = 'Atualizou a entrada Nº ' . $entrada->id . '.';
            $historico->insert($entrada->getTable(), $entrada->id, $descricao);

            DB::commit();
            $json['success'] = true;
            $json['message'] = 'Entrada atualizada com sucesso.';
            $json['code'] = 200;

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();
            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422;
        } catch (\Exception $e) {
            DB::rollBack();
            $json['success'] = false;
            $json['message'] = $e->getMessage();
            $json['code'] = 500;
        }

        echo json_encode($json);
    }

    public function delete(Request $request)
    {
        $id = $request->input('entrada_id');
        $estado = $request->input('estado');
        $json['success'] = false;
        $entrada = Entrada::find($id);
        $historico = new Historico();

        if (!empty($entrada)) {
            $data = ['estado' => $estado];
            if ($entrada->update($data)) {
                $json['success'] = true;
                if ($estado == '1') {
                    $json['message'] = 'Entrada ativada com sucesso.';
                    $descricao = 'Ativou a entrada Nº ' . $entrada->id . '.';
                } else if ($estado == '2') {
                    $json['message'] = 'Entrada removida com sucesso.';
                    $descricao = 'Removeu a entrada Nº ' . $entrada->id . '.';
                }
                $historico->insert($entrada->getTable(), $entrada->id, $descricao);
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao remover a entrada.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }
}