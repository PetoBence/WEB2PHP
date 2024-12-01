<?php
include_once '../includes/header.php'; // Adjust path as needed

// Display basic HTML structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOAP Client</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        h2, h3 {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>SOAP Server: Teszt</h2>
        <p>A SOAP szerveren található adatok kiiratása:</p>

        <!-- Vizsgazo Table -->
        <h3>Vizsgázó tábla</h3>
        <div id="vizsgazo-table">
            <?php
            try {
                $client = new SoapClient(null, [
                    'location' => 'http://localhost/soap/soap-server.php', // Update with your SOAP server URL
                    'uri' => 'http://localhost/soap/', // Update to match the URI on your server
                    'trace' => 1
                ]);

                $vizsgazoData = $client->__soapCall('getVizsgazo', []); // Call the function

                echo '<table>';
                echo '<tr><th>Azon</th><th>Neve</th><th>Osztaly</th></tr>';
                foreach ($vizsgazoData as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['azon']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nev']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['osztaly']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } catch (SoapFault $e) {
                echo '<p>Error fetching Vizsgazo data: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <!-- Vizsgatargy Table -->
        <h3>Vizsgatárgy tábla</h3>
        <div id="vizsgatargy-table">
            <?php
            try {
                $vizsgatargyData = $client->__soapCall('getVizsgatargy', []); // Call the function

                echo '<table>';
                echo '<tr><th>Azon</th><th>Neve</th><th>Szomax</th><th>Irmax</th></tr>';
                foreach ($vizsgatargyData as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['azon']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['nev']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['szomax']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['irmax']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } catch (SoapFault $e) {
                echo '<p>Error fetching Vizsgatargy data: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>

        <!-- Vizsga Table -->
        <h3>Vizsga tálba</h3>
        <div id="vizsga-table">
            <?php
            try {
                $vizsgaData = $client->__soapCall('getVizsga', []); // Call the function

                echo '<table>';
                echo '<tr><th>VizsgazoAz</th><th>VizsgatargyAz</th><th>Szobeli</th><th>Irasbeli</th></tr>';
                foreach ($vizsgaData as $row) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['vizsgazoaz']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['vizsgatargyaz']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['szobeli']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['irasbeli']) . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } catch (SoapFault $e) {
                echo '<p>Error fetching Vizsga data: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>


