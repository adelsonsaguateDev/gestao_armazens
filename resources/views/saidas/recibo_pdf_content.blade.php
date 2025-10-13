<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <style>
        body {
            font-size: 12px;
            font-family: Cambria, Georgia, serif;
            color: #000000;
        }

        div,
        table,
        p,
        span {
            color: black;
        }

        hr {
            display: block;
            height: 1px;
            border: 0;
            border-top: 1px solid black;
            margin: 1em 0;
            padding: 0;
        }

        td,
        tr,
        th {
            color: #000000;
        }

        .card-invoice {
            padding-top: 16px;
            margin-top: 0;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .card-header {
            padding-top: 0;
            margin-top: 0;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .invoice-header {
            text-align: center;
        }

        .card-body {
            padding-top: 0;
            margin-top: 0;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .separator-solid {
            padding-top: 0;
            margin-top: 8px;
            padding-bottom: 0;
            margin-bottom: 0;
            border-top: 1px solid black;
        }

        .table {
            width: 100%;
            text-align: center;
            padding-top: 0;
            margin-top: 0;
            padding-bottom: 8px;
            margin-bottom: 8px;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 4px;
            /* Adjusted padding for smaller size */
            text-align: left;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .card-footer {
            color: black;
            padding-top: 0;
            margin-top: 8px;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .mb-3 {
            margin-bottom: 12px;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .text-uppercase {
            text-transform: uppercase;
        }
    </style>
</head>

<body style="font-size: 12px; font-family: Cambria,Georgia,serif; background: white;color: black;">

    <div class="card card-invoice">
        <div class="card-header">
            <div class="invoice-header">
                <img src="{{ public_path('dist-assets/images/logo.png') }}" alt="Logo" style="width: 140px;"><br><br>
                @if (isset($empresa))
                    <p style="margin: 0; font-weight: bold;">{{ $empresa->nome ?? 'Nome da Empresa' }}</p>
                    <p style="margin: 0;">{{ $empresa->endereco ?? 'Endereço da Empresa' }}</p>
                    <p style="margin: 0;">Nuit: {{ $empresa->nuit ?? 'N/A' }}</p>
                    <p style="margin: 0;">Contacto: {{ $empresa->contacto ?? 'N/A' }}</p>
                    <p style="margin: 0;">Email: {{ $empresa->email ?? 'N/A' }}</p>
                @else
                    <p>Detalhes da Empresa não disponíveis.</p>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="separator-solid"></div>
            <table class="table">
                <tbody>
                    <tr>
                        <td style="text-align:left">
                            <div class="info-invoice text-left">
                                <h6 class="sub font-weight-bold">Cliente </h6>
                                <p class="text-left">
                                    Nome:{{ $saidaData['cliente_nome'] }}
                                    <br />Nuit: {{ $saidaData['cliente_nuit'] ?? '' }}
                                    <br />Contacto: {{ $saidaData['cliente_contacto'] }}
                                </p>
                            </div>

                            <div class="info-invoice text-left">
                                <h5 class="sub font-weight-bold">Número </h5>
                                {{ $saidaData['tipo_saida_id'] == 3 ? 'Factura' : 'Factura-recibo' }}:{{ $saidaData['numero_factura'] }}<br>
                                Data: {{ \Carbon\Carbon::parse($saidaData['created_at'])->format('d/m/Y H:i') }}
                                <br /><br />
                                Pago:
                                {{ number_format($saidaData['tipo_saida_id'] == 3 ? $saidaData['valor_pago'] : $saidaData['valor_entregue'], 2, ',', '.') }}
                                MT <br />
                                Trocos: {{ number_format($saidaData['trocos'], 2, ',', '.') }} MT
                                @if (isset($tipoPagamentoUsado))
                                    <br />T.P=>{{ $tipoPagamentoUsado->designacao }}:
                                    {{ number_format($saidaData['valor_entregue'], 2, ',', '.') }}
                                @endif
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="invoice-detail">
                <div class="invoice-top">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-left font-weight-bold">Descrição</th>
                                <th class="text-left font-weight-bold">Qtd</th>
                                <th class="text-left font-weight-bold">P.Unitário</th>
                                <th class="text-left font-weight-bold">IVA</th>
                                <th class="text-left font-weight-bold">Totais(MT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $total_iva2 = 0; @endphp
                            @foreach ($saidaItemsData as $item)
                                @php
                                    $iva_produto = 0;
                                    if ($item['taxa'] != 0) {
                                        $valor = $item['quantidade'] * $item['preco_unitario'];
                                        $iva_produto = $valor - $valor / ($empresa->iva ?? 1.23); // Assuming iva is 23%
                                        $total_iva2 += $iva_produto;
                                    }
                                @endphp
                                <tr>
                                    <td>{{ strlen($item['produto_descricao']) > 25 ? substr($item['produto_descricao'], 0, 25) . '...' : $item['produto_descricao'] }}
                                    </td>
                                    <td class="text-right">{{ (float) $item['quantidade'] }}</td>
                                    <td class="text-right">{{ number_format($item['preco_unitario'], 2, ',', '.') }}
                                    </td>
                                    <td class="text-right">{{ number_format($iva_produto, 2, ',', '.') }}</td>
                                    <td class="text-right">
                                        {{ number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach

                            <tr>
                                <td></td>
                                <td colspan="3" class="text-right font-weight-bold"><strong>Subtotal</strong></td>
                                <td class="text-right">
                                    {{ number_format($saidaData['valor_total'] - $saidaData['valor_total_iva'], 2, ',', '.') }}MT
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="3" class="text-right font-weight-bold"><strong>Desconto</strong></td>
                                <td class="text-right">{{ number_format($saidaData['desconto'], 2, ',', '.') }}MT</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan="3" class="text-right font-weight-bold"><strong>Total
                                        IVA(Incluso)</strong></td>
                                <td class="text-right">
                                    {{ number_format($saidaData['valor_total_iva'], 2, ',', '.') }}MT</td>
                            </tr>
                            @if ($saidaData['tipo_saida_id'] == 3)
                                <tr>
                                    <td></td>
                                    <td colspan="3" class="text-right font-weight-bold"><strong>Valor Pago</strong>
                                    </td>
                                    <td class="text-right">{{ number_format($saidaData['valor_pago'], 2, ',', '.') }}MT
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td></td>
                                @if ($saidaData['tipo_saida_id'] == 3)
                                    <td colspan="3" class="text-right font-weight-bold"><strong>Dívida</strong></td>
                                    <td class="text-right font-weight-bold">
                                        <strong>{{ number_format($saidaData['valor_total'] - $saidaData['desconto'] - $saidaData['valor_pago'], 2, ',', '.') }}MT</strong>
                                    </td>
                                @else
                                    <td colspan="3" class="text-right font-weight-bold"><strong>Total</strong></td>
                                    <td class="text-right font-weight-bold">
                                        <strong>{{ number_format($saidaData['valor_total'], 2, ',', '.') }}MT</strong>
                                    </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="separator-solid"></div>
        </div>

        <div class="card-footer">
            <p class="mt-4 mb-3 fw-bold text-center text-uppercase">
                <strong>{{ $empresa->slogan ?? null }}</strong>
            </p>
            <p class="text-capitalize mt-4 mb-3 fw-bold text-center">
                Processado por computador
            </p>
            <p class="text-capitalize mt-4 mb-3 fw-bold text-center">
                Operador: {{ $saidaData['userName'] }}
            </p>
            <p class="mt-4 mb-3 fw-bold text-center">
                Desenvolvido pela: Eng Isabel Guivalar
            </p>
        </div>
    </div>
</body>

</html>
