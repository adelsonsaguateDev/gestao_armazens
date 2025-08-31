<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\TipoProdutoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\FarmaciaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UtilizadorController;
use App\Http\Controllers\TipoUtilizadorController;
use App\Http\Controllers\TipoEntradaController;
use App\Http\Controllers\TipoSaidaController;
use App\Http\Controllers\TipoProvinienciaController;
use App\Http\Controllers\NotificarController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\Saida\SaidaController;
use App\Http\Controllers\Saida\SaidaItemController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\InventarioController1;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\ViewController;

// Middleware
use App\Http\Middleware\CheckUserMiddleware;
use App\Http\Middleware\CheckUser2Middleware;
use App\Http\Middleware\CheckUser3Middleware;
use App\Http\Middleware\CheckUser4Middleware;
use App\Http\Middleware\CheckUser5Middleware;
use App\Http\Middleware\AllUsersMiddleware;

// Route::get('/', [LoginController::class, 'dashboard'])->name('dashboard');

// Autenticação
Route::get('/entrar', [LoginController::class, 'show'])->name('entrar');
Route::get('/login', function () {
    return redirect('/entrar');
})->name('login');
Route::get('/', function () {
    return redirect('/entrar');
});
Route::post('/autenticar', [LoginController::class, 'autenticar'])->name('autenticar');

Route::middleware('auth')->group(function () {

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('utilizador/{utilizadore}', [UtilizadorController::class, 'show'])->name('utilizador.show');

Route::get('/updateDiary', [LoginController::class, 'actualizarProdutoDiario'])->name('updateDiary');
Route::get('/updateDiaryWithParamData', [LoginController::class, 'actualizarRelatorioDiarioParamentrosData'])->name('updateDiaryWithParamData');
Route::get('/updateDiaryHora', [LoginController::class, 'actualizarRelatorioDiarioHora'])->name('updateDiaryHora');
Route::get('/checkDataFromDiario', [LoginController::class, 'toCheckDataFromDiario'])->name('checkDataFromDiario');

// Dashboard Resumo
Route::middleware(CheckUserMiddleware::class)->group(function () {
    Route::get('/dashboard_resumo', [ReportController::class, 'dashboard_resumo'])->name('dashboard_resumo');
    Route::post('/dashboard_resumo_table', [ReportController::class, 'dashboard_resumo_table'])->name('dashboard_resumo_table');
    Route::post('/dashboard_resumo_graphics', [ReportController::class, 'dashboard_resumo_graphics'])->name('dashboard_resumo_graphics');
    Route::post('/dashboard_resumo_graphics2', [ReportController::class, 'dashboard_resumo_graphics2'])->name('dashboard_resumo_graphics2');
});


//Produtos
Route::post('/loteProduto', [LoteController::class, 'loteProduto'])->name('loteProduto');
Route::post('/produtosLotes', [LoteController::class, 'produtosLotes'])->name('produtosLotes');
Route::post('/produtosCombo', [LoteController::class, 'produtos'])->name('produtos');
Route::post('/produtosComboAll', [LoteController::class, 'Allprodutos'])->name('produtosComboAll');
Route::post('/produtosTipoComboAll', [TipoProdutoController::class, 'AllTiposprodutos'])->name('produtosTipoComboAll');
Route::post('/ultimoPreco/{id}', [ProdutoController::class, 'ultimoPreco'])->name('ultimoPreco');
Route::post('/alterarPrecoVenda', [ProdutoController::class, 'alterarPrecoVenda'])->name('alterarPrecoVenda');
Route::post('/alterarPrecoVendaLote', [ProdutoController::class, 'alterarPrecoVendaLote'])->name('alterarPrecoVendaLote');
Route::post('/alterarDataValidade', [ProdutoController::class, 'alterarDataValidade'])->name('alterarDataValidade');
//endProdutos

//Help
Route::get('/contact', [HelpController::class, 'index'])->name('contact');
Route::post('/contact', [HelpController::class, 'sendEmail'])->name('contact');
Route::post('/contact2', [LoginController::class, 'sendEmail'])->name('contact2');
Route::post('/recuperar', [LoginController::class, 'sendEmail2'])->name('recuperar');
Route::get('/recuperar_senha/{email}', [LoginController::class, 'recuperar_senha_index'])->name('recuperar_senha');
Route::post('/recuperar_senha', [LoginController::class, 'recuperar_senha'])->name('recuperar_senha');
Route::post('/updateTotalFactura', [LoteController::class, 'updateTotalFactura'])->name('updateTotalFactura');
//EndHElp

//forma pagamento
Route::middleware(CheckUserMiddleware::class)->get('/index_forma_pagamento', [HelpController::class, 'index_forma_pagamento'])->name('index_forma_pagamento');
Route::middleware(CheckUserMiddleware::class)->get('/forma_pagamento', [HelpController::class, 'forma_pagamento_dd'])->name('forma_pagamento');
Route::post('/forma_pagamento', [HelpController::class, 'forma_pagamento_store'])->name('forma_pagamento');
Route::middleware(CheckUserMiddleware::class)->get('/forma_pagamento_update/{id}', [HelpController::class, 'forma_pagamento_edit'])->name('forma_pagamento_update');
Route::post('/forma_pagamento_update/{id}', [HelpController::class, 'forma_pagamento_update'])->name('forma_pagamento_update');
Route::post('/listar_forma_pagamento', [HelpController::class, 'listar_forma_pagamento'])->name('listar_forma_pagamento');
Route::post('/forma_pagamento_delete', [HelpController::class, 'delete'])->name('forma_pagamento_delete');
//End pagamento

//Empresa
Route::post('/empresa', [FarmaciaController::class, 'empresa'])->name('empresa');
Route::post('/updateEmpresa{id}', [FarmaciaController::class, 'updateEmpresa'])->name('updateEmpresa');

//Produtos de um fornecedor
Route::middleware(CheckUser5Middleware::class)->get('prodFornec', [LoteController::class, 'indexFornProd'])->name('prodFornec');
Route::post('prodFornecTable', [LoteController::class, 'fornProd'])->name('prodFornecTable');

//Receita user
Route::middleware(CheckUserMiddleware::class)->get('/receitaUser', [ReportController::class, 'indexReceitaUser'])->name('receitaUser');
Route::post('/receitaUser', [ReportController::class, 'receitasUser'])->name('receitaUser');

//Relatório Resumo
Route::middleware(CheckUserMiddleware::class)->get('/report', [ReportController::class, 'index'])->name('report');
Route::post('/report', [ReportController::class, 'report'])->name('report');
Route::post('/report2', [ReportController::class, 'report2'])->name('report2');
Route::post('/report3', [ReportController::class, 'report3'])->name('report3');

// Expirados por fornecedor
Route::middleware(CheckUserMiddleware::class)->get('/expirados_fornecedor', [ReportController::class, 'indexExpiradosPorFornecedor'])->name('expirados_fornecedor');
Route::post('/expirados_fornecedor', [ReportController::class, 'expiradosPorFornecedor'])->name('expirados_fornecedor');

//Relatório do ponto de encomenda
Route::middleware(CheckUser5Middleware::class)->get('/ponto_encomenda', [ReportController::class, 'indexPEncomenda'])->name('ponto_encomenda');
Route::post('/ponto_encomenda', [ReportController::class, 'pontoEncomenda'])->name('ponto_encomenda');
Route::middleware(CheckUser5Middleware::class)->get('/vendas_canceladas', [ReportController::class, 'index_vendas_canceladas'])->name('vendas_canceladas');
Route::post('/vendas_canceladas', [ReportController::class, 'vendas_canceladas'])->name('vendas_canceladas');

//Relatório Abates Efectuados
Route::middleware(CheckUser5Middleware::class)->get('/abates', [ReportController::class, 'index_abates_efectuados'])->name('abates');
Route::post('/abates', [ReportController::class, 'abates_efectuados'])->name('abates');

//Relatório de produtos fora do prazo
Route::middleware(CheckUser5Middleware::class)->get('produtosForaPrazo', [ProdutoController::class, 'produtosForaPrazo'])->name('produtosForaPrazo');
Route::post('listarFraPrazo', [ProdutoController::class, 'listarFraPrazo'])->name('listarFraPrazo');

// Group routes for gastos_operacionais
Route::prefix('gastos_operacionais')->name('gastos_operacionais.')->group(function () {
    Route::get('/index', [HelpController::class, 'gastoOperacional'])->name('index');
    Route::get('/create', [HelpController::class, 'createGastosOperacionais'])->name('create');
    Route::get('/listar', [HelpController::class, 'listarGastoOperacional'])->name('listar');
    Route::post('/create', [HelpController::class, 'storeGastosOperacionais'])->name('create');
    Route::post('/table', [HelpController::class, 'table_gastos'])->name('table');
    Route::get('/index', [HelpController::class, 'index_gastos'])->name('index');
    Route::get('/update/{id}', [HelpController::class, 'edit_gastos'])->name('update');
    Route::post('/update/{id}', [HelpController::class, 'update_gastos'])->name('update');
});

// Group routes for servicos
Route::prefix('servicos')->name('servicos.')->group(function () {
    Route::get('/index', [HelpController::class, 'index_servicos'])->name('index');
    Route::get('/create', [HelpController::class, 'create_servicos'])->name('create');
    Route::post('/create', [HelpController::class, 'store_servicos'])->name('create');
    Route::get('/listar', [HelpController::class, 'listarServicos'])->name('listar');
    Route::post('/table', [HelpController::class, 'table_servicos'])->name('table');
    Route::get('/update/{id}', [HelpController::class, 'edit_servicos'])->name('update');
    Route::post('/update/{id}', [HelpController::class, 'store_servicos_update'])->name('update');
});

// Admin routes with middleware
Route::middleware(CheckUserMiddleware::class)
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('utilizadores', UtilizadorController::class, ['except' => ['filtros']]);
        Route::post('utilizadores/listar', [UtilizadorController::class, 'index'])->name('utilizadores.listar');
        Route::get('utilizadores/', [UtilizadorController::class, 'filtros'])->name('utilizadores');
        Route::get('admin/pdf', [UtilizadorController::class, 'gerapdf'])->name('utilizadores.pdf');
    });

// Admin routes for all users
Route::middleware(AllUsersMiddleware::class)
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('edit2/{id}', [UtilizadorController::class, 'edit2'])->name('edit2');
        Route::patch('update2/{utilizadore}', [UtilizadorController::class, 'update2'])->name('update2');
    });

// Admin dashboard routes
Route::middleware(CheckUserMiddleware::class)
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboard/diario_vendas', [DashboardController::class, 'diario_vendas'])->name('dashboard.diario_vendas');
        Route::get('/dashboard_geral', [DashboardController::class, 'index2'])->name('dashboard.index2');
    });

// Tipo utilizador routes
Route::middleware(CheckUserMiddleware::class)->prefix('tipo_utilizador')->group(function () {
    Route::post('listar', [TipoUtilizadorController::class, 'index'])->name('tipo_utilizador');
    Route::get('/', [TipoUtilizadorController::class, 'filtros']);
    Route::get('create', [TipoUtilizadorController::class, 'create']);
    Route::post('create', [TipoUtilizadorController::class, 'store']);
    Route::get('pdf', [TipoEntradaController::class, 'gerapdf']);
    Route::post('delete', [TipoUtilizadorController::class, 'delete']);
    Route::get('update/{tipo_utilizador}', [TipoUtilizadorController::class, 'edit']);
    Route::patch('update/{tipo_utilizador}', [TipoUtilizadorController::class, 'update']);
});

// Tipo entrada routes
Route::middleware(CheckUserMiddleware::class)->prefix('tipo_entrada')->name('tipo_entrada.')->group(function () {
    Route::post('listar', [TipoEntradaController::class, 'index'])->name('listar');
    Route::get('/', [TipoEntradaController::class, 'filtros'])->name('filtros');
    Route::get('create', [TipoEntradaController::class, 'create'])->name('create');
    Route::post('create', [TipoEntradaController::class, 'store'])->name('create');
    Route::post('delete', [TipoEntradaController::class, 'delete'])->name('delete');
    Route::get('pdf', [TipoEntradaController::class, 'gerapdf'])->name('pdf');
    Route::get('update/{tipo_entrada}', [TipoEntradaController::class, 'edit'])->name('edit');
    Route::patch('update/{tipo_entrada}', [TipoEntradaController::class, 'update'])->name('update');
});

// Tipo saida routes
Route::middleware(CheckUserMiddleware::class)->prefix('tipo_saida')->name('tipo_saida.')->group(function () {
    Route::post('listar', [TipoSaidaController::class, 'index'])->name('listar');
    Route::get('/', [TipoSaidaController::class, 'filtros'])->name('filtros');
    Route::get('create', [TipoSaidaController::class, 'create'])->name('create');
    Route::post('create', [TipoSaidaController::class, 'store'])->name('create');
    Route::post('delete', [TipoSaidaController::class, 'delete'])->name('delete');
    Route::get('pdf', [TipoSaidaController::class, 'gerapdf'])->name('pdf');
    Route::get('update/{tipo_saida}', [TipoSaidaController::class, 'edit'])->name('edit');
    Route::patch('update/{tipo_saida}', [TipoSaidaController::class, 'update'])->name('update');
});

// Tipo produto routes
Route::middleware(CheckUserMiddleware::class)->prefix('tipo_produto')->name('tipo_produto.')->group(function () {
    Route::post('listar', [TipoProdutoController::class, 'index'])->name('listar');
    Route::get('/', [TipoProdutoController::class, 'filtros'])->name('filtros');
    Route::get('create', [TipoProdutoController::class, 'create'])->name('create');
    Route::get('pdf', [TipoProdutoController::class, 'gerapdf'])->name('pdf');
    Route::post('create', [TipoProdutoController::class, 'store'])->name('create');
    Route::post('delete', [TipoProdutoController::class, 'delete'])->name('delete');
    Route::get('update/{tipo_produto}', [TipoProdutoController::class, 'edit'])->name('edit');
    Route::patch('update/{tipo_produto}', [TipoProdutoController::class, 'update'])->name('update');
});

// Tipo proviniencia routes
Route::middleware(CheckUserMiddleware::class)->prefix('tipo_proviniencia')->name('tipo_proviniencia.')->group(function () {
    Route::post('listar', [TipoProvinienciaController::class, 'index'])->name('listar');
    Route::get('/', [TipoProvinienciaController::class, 'filtros'])->name('filtros');
    Route::get('create', [TipoProvinienciaController::class, 'create'])->name('create');
    Route::get('pdf', [TipoProvinienciaController::class, 'gerapdf'])->name('pdf');
    Route::post('create', [TipoProvinienciaController::class, 'store'])->name('create');
    Route::post('delete', [TipoProvinienciaController::class, 'delete'])->name('delete');
    Route::patch('update/{tipo_produto}', [TipoProvinienciaController::class, 'update'])->name('update');
    Route::get('edit/{tipo_produto}', [TipoProvinienciaController::class, 'edit'])->name('edit');
});

// Notificacoes routes
Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
    Route::post('listar', [NotificarController::class, 'index'])->name('listar');
    Route::get('notify', [NotificarController::class, 'notify'])->name('notify');
    Route::get('/', [NotificarController::class, 'filtros'])->name('filtros');
});

// Cliente routes
Route::middleware(CheckUser4Middleware::class)->prefix('cliente')->name('cliente.')->group(function () {
    Route::post('listar', [ClienteController::class, 'index'])->name('listar');
    Route::get('/', [ClienteController::class, 'filtros'])->name('filtros');
    Route::get('create', [ClienteController::class, 'create'])->name('create');
    Route::post('create', [ClienteController::class, 'store'])->name('create');
    Route::get('show/{cliente}', [ClienteController::class, 'show'])->name('show');
    Route::get('pdf', [ClienteController::class, 'gerapdf'])->name('pdf');
    Route::post('/addSaida', [ClienteController::class, 'addSaida'])->name('addSaida');
    Route::post('/addSaida2', [ClienteController::class, 'addSaida2'])->name('addSaida2');
    Route::post('delete', [ClienteController::class, 'delete'])->name('delete');
    Route::get('update/{cliente}', [ClienteController::class, 'edit'])->name('update');
    Route::patch('update/{cliente}', [ClienteController::class, 'update'])->name('update');
    Route::post('sync', [ClienteController::class, 'sync'])->name('sync');

    // Tipo cliente
    Route::post('listarTipoCliente', [ClienteController::class, 'table_tipo_cliente'])->name('listarTipoCliente');
    Route::get('indexTipoCliente', [ClienteController::class, 'tipo_cliente_index'])->name('indexTipoCliente');
    Route::post('createTipoCliente', [ClienteController::class, 'storeTipoCliente'])->name('createTipoCliente');
    Route::get('editTipoCliente/{id}', [ClienteController::class, 'tipo_cliente_edit'])->name('editTipoCliente');
    Route::post('updateTipoCliente/{tipoCliente}', [ClienteController::class, 'updateTipoCliente'])->name('updateTipoCliente');
    Route::post('deleteTipoCliente/{tipoCliente}', [ClienteController::class, 'deleteTipoCliente'])->name('deleteTipoCliente');
});

Route::post('cliente/pdf', [PrintController::class, 'imprimir']);

// Devolucoes routes
Route::middleware(CheckUser4Middleware::class)->get('/devolucoes', [SaidaController::class, 'devolucoes_index'])->name('devolucoes');
Route::post('devolucoes_table', [SaidaController::class, 'devolucoes'])->name('devolucoes_table');

// Saida routes
Route::prefix('saida')->name('saida.')->group(function () {
    // Cotacao
    Route::middleware(CheckUser4Middleware::class)->get('/cotacao', [SaidaController::class, 'create_cotacao'])->name('cotacao');
    Route::middleware(CheckUser4Middleware::class)->get('editCotacao/{saida}', [SaidaController::class, 'editCotacao'])->name('editCotacao');
    Route::post('removerCotacao/{saida}', [SaidaController::class, 'apagarCotacao'])->name('removerCotacao');
    Route::post('/cotacao', [SaidaController::class, 'storeCotacao'])->name('cotacao');
    Route::post('/cotacao_store', [SaidaController::class, 'storeCotacao1'])->name('cotacao_store');
    Route::post('/cotacaoUpdate/{saida_id}', [SaidaController::class, 'updateCotacao'])->name('cotacaoUpdate');
    Route::middleware(CheckUser4Middleware::class)->get('indexCotacao', [SaidaController::class, 'paramsCotacao'])->name('indexCotacao');
    Route::post('listarCotacao/', [SaidaController::class, 'indexCotacao'])->name('listarCotacao');
    Route::post('clientesPorTipo/', [SaidaController::class, 'clientesPorTipo'])->name('clientesPorTipo');

    // Devolucao
    Route::middleware(CheckUser4Middleware::class)->get('/index_devolucao', [SaidaController::class, 'index_devolucao'])->name('index_devolucao');
    Route::post('/fetch_devolucao', [SaidaController::class, 'fetch_devolucao'])->name('fetch_devolucao');
    Route::get('show_devolucao/{saida}', [SaidaController::class, 'show_devolucao'])->name('show_devolucao');
    Route::post('/fetch_show', [SaidaController::class, 'fetch_show'])->name('fetch_show');
    Route::post('/recibo_devolucao/{saida}', [SaidaController::class, 'recibo_devolucao'])->name('recibo_devolucao');

    // Sincronizar vendas
    Route::post('sync_vendas', [SaidaController::class, 'sincronizar_vendas'])->name('sync');

    // Tipo de pagamentos carrinho
    Route::post('adicionarTipoPagamento', [SaidaController::class, 'adicionarTipoPagamento'])->name('adicionarTipoPagamento');
    Route::post('listarTipoPagamento/', [SaidaController::class, 'listarCarinhoTipoPagamento'])->name('listarTipoPagamento');
    Route::post('removerTipoPagamento/{id}', [SaidaController::class, 'removerTipoPagamento'])->name('removerTipoPagamento');
    Route::get('limparTipoPagamento', [SaidaController::class, 'limparTipoPagamento'])->name('limparTipoPagamento');

    // Tipo de pagamentos carrinho 2
    Route::post('adicionarTipoPagamento2', [SaidaController::class, 'adicionarTipoPagamento2'])->name('adicionarTipoPagamento2');
    Route::post('listarTipoPagamento2/', [SaidaController::class, 'listarCarinhoTipoPagamento2'])->name('listarTipoPagamento2');
    Route::post('removerTipoPagamento2/{id}', [SaidaController::class, 'removerTipoPagamento2'])->name('removerTipoPagamento2');

    // Pagamentos
    Route::middleware(CheckUserMiddleware::class)->get('indexPagamentos', [SaidaController::class, 'indexPagamentos'])->name('indexPagamentos');
    Route::post('listarPagamentos/', [SaidaController::class, 'pagamentos'])->name('listarPagamentos');
    Route::middleware(CheckUserMiddleware::class)->get('detailsPagamentos/{cliente_id}/{dataInicio}/{dataFim}', [SaidaController::class, 'detailsPagamentos'])->name('detailsPagamentos');

    // Nota credito
    Route::middleware(CheckUser4Middleware::class)->get('indexNotaCredito', [SaidaController::class, 'indexNotaCredito'])->name('indexNotaCredito');
    Route::post('listarNotaCredito/', [SaidaController::class, 'nota_credito'])->name('listarNotaCredito');
    Route::middleware(CheckUser4Middleware::class)->get('detailsNotaCredito/{cliente_id}/{saida_id}/{dataInicio}/{dataFim}', [SaidaController::class, 'detailsNotaCredito'])->name('detailsNotaCredito');

    // Dividas
    Route::middleware(CheckUserMiddleware::class)->get('dividaCliente', [SaidaController::class, 'listarDividaClientes'])->name('dividaCliente');
    Route::middleware(CheckUserMiddleware::class)->get('detalhesDividaCliente', [SaidaController::class, 'detalhesDividaCliente'])->name('detalhesDividaCliente');
    Route::middleware(CheckUserMiddleware::class)->get('dividas', [SaidaController::class, 'listarDividas'])->name('dividas');

    // Other saida routes
    Route::get('', [SaidaController::class, 'params'])->name('filtros');
    Route::middleware(CheckUserMiddleware::class)->get('listaAbate', [SaidaController::class, 'indexAbate'])->name('listaAbate');
    Route::post('listar/', [SaidaController::class, 'index'])->name('listar');
    Route::post('listar2/', [SaidaController::class, 'index2'])->name('listar2');
    Route::middleware(CheckUser4Middleware::class)->get('pdf', [SaidaController::class, 'gerapdf'])->name('pdf');
    Route::get('/create', [SaidaController::class, 'create'])->name('create');
    Route::post('/create', [SaidaController::class, 'store'])->name('create');

    Route::get('/credito', [SaidaController::class, 'create_credito'])->name('credito');
    Route::post('/credito', [SaidaController::class, 'store_credito'])->name('credito');
    Route::post('/notificar', [NotificarController::class, 'notificar'])->name('notificar');
    Route::get('/notificar', [NotificarController::class, 'notificar'])->name('notificar');
    Route::post('/storeAbate', [SaidaController::class, 'storeAbate'])->name('storeAbate');
    Route::get('/{saida}', [SaidaController::class, 'show'])->name('show');
    Route::get('/devolucao/{saida}', [SaidaController::class, 'devolucaoVenda'])->name('devolucao');
    Route::post('devolucao', [SaidaController::class, 'storedevolucaoVenda'])->name('devolucao');
    Route::get('show2/{saida}', [SaidaController::class, 'show2'])->name('show2');

    Route::middleware(CheckUserMiddleware::class)->get('viewUpdateAbate/{saida_id}', [SaidaController::class, 'viewUpdateAbate'])->name('viewUpdateAbate');

    // Carinho abate update routes
    Route::prefix('carinhoAbateUpdate')->name('carinhoLoteUpdate.')->group(function () {
        Route::post('/', [SaidaController::class, 'listarCarinhoAbateUpdate'])->name('listarCarinhoAbateUpdate');
        Route::post('AddCarinhoShowAbate/{saida_id}', [SaidaController::class, 'AddCarinhoShowAbate'])->name('AddCarinhoShowAbate');
        Route::post('removerAbate/{produto}', [SaidaController::class, 'removerCarinhoAbate'])->name('removerAbate');
        Route::post('replaceAbate/{produto}', [SaidaController::class, 'replaceAbate'])->name('replaceAbate');
        Route::post('/storeUpdateAbate', [SaidaController::class, 'storeUpdateAbate'])->name('storeUpdateAbate');
        Route::post('adicionarAbateUpdate/{produto}', [SaidaController::class, 'adicionarAbateUpdate'])->name('adicionarAbateUpdate');
    });

    // Payments
    Route::get('/pay/{id}', [SaidaController::class, 'paymentInfo'])->name('pay');
    Route::get('/pay_all/{id}/{date}/{date2}', [SaidaController::class, 'paymentAllInfo'])->name('pay_all');
    Route::post('/pay', [SaidaController::class, 'storePayment'])->name('pay');
    Route::post('/pay_all', [SaidaController::class, 'storePaymentAll'])->name('pay_all');

    Route::post('/cancelar/{saida}', [SaidaController::class, 'cancelar'])->name('cancelar');
    Route::get('/pdf/{saida}', [PrintController::class, 'printSaida'])->name('pdfsida');
    Route::get('showPdf/{saida}', [SaidaController::class, 'gerashowpdf'])->name('showPdf');

    // Carinho routes
    Route::prefix('carinho')->name('carinho.')->group(function () {
        Route::post('/', [SaidaController::class, 'listarCarinho'])->name('listar');
        Route::post('adicionar/{produto}', [SaidaController::class, 'adicionar'])->name('adicionar');
        Route::post('adicionarCodBar/{produto}', [SaidaController::class, 'adicionarCodigo'])->name('adicionarCodBar');
        Route::post('adicionarVCCodBar/{produto}', [SaidaController::class, 'adicionarCodigoVC'])->name('adicionarVCCodBar');
        Route::post('adicionar_credito/{produto}', [SaidaController::class, 'adicionar_credito'])->name('adicionar_credito');
        Route::post('remover/{produto}', [SaidaController::class, 'remover'])->name('remover');
        Route::post('remover_credito/{produto}', [SaidaController::class, 'remover_credito'])->name('remover_credito');
        Route::post('/cancelar', [SaidaController::class, 'cancelarVenda'])->name('cancelar');
        Route::post('/cancelar_credito', [SaidaController::class, 'cancelarCredito'])->name('cancelar_credito');
    });

    // Carinho credito routes
    Route::prefix('carinhoCredito')->name('carinhoCredito.')->group(function () {
        Route::post('/listarCredito', [SaidaController::class, 'listarCarinhoCredito'])->name('listarCredito');
    });

    // Abate
    Route::post('dados_abate', [SaidaController::class, 'dados_abate'])->name('dados_abate');
    Route::post('selectLotes', [SaidaController::class, 'selectLotes'])->name('selectLotes');
    Route::post('actualizarDadosAbate', [SaidaController::class, 'actualizarDadosAbate'])->name('actualizarDadosAbate');

    // Carinho abate routes
    Route::prefix('carinhoAbate')->name('carinhoAbate.')->group(function () {
        Route::post('/', [SaidaController::class, 'listarCarinhoAbate'])->name('listarAbate');
        Route::post('adicionarAbate/{protudo}', [SaidaController::class, 'adicionarAbate'])->name('adicionarAbate');
        Route::post('removerAbate/{produto}', [SaidaController::class, 'removerAbate'])->name('removerAbate');
        Route::post('/cancelarAbate', [SaidaController::class, 'cancelarAbate'])->name('cancelarAbate');
    });

    // Carinho cotacao routes
    Route::prefix('carinhoCotacao')->name('carinhoCotacao.')->group(function () {
        Route::post('/', [SaidaController::class, 'listarCarinhoCotacao'])->name('listarCotacao');
        Route::post('/listarCotacaoUpdate', [SaidaController::class, 'listarCarinhoCotacaoUpdate'])->name('listarCotacaoUpdate');
        Route::post('adicionarCotacao/{produto}', [SaidaController::class, 'adicionarCotacao'])->name('adicionarCotacao');
        Route::post('adicionarCotacaoUpdate/{produto}', [SaidaController::class, 'adicionarCotacao2'])->name('adicionarCotacaoUpdate');
        Route::post('adicionarCotacaoBD/{produto}', [SaidaController::class, 'adicionarCotacaoBD'])->name('adicionarCotacaoBD');
        Route::post('adicionarCodBar/{produto}', [SaidaController::class, 'adicionarCodigo2'])->name('adicionarCodBar');
        Route::post('remover/{produto}', [SaidaController::class, 'removerCotacao'])->name('remover');
        Route::post('remover2/{produto}', [SaidaController::class, 'removerCotacao2'])->name('remover2');
        Route::post('/cancelar', [SaidaController::class, 'cancelarCotacao'])->name('cancelar');
        Route::post('/cancelar2', [SaidaController::class, 'cancelarCotacao2'])->name('cancelar2');
        // Vender
        Route::post('adicionarVendaCotacao/{saida_id}', [SaidaController::class, 'adicionarVendaCotacao'])->name('adicionarVendaCotacao');
        Route::post('adicionarVendaCotacao2/{saida_id}', [SaidaController::class, 'adicionarVendaCotacao2'])->name('adicionarVendaCotacao2');
    });

    // Invoice
    Route::get('/invoice/{saida}', [SaidaController::class, 'invoice'])->name('invoice');
    Route::get('/invoiceProforma/{saida}', [SaidaController::class, 'invoiceProforma'])->name('invoiceProforma');
    Route::post('/invoice_listar', [SaidaController::class, 'invoice_listar'])->name('invoice_listar');

    // Recibo
    Route::post('/recibo/{saida}', [SaidaController::class, 'recibo'])->name('recibo');
    Route::post('/recibo_cotacao/{saida}', [SaidaController::class, 'recibo_cotacao'])->name('recibo_cotacao');
});

// SaidaItem routes
Route::middleware(CheckUser4Middleware::class)->prefix('saidaItem')->name('saidaItem.')->group(function () {
    Route::get('/receita', [SaidaItemController::class, 'filtros'])->name('filtros');
    Route::post('/listar', [SaidaItemController::class, 'report'])->name('listar');
    Route::post('/printVendas', [SaidaItemController::class, 'printVendas'])->name('printVendas');
    Route::get('/receitaAbate', [SaidaItemController::class, 'filtros2'])->name('filtrosAbate');
    Route::post('/listarAbate', [SaidaItemController::class, 'report2'])->name('listarAbate');
    Route::get('pdf', [SaidaItemController::class, 'gerapdf'])->name('pdf');
});

// Fornecedor routes
Route::prefix('fornecedor')->name('fornecedor.')->group(function () {
    Route::post('listar', [FornecedorController::class, 'index'])->name('listar');
    Route::middleware(CheckUser2Middleware::class)->get('/', [FornecedorController::class, 'filtros'])->name('filtros');
    Route::middleware(CheckUser2Middleware::class)->get('create', [FornecedorController::class, 'create'])->name('create');
    Route::post('create', [FornecedorController::class, 'store'])->name('create');
    Route::post('addEntradaFornecedor', [FornecedorController::class, 'addEntradaFornecedor'])->name('addEntradaFornecedor');
    Route::post('delete', [FornecedorController::class, 'delete'])->name('delete');
    Route::middleware(CheckUser2Middleware::class)->get('show/{fornecedor}', [FornecedorController::class, 'show'])->name('show');
    Route::middleware(CheckUser2Middleware::class)->get('pdf', [FornecedorController::class, 'gerapdf'])->name('pdf');
    Route::middleware(CheckUser2Middleware::class)->get('update/{fornecedor}', [FornecedorController::class, 'edit'])->name('update');
    Route::patch('update/{fornecedor}', [FornecedorController::class, 'update'])->name('update');
    Route::post('sync', [FornecedorController::class, 'sync'])->name('sync');
});

// Inventario routes
Route::middleware(CheckUser3Middleware::class)->prefix('inventario')->name('inventario.')->group(function () {
    Route::post('listar', [InventarioController1::class, 'index'])->name('listar');
    Route::get('/', [InventarioController1::class, 'filtros'])->name('filtros');
    Route::get('create', [InventarioController1::class, 'create'])->name('create');
    Route::post('show/{inventario}', [InventarioController1::class, 'listarCarinhoItems2'])->name('show');
    Route::post('showPending/{inventario}', [InventarioController1::class, 'listarProdutosPendentes'])->name('showPending');
    Route::get('create_items/{inventario}', [InventarioController1::class, 'create_items'])->name('create_items');
    Route::get('showItems/{inventario}', [InventarioController1::class, 'showItems'])->name('showItems');
    Route::get('showNotCounts/{inventario}', [InventarioController1::class, 'showNotCounts'])->name('showNotCounts');
    Route::get('reportInventario/{inventario}', [InventarioController1::class, 'IndexReportInventario'])->name('reportInventario');
    Route::post('reportInventario/{inventario}', [InventarioController1::class, 'reportReportInventario'])->name('reportInventario');
    Route::post('create', [InventarioController1::class, 'store'])->name('create');
    Route::post('delete', [InventarioController1::class, 'delete'])->name('delete');
    Route::get('update/{inventario}', [InventarioController1::class, 'edit'])->name('update');
    Route::patch('update/{inventario}', [InventarioController1::class, 'update'])->name('update');
    Route::post('updateEstado/{inventario}', [InventarioController1::class, 'updateEstado'])->name('updateEstado');

    // Carinho user routes
    Route::prefix('carinhoUser')->name('carinhoUser.')->group(function () {
        Route::post('/', [InventarioController1::class, 'listarCarinho'])->name('listar');
        Route::post('adicionar/{user}', [InventarioController1::class, 'adicionar'])->name('adicionar');
        Route::post('remover/{user}', [InventarioController1::class, 'remover'])->name('remover');
        Route::post('/cancelar', [InventarioController1::class, 'cancelar'])->name('cancelar');
    });

    // Carinho items routes
    Route::prefix('carinhoItems')->name('carinhoItems.')->group(function () {
        Route::post('/', [InventarioController1::class, 'listarCarinhoItems'])->name('listar');
        Route::post('/adicionar', [InventarioController1::class, 'adicionar_items'])->name('adicionar');
        Route::post('replaceInventario/{produto}', [InventarioController1::class, 'replaceInventario'])->name('replaceInventario');
        Route::post('remover/{produto}', [InventarioController1::class, 'removerItems'])->name('remover');
        Route::post('/cancelar', [InventarioController1::class, 'cancelarItems'])->name('cancelar');
    });
});

// Lote routes
Route::prefix('lote')->name('lote.')->group(function () {
    Route::post('listar', [LoteController::class, 'index'])->name('listar');
    Route::middleware(CheckUser2Middleware::class)->get('/', [LoteController::class, 'filtros'])->name('filtros');
    Route::middleware(CheckUser2Middleware::class)->get('create', [LoteController::class, 'create'])->name('create');
    Route::post('create', [LoteController::class, 'store'])->name('create');
    Route::post('/consulta', [LoteController::class, 'consulta'])->name('consulta');
    Route::post('/getLotesByProduto', [LoteController::class, 'getLotesByProduto'])->name('getLotesByProduto');
    Route::post('/getLotesByProdutoInventario', [LoteController::class, 'getLotesByProdutoInventario'])->name('getLotesByProdutoInventario');
    Route::post('/getLotesActivosByProduto', [LoteController::class, 'getLotesActivosByProduto'])->name('getLotesActivosByProduto');
    Route::post('/consultaLote', [LoteController::class, 'consultaLote'])->name('consultaLote');
    Route::post('/consultaPreco', [LoteController::class, 'consultaPreco'])->name('consultaPreco');
    Route::post('/consulta2', [LoteController::class, 'consulta2'])->name('consulta2');
    Route::post('/consultaUltimoPreco', [LoteController::class, 'consultaUltimoPreco'])->name('consultaUltimoPreco');
    Route::post('/consulta3', [LoteController::class, 'consulta3'])->name('consulta3');
    Route::post('/consultaLotes', [LoteController::class, 'consultaLotes'])->name('consultaLotes');
    Route::middleware(CheckUser2Middleware::class)->get('update/{lote}', [LoteController::class, 'edit'])->name('update');
    Route::post('delete', [LoteController::class, 'delete'])->name('delete');
    Route::middleware(CheckUser2Middleware::class)->get('show/{lote}', [LoteController::class, 'show'])->name('show');
    Route::get('pdf', [LoteController::class, 'gerapdf'])->name('pdf');

    // Lote update data
    Route::post('dados_factura', [LoteController::class, 'dados_factura'])->name('dados_factura');
    Route::post('dados_factura2', [LoteController::class, 'dados_factura2'])->name('dados_factura2');

    Route::get('relatoriopdf', [LoteController::class, 'geraRelatorioPdf'])->name('relatoriopdf');
    Route::post('lotes', [LoteController::class, 'relatorio'])->name('lotes');
    Route::middleware(CheckUser2Middleware::class)->get('/relatorio', [LoteController::class, 'report'])->name('relatorio');

    // Show detalhes ajax
    Route::get('showDetalhesTable/{id}', [LoteController::class, 'showDetalhesTable'])->name('showDetalhesTable');

    // Preço de compra/custo
    Route::post('preco_custo/{produto}', [LoteController::class, 'preco_custo'])->name('preco_custo');

    // Notas de crédito fornecedor
    Route::get('index_nota_credito2', [LoteController::class, 'nota_credito_index'])->name('index_nota_credito2');
    Route::post('nota_credito_table', [LoteController::class, 'table_nota_credito'])->name('nota_credito_table');
    Route::get('index_nota_credito/{id}', [LoteController::class, 'index_nota_credito'])->name('index_nota_credito');
    Route::post('nota_credito_entrada', [LoteController::class, 'storeNotaCredito'])->name('nota_credito_entrada');
    Route::post('nota_credito_update/{id}', [LoteController::class, 'nota_credito_update'])->name('nota_credito_update');
    Route::post('listarCarinhoNotaCredito', [LoteController::class, 'listarCarinhoNotaCredito'])->name('listarCarinhoNotaCredito');
    Route::post('removerNotaCredito/{id}', [LoteController::class, 'remover_nota_credito'])->name('removerNotaCredito');
    Route::post('nota_credito_edit/{id}', [LoteController::class, 'nota_credito_edit'])->name('nota_credito_edit');

    Route::middleware(CheckUser2Middleware::class)->get('/relatorioProduto', [LoteController::class, 'reportProduto'])->name('relatorioProduto');
    Route::middleware(CheckUser2Middleware::class)->get('relatorioProdutopdf', [LoteController::class, 'geraRelatorioProdutoPdf'])->name('relatorioProdutopdf');
    Route::middleware(CheckUser2Middleware::class)->get('showPdf/{lote}', [LoteController::class, 'gerashowpdf'])->name('showPdf');
    Route::patch('update/{lote}', [LoteController::class, 'update'])->name('update');

    Route::get('/abate', [SaidaController::class, 'createAbate'])->name('createabate');

    // Carinho lote routes
    Route::prefix('carinhoLote')->name('carinhoLote.')->group(function () {
        Route::post('/', [LoteController::class, 'listarCarinho'])->name('listar');
        Route::post('adicionar/{produto}', [LoteController::class, 'adicionar'])->name('adicionar');
        Route::post('replace2/{produto}', [LoteController::class, 'replace2'])->name('replace2');
        Route::post('remover/{produto}', [LoteController::class, 'remover'])->name('remover');
        Route::post('/cancelar', [LoteController::class, 'cancelar'])->name('cancelar');
    });

    // Carinho lote update routes
    Route::prefix('carinhoLoteUpdate')->name('carinhoLoteUpdate.')->group(function () {
        Route::post('/', [LoteController::class, 'AddCarinhoShow'])->name('listar2');
        Route::post('addCarinhoShow/{lote}', [LoteController::class, 'AddCarinhoShow'])->name('addCarinhoShow');
        Route::post('get_valor_remanescente', [LoteController::class, 'get_valor_remanescente'])->name('get_valor_remanescente');
        Route::post('replace/{produto}', [LoteController::class, 'replace'])->name('replace');
        Route::post('adicionar2/{produto}', [LoteController::class, 'adicionar2'])->name('adicionar2');
        Route::post('removerCarinhoLoteUpdate/{produto}', [LoteController::class, 'removerCarinhoLoteUpdate'])->name('removerCarinhoLoteUpdate');
        Route::post('/cancelarCarinhoLoteUpdate', [LoteController::class, 'cancelarCarinhoLoteUpdate'])->name('cancelarCarinhoLoteUpdate');
        Route::post('/updateUsingCarinho', [LoteController::class, 'updateUsingCarinho'])->name('updateUsingCarinho');
    });

    Route::post('listarLotes', [LoteController::class, 'entradaLote'])->name('listarLotes');
    Route::middleware(CheckUser2Middleware::class)->get('/indexEntradaLote', [LoteController::class, 'indexEntradaLote'])->name('indexEntradaLote');

    Route::post('listarLotesProduto', [LoteController::class, 'lotesProduto'])->name('listarLotesProduto');
    Route::middleware(CheckUser2Middleware::class)->get('/indexLotesProduto', [LoteController::class, 'indexLotesProduto'])->name('indexLotesProduto');
});

// Produto routes
Route::prefix('produto')->name('produto.')->group(function () {
    Route::get('acertos_lotes_produto_index', [ProdutoController::class, 'acertos_lotes_produto_index'])->name('acertos_lotes_produto_index');
    Route::post('listar', [ProdutoController::class, 'index'])->name('listar');
    Route::middleware(CheckUser5Middleware::class)->get('/', [ProdutoController::class, 'filtros'])->name('filtros');
    Route::post('/listComboInventory', [ProdutoController::class, 'listComboInventory'])->name('listComboInventory');
    Route::post('/listComboOutInventory', [ProdutoController::class, 'listComboOutInventory'])->name('listComboOutInventory');
    Route::get('/listProduct', [ProdutoController::class, 'listProduct'])->name('listProduct');
    Route::middleware(CheckUser2Middleware::class)->get('create', [ProdutoController::class, 'create'])->name('create');
    Route::post('create', [ProdutoController::class, 'store'])->name('create');
    Route::post('/addEntradaProduto', [ProdutoController::class, 'addEntradaProduto'])->name('addEntradaProduto');
    Route::post('delete', [ProdutoController::class, 'delete'])->name('delete');
    Route::post('reactivar-produto', [ProdutoController::class, 'reactivarProduto'])->name('reactivar-produto');
    Route::post('/consulta', [ProdutoController::class, 'consulta'])->name('consulta');
    Route::middleware(CheckUser2Middleware::class)->get('/{produto}/edit', [ProdutoController::class, 'edit'])->name('edit');
    Route::middleware(CheckUser2Middleware::class)->get('update/{produto}', [ProdutoController::class, 'edit']);
    Route::middleware(CheckUser2Middleware::class)->get('pdf', [ProdutoController::class, 'geraPdf'])->name('pdf');
    Route::middleware(CheckUser2Middleware::class)->get('{produto}/codigobarras', [ProdutoController::class, 'gerarCodigobarras'])->name('codigobarras');
    Route::middleware(CheckUser2Middleware::class)->get('show/{produto}', [ProdutoController::class, 'show'])->name('show');
    Route::middleware(CheckUser2Middleware::class)->get('/pdf', [ProdutoController::class, 'gerapdf'])->name('pdf');
    Route::patch('update/{produto}', [ProdutoController::class, 'update'])->name('update');
    Route::post('/actualizarValidade/{produto}', [ProdutoController::class, 'actualizarValidade'])->name('actualizarValidade');
    Route::post('historico', [ProdutoController::class, 'historico'])->name('historico');
    Route::middleware(CheckUser2Middleware::class)->get('showHistorico/{produto}', [ProdutoController::class, 'showHistorico'])->name('showHistorico');
    Route::post('categoria', [ProdutoController::class, 'tipoCombo'])->name('categoria');
    // Print all produtos
    Route::post('/printProdutos', [ProdutoController::class, 'printProdutos'])->name('printProdutos');
});

Route::middleware(CheckUserMiddleware::class)->get('indexHistorico', [ProdutoController::class, 'indexHistorico'])->name('indexHistorico');

// Dashboard geral routes
Route::prefix('dashboardGeral')->name('dashboardGeral.')->group(function () {
    Route::post('caixinhas', [App\Http\Controllers\DashboardController::class, 'caixinhas'])->name('caixinhas');
    Route::post('fluxoDiario', [App\Http\Controllers\DashboardController::class, 'fluxoDiario'])->name('fluxoDiario');
    Route::post('produtosMaisVendidosTop10', [App\Http\Controllers\DashboardController::class, 'top10ProdutosMaisVendidos'])->name('produtosMaisVendidosTop10');
});

// Vendas view routes
// Route::prefix('vendas_view')->name('vendas_view')->group(function () {
//     Route::get('/', [ViewController::class, 'index'])->name('vendas_view');
// });

// Irregularidades routes
Route::prefix('irregularidades')->name('irregularidades')->group(function () {
    Route::get('/', [ProdutoController::class, 'filtros_irregularidades']);
    Route::post('list', [ProdutoController::class, 'index_irregularidades'])->name('irregularidades');
});

// Usar lotes config routes
Route::prefix('usarLotesConfig')->name('usarLotesConfig')->group(function () {
    Route::post('/', [LoginController::class, 'usarLotesConfig'])->name('usarLotesConfig');
});

// Sessao routes
Route::prefix('sessao')->name('sessao.')->group(function () {
    Route::get('/indexSessoes', [LoginController::class, 'indexSessoes'])->name('indexSessoes');
    Route::post('/filtros', [LoginController::class, 'listarSessoes'])->name('listarSessoes');
    Route::post('/store_sessao', [LoginController::class, 'store_sessao'])->name('store_sessao');
    Route::post('/close_sessao', [LoginController::class, 'close_sessao'])->name('close_sessao');
    Route::post('/recibo_fecho_dia', [SaidaController::class, 'recibo_fecho_dia'])->name('recibo_fecho_dia');
});

});
// Auth routes
// Auth::routes();
Auth::routes(['login' => false]);

