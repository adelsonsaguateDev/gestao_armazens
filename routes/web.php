<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\RequisicoesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntradasController;
use App\Http\Controllers\SaidasController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UtilizadorController;
use App\Http\Controllers\Admin\ClientesController;





// use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('auth.login');
});

//Pagina inicial
Route::get('/home',  [DashboardController::class, 'index'])->name('pagina_inicial');

//Login & Logout
Route::post('/autenticar', [LoginController::class, 'autenticar'])->name('autenticar');
Route::get('/', [LoginController::class, 'logout'])->name('logout');


//Produto
Route::get('produto', [ProdutosController::class, 'index'])->name('produto.list');
Route::get('produtos', [ProdutosController::class, 'index'])->name('produtos');
Route::get('produto/{id}', [ProdutosController::class, 'show'])->name('show');
Route::get('produto_detalhes/{id}', [ProdutosController::class, 'show_details'])->name('detalhes');
Route::post('produtos', [ProdutosController::class, 'list'])->name('listar');
Route::post('produto/add', [ProdutosController::class, 'add'])->name('create');
Route::post('produto/delete', [ProdutosController::class, 'delete'])->name('delete');
Route::post('produto/edit', [ProdutosController::class, 'edit'])->name('produto.edit');
Route::post('produto/requisicao', [ProdutosController::class, 'add_requisicao'])->name('requisicao');


//Entradas
Route::get('entrada', [EntradasController::class, 'index'])->name('entrada.list');
Route::get('entrada/create', [EntradasController::class, 'create'])->name('entrada.create');
Route::post('entradas', [EntradasController::class, 'list'])->name('entradas.list'); // Para listagem via AJAX
Route::post('entrada/add', [EntradasController::class, 'add'])->name('entrada.add');
Route::get('entrada/{id}', [EntradasController::class, 'show'])->name('entrada.show'); // Para formulário de edição/visualização
Route::get('entrada_detalhes/{id}', [EntradasController::class, 'show_details'])->name('entrada.detalhes');
Route::post('entrada/edit', [EntradasController::class, 'edit'])->name('entrada.edit');
Route::post('entrada/delete', [EntradasController::class, 'delete'])->name('entrada.delete');


// Saida routes
// Saidas (Refatorado e Unificado)
// Mantendo as rotas originais e adicionando as que faltavam do bloco duplicado.

// Rotas GET
Route::get('saida', [SaidasController::class, 'index'])->name('saida.list');
Route::get('saida/create', [SaidasController::class, 'create'])->name('saida.create');
Route::get('saida/create-credito', [SaidasController::class, 'createCredito'])->name('saida.create.credito'); // Adicionada
Route::get('saida/recibo/{id}', [SaidasController::class, 'recibo'])->name('saida.recibo'); // Adicionada
Route::get('saida_detalhes/{id}', [SaidasController::class, 'show_details'])->name('saida.detalhes');
Route::get('saida/{id}', [SaidasController::class, 'show'])->name('saida.show'); // Rota com parâmetro no final

// Rotas POST
Route::post('saidas', [SaidasController::class, 'list'])->name('saidas.list'); // Para listagem via AJAX
Route::post('saida/add', [SaidasController::class, 'add'])->name('saida.add');
Route::post('saida/edit', [SaidasController::class, 'edit'])->name('saida.edit');
Route::post('saida/delete', [SaidasController::class, 'delete'])->name('saida.delete');
Route::post('saida/getBatchesByProduct', [SaidasController::class, 'getBatchesByProduct'])->name('saida.getBatchesByProduct'); // Adicionada

//Utilizador
Route::get('utilizador', [UtilizadorController::class, 'index'])->name('utilizador.list');
Route::get('utilizadores', [UtilizadorController::class, 'list'])->name('listar');
Route::get('utilizador/{id}', [UtilizadorController::class, 'show'])->name('show');
Route::get('utilizador_detalhes/{id}', [UtilizadorController::class, 'show_details'])->name('utilizador.detalhes');
Route::post('utilizador/add', [UtilizadorController::class, 'add'])->name('create');
Route::post('utilizador/delete', [UtilizadorController::class, 'delete'])->name('delete');
Route::post('utilizador/edit', [UtilizadorController::class, 'edit'])->name('edit');

//Cliente
Route::get('cliente', [ClientesController::class, 'index'])->name('cliente.list');
Route::get('clientes', [ClientesController::class, 'list'])->name('listar');
Route::get('cliente/{id}', [ClientesController::class, 'show'])->name('show');
Route::get('cliente_detalhes/{id}', [ClientesController::class, 'show_details'])->name('cliente.detalhes');
Route::post('cliente/add', [ClientesController::class, 'add'])->name('create');
Route::post('cliente/delete', [ClientesController::class, 'delete'])->name('delete');
Route::post('cliente/edit', [ClientesController::class, 'edit'])->name('edit');




//Requisições

Route::get('requisicao', [RequisicoesController::class, 'index'])->name('requisicao.list');
Route::get('requisicoes', [RequisicoesController::class, 'list'])->name('listar');
Route::get('requisicao/{id}', [RequisicoesController::class, 'show'])->name('show');
Route::get('requisicao_detalhes/{id}', [RequisicoesController::class, 'show_details'])->name('requisicao.detalhes');
Route::post('requisicao/add', [RequisicoesController::class, 'add'])->name('create');
Route::post('requisicao/delete', [RequisicoesController::class, 'delete'])->name('delete');
Route::post('requisicao/edit', [RequisicoesController::class, 'edit'])->name('edit');

Route::post('requisicao/movimento/aprovacao', [RequisicoesController::class, 'aprovacao'])->name('requisicao.aprovacao');
Route::post('requisicao/movimento/reprovacao', [RequisicoesController::class, 'reprovacao'])->name('requisicao.reprovacao');

//Grafico de requisicoes
Route::get('/dashboard/requisicoes_grafico_barra', [DashboardController::class, 'getGraficoBarras']);
Route::get('/dashboard/requisicoes_grafico_pizza', [DashboardController::class, 'getGraficoPizza']);

//Relatorios
Route::get('relatorio', [DashboardController::class, 'index1'])->name('relatorio.list');
Route::get('relatorios', [DashboardController::class, 'list'])->name('listar');
