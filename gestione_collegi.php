<?php
session_start();

if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
    ?>
    <h1>Non sei loggato, corri a loggarti.</h1> <?php
    header("refresh:2; index.php");
    exit();
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    ?>
    <h1>Non sei autorizzato ad accedere a questa pagina.</h1>
    <?php
    header("refresh:2; index.php");
    // TEST
    exit();
}

include 'connessione.php';

if (isset($_POST['btnCreaProposta'])) {
    $data_collegio = mysqli_real_escape_string($db_conn, $_POST['data_collegio']);
    $ora_inizio = mysqli_real_escape_string($db_conn, $_POST['ora_inizio']);
    $ora_fine = mysqli_real_escape_string($db_conn, $_POST['ora_fine']);
    $descrizione = mysqli_real_escape_string($db_conn, $_POST['descrizione']);

    $query_collegio = "INSERT INTO tcollegiodocenti (data_collegio, ora_inizio, ora_fine, descrizione) 
                            VALUES ('$data_collegio', '$ora_inizio', '$ora_fine', '$descrizione')";

    if (mysqli_query($db_conn, $query_collegio)) {
        echo "<h2>Collegio creato con successo!</h2>";
    } else {
        echo "<h2>Errore nella creazione del collegio: " . mysqli_error($db_conn) . "</h2>";
    }
    // Redirect per evitare duplicati
    header("Location: gestione_collegi.php");
    exit();
}

// Recupera i collegi esistenti per il menu a tendina
$query_collegi = "SELECT * FROM tcollegiodocenti ORDER BY data_collegio DESC";
$result_collegi = mysqli_query($db_conn, $query_collegi);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Gestione collegi</title>
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

        li {
            list-style-type: none;
        }

        .container{
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            margin-top: 50px;
            text-justify: auto;
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
            overflow-wrap: break-word;
        }
        

        th:nth-child(1), td:nth-child(1) {  /* Colonna Descrizione */ 
            width: 55%;
            min-width: 150px;
            max-width: 300px;
        }

        th:nth-child(2), td:nth-child(2),   /* Colonna Data */
        th:nth-child(3), td:nth-child(3),   /* Colonna Ora_inizio */
        th:nth-child(4), td:nth-child(4) {  /* Colonna Ora_fine */
            width: 8%;
            min-width: 50px;
            text-align: center;
        }

        th:nth-child(5), td:nth-child(5),   /* Colonna Conferma */
        th:nth-child(6), td:nth-child(6),   /* Colonna Modifica */
        th:nth-child(7), td:nth-child(7) {  /* Colonna Elimina */
            width: 2%;
            min-width: 50px;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Navbar fissa in cima -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Crea un nuovo collegio</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="admin.php" class="nav-link text-danger">Home Page</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container"><br>
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
            </tr>
            <tr>
                <?php
                if ($result_collegi && mysqli_num_rows($result_collegi) > 0) {
                    while ($row = mysqli_fetch_assoc($result_collegi)) {
                ?>
                        <tr>
                            <li>
                            <td><?= htmlspecialchars($row['descrizione'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['data_collegio'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['ora_inizio'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['ora_fine'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            </li>
                            <td>
                                <form method="post" action="crea_votazione.php">
                                    <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                    <button type="submit" name="btnModifica" class="btn btn-link">
                                        <img src="images/conferma.png">
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="modifica_collegio.php">
                                    <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                    <button type="submit" name="btnModifica" class="btn btn-link">
                                        <img src="images/penna_modifica.png">
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="id_collegio" value="<?= htmlspecialchars($row['id_collegio']) ?>">
                                    <button type="submit" name="btnElimina" class="btn btn-link">
                                        <img src="images/eliminazione.png" width="20px" height="20px">
                                    </button>
                                </form>
                            </td>
                        </tr>
                <?php
                        }
                    } else {
                ?>
                    <td colspan='7'>
                        <li>Nessuna proposta trovata</li>
                    </td>
                <?php
                    }
                ?>
            </tr>
        </table>
        <!-- <h2>Seleziona un collegio esistente</h2>
        <form method="get" action="crea_votazione.php">
            <div class="form-group">
                <label for="id_collegio">Collegio:</label>
                <select class="form-control" id="id_collegio" name="id_collegio" required>
                    <?php
                    if ($collegi_result && mysqli_num_rows($collegi_result) > 0) {
                        while ($row = mysqli_fetch_assoc($collegi_result)) {
                            echo "<option value='" . $row['id_collegio'] . "'>" . htmlspecialchars($row['descrizione']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>Nessun collegio disponibile</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Seleziona Collegio</button>
        </form> -->
    </div>
</body>

</html>