<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Saída - {{ $saida->numero_factura ?? $saida->id }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 20px;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .details, .items {
            margin-bottom: 20px;
        }
        .details p {
            margin: 5px 0;
        }
        .items table {
            width: 100%;
            border-collapse: collapse;
        }
        .items th, .items td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }
        .items th {
            background-color: #f2f2f2;
        }
        .totals {
            text-align: right;
            margin-top: 20px;
        }
        .totals p {
            margin: 5px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Recibo de Saída</h1>
            <p>Data: {{ \Carbon\Carbon::parse($saida->data)->format('d/m/Y H:i') }}</p>
            <p>Nº da Factura: {{ $saida->numero_factura ?? 'N/A' }}</p>
        </div>

        <div class="details">
            <p><strong>Cliente:</strong> {{ $saida->cliente->nome ?? 'N/A' }}</p>
            <p><strong>Tipo de Saída:</strong> {{ $saida->tipoSaida->nome ?? 'N/A' }}</p>
            <p><strong>Registado por:</strong> {{ $saida->user->name ?? 'N/A' }}</p>
        </div>

        <div class="items">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unit.</th>
                        <th>IVA</th>
                        <th>Desconto</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($saida->itens as $item)
                    <tr>
                        <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                        <td>{{ $item->quantidade }}</td>
                        <td>{{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->valor_iva, 2, ',', '.') }}</td>
                        <td>{{ number_format($item->desconto_valor, 2, ',', '.') }}</td>
                        <td>{{ number_format(($item->quantidade * $item->preco_unitario) - $item->desconto_valor + $item->valor_iva, 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals">
            <p>Subtotal: {{ number_format($saida->valor_total - $saida->valor_total_iva, 2, ',', '.') }}</p>
            <p>Total IVA: {{ number_format($saida->valor_total_iva, 2, ',', '.') }}</p>
            <p>Desconto Total: {{ number_format($saida->desconto, 2, ',', '.') }}</p>
            <p><strong>Total Geral: {{ number_format($saida->valor_total, 2, ',', '.') }}</strong></p>
            <p>Valor Pago: {{ number_format($saida->valor_pago, 2, ',', '.') }}</p>
            <p>Trocos: {{ number_format($saida->trocos, 2, ',', '.') }}</p>
        </div>

        <div class="footer">
            <p>Obrigado pela sua preferência!</p>
        </div>
    </div>
</body>
</html>