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

    //OTP e l'ID della votazione dalla URL
    $otp = isset($_GET['otp']) ? $_GET['otp'] : '';
    $id_votazione = isset($_GET['id_votazione']) ? $_GET['id_votazione'] : '';

    //proposta
    $proposta_titolo = '';
    $proposta_descrizione = '';
    if ($id_votazione) {
        $votazione_result = mysqli_query($db_conn, "SELECT tproposta.titolo, tproposta.descrizione 
                                                    FROM tvotazione 
                                                    JOIN tproposta ON tvotazione.id_proposta = tproposta.id_proposta 
                                                    WHERE tvotazione.id_votazione = '$id_votazione'");
        if ($votazione_result && mysqli_num_rows($votazione_result) > 0) {
            $votazione_row = mysqli_fetch_assoc($votazione_result);
            $proposta_titolo = $votazione_row['titolo'];
            $proposta_descrizione = $votazione_row['descrizione'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Visualizza OTP</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #000;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 255, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        h1, h2, h3, p {
            margin-bottom: 20px;
        }
        h1 {
            color: #007bff;
        }
        .btn-primary {
            background-color: #344ceb;
            color: white;
            width: 100%;
            margin-top: 10px;
        }
        .btn-primary:hover {
            background-color: #2a3b9d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>OTP Generato</h1>
        <h2>Il tuo OTP è: <?php echo htmlspecialchars($otp); ?></h2>
        <h3>Proposta Votata:</h3>
        <p><strong>Titolo:</strong> <?php echo htmlspecialchars($proposta_titolo); ?></p>
        <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($proposta_descrizione); ?></p>
        <a href="crea_votazione.php" class="btn btn-primary">Indietro</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>