<?php
    include 'connessione.php';
    include 'funzioni.php';
    session_start();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Area Riservata</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        h2 {
            color: black;
        }
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;  /* Centra orizzontalmente */
            align-items: flex-start;  /* Posiziona i div in alto */
            height: 100vh;            /* Altezza della viewport */
            padding: 0;
            flex-direction: column;   /* Rende possibile l'uso di flexbox per il layout */
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
            justify-content: space-evenly;  /* Spazio uniforme tra i div */
            margin-top: 80px;  /* Spazio sopra il contenitore centrale per la navbar */
            margin-left: 15%;
            margin-right: 15%;
            width: 70%;  /* Imposta la larghezza totale per i div a sinistra e destra */
            padding: 20px;
        }

        .center-box {
            width: 45%;  /* Ogni div occuperà il 45% della larghezza */
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }

        .list-group-item{
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
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <?php
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header("Location: index.php");
            exit();
        }

        $email = $_SESSION['email_utente'];
        if (isset($_SESSION['if_loggato']) && $_SESSION['if_loggato'] === true) {

            if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
                header("Location: admin.php");
                exit();
            }

            $email = mysqli_real_escape_string($db_conn, $email);

            $query = "SELECT effettua.voto, tvotazione.descrizione
                FROM effettua
                JOIN tdocente ON effettua.id_docente = tdocente.id_docente
                JOIN tvotazione ON effettua.id_votazione = tvotazione.id_votazione
                WHERE tdocente.email = '$email'
            ";
            $result = mysqli_query($db_conn, $query);
    ?>
    <!-- Navbar fissa in cima -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Benvenuto, <?php echo htmlspecialchars($_SESSION['nome_utente']); ?>!</a>
            </div>
            <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="gestione_account.php">Gestione Account</a></li>
                <li>
                    <a href="#" class="nav-link text-danger" onclick="document.getElementById('logoutForm').submit();">Logout</a>
                    <form id="logoutForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" style="display: none;">
                        <input type="hidden" name="logout">
                    </form>
                </li>
            </ul>
            </div>
        </div>
    </nav>

    <!-- Contenitore centrale con div sinistro e destro -->
    <div class="center-container">
        <!-- Div sinistro -->
        <div class="center-box">
            <h2>Accedi al Collegio</h2>
            <form name="frmCollegio" method="post" action="votazione.php">
                <div class="form-group">
                    <label for="txtOTPCollegio">One Time Password del Collegio:</label>
                    <input type="password" class="form-control" id="txtOTPCollegio" name="otp_collegio" required placeholder="Inserisci la OTP del Collegio">
                </div>
                <button type="submit" class="btn btn-primary" name="verifica_otp_collegio">Accedi</button>
            </form>
        </div>

        <!-- Div destro -->
        <div class="center-box">
            <h2>Storico Votazioni</h2>
            <ul class='list-group'>
                <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <li class='list-group-item'>
                        Votazione: <?= htmlspecialchars($row['descrizione']) ?> - Voto: <?= htmlspecialchars($row['voto']) ?>
                    </li>
                <?php
                        }
                    } else {
                ?>
                    <li class='list-group-item'>Nessun voto effettuato</li>
                <?php
                    }
                ?>
            </ul>
        </div>
    </div>

    <?php
        } else {
    ?>
        <h1>Non puoi stare su questa pagina senza essere registrato.</h1>
    <?php
            header("refresh:2; login.php");
            exit();//se non metto exit() esplode tutto (entra lostesso nell'else), non so pk
        }
    ?>
</body>
</html>

