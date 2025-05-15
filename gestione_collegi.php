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

if (isset($_POST['btnCreaProposta'])) {
    $data_collegio = mysqli_real_escape_string($db_conn, $_POST['data_collegio']);
    $ora_inizio = mysqli_real_escape_string($db_conn, $_POST['ora_inizio']);
    $ora_fine = mysqli_real_escape_string($db_conn, $_POST['ora_fine']);
    $descrizione = mysqli_real_escape_string($db_conn, $_POST['descrizione']);
    
    // Genera un OTP di 3 cifre
    $otp = rand(100, 999);

    $query_collegio = "INSERT INTO tcollegiodocenti (data_collegio, ora_inizio, ora_fine, descrizione, otp) 
                       VALUES ('$data_collegio', '$ora_inizio', '$ora_fine', '$descrizione', '$otp')";

    if (mysqli_query($db_conn, $query_collegio)) {
        echo "<h2>Collegio creato con successo!</h2>";
    } else {
        echo "<h2>Errore nella creazione del collegio: " . mysqli_error($db_conn) . "</h2>";
    }
    // Redirect per evitare duplicati
    header("Location: gestione_collegi.php");
    exit();
}

if (isset($_POST['btnElimina'])) {
    $id_collegio = mysqli_real_escape_string($db_conn, $_POST['id_collegio']);

    $query_delete = "DELETE FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'";
    $delete_result = mysqli_query($db_conn, $query_delete);
    
    if ($delete_result) {
        $_SESSION['messaggio'] = "Proposta eliminata con successo!";
    } else {
        $_SESSION['messaggio'] = "Errore: " . mysqli_error($db_conn);
    }
    
    header("Location: gestione_collegi.php");
    exit();
}

if (isset($_POST['btnConferma'])) {    
    $id_collegio = mysqli_real_escape_string($db_conn, $_POST['id_collegio']);

    $query_collegio = "SELECT * FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'";
    $result_collegio = mysqli_query($db_conn, $query_collegio);

    if ($result_collegio && mysqli_num_rows($result_collegio) > 0) {
        $collegio = mysqli_fetch_assoc($result_collegio);

        // Memorizza i dati del collegio nella sessione
        $_SESSION['collegio'] = [
            'id_collegio'  => $collegio['id_collegio'],
            'descrizione'  => $collegio['descrizione'],
            'data_collegio'=> $collegio['data_collegio'],
            'ora_inizio'   => $collegio['ora_inizio'],
            'ora_fine'     => $collegio['ora_fine']
        ];

        header("Location: crea_votazione.php");
        exit();
    } else {
        echo "<h2>Errore: Collegio non trovato.</h2>";
    }
}

// Recupera i collegi esistenti per il menu a tendina
$query_collegi = "SELECT * FROM tcollegiodocenti ORDER BY data_collegio DESC";
$result_collegi = mysqli_query($db_conn, $query_collegi);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Gestione collegi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Puoi includere qui i CSS necessari -->
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
    </style>
</head>
<body>
    <!-- Navbar (opzionale) -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
         <div class="navbar-header">
            <a class="navbar-brand" href="#">Crea un nuovo collegio</a>
         </div>
         <ul class="nav navbar-nav navbar-right">
            <li>
                <a href="admin.php" class="nav-link text-danger">Home Page</a>
            </li>
         </ul>
      </div>
    </nav>

    <div class="container">
        <br>
        <form method="post" action="">
            <div class="form-group">
                <label for="data_collegio">Data Collegio:</label>
                <input type="date" class="form-control" id="data_collegio" name="data_collegio" required>
            </div>
            <div class="form-group">
                <label for="ora_inizio">Ora Inizio:</label>
                <input type="time" class="form-control" id="ora_inizio" name="ora_inizio" required>
            </div>
            <div class="form-group">
                <label for="ora_fine">Ora Fine:</label>
                <input type="time" class="form-control" id="ora_fine" name="ora_fine" required>
            </div>
            <div class="form-group">
                <label for="descrizione">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione" name="descrizione" required>
            </div>
            <button type="submit" class="btn btn-primary" name="btnCreaProposta">Crea collegio</button>
        </form>
        <br><br><br>

        <h2>Seleziona un collegio</h2>
        <h3>Collegi:</h3>
        <table class="table table-bordered">
            <tr>
                <th>Descrizione</th>
                <th>Data</th>
                <th>Ora inizio</th>
                <th>Ora fine</th>
                <th>Seleziona</th>
                <th>Modifica</th>
                <th>Elimina</th>
                <th>Visualizza OTP</th>
                <th>Visualizza</th>
            </tr>
            <?php
            if ($result_collegi && mysqli_num_rows($result_collegi) > 0) {
                while ($row = mysqli_fetch_assoc($result_collegi)) {
            ?>
                    <tr>
                        <td><?= htmlspecialchars($row['descrizione'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['data_collegio'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['ora_inizio'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($row['ora_fine'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                <button type="submit" name="btnConferma" class="btn btn-link">
                                    <img src="images/conferma.png" alt="Conferma">
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="modifica_collegio.php">
                                <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                <button type="submit" name="btnModifica" class="btn btn-link">
                                    <img src="images/penna_modifica.png" alt="Modifica">
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="">
                                <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                <button type="submit" name="btnElimina" class="btn btn-link">
                                    <img src="images/eliminazione.png" alt="Elimina" width="20px" height="20px">
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="otp_collegio.php">
                                <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                <button type="submit" class="btn btn-link">
                                    <img src="images/otp.png" alt="Visualizza OTP" width="20px" height="20px">
                                </button>
                            </form>
                        </td>
                        <td>
                            <form method="post" action="visualizza_partecipanti.php">
                                <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                <button type="submit" class="btn btn-link">
                                    <img src="images/visualizza.png" alt="Visualizza" width="20px" height="20px">
                                </button>
                            </form>
                        </td>
                    </tr>
            <?php
                }
            } else {
            ?>
                <tr>
                    <td colspan="9">Nessuna proposta trovata</td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>

    <!-- Includi qui i tuoi script JS, se necessari -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>

<?php
mysqli_close($db_conn);
?>
