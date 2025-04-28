<?php
session_start();

if (!isset($_SESSION['if_loggato']) || $_SESSION['if_loggato'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: index.php");
    exit();
}

include 'connessione.php';

// Query per ottenere le proposte e i risultati delle votazioni
$query = "
    SELECT 
        c.descrizione AS collegio_descrizione,
        v.descrizione AS votazione_descrizione,
        p.titolo AS proposta_titolo,
        SUM(CASE WHEN e.voto = 'Favorevole' THEN 1 ELSE 0 END) AS favorevoli,
        SUM(CASE WHEN e.voto = 'Astenuto' THEN 1 ELSE 0 END) AS astenuti,
        SUM(CASE WHEN e.voto = 'Contrario' THEN 1 ELSE 0 END) AS contrari
    FROM tcollegiodocenti c
    JOIN tvotazione v ON c.id_collegio = v.id_collegio
    JOIN tproposta p ON v.id_proposta = p.id_proposta
    LEFT JOIN effettua e ON v.id_votazione = e.id_votazione
    GROUP BY c.id_collegio, v.id_votazione, p.id_proposta
    ORDER BY c.descrizione, v.descrizione, p.titolo
";

$result = mysqli_query($db_conn, $query);

// Inizializziamo un array per contenere i dati delle votazioni per ogni proposta
$votazioniPerProposta = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $votazioniPerProposta[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Risultati Votazioni</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .cont-graph {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #graficoTorta {
            width: 100% !important;
            height: auto !important;
        }

        @media (max-width: 768px) {
            .cont-graph {
                padding: 10px;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <h1>Risultati Votazioni</h1>

        <!-- Menu a tendina per scegliere la proposta -->
        <label for="proposta">Scegli una proposta:</label>
        <select id="proposta" class="form-control">
            <option value="">Seleziona una proposta</option>
            <?php foreach ($votazioniPerProposta as $row): ?>
                <option value="<?= $row['proposta_titolo'] ?>"><?= $row['proposta_titolo'] ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Menu a tendina per scegliere la votazione -->
        <label for="votazione">Scegli una votazione:</label>
        <select id="votazione" class="form-control" disabled>
            <option value="">Seleziona una votazione</option>
        </select>

        <!-- Canvas per il grafico -->
        <div class="cont-graph" style="margin-top: 20px;">
            <canvas id="graficoTorta" width="60" height="60"></canvas>
        </div>

        <a href="admin.php" class="btn btn-primary" style="margin-top: 20px;">Torna indietro</a>
    </div>

    <script>
        // Dati delle votazioni per ciascuna proposta
        const votazioniPerProposta = <?php echo json_encode($votazioniPerProposta); ?>;

        // Elementi HTML
        const propostaSelect = document.getElementById('proposta');
        const votazioneSelect = document.getElementById('votazione');
        const graficoCanvas = document.getElementById('graficoTorta');
        let chartInstance = null;  // Variabile per tenere traccia dell'istanza del grafico


        // Popolare il secondo menu con le votazioni quando si seleziona una proposta
        propostaSelect.addEventListener('change', function() {
            const propostaSelezionata = propostaSelect.value;
            if (propostaSelezionata) {
                // Abilita il secondo menu e popola le votazioni corrispondenti
                votazioneSelect.disabled = false;
                votazioneSelect.innerHTML = `<option value="">Seleziona una votazione</option>`;

                // Filtrare le votazioni per la proposta selezionata
                const votazioni = votazioniPerProposta.filter(item => item.proposta_titolo === propostaSelezionata);
                votazioni.forEach(votazione => {
                    votazioneSelect.innerHTML += `<option value="${votazione.votazione_descrizione}">${votazione.votazione_descrizione}</option>`;
                });
            } else {
                votazioneSelect.disabled = true;
                votazioneSelect.innerHTML = `<option value="">Seleziona una votazione</option>`;
            }
        });

        // Mostra il grafico con i risultati quando si seleziona una votazione
        votazioneSelect.addEventListener('change', function() {
            const propostaSelezionata = propostaSelect.value;
            const votazioneSelezionata = votazioneSelect.value;

            if (propostaSelezionata && votazioneSelezionata) {
                // Trova i risultati corrispondenti
                const votazione = votazioniPerProposta.find(item => item.proposta_titolo === propostaSelezionata && item.votazione_descrizione === votazioneSelezionata);

                if (votazione) {
                    // Se esiste un grafico precedente, distruggilo
                    if (chartInstance) {
                        chartInstance.destroy();
                    }

                    // Dati per il grafico a torta
                    const datiGrafico = {
                        labels: ['Favorevoli', 'Contrari', 'Astenuti'],
                        datasets: [{
                            data: [votazione.favorevoli, votazione.contrari, votazione.astenuti],
                            backgroundColor: ['#007bff', '#ff0000', '#ffc400']
                        }]
                    };

                    // Configurazione del grafico
                    const config = {
                        type: 'pie',
                        data: datiGrafico,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(tooltipItem) {
                                            return tooltipItem.label + ': ' + tooltipItem.raw;
                                        }
                                    }
                                }
                            }
                        }
                    };

                    // Creare il grafico
                    chartInstance = new Chart(graficoCanvas, config);
                }
            }
        });
    </script>
</body>
</html>
