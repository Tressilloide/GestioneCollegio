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

    $docenti_non_trovati = [];
    
    $id_collegio = mysqli_real_escape_string($db_conn, $_GET['id_collegio']);
    $query_orari = "SELECT ora_inizio, ora_fine FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'";
    $result_orari = mysqli_query($db_conn, $query_orari);

    if ($result_orari && mysqli_num_rows($result_orari) > 0) {
        $row_orari = mysqli_fetch_assoc($result_orari);
        $ora_inizio_collegio = $row_orari['ora_inizio'];
        $ora_fine_collegio = $row_orari['ora_fine'];
    } else {
        $ora_inizio_collegio = "Orario non trovato";
        $ora_fine_collegio = "Orario non trovato";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $descrizione_votazione = mysqli_real_escape_string($db_conn, $_POST['descrizione_votazione']);
        $ora_inizio = mysqli_real_escape_string($db_conn, $_POST['ora_inizio']);
        $ora_fine = mysqli_real_escape_string($db_conn, $_POST['ora_fine']);
        $id_proposta = mysqli_real_escape_string($db_conn, $_POST['id_proposta']);
        $id_collegio = mysqli_real_escape_string($db_conn, $_POST['id_collegio']);

        

        //OTP di 3 cifre
        $otp = rand(100, 999);

        $ora1 = new DateTime($ora_inizio_collegio);
        $ora2 = new DateTime($ora_inizio);
        $ora3 = new DateTime($ora_fine_collegio);
        $ora4 = new DateTime($ora_fine);

        if ($ora1 > $ora2 || $ora2 > $ora3) {
            echo "<script>alert('L\'orario inserito non è valido.');</script>";
        } else {
            if ($ora3 < $ora4){
                echo 'L\'orario inserito non è valido';
            } else{
                $query_votazione = "INSERT INTO tvotazione (descrizione, ora_inizio, ora_fine, id_proposta, id_collegio, otp) 
                            VALUES ('$descrizione_votazione', '$ora_inizio', '$ora_fine', '$id_proposta', '$id_collegio', '$otp')";

        if (mysqli_query($db_conn, $query_votazione)) {
            $id_votazione = mysqli_insert_id($db_conn);

            // Gestione caricamento file CSV
            if (isset($_FILES['file_csv']) && $_FILES['file_csv']['error'] == 0) {
                $file_tmp = $_FILES['file_csv']['tmp_name'];
                $file = fopen($file_tmp, 'r');
                
                // Salta l'intestazione del file CSV
                fgetcsv($file);

                while (($line = fgetcsv($file)) !== FALSE) {
                    $email_docente = mysqli_real_escape_string($db_conn, $line[1]);

                    // Recupera l'id_docente dal database usando l'email
                    $docente_result = mysqli_query($db_conn, "SELECT id_docente FROM tdocente WHERE email = '$email_docente'");
                    if ($docente_result && mysqli_num_rows($docente_result) > 0) {
                        $docente_row = mysqli_fetch_assoc($docente_result);
                        $id_docente = $docente_row['id_docente'];

                        // Inserisci il docente nella tabella ammesso
                        $query_ammesso = "INSERT INTO ammesso (id_docente, id_votazione) VALUES ('$id_docente', '$id_votazione')";
                        mysqli_query($db_conn, $query_ammesso);
                    } else {
                        $docenti_non_trovati[] = $email_docente;
                    }
                }
                fclose($file);
            }

            if (empty($docenti_non_trovati)) {
                header("Location: visualizza_otp.php?otp=$otp&id_votazione=$id_votazione");
                exit();
            }
        } else {
            ?><h2>Errore nella creazione della votazione: </h2><?php
        }
            }
        }

        
    }

    $proposte_result = mysqli_query($db_conn, "SELECT id_proposta, titolo FROM tproposta");

    $id_collegio = isset($_GET['id_collegio']) ? $_GET['id_collegio'] : '';

    //Recupera il titolo del collegio dal database
    $collegio_titolo = '';
    if ($id_collegio) {
        $collegio_result = mysqli_query($db_conn, "SELECT descrizione FROM tcollegiodocenti WHERE id_collegio = '$id_collegio'");
        if ($collegio_result && mysqli_num_rows($collegio_result) > 0) {
            $collegio_row = mysqli_fetch_assoc($collegio_result);
            $collegio_titolo = $collegio_row['descrizione'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Crea Votazione</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <style>
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 50px; /* Evita che il contenuto finisca sotto la navbar */
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .center-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 70%;
            padding: 20px;
        }

        .center-box {
            width: 100%;  /* I box occuperanno tutta la larghezza disponibile */
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
            margin-bottom: 20px;  /* Aggiungi un margine inferiore per separare i box */
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
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Crea nuova votazione</a>
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
        <?php //necessario quando si vuole caricare file . ?>
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="descrizione_votazione">Descrizione:</label>
                <input type="text" class="form-control" id="descrizione_votazione" name="descrizione_votazione" required>
            </div>
            <div class="form-group">
                <label for="ora_inizio">Ora Inizio:</label>
                <input type="time" class="form-control" id="ora_inizio" name="ora_inizio" value="<?= htmlspecialchars($ora_inizio_collegio, ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="form-group">
                <label for="ora_fine">Ora Fine:</label>
                <input type="time" class="form-control" id="ora_fine" name="ora_fine" value="<?= htmlspecialchars($ora_fine_collegio, ENT_QUOTES, 'UTF-8') ?>" required>
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
                <input type="text" class="form-control" id="collegio_titolo" name="collegio_titolo" value="<?php echo htmlspecialchars($collegio_titolo); ?>" readonly>
                <input type="hidden" name="id_collegio" value="<?php echo htmlspecialchars($id_collegio);//passo id collegio  ?>">
            </div>
            <div class="form-group">
                <label for="file_csv">Carica CSV Docenti Ammessi:</label>
                <input type="file" class="form-control" id="file_csv" name="file_csv" accept=".csv">
            </div>
            <button type="submit" class="btn btn-primary" name="crea_votazione">Crea Votazione</button>
        </form>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
