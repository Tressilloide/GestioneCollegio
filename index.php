<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Immagini Divise con Testo</title>
    <style>
        .title {
            background-color: #344ceb;
            height: 13vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h1 {
            color: white;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            height: 87vh;
            overflow: hidden;
        }

        .image-box {
            flex: 1;
            background-size: cover;
            background-position: center;
            transition: transform 0.3s; 
            display: block;
            position: relative;
            color: white;
            text-decoration: none;
        }

        .image-box:hover {
            transform: scale(1.05);
        }

        .image-text {
            position: absolute;
            top: 50%; 
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2em;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px 20px; 
            border-radius: 20px;
            text-align: center; 
            box-shadow: 0 2px 10px rgba(0, 0, 255, 0.3); 
            color: #344ceb;
            font-weight: bold;
            text-decoration-line: underline;
        }
    </style>
</head>
<body>
    <div class="title">
        <h1>Home Collegio Docenti</h1>
    </div>
    <div class="container">
        <a href="login.php" class="image-box" style="background-image: url('images/access.jpg');">
            <span class="image-text">Login</span>
        </a>
        <a href="register.php" class="image-box" style="background-image: url('images/register.jpg');">
            <span class="image-text">Registrati</span>
        </a>
        <a href="https://www.buonarroti.tn.it/" class="image-box" style="background-image: url('images/Buonarroti_Icona.jpg');">
            <span class="image-text">Buonarroti</span>
        </a>
    </div>
</body>
</html>
