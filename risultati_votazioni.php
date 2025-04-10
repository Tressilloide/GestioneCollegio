<!-- filepath: c:\xampp\htdocs\gestionecollegio\GestioneCollegio\risultati_votazioni.php -->
<?php
session_start();

if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit();
}

include 'connessione.php';

// Query per ottenere i collegi, le votazioni e i risultati
$query = "
    SELECT 
        c.descrizione AS collegio_descrizione,
        v.descrizione AS votazione_descrizione,
        p.titolo AS proposta_titolo,
        SUM(CASE WHEN e.voto = 1 THEN 1 ELSE 0 END) AS favorevoli,
        SUM(CASE WHEN e.voto = 0 THEN 1 ELSE 0 END) AS astenuti,
        SUM(CASE WHEN e.voto = -1 THEN 1 ELSE 0 END) AS contrari
    FROM tcollegiodocenti c
    JOIN tvotazione v ON c.id_collegio = v.id_collegio
    JOIN tproposta p ON v.id_proposta = p.id_proposta
    LEFT JOIN effettua e ON v.id_votazione = e.id_votazione
    GROUP BY c.id_collegio, v.id_votazione, p.id_proposta
    ORDER BY c.descrizione, v.descrizione, p.titolo
";

$result = mysqli_query($db_conn, $query);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Risultati Votazioni</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            background: #007bff;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Risultati Votazioni</h1>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Collegio</th>
                        <th>Votazione</th>
                        <th>Proposta</th>
                        <th>Favorevoli</th>
                        <th>Astenuti</th>
                        <th>Contrari</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['collegio_descrizione']) ?></td>
                            <td><?= htmlspecialchars($row['votazione_descrizione']) ?></td>
                            <td><?= htmlspecialchars($row['proposta_titolo']) ?></td>
                            <td><?= htmlspecialchars($row['favorevoli']) ?></td>
                            <td><?= htmlspecialchars($row['astenuti']) ?></td>
                            <td><?= htmlspecialchars($row['contrari']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="alert alert-warning">Nessun risultato trovato.</p>
        <?php endif; ?>
        <a href="admin.php" class="btn btn-primary">Torna indietro</a>
    </div>
</body>
</html>