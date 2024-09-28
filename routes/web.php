<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\RequisicoesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\UtilizadorController;





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
Route::get('/home',  [DashboardController::class , 'index'])->name('pagina_inicial');

//Login & Logout
Route::post('/autenticar', [LoginController::class , 'autenticar'])->name('autenticar');
Route::get('/', [LoginController::class , 'logout'])->name('logout');


//Produto
Route::get('produto', [ProdutosController::class, 'index'])->name('produto.list');
Route::get('produtos', [ProdutosController::class, 'list'])->name('listar');
Route::get('produto/{id}', [ProdutosController::class, 'show'])->name('show');
Route::get('produto_detalhes/{id}', [ProdutosController::class, 'show_details'])->name('detalhes');
Route::post('produto/add', [ProdutosController::class, 'add'])->name('create');
Route::post('produto/delete', [ProdutosController::class, 'delete'])->name('delete');
Route::post('produto/edit', [ProdutosController::class, 'edit'])->name('edit');
Route::post('produto/requisicao', [ProdutosController::class, 'add_requisicao'])->name('requisicao');



//Utilizador
Route::get('utilizador', [UtilizadorController::class, 'index'])->name('utilizador.list');
Route::get('utilizadores', [UtilizadorController::class, 'list'])->name('listar');
Route::get('utilizador/{id}', [UtilizadorController::class, 'show'])->name('show');
Route::get('utilizador_detalhes/{id}', [UtilizadorController::class, 'show_details'])->name('utilizador.detalhes');
Route::post('utilizador/add', [UtilizadorController::class, 'add'])->name('create');
Route::post('utilizador/delete', [UtilizadorController::class, 'delete'])->name('delete');
Route::post('utilizador/edit', [UtilizadorController::class, 'edit'])->name('edit');




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





