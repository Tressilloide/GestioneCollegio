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

        .title-bar {
            background-color: #007bff;
            color: white;
            width: 100%;
            padding: 20px;
            text-align: center;
            font-size: 44px;
            font-weight: bold;
            position: relative;
            height: 100px; /* Imposta l'altezza della barra del titolo */
        }

        .title-bar .image-container {
            font-size: 9px;
            position: absolute;
            bottom: 0; /* Allinea l'immagine al fondo della barra del titolo */
            right: 0px; /* Regola la posizione a destra */
            height: 100%; /* Imposta l'altezza del contenitore al 100% */
        }

        .title-bar .image-container img {
            width: auto; /* Mantieni il rapporto d'aspetto */
            height: 100%; /* Imposta l'altezza dell'immagine al 100% */
        }

        .container {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100%;
        }

        .box {
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            padding: 20px;
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
            background-color:rgb(240, 251, 255);
        }

        .box span {
            margin-right: 10px;
            font-size: 90px;
        }
    

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
        

        @media (max-width: 767px) {
            .title-bar {
                font-size: 24px;
                height: auto;
                display: flex;
                align-items: center;
                justify-content: space-between; /* Distribuisce titolo e immagine */
                padding: 10px 20px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .title-bar .image-container {
                height: 50px;
                display: flex;
                align-items: center;
            }

            .title-bar .image-container img {
                height: 40px; /* Ridimensiona l'immagine */
            }
        }
        
    </style>
</head>
<body>
    <div class="title-bar">
        Home Collegio Docenti
        <div class="image-container">
            <a href="https://www.buonarroti.tn.it/" target="_blank">
                <img src="images\Buonarroti_Icona_Updated.jpg" alt="Immagine Buonarroti">
            </a>
        </div>
    </div>

    <div class="container">
        <a href="login.php" class="box"><span>🔑</span>Login</a>
        <a href="register.php" class="box"><span>📝</span>Registrazione</a>
    </div>
</body>
</html>
