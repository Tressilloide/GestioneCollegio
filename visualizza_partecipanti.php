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
</head>
<body>
    <div class="container">
        <h1>Partecipanti al Collegio</h1>
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
</body>
</html>