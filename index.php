<!DOCTYPE html>
<html lang="en">
<head>
  <title>Sito Collegio Docenti</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  
  <style>
        h1 {
            font-weight: bold;
            letter-spacing: 1px;
            line-height: 1.2;
            font-family: 'Arial', sans-serif;
        }

        h2 {
            color: white;
            font-weight: bold;
            letter-spacing: 1px;
            line-height: 1.2;
            font-family: 'Arial', sans-serif;
        }
        body {
            background-color: #e6fdff;
            color: rgba (0, 0, 0, 1);
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;  
            align-items: flex-start;  
            height: 100vh;            
            padding: 0;
            flex-direction: column;   
        }


        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }


        .center-container {
            display: flex;
            justify-content: space-evenly;
            margin-top: 80px;  
            margin-left: 2.5%;
            margin-right: 2.5%;
            width: 95%;
            padding: 20px;
        }


        .center-box {
            width: 100%;
            background: #0062ff;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
        }

        .clickable-link {
            text-decoration: none; /* Rimuove la sottolineatura */
        }


    </style>
</head>
<body>
  
    <div class="container text-center">
    <h1>Benvenuto alla home page del Collegio Docenti</h1>
    <br>
    
    <a href="#" class="clickable-link">
        <div class="center-container">
            <div class="center-box">
                <h2>Home</h2>
            </div>
        </div>
    </a>
    <a href="login.php" class="clickable-link">
        <div class="center-container">
            <div class="center-box">
                <h2>Login</h2>
            </div>
        </div>
    </a>
    <a href="register.php" class="clickable-link">
        <div class="center-container">
            <div class="center-box">
                <h2>Registrati</h2>
            </div>
        </div>
    </a>

    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</html>
