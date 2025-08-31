<?php
ini_set('memory_limit', '6144M');
// Aumentar o limite de backtrack do PCRE para resolver possíveis erros
ini_set('pcre.backtrack_limit', '10000000');

require_once '../../../vendor/autoload.php';
$app = require_once '../../../bootstrap/app.php';
// Inicialize o ambiente Laravel
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();




$user = $_SESSION['user'] ?? 'Não identificado';

date_default_timezone_set('Africa/Maputo');

// Obter o conteúdo HTML
ob_start();
if($empresa->fonte == '1'){
    include('./res/ver_htmlFacturaImportadora.php');
} else {
    include('./res/ver_htmlRecibo.php');
}
$content = ob_get_clean();

// Configurar o MPDF de acordo com o tipo de documento
if($empresa->fonte == '1') {
    // Configuração para Fatura Importadora
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9,
        // 'tempDir' => sys_get_temp_dir(),
        'simpleTables' => true,
        'packTableData' => true,
        'use_kwt' => true,
        'keepColumns' => true,
        'shrink_tables_to_fit' => 1
    ]);

    $mpdf->SetDisplayMode('fullpage');
    $mpdf->SetHTMLFooter("<small><div style='text-align: left; font-weight: bold;' class='h6'>&copy; Copyright <strong>Academia, Consultoria & Serviços Universo - ACSUN <img src='../img/logo.png' alt='' style='width: 30px; height: 30px; vertical-align: middle;'></strong></div> <div style='text-align: right'>{PAGENO} of {nbpg}</div></small>", 'O');
    $mpdf->SetHTMLHeader("<small><div style='text-align: left;' class='h6'><strong>Data da impressão: </strong>" . date("d/m/Y H:i:s") . "</div>
    <div style='text-align: left;' class='h6'><strong>Usuário: </strong>" . $user . "</div></small>");

    $outputName = 'Fatura.pdf';
} else {
    // Configuração para Recibo (formato menor, tipo ticket)
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => [72.1, 350],
        'orientation' => 'P',
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 5,
        'margin_bottom' => 5,
        // 'tempDir' => sys_get_temp_dir(),
        'simpleTables' => true
    ]);

    $outputName = 'Recibo.pdf';
}

// Otimizar para documentos grandes
$mpdf->simpleTables = true;
$mpdf->packTableData = true;

// Processar o conteúdo em chunks para evitar problemas de memória com documentos grandes
$chunk_size = 500000;
$length = strlen($content);
$position = 0;

if ($length > $chunk_size) {
    // Para documentos grandes, processar em chunks
    while ($position < $length) {
        // Pega um pedaço do tamanho especificado
        $chunk = substr($content, $position, $chunk_size);

        // Encontra o último fechamento de tag completo neste pedaço
        $last_closed_tag = strrpos($chunk, '</');
        if ($last_closed_tag !== false) {
            // Encontra o fim desta tag
            $tag_end = strpos($chunk, '>', $last_closed_tag);
            if ($tag_end !== false) {
                $chunk = substr($content, $position, $tag_end + 1);
            }
        }

        // Escreve este pedaço
        $mpdf->writeHTML($chunk);

        // Move a posição para o próximo pedaço
        $position += strlen($chunk);
    }
} else {
    // Para documentos menores, processar de uma vez
    $mpdf->writeHTML($content);
}

// Gerar e enviar o PDF
$mpdf->Output($outputName, 'I');
