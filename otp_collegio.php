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
        <h1>Dettagli Collegio</h1>
        <h2>OTP COLLEGIO: <?php echo htmlspecialchars($_SESSION['collegio']['otp']); ?></h2>
        <h3>Informazioni Collegio:</h3>
        <p><strong>Descrizione:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['descrizione']); ?></p>
        <p><strong>Data Collegio:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['data_collegio']); ?></p>
        <p><strong>Ora Inizio:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['ora_inizio']); ?></p>
        <p><strong>Ora Fine:</strong> <?php echo htmlspecialchars($_SESSION['collegio']['ora_fine']); ?></p>
        <a href="gestione_collegi.php" class="btn btn-primary">Torna alla gestione collegi</a>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>