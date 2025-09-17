<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Não Encontrada</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Karla', sans-serif;
            background-color: #f4f7f6;
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
        .error-code {
            font-size: 8rem;
            font-weight: 900;
            color: #FFD1D1;
            margin: 0;
            line-height: 1;
        }
        .title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        .message {
            font-size: 1.125rem;
            margin-bottom: 2rem;
        }
        .home-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: bold;
            color: #fff;
            background-color: #FF6666;
            border-radius: 0.375rem;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .home-button:hover {
            background-color: #E55A5A;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-code">404</div>
        <div class="title">Página Não Encontrada</div>
        <div class="message">
            <p>Lamentamos, mas a página que procura não foi encontrada. Pode ter sido movida, eliminada ou talvez nunca tenha existido.</p>
        </div>
        <a href="/" class="home-button">Ir para a Página Inicial</a>
    </div>
</body>
</html>
