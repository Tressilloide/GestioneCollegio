<?php
    session_start();
    if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true) {
        header("Location: index.php");
        exit();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        ?> <h1>Non sei autorizzato ad accedere a questa pagina.</h1> <?php
        header("refresh:2; index.php");
        exit();
    }

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <title>Pagina Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            padding: 0;
            flex-direction: column;
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
            justify-content: space-evenly;
            margin-top: 80px;
            margin-left: 15%;
            margin-right: 15%;
            width: 70%;
            padding: 20px;
        }

        .center-box {
            width: 45%;
            background: white;
            padding: 30px;
            text-align: center;
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
                margin-bottom: 20px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Area Admin</a>
            </div>
            <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
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
    <div class="center-container">
        <div class="center-box">
            <h2>Statistiche</h2>
            <?php
            include 'connessione.php';
            $query_docenti = "SELECT COUNT(*) AS num_docenti FROM tdocente";
            $result_docenti = mysqli_query($db_conn, $query_docenti);
            if ($result_docenti) {
                $row_docenti = mysqli_fetch_assoc($result_docenti);
                echo "<h3>Numero docenti registrati: " . $row_docenti['num_docenti'] - 1 . "</h3>";
            } else {
                echo "<h3>Errore nel recupero dati.</h3>";
            }
            ?>
        </div>

        <div class="center-box">
            <h2>Gestione</h2>
            <a href="crea_proposta.php" class="btn btn-primary">Gestione proposte</a><br><br>
            <a href="gestione_collegi.php" class="btn btn-primary">Gestione collegi</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>
