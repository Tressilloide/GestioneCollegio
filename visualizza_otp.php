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

    // Recupera l'OTP e l'ID della votazione dalla URL
    $otp = isset($_GET['otp']) ? $_GET['otp'] : '';
    $id_votazione = isset($_GET['id_votazione']) ? $_GET['id_votazione'] : '';

    // Recupera i dettagli della proposta votata
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
    <h1>OTP Generato</h1>

    <div class="container">
        <h2>Il tuo OTP è: <?php echo htmlspecialchars($otp); ?></h2>
        <h3>Proposta Votata:</h3>
        <p><strong>Titolo:</strong> <?php echo htmlspecialchars($proposta_titolo); ?></p>
        <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($proposta_descrizione); ?></p>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>