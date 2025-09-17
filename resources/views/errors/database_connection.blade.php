<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erro de Conexão</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f7fafc;
            color: #718096;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 2rem;
        }
        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #4a5568;
            margin-bottom: 1rem;
        }
        .message {
            font-size: 1.125rem;
            margin-bottom: 1rem;
        }
        .footer {
            font-size: 0.875rem;
            color: #a0aec0;
            margin-top: 2rem;
        }
        .back-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background-color: #4a5568;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.2s;
            margin-top: 1.5rem;
        }
        .back-button:hover {
            background-color: #2d3748;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Erro de Conexão com o Servidor</div>
        <div class="message">
            <p>Não foi possível estabelecer a conexão com a base de dados.</p>
            <p>Por favor, tente novamente mais tarde. Se o problema persistir, contacte o suporte técnico.</p>
        </div>
        <a href="javascript:history.back()" class="back-button">Voltar</a>
        <div class="footer">
            O sistema não está a conseguir comunicar com os seus serviços essenciais.
        </div>
    </div>
</body>
</html>
