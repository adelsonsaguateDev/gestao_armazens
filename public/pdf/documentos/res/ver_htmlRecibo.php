<?php session_start();



$formaPagamentos = json_decode($_SESSION['formaPagamentos']);
$saida = json_decode($_SESSION['saida']);
$saidaItems = json_decode($_SESSION['saidaItems']);
$tipoPagamento = json_decode($_SESSION['tipoPagamento']);
$user = json_decode($_SESSION['user']);
$ord=1;
$valor_pago = 0;
$user_ip = $_SERVER['REMOTE_ADDR'];
$result = filter_var(
    $user_ip,
    FILTER_VALIDATE_IP,
    FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
)
?>
<html>
<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../css/style.css" type="text/css" />
    <style>
        @page {
            margin: 0;
            font-family: Cambria,Georgia,serif;
        }
        body{
            color:#000000;

        }
        div{
            color: black;
        }
        table{
            color: black;
        }
        p{
            color:#000000;
        }
        span{
            color: black;
        }
        hr{
            display: block; height: 1px;
            border: 0; border-top: 1px solid black;
            margin: 1em 0; padding: 0;
        }

        td, tr, th {
            color:#000000;
        }
    </style>
</head>

<body style="font-size: 12px; font-family: Cambria,Georgia,serif; background: white;color: black;">

<div class="card card-invoice" style="padding-top: 16px; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
    <div class="card-header" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
        <div class="invoice-header" style="text-align: center;">
            <?php include("cabecalho.php");?>
        </div>
        <!-- <div class="invoice-desc" style="text-align: center; padding-top: 0; margin-top: 0">
            <img style="width: <?=$empresa->codigo == "FES01" ? "260px" : "140px" ?>" src="../../img/logo.png" alt="Logo"><br><br>
            <?=$empresa->nome?><br/>
            <?=$empresa->endereco?><br/>
            Nuit: <?=$empresa->nuit?><br/>
            Contacto: <?=$empresa->contacto?> ou <?=$empresa->contacto2?><br/>
            Email: <?=$empresa->email?>
            <?php if($result):?>
                <br/>
                <span style="color: #0b2e13; position: absolute; right: 16px; font-size: 1.2em">Versão Online</span>
            <?php endif;?>
        </div> -->
    </div>
    <div class="card-body" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
        <div class="separator-solid" style="padding-top: 0; margin-top: 8px; padding-bottom: 0; margin-bottom: 0"></div>
        <table class="table" style="width: 100%; text-align:center; padding-top: 0; margin-top: 0; padding-bottom: 8px; margin-bottom: 8px">
            <tbody>
            <tr>
                <!-- <td>
                    <div class="info-invoice" style="width: 100% ">
                        <h5 class="sub" style="font-weight: bold">Data da Factura</h5>
                        <p><?=$saida->data?></p>
                    </div>
                </td-->
                <td style="text-align:left">

                    <div class="info-invoice text-left" style="width: 100%; text-align: left; margin-bottom: 400px">
                        <h6 class="sub" style="font-weight: bold; text-align: left">Cliente </h6>
                        <p class="text-left">
                             Nome:<?=$saida->cliente_nome?><?=empty($saida->cliente_endereco)?"":",".$saida->cliente_endereco?>
                            <br/>Nuit: <?=$saida->cliente_nuit?>
                            <br/>Contacto: <?=$saida->cliente_contacto?>
                        </p>
                    </div>

                    <div class="info-invoice text-left" style="width: 100%; text-align: left">
                        <h5 class="sub" style="font-weight: bold">Número </h5>
                        <?=$saida->tipo_saida_id == 3 ? "Factura" : "Factura-recibo"?>:<?=$saida->numero_factura?><br>
                        Data: <?=$saida->created_at?>
                        <?php //if ($saida->tipo_saida_id != 3):?>
                            <br/><br/>
                            Pago: <?= number_format($saida->tipo_saida_id == 3 ? $saida->valor_pago :$saida->valor_entregue,2,',','.') ?> MT <br/>
                            Trocos: <?= number_format($saida->trocos,2,',','.') ?> MT
                                <?php
                                $cont=1;
                                foreach($tipoPagamento as $item){?>
                                <br/>T.P=><?= $item->designacao ?>: <?= number_format($item->valor,2,',','.') ?>
                                <?php }
                                ?>
                        <?php //endif; ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div class="invoice-detail"  style="width: 100%; text-align:center;padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
            <div class="invoice-top"  style="width: 100%">
                <table class="table" style="width: 100%; color:black; font-family: Cambria,Georgia,serif; padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
                    <thead>
                    <tr>
<!--                        <th style="font-weight: bold; color:black;"><strong>#</strong></th>-->
                        <!-- <th class="text-left" style="font-weight: bold"><strong>Código de Barra</strong></th> -->
                        <th class="text-left" style="font-weight: bold ; color:black;"><strong>Descrição</strong></th>
                        <th class="text-left" style="font-weight: bold ; color:black;"><strong>Qtd</strong></th>
                        <th class="text-left" style="font-weight: bold ; color:black;"><strong>P.Unitário</strong></th>
                        <th class="text-left" style="font-weight: bold ; color:black;"><strong>IVA</strong></th>
                        <th class="text-left" style="font-weight: bold ; color:black;"><strong>Totais(MT)</strong></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $total_iva2 = 0; ?>
                    <?php $iva2 = 0; $subTotal = 0; $iva_produto = 0;?>
                    <?php  $totalFinal = 0;
                    foreach($saidaItems AS $item): ?>
                        <?php $iva_produto = 0;

                        if($item->activo != 2): ?>
                            <?php
                            //if ($item->taxa != 0) {
//                                $iva2 = ($item->preco_unitario * $item->quantidade) * ($item->taxa / 100);
//                                $total_iva2 += $iva2;
//                            }else{
//                                $total_iva2 += 0;
//                                $iva2 = 0;
//                            }
                        if ($item->taxa != 0) {
                            $valor = ($item->quantidade)*$item->preco_unitario;
                            $iva_produto = $valor-($valor/$empresa->iva);
                            $total_iva2 += $iva_produto;
                        }
                            ?>
                            <tr>
<!--                                <td>--><?//= $ord++ ?><!--</td>-->
                                <!-- <td><?= $item->codigo ?></td> -->
                                <td><?= (strlen($item->produto_descricao) > 25) ? substr($item->produto_descricao, 0, 25). '...' : $item->produto_descricao ?></td>
                                <td class="text-right"><?= (float)$item->quantidade; //number_format($item->quantidade-$item->qntDevolucao,0,',','.') ?></td>
                                <td class="text-right"><?= number_format($item->preco_unitario,2,',','.') ?></td>
                                <td class="text-right"><?= number_format($iva_produto,2,',','.') ?></td>
                               <td class="text-right"><?= number_format(($item->quantidade)*$item->preco_unitario,2,',','.') ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php
                    $totalFinal+=($item->quantidade)*$item->preco_unitario;

                endforeach; ?>

                    <?php

                        //    $totalFinal=($item->quantidade-$item->qntDevolucao)*$item->preco_unitario;
                    ?>
                    <tr>
                        <td></td>
                        <td colspan="3" class="text-right" style="font-weight: bold"><strong>Subtotal</strong></td>
                        <td class="text-right"><?= number_format($totalFinal,2,',','.') ?>MT</td>
                    </tr>
                    <tr>
                        <td></td>
<!--                        <td></td>-->
                        <!-- <td></td> -->
                        <td colspan="3" class="text-right" style="font-weight: bold"><strong>Desconto</strong></td>
                        <td class="text-right"><?= number_format($saida->desconto,2,',','.') ?>MT</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="3" class="text-right" style="font-weight: bold"><strong>Total IVA(Incluso)</strong></td>
                        <td class="text-right"><?= number_format($total_iva2,2,',','.') ?>MT</td>
                    </tr>
                    <?php if ($saida->tipo_saida_id == 3): $valor_pago=$saida->valor_pago ?>
                        <tr>
                            <td></td>
                            <td colspan="3" class="text-right" style="font-weight: bold"><strong>Valor Pago</strong></td>
                            <td class="text-right"><?= number_format($valor_pago,2,',','.') ?>MT</td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td></td>
<!--                        <td></td>-->
                        <?php if ($saida->tipo_saida_id == 3):?>
                            <td colspan="3" class="text-right" style="font-weight: bold"><strong>Dívida</strong></td>
                            <td class="text-right" style="font-weight: bold"><strong><?= number_format(($totalFinal-$saida->desconto-$valor_pago),2,',','.') ?>MT</strong></td>
                        <?php else: ?>
                            <td colspan="3" class="text-right" style="font-weight: bold"><strong>Total</strong></td>
                            <td class="text-right" style="font-weight: bold"><strong><?= number_format(($totalFinal-$saida->desconto-$valor_pago),2,',','.') ?>MT</strong></td>
                        <?php endif; ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="separator-solid  mb-3" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 8px"></div>
    </div>

    <div class="card-footer" style="color: black; padding-top: 0; margin-top: 8px; padding-bottom: 0; margin-bottom: 0">
        <?php if ($saida->tipo_saida_id == 3):?>
        <table class="table" style="width: 100%; color:black; font-size: 10px; padding-top: 8px; margin-top: 8px; padding-bottom: 0; margin-bottom: 0">
            <tbody>
                <tr>
                    <td style="text-align: left; width: 100px;margin-right: 0;padding-right: 0">Cliente: </td>
                    <td style="text-align: left; border-color: black;"><hr></td>
                </tr>
                <tr>
                    <td style="text-align: left; width: 100px;margin-right: 0;padding-right: 0">Contacto: </td>
                    <td style="text-align: left; border-color: black;"><hr></td>
                </tr>
                <tr>
                    <td style="text-align: left; width: 100px;margin-right: 0;padding-right: 0">N.º Membro: </td>
                    <td style="text-align: left; border-color: black;"><?=$saida->numero_membro?></td>
                </tr>
                <tr>
                    <td style="text-align: left; width: 100px;margin-right: 0;padding-right: 0">C. Autorização: </td>
                    <td style="text-align: left; border-color: black;"><?=$saida->codigo_autorizacao?></td>
                </tr>
            </tbody>
        </table>
        <br>
        <?php endif; ?>
            <p class="mt-4 mb-3 fw-bold text-center" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0; text-transform: uppercase; font-weight: bold">
                <strong><?=$empresa->slogan ?? null?></strong>
            </p>
            <p class="text-capitalize mt-4 mb-3 fw-bold text-center" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
                Processado por computador
            </p>
            <p class="text-capitalize mt-4 mb-3 fw-bold text-center" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
                Operador: <?= $saida->userName;?>
            </p>
            <p class="mt-4 mb-3 fw-bold text-center" style="padding-top: 0; margin-top: 0; padding-bottom: 0; margin-bottom: 0">
               Desenvolvido pela: www.acsun-mz.com
            </p>
    </div>
</div>
</body>
</html>

