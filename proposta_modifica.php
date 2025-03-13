<?php
    session_start();
    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        header("Location: index.php");
        exit();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        ?> <h1>Non sei autorizzato ad accedere a questa pagina.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }

    include 'connessione.php';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Credenziali</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
        }
        .form-group + .form-group {
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="form-container">
        


    </div>
</body>
</html>
