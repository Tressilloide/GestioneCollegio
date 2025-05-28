<?php
session_start();

if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
    echo "<h1>Non sei loggato, corri a loggarti.</h1>";
    header("refresh:2; index.php");
    exit();
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo "<h1>Non sei autorizzato ad accedere a questa pagina.</h1>";
    header("refresh:2; index.php");
    exit();
}

include 'connessione.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_collegio'])) {
    $id_collegio = mysqli_real_escape_string($db_conn, $_POST['id_collegio']);
    $query = "SELECT descrizione, data_collegio, ora_inizio, ora_fine, otp FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'";
    $result = mysqli_query($db_conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $collegio = mysqli_fetch_assoc($result);
        $_SESSION['collegio'] = $collegio;
    } else {
        echo "<h2>Errore: Collegio non trovato.</h2>";
        header("refresh:2; gestione_collegi.php");
        exit();
    }
} else {
    echo "<h2>Errore: Dati non validi.</h2>";
    header("refresh:2; gestione_collegi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <title>Visualizza OTP Collegio</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
        body {
            background: #007bff;
            background-image: url('images/admin.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-size: 20%;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
        }
        /* Impostazioni per le colonne */
        th:nth-child(1), td:nth-child(1) {
            width: 55%;
            min-width: 150px;
            max-width: 300px;
        }
        th:nth-child(2), td:nth-child(2),
        th:nth-child(3), td:nth-child(3),
        th:nth-child(4), td:nth-child(4) {
            width: 8%;
            min-width: 50px;
            text-align: center;
        }
        th:nth-child(5), td:nth-child(5),
        th:nth-child(6), td:nth-child(6),
        th:nth-child(7), td:nth-child(7),
        th:nth-child(8), td:nth-child(8),
        th:nth-child(9), td:nth-child(9) {
            width: 2%;
            min-width: 50px;
            text-align: center;
        }
        .center-container {
            text-align: center; /* Per centrare il contenuto inline-block (center-box) */
            margin-top: 80px;
        }

        .center-box {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
            text-align: center; /* Centra il contenuto testuale */
            display: inline-block; /* Rende la larghezza dinamica in base al contenuto */
            max-width: 90%; /* Previene che il contenitore superi troppo lo schermo */
        }

        @media (max-width: 768px) {
            .center-container {
                flex-direction: column;
                align-items: center;
                width: 90%;
                margin-left: auto;
                margin-right: auto;
            }

            .center-box {
                width: 100%;
            }
        }

    </style>
</head>
<body>
    <div class="center-container">
        <div class="center-box">
            <h1>Dettagli Collegio</h1>
            <h2>OTP COLLEGIO: <?php echo htmlspecialchars($_SESSION['collegio']['otp']); ?></h2>
            <h3>Informazioni Collegio:</h3>
            <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['descrizione']); ?></p>
            <p><strong>Data Collegio:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['data_collegio']); ?></p>
            <p><strong>Ora Inizio:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['ora_inizio']); ?></p>
            <p><strong>Ora Fine:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['ora_fine']); ?></p>
            <a href="gestione_collegi.php" class="btn btn-primary">Torna alla gestione collegi</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>