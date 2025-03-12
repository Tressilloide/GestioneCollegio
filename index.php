<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Collegio Docenti</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            width: 100vw;
        }

        /* Barra superiore stile Mastercom */
        .title-bar {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        /* Container per i box */
        .container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
        }

        /* Stile dei pulsanti */
        .box {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            background-color: white;
            color: black;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            border-bottom: 1px solid #ddd;
            transition: background-color 0.3s;
        }

        .box:last-child {
            border-bottom: none;
        }

        .box:hover {
            background-color: #f0f0f0;
        }

        /* Aggiunta icone */
        .box span {
            margin-right: 10px;
            font-size: 28px;
        }

        /* Layout orizzontale su schermi più grandi */
        @media (min-width: 768px) {
            .container {
                flex-direction: row;
            }

            .box {
                border-bottom: none;
                border-right: 1px solid #ddd;
            }

            .box:last-child {
                border-right: none;
            }
        }
    </style>
</head>
<body>
    <div class="title-bar">
        Home Collegio Docenti
    </div>

    <div class="container">
        <a href="login.php" class="box"><span>🔑</span>Login</a>
        <a href="register.php" class="box"><span>📝</span>Registrazione</a>
        <a href="https://www.buonarroti.tn.it" class="box"><span>🌐</span>Buonarroti</a>
    </div>
</body>
</html>