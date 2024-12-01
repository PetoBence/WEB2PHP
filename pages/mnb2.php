<?php

include_once '../includes/header.php';

// minden előrhető pénznem megjelenitése 
function currencies() {
    $client = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?WSDL");
    $result = new SimpleXMLElement($client->GetCurrencies()->GetCurrenciesResult);
    $stack = [];
    foreach ($result->xpath("//Currencies/Curr") as $item) {
        $stack[] = $item[0]->__toString();
    }
    return $stack;
}

// MNB SOAP szolgáltatásán keresztüli adatok kiiratása
function exc_rates($start_date, $end_date, $currency) {
    $soapClient = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?singleWsdl");
    try {
        $res = $soapClient->GetExchangeRates([
            'startDate' => $start_date, 
            'endDate' => $end_date, 
            'currencyNames' => $currency
        ]);
        return $res->GetExchangeRatesResult;
    } catch (Exception $e) {
        return null;  
    }
}

// a mai naptól számított 30 napra visszamenő adatok megjelenítése
$today = date("Y-m-d");
$start_date_30_days_ago = date("Y-m-d", strtotime("-30 days"));
$exchange_rates_data = [];  // 

$currency1 = $currency2 = "";  
$error = "";  

// kérés kezelése
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['penznem'], $_POST['penznem2'])) {
    $currency1 = $_POST['penznem'];
    $currency2 = $_POST['penznem2'];

    // az elmúlt 30 napra vonatkozó adatok kiiratása
    $exchange_rates_xml1 = simplexml_load_string(exc_rates($start_date_30_days_ago, $today, $currency1));
    $exchange_rates_xml2 = simplexml_load_string(exc_rates($start_date_30_days_ago, $today, $currency2));

    if ($exchange_rates_xml1 && $exchange_rates_xml2) {
        // foreach ciklus futtatása az elmúlt 30 napra
        $rate1_array = [];
        $rate2_array = [];
        foreach ($exchange_rates_xml1->Day as $day1) {
            $date = (string)$day1['date'];
            $rate1_array[$date] = floatval(str_replace(',', '.', (string)$day1->Rate));
        }

        foreach ($exchange_rates_xml2->Day as $day2) {
            $date = (string)$day2['date'];
            $rate2_array[$date] = floatval(str_replace(',', '.', (string)$day2->Rate));
        }

        // elmúlt 30 nap átváltási értékei
        $current_date = strtotime($start_date_30_days_ago);
        $end_date = strtotime($today);

        while ($current_date <= $end_date) {
            $date = date('Y-m-d', $current_date);
            $rate1 = isset($rate1_array[$date]) ? $rate1_array[$date] : 0; 
            $rate2 = isset($rate2_array[$date]) ? $rate2_array[$date] : 0; 

            if ($rate1 && $rate2) {
                
                $exchange_rates_data[] = [
                    'date' => $date,
                    'currency1' => 1,  
                    'currency2' => number_format($rate1 / $rate2, 4), 
                ];
            }

            $current_date = strtotime("+1 day", $current_date);
        }
    } else {
        $error = "Nem található adat a megadott dátumra és devizákra!"; 
    }
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deviza Árfolyamok - 30 napos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>

<body>
    <div class="container">
        <h1 class="text-center mt-4">Deviza Árfolyamok (30 nap)</h1>

        <div class="form-container">
            <form method="POST">
                <div class="form-group">
                    <label for="penznem">Első Pénznem:</label>
                    <select class="form-control" id="penznem" name="penznem" required>
                        <option value="">Válasszon</option>
                        <?php foreach (currencies() as $curr): ?>
                            <option value="<?php echo $curr; ?>" <?php echo ($curr == $currency1) ? 'selected' : ''; ?>><?php echo $curr; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="penznem2">Második Pénznem:</label>
                    <select class="form-control" id="penznem2" name="penznem2" required>
                        <option value="">Válasszon</option>
                        <?php foreach (currencies() as $curr): ?>
                            <option value="<?php echo $curr; ?>" <?php echo ($curr == $currency2) ? 'selected' : ''; ?>><?php echo $curr; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Küld</button>
            </form>
        </div>

        <?php if (!empty($exchange_rates_data)): ?>
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Dátum</th>
                            <th><?php echo $currency1; ?> (1)</th>
                            <th><?php echo $currency2; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($exchange_rates_data as $rate): ?>
                            <tr>
                                <td><?php echo $rate['date']; ?></td>
                                <td><?php echo $rate['currency1']; ?></td>
                                <td><?php echo $rate['currency2']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (!empty($error)): ?>
            <p class="text-center text-danger mt-4"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</body>

</html>
