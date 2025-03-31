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

$message = "";
$directory = __DIR__ . '/csvDocenti';
$filename = $directory . '/elenco_docenti.csv'; // Nome fisso per il file CSV
$csv_data = [];

// Assicurati che la directory esista
if (!is_dir($directory)) {
    mkdir($directory, 0777, true);
}

// Carica il file CSV esistente per la modifica
if (file_exists($filename)) {
    if (($handle = fopen($filename, "r")) !== FALSE) {
        $csv_data = [];
        $header = fgetcsv($handle); // Leggi la prima riga come intestazione
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $csv_data[] = $data; // Aggiungi le righe successive come dati
        }
        fclose($handle);
    }
}

// Salva le modifiche al file CSV
if (isset($_POST['update_csv'])) {
    $updated_data = json_decode($_POST['csv_data'], true);

    // Verifica che i dati siano stati decodificati correttamente
    if (is_array($updated_data)) {
        $output = fopen($filename, 'w'); // Sovrascrive il file esistente
        if ($output === false) {
            http_response_code(500); // Errore interno del server
            echo "Errore nella creazione del file CSV.";
            exit();
        } else {
            // Scrivi l'intestazione
            fputcsv($output, $header);

            // Scrivi i dati
            foreach ($updated_data as $row) {
                fputcsv($output, $row);
            }
            fclose($output);
            echo "Modifiche salvate con successo nel file CSV.";
            exit();
        }
    } else {
        http_response_code(400); // Richiesta non valida
        echo "Errore nella decodifica dei dati inviati.";
        exit();
    }
}

// Carica un nuovo file CSV e sovrascrive quello esistente
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    if (move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
        $message = "Nuovo file CSV caricato con successo.";
    } else {
        $message = "Errore durante il caricamento del file.";
    }
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
    <title>Gestisci Docenti</title>
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
            width: 100%;
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            color: black;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .delete-btn {
            color: red;
            cursor: pointer;
        }

        .add-row-btn {
            margin-top: 10px;
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
                <a class="navbar-brand" href="#">Gestisci Docenti</a>
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
            <h2>Gestisci Docenti</h2>
            <?php if ($message) { echo "<p class='alert alert-success'>$message</p>"; } ?>

            <!-- Carica un nuovo file CSV -->
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file">Carica un nuovo file CSV:</label>
                    <input type="file" name="file" id="file" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Carica Nuovo CSV</button>
            </form>

            <!-- Tabella per modificare i dati -->
            <?php if (!empty($csv_data)) { ?>
                <form id="csvForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <table id="csvTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <?php foreach ($header as $col) { echo "<th>$col</th>"; } ?>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($csv_data as $row) { ?>
                                <tr>
                                    <?php foreach ($row as $cell) { ?>
                                        <td><input type="text" value="<?php echo htmlspecialchars($cell); ?>"></td>
                                    <?php } ?>
                                    <td><span class="delete-btn" onclick="deleteRow(this)">Elimina</span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-success" onclick="updateCSV()">Salva CSV Modificato</button>
                    <button type="button" class="btn btn-info add-row-btn" onclick="addRow()">Aggiungi Nuova Riga</button>
                    <input type="hidden" name="csv_data" id="csv_data">
                </form>
            <?php } ?>
        </div>
    </div>

    <script>
        function deleteRow(button) {
            const row = button.closest("tr");
            row.remove();
        }

        function addRow() {
            const table = document.getElementById("csvTable").querySelector("tbody");
            const headerCount = document.querySelectorAll("#csvTable thead th").length - 1; // Escludi la colonna "Azioni"
            const newRow = document.createElement("tr");

            for (let i = 0; i < headerCount; i++) {
                const cell = document.createElement("td");
                const input = document.createElement("input");
                input.type = "text";
                input.value = ""; // Rendi la cella vuota
                cell.appendChild(input);
                newRow.appendChild(cell);
            }

            const actionCell = document.createElement("td");
            actionCell.innerHTML = '<span class="delete-btn" onclick="deleteRow(this)">Elimina</span>';
            newRow.appendChild(actionCell);

            table.appendChild(newRow);
        }

        function updateCSV() {
            const table = document.querySelector("#csvTable");
            const rows = table.querySelectorAll("tbody tr");
            const csvData = [];

            rows.forEach(row => {
                const cells = row.querySelectorAll("td input");
                const rowData = [];
                cells.forEach(cell => rowData.push(cell.value.trim())); // Rimuovi spazi inutili
                csvData.push(rowData);
            });

            // Verifica che i dati siano stati raccolti correttamente
            console.log("Dati raccolti:", csvData);

            // Invia i dati al server tramite una richiesta AJAX
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log("Risposta dal server:", xhr.responseText);

                    // Mostra un messaggio di successo
                    const messageBox = document.createElement("p");
                    messageBox.className = "alert alert-success";
                    messageBox.textContent = "Modifiche salvate con successo nel file CSV.";
                    document.querySelector(".center-box").prepend(messageBox);

                    // Rimuovi il messaggio dopo 3 secondi
                    setTimeout(() => {
                        messageBox.remove();
                    }, 3000);
                } else {
                    console.error("Errore durante il salvataggio del file CSV.");
                }
            };

            xhr.send("update_csv=1&csv_data=" + encodeURIComponent(JSON.stringify(csvData)));
        }
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>

</html>
