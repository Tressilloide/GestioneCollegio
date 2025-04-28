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

// Recupera i dati del collegio dalla sessione
if (!isset($_SESSION['collegio'])) {
    echo "<h2>Nessun collegio selezionato.</h2>";
    header("refresh:2; gestione_collegi.php");
    exit();
}

$collegio = $_SESSION['collegio'];
$id_collegio = $collegio['id_collegio'];

// Recupera i dettagli aggiornati del collegio dal database
$query = "SELECT id_collegio, data_collegio, ora_inizio, ora_fine, descrizione FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'";
$result = mysqli_query($db_conn, $query);
$proposta = mysqli_fetch_assoc($result);

// Gestione del caricamento del CSV e della creazione della votazione
$docenti_non_trovati = [];
$directory = __DIR__ . '/csvDocenti';
$filename = $directory . '/elenco_docenti.csv';
$csv_table_data = [];

if (file_exists($filename)) {
    $file = fopen($filename, 'r');
    $csv_header = fgetcsv($file);
    while (($line = fgetcsv($file)) !== FALSE) {
        $csv_table_data[] = $line;
    }
    fclose($file);
}

if (isset($_POST['crea_votazione'])) {
    $descrizione_votazione = mysqli_real_escape_string($db_conn, $_POST['descrizione_votazione']);
    $ora_inizio_votazione = mysqli_real_escape_string($db_conn, $_POST['ora_inizio_votazione']);
    $ora_fine_votazione = mysqli_real_escape_string($db_conn, $_POST['ora_fine_votazione']);
    $id_proposta = mysqli_real_escape_string($db_conn, $_POST['id_proposta']);
    
    // OTP a 3 cifre
    $otp = rand(100, 999);
    
    $query_votazione = "INSERT INTO tvotazione (descrizione, ora_inizio, ora_fine, id_proposta, id_collegio, otp) 
                          VALUES ('$descrizione_votazione', '$ora_inizio_votazione', '$ora_fine_votazione', '$id_proposta', '$id_collegio', '$otp')";

    if (empty($id_collegio)) {
        header("refresh:2; gestione_collegi.php");
        exit();
    }

    if (mysqli_query($db_conn, $query_votazione)) {
        $id_votazione = mysqli_insert_id($db_conn);

        // Salvataggio OTP e ID della votazione
        $_SESSION['otp'] = $otp;
        $_SESSION['id_votazione'] = $id_votazione;
        
        // Utilizzo del file CSV predefinito
        if (file_exists($filename)) {
            $file = fopen($filename, 'r');
            fgetcsv($file); // salta l'intestazione
            while (($line = fgetcsv($file)) !== FALSE) {
                $email_docente = mysqli_real_escape_string($db_conn, $line[1]);
                $docente_result = mysqli_query($db_conn, "SELECT id_docente FROM tdocente WHERE email = '$email_docente'");
                if ($docente_result && mysqli_num_rows($docente_result) > 0) {
                    $docente_row = mysqli_fetch_assoc($docente_result);
                    $id_docente = $docente_row['id_docente'];
                    $query_ammesso = "INSERT INTO ammesso (id_docente, id_votazione) VALUES ('$id_docente', '$id_votazione')";
                    mysqli_query($db_conn, $query_ammesso);
                } else {
                    $docenti_non_trovati[] = $email_docente;
                }
            }
            fclose($file);
        }
        // Dopo la creazione, l'OTP è disponibile nel database per la votazione
        header("Location: visualizza_otp.php");
        exit();
    } else {
        echo "<h2>Errore nella creazione della votazione: " . mysqli_error($db_conn) . "</h2>";
    }
}

$proposte_result = mysqli_query($db_conn, "SELECT id_proposta, titolo FROM tproposta");
?>

<!DOCTYPE html>
<html lang="it">
<head>
  <title>Crea Votazione</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Inclusione dei CSS necessari -->
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
    <h1>Crea una nuova votazione</h1>

    <div class="container">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="data_collegio">Data collegio:</label>
                <input type="text" class="form-control" id="data_collegio" name="data_collegio" value="<?= htmlspecialchars($proposta['data_collegio'] ?? $collegio['data_collegio']) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="ora_inizio_votazione">Ora Inizio:</label>
                <input type="time" class="form-control" id="ora_inizio_votazione" name="ora_inizio_votazione" required>
            </div>
            <div class="form-group">
                <label for="ora_fine_votazione">Ora Fine:</label>
                <input type="time" class="form-control" id="ora_fine_votazione" name="ora_fine_votazione" required>
            </div>
            <div class="form-group">
                <label for="descrizione_votazione">Descrizione votazione:</label>
                <input type="text" class="form-control" id="descrizione_votazione" name="descrizione_votazione" required>
            </div>
            <div class="form-group">
                <label for="id_proposta">Proposta:</label>
                <select class="form-control" id="id_proposta" name="id_proposta" required>
                    <?php
                        if ($proposte_result && mysqli_num_rows($proposte_result) > 0) {
                            while ($row = mysqli_fetch_assoc($proposte_result)) {
                                echo "<option value='" . $row['id_proposta'] . "'>" . htmlspecialchars($row['titolo']) . "</option>";
                            }
                        } else {
                            echo "<option value=''>Nessuna proposta disponibile</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="collegio_titolo">Collegio:</label>
                <input type="text" class="form-control" id="collegio_titolo" name="collegio_titolo" value="<?= htmlspecialchars($proposta['descrizione'] ?? $collegio['descrizione']) ?>" disabled>
                <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($collegio['id_collegio']) ?>">
            </div>
            <div class="form-group">
                <label for="file_csv">Carica il file CSV dei docenti:</label>
                <p class="alert alert-info">Il file predefinito "elenco_docenti.csv" sarà utilizzato automaticamente.</p>
            </div>
            <button type="submit" class="btn btn-primary" name="crea_votazione">Crea Votazione</button>
        </form>

        <!-- Bottone per modificare il CSV -->
        <a href="gestisci_docenti.php" class="btn btn-secondary" style="margin-top: 20px;">Modifica CSV</a>

        <?php
            if (!empty($docenti_non_trovati)) {
                echo "<h3>I seguenti docenti non sono stati trovati nel database:</h3>";
                echo "<ul>";
                foreach ($docenti_non_trovati as $email) {
                    echo "<li>" . htmlspecialchars($email) . "</li>";
                }
                echo "</ul>";
            }
        ?>
    </div>

    <div class="container" style="margin-top: 20px;">
        <h2>Contenuto del CSV Selezionato</h2>
        <?php if (!empty($csv_table_data)) { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <?php foreach ($csv_header as $header) { ?>
                            <th><?= htmlspecialchars($header) ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($csv_table_data as $row) { ?>
                        <tr>
                            <?php foreach ($row as $cell) { ?>
                                <td><?= htmlspecialchars($cell) ?></td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p class="alert alert-warning">Il file CSV è vuoto o non esiste.</p>
        <?php } ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
