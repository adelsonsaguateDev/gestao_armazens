<?php
session_unset();
session_start();
date_default_timezone_set('Africa/Maputo');

$conteudo = isset($_SESSION['html'] ) ? $_SESSION['html'] : "<h4>Nenhuma informação encontrada!!!</h4>";
$title = isset($_SESSION['title'] ) ? $_SESSION['title'] : "Sem título";
$dataInicio = isset($_SESSION['dataInicio'])  ? $_SESSION['dataInicio'] : "";
$dataFim = isset($_SESSION['dataFim'] ) ? $_SESSION['dataFim'] : "";
$dataImpressao = date('d/m/Y H:i:s');
$nome = isset($_SESSION['nome_utilizador']) ? $_SESSION['nome_utilizador'] : "";
$nome_completo = $nome;
$parametro1 = isset($_SESSION['parametro1']) ? $_SESSION['parametro1'] : "";
$parametro2 = isset($_SESSION['parametro2']) ? $_SESSION['parametro2'] : "";
$parametro3 = isset($_SESSION['parametro3'] ) ? $_SESSION['parametro3'] : "";
$parametro4 = isset($_SESSION['parametro4'] ) ? $_SESSION['parametro4'] : "";


?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <!-- <link rel="stylesheet" href="../css/style.css" type="text/css" /> -->

    <style>

        body {
            color: black !important;
        }

        div,table,thead,tr,th,td {
            color: black !important;
        }

        p {
            color: black !important;
        }
    </style>
</head>

<body style="font-size: 10px; font-family: arial; background: white; padding-top: 0; color:black">
<div class="padding white" style="padding-top: 0; color:black">
    <div class="">
    <table cellspacing="0" style="width: 100%;">
        <tr>
            <td style="width: 100%;text-align: center; color: #444444;" class="text-center">
                <div class="invoice-desc" style="text-align: center; padding-top: 0; margin-top: 0">
                    <img style="width: 80px" src="./img/logo.png" alt="Logo"><br><br>

                    SISTEMA DE GESTÃO DE ARMAZENS<br/>
                    Email: gestao_armazens@gmail.com
                </div>
            </td>
        </tr>
    </table>
        <table cellspacing="0" style="width: 100%; color: black; margin-top: 20px">
            <tr>
                <td style="width: 100%; color: black;" class="text-center" style="text-align: left">
                    <h3>Data de impressão: <?=$dataImpressao?></h3>
                    <h3>Utilizador: <?=$nome_completo ?></h3>



                    <h3>Parametros da pesquisa:</h3><br>

                </td>
            </tr>
        </table>


        <div class="" style="color:black;">

            <div class="text-center" style="text-align:center; color:black">
                <h2 style="color:black;">
                    <?= $title ?>
                </h2>
            </div>
            <br/>
            <div style=" color:black">
                <?=$conteudo?>


            </div>
        </div>
        <br>
    </div>
</div>
</body>
</html>

