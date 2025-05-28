<?php
session_start();
include 'connessione.php';

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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_collegio'])) {
    $id_collegio = mysqli_real_escape_string($db_conn, $_POST['id_collegio']);

    // Recupera i partecipanti al collegio
    $query_partecipanti = "SELECT d.nome, d.cognome, p.ora_entrata, p.ora_uscita 
                           FROM partecipa p
                           JOIN tdocente d ON p.id_docente = d.id_docente
                           WHERE p.id_collegio = '$id_collegio'";
    $result_partecipanti = mysqli_query($db_conn, $query_partecipanti);

    // Recupera le statistiche del collegio
    $query_statistiche = "SELECT COUNT(*) AS totale_partecipanti, 
                                 COUNT(p.ora_uscita IS NOT NULL OR NULL) AS usciti
                          FROM partecipa p
                          WHERE p.id_collegio = '$id_collegio'";
    $result_statistiche = mysqli_query($db_conn, $query_statistiche);
    $statistiche = mysqli_fetch_assoc($result_statistiche);
} else {
    echo "<h1>Errore: Collegio non selezionato.</h1>";
    header("refresh:2; gestione_collegi.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Partecipanti Collegio</title>
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
            display: block;
            width: 80%;
            margin: 80px auto 0 auto;
        }

        .center-box {
            background: white;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
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
    <!-- Navbar -->
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="#">Partecipanti Collegio</a>
      </div>
      <ul class="nav navbar-nav navbar-right">
        <li>
          <a href="gestione_collegi.php" class="nav-link text-danger">Home Collegio</a>
        </li>
      </ul>
    </div>
  </nav>
    <div class="center-container">
        <div class="center-box">
            <h3>Statistiche</h3>
            <ul>
                <li>Totale partecipanti: <?= htmlspecialchars($statistiche['totale_partecipanti'] ?? 0) ?></li>
                <li>Partecipanti usciti: <?= htmlspecialchars($statistiche['usciti'] ?? 0) ?></li>
            </ul>
            <h3>Elenco Partecipanti</h3>
            <table class="table table-bordered">
                <tr>
                    <th>Nome</th>
                    <th>Cognome</th>
                    <th>Ora Entrata</th>
                    <th>Ora Uscita</th>
                </tr>
                <?php
                if ($result_partecipanti && mysqli_num_rows($result_partecipanti) > 0) {
                    while ($row = mysqli_fetch_assoc($result_partecipanti)) {
                ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['cognome'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['ora_entrata'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['ora_uscita'] ?? 'Non registrata', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="4">Nessun partecipante trovato</td>
                    </tr>
                <?php
                }
                ?>
            </table>
            <a href="gestione_collegi.php" class="btn btn-primary">Torna indietro</a>
        </div>
    </div>
</body>
</html>