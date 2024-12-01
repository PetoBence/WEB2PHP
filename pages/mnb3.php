<?php

include_once '../includes/header.php';


function currencies()
{
    $client = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?WSDL");
    $result = new SimpleXMLElement($client->GetCurrencies()->GetCurrenciesResult);
    $stack = [];
    foreach ($result->xpath("//Currencies/Curr") as $item) {
        $stack[] = $item[0]->__toString();
    }
    return $stack;
}


function exc_rates($start_date, $end_date, $currency)
{
    $soapClient = new SoapClient("http://www.mnb.hu/arfolyamok.asmx?singleWsdl");
    $res = $soapClient->GetExchangeRates(['startDate' => $start_date, 'endDate' => $end_date, 'currencyNames' => $currency]);
    return $res->GetExchangeRatesResult;
}

$today = date("m/d/Y");
$start_date_30_days_ago = date("Y-m-d", strtotime("-30 days"));
$exchange_rates_data = [];
$currency1 = $currency2 = "";
$error = "A kiválasztott devizákra az adott napon nem található adat!";
$er = 0;


if (isset($_POST['penznem']) && isset($_POST['penznem2']) && isset($_POST['kuld']) && $_POST['penznem'] != "" && $_POST['penznem2'] != "") {
    $currency1 = $_POST['penznem'];
    $currency2 = $_POST['penznem2'];

    
    $exchange_rates_xml1 = simplexml_load_string(exc_rates($start_date_30_days_ago, $today, $currency1));
    $exchange_rates_xml2 = simplexml_load_string(exc_rates($start_date_30_days_ago, $today, $currency2));
    
    if ($exchange_rates_xml1 && $exchange_rates_xml2) {
        foreach ($exchange_rates_xml1->Day as $day1) {
            $date = (string)$day1['date'];
            $rate1 = floatval(str_replace(',', '.', (string)$day1->Rate));
            $rate2 = 1; 

            foreach ($exchange_rates_xml2->Day as $day2) {
                if ($day2['date'] == $date) {
                    $rate2 = floatval(str_replace(',', '.', (string)$day2->Rate));
                    break;
                }
            }

            
            if ($rate2 != 0) {
                $exchange_rates_data[] = [
                    'date' => $date,
                    'currency1' => 1, 
                    'currency2' => $rate1 / $rate2 
                ];
            } else {
                
                $exchange_rates_data[] = [
                    'date' => $date,
                    'currency1' => 1, 
                    'currency2' => 'N/A' 
                ];
            }
        }
    } else {
        $er = 1; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deviza Árfolyam Grafikon</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>


<body>

    <div class="container">
        <h1 class="text-center mt-4 mb-4">Deviza Árfolyam Grafikon</h1> 

        <form class="mt-4" id="center" method="POST">
            <div class="form-group">
                <label for="penznem">Az első pénznem:</label>
                <select class="form-control" id="penznem" name="penznem" required="required">
                    <option value="">Válasszon Devizát!</option>
                    <?php foreach (currencies() as $curr) : ?>
                        <option value="<?php echo $curr; ?>" <?php echo ($curr == $currency1) ? 'selected' : ''; ?>><?php echo $curr; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="penznem2">A második pénznem:</label>
                <select class="form-control" id="penznem2" name="penznem2" required="required">
                    <option value="">Válasszon Devizát!</option>
                    <?php foreach (currencies() as $curr) : ?>
                        <option value="<?php echo $curr; ?>" <?php echo ($curr == $currency2) ? 'selected' : ''; ?>><?php echo $curr; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-outline-primary btn-lg btn-block" name="kuld">Küld</button>
            </div>
        </form>

        <?php if ($currency1 != "" && $currency2 != "" && $er == 0) : ?>
            <h3 class="text-center mt-4">Grafikon: 1 <?php echo $currency1; ?> = <?php echo number_format($exchange_rates_data[0]['currency2'], 4); ?> <?php echo $currency2; ?></h3>

            <?php if (!empty($exchange_rates_data)): ?>
                <div class="chart-container">
                    <canvas id="exchangeRateChart"></canvas>
                </div>

                <script>
                    var ctx = document.getElementById('exchangeRateChart').getContext('2d');
                    var exchangeRateChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: <?php echo json_encode(array_column($exchange_rates_data, 'date')); ?>,
                            datasets: [{
                                label: '1 <?php echo $currency1; ?> = <?php echo $currency2; ?>',
                                data: <?php echo json_encode(array_column($exchange_rates_data, 'currency2')); ?>,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: true,
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Dátum'
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: '<?php echo $currency2; ?> Árfolyam'
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php endif; ?>
        <?php elseif ($er == 1): ?>
            <h4 class="text-center text-danger mt-4"><?php echo $error; ?></h4>
        <?php endif; ?>
    </div>

    <script>
        $(function () {
            $("#datepicker").datepicker({
                maxDate: "<?php echo $today; ?>"
            });
        });
    </script>
</body>

</html>
