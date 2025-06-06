<?php
    session_start();

    function console_log($output, $with_script_tags = true) {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

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

    if(isset($_POST['btnCreaProposta'])) {
        $titolo = mysqli_real_escape_string($db_conn, $_POST['titolo']);
        $descrizione_proposta = mysqli_real_escape_string($db_conn, $_POST['descrizione_proposta']);

        // Controlla se la proposta esiste già
        $query_check = "SELECT * FROM tproposta WHERE titolo = '$titolo' AND descrizione = '$descrizione_proposta'";
        $result_check = mysqli_query($db_conn, $query_check);

        if (mysqli_num_rows($result_check) > 0) {
            ?> <h2>Proposta già esistente!</h2> <?php
        } else {
            $query_proposta = "INSERT INTO tproposta (titolo, descrizione) VALUES ('$titolo', '$descrizione_proposta')";

            if (mysqli_query($db_conn, $query_proposta)) {
                ?><h2>Proposta creata con successo!</h2><?php 
            } else {
                ?><h2>Errore nella creazione della proposta</h2><?php 
            }
        }
        //evita duplicato inserimento
        header("Location: crea_proposta.php");
        exit();
    }

    if (isset($_POST['btnModifica'])) {
        $new_titolo = mysqli_real_escape_string($db_conn, trim($_POST['titolo']));
        $new_descrizione = mysqli_real_escape_string($db_conn, trim($_POST['descrizione']));
        
        $query_update = "UPDATE tproposta SET titolo = '$new_titolo', descrizione = '$new_descrizione' WHERE id_proposta = '$id_proposta'";
    }

    if (isset($_POST['btnElimina'])) {    
        $id_proposta = mysqli_real_escape_string($db_conn, $_POST['id_proposta']);
    
        $query_delete = "DELETE FROM tproposta WHERE id_proposta = '$id_proposta'";
    
        $delete_result = mysqli_query($db_conn, $query_delete);
        
        if ($delete_result) {
            $_SESSION['messaggio'] = "Proposta eliminata con successo!";
        } else {
            $_SESSION['messaggio'] = "Errore: " . mysqli_error($db_conn);
        }
        
        header("Location: crea_proposta.php");
        exit();
    }

    // Query per ottenere tutti i collegi
    $query_collegi = "SELECT * FROM tproposta";
    $result_collegi = mysqli_query($db_conn, $query_collegi);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Crea Proposta</title>
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
    <!-- Navbar fissa in cima -->
        <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Crea nuova proposta</a>
            </div>
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="admin.php" class="nav-link text-danger">Home Page</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="center-container">
    <div class="center-box">
        <form method="post" action="">
            <div class="form-group">
                <label for="titolo">Titolo:</label>
                <input type="text" class="form-control" id="titolo" name="titolo" required>
            </div>
            <div class="form-group">
                <label for="descrizione_proposta">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione_proposta" name="descrizione_proposta" required>
            </div>
            <button type="submit" class="btn btn-primary" name="btnCreaProposta">Crea Proposta</button>
            <a href="admin.php" class="btn btn-primary">Torna indietro</a>
        </form>
    </div>
    <div class="center-box">
    <h2>Proposte disponibili</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Descrizione</th>
                    <th>Modifica</th>
                    <th>Elimina</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_collegi && mysqli_num_rows($result_collegi) > 0) {
                    while ($row = mysqli_fetch_assoc($result_collegi)) {
                ?>
                        <tr>
                            <td><?= htmlspecialchars($row['titolo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['descrizione'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="post" action="proposta_modifica.php">
                                    <input type="hidden" name="id_proposta" value="<?= htmlspecialchars($row['id_proposta']) ?>">
                                    <button type="submit" name="btnModifica" class="btn btn-link">
                                        <img src="images/penna_modifica.png" alt="Modifica">
                                    </button>
                                </form>
                            </td>
                            <td>
                                <form method="post" action="">
                                    <input type="hidden" name="id_proposta" value="<?= htmlspecialchars($row['id_proposta']) ?>">
                                    <button type="submit" name="btnElimina" class="btn btn-link">
                                        <img src="images/eliminazione.png" alt="Elimina" width="20px" height="20px">
                                    </button>
                                </form>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="4">Nessuna proposta trovata</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>