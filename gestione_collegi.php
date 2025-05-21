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
    $data_collegio = $_POST['data_collegio'];
    $ora_inizio = $_POST['ora_inizio'];
    $ora_fine = $_POST['ora_fine'];
    $descrizione = mysqli_real_escape_string($db_conn, $_POST['descrizione']);

    $oggi = date('Y-m-d');
    $anno_prossimo = date('Y', strtotime('+1 year')) . "-12-31";

    if ($data_collegio < $oggi || $data_collegio > $anno_prossimo) {
        echo "<h2>Errore: La data deve essere tra oggi e il 31 dicembre dell’anno prossimo.</h2>";
        exit();
    }

    if ($ora_inizio >= $ora_fine) {
        echo "<h2>Errore: L'orario di inizio deve essere precedente all'orario di fine.</h2>";
        exit();
    }

    // Tutto valido, procedi
    $data_collegio = mysqli_real_escape_string($db_conn, $data_collegio);
    $ora_inizio = mysqli_real_escape_string($db_conn, $ora_inizio);
    $ora_fine = mysqli_real_escape_string($db_conn, $ora_fine);

    $otp = rand(100, 999);

    $query_collegio = "INSERT INTO tcollegiodocenti (data_collegio, ora_inizio, ora_fine, descrizione, otp) 
                       VALUES ('$data_collegio', '$ora_inizio', '$ora_fine', '$descrizione', '$otp')";

    if (mysqli_query($db_conn, $query_collegio)) {
        echo "<h2>Collegio creato con successo!</h2>";
    } else {
        echo "<h2>Errore nella creazione del collegio: " . mysqli_error($db_conn) . "</h2>";
    }
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

    <div class="center-container">
    <!-- Form di creazione collegio -->
    <div class="center-box">
        <h2>Crea un nuovo collegio</h2>
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
    </div>

    <!-- Tabella collegi -->
    <div class="center-box">
    <h2>Collegi disponibili</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th>Descrizione</th>
                <th>Data</th>
                <th>Inizio</th>
                <th>Fine</th>
                <th colspan="5">Azioni</th>
            </tr>
            <?php
            if ($result_collegi && mysqli_num_rows($result_collegi) > 0) {
                while ($row = mysqli_fetch_assoc($result_collegi)) {
            ?>
            <tr>
                <td><?= htmlspecialchars($row['descrizione']) ?></td>
                <td><?= htmlspecialchars($row['data_collegio']) ?></td>
                <td><?= htmlspecialchars($row['ora_inizio']) ?></td>
                <td><?= htmlspecialchars($row['ora_fine']) ?></td>
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
                echo "<tr><td colspan='9'>Nessun collegio trovato</td></tr>";
            }
            ?>
        </table>
    </div>
</div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            const data = document.getElementById("data_collegio").value;
            const oraInizio = document.getElementById("ora_inizio").value;
            const oraFine = document.getElementById("ora_fine").value;

            const oggi = new Date().toISOString().split('T')[0];
            const annoProssimo = new Date(new Date().getFullYear() + 1, 11, 31).toISOString().split('T')[0];

            if (data < oggi || data > annoProssimo) {
                alert("La data deve essere compresa tra oggi e il 31 dicembre dell’anno prossimo.");
                e.preventDefault();
                return;
            }

            if (oraInizio >= oraFine) {
                alert("L'ora di inizio deve essere precedente all'ora di fine.");
                e.preventDefault();
            }
        });
    </script>

    </body>
</html>

<?php
mysqli_close($db_conn);
?>
