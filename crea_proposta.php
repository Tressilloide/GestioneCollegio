<?php
    session_start();

    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        ?> <h1>Non sei loggato, corri a loggarti.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        ?> <h1>Non sei autorizzato ad accedere a questa pagina.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    include 'connessione.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $titolo = mysqli_real_escape_string($db_conn, $_POST['titolo']);
        $descrizione_proposta = mysqli_real_escape_string($db_conn, $_POST['descrizione_proposta']);

        // ontrolla se la proposta esiste già
        $query_check = "SELECT * FROM tproposta WHERE titolo = '$titolo' AND descrizione = '$descrizione_proposta'";
        $result_check = mysqli_query($db_conn, $query_check);

        if (mysqli_num_rows($result_check) > 0) {
           ?> <h2>Proposta già esistente!</h2> <?php;
        } else {
            $query_proposta = "INSERT INTO tproposta (titolo, descrizione) VALUES ('$titolo', '$descrizione_proposta')";

            if (mysqli_query($db_conn, $query_proposta)) {
                ?><h2>Proposta creata con successo!</h2><?php 
            } else {
                ?><h2>Errore nella creazione della proposta</h2><?php 
            }
        }
        //evita duplicato inserimento
        header("Location: crea_proposta.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Crea Proposta</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
        body {
            background-image: url('images/admin.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-size: 20%;
            height: 100vh; 
        }
    </style>
</head>
<body>
    <h1>Crea una nuova proposta</h1>

    <div class="container">
        <form method="post" action="">
            <div class="form-group">
                <label for="titolo">Titolo:</label>
                <input type="text" class="form-control" id="titolo" name="titolo" required>
            </div>
            <div class="form-group">
                <label for="descrizione_proposta">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione_proposta" name="descrizione_proposta" required>
            </div>
            <button type="submit" class="btn btn-primary" name="crea_proposta">Crea Proposta</button>
        </form>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>