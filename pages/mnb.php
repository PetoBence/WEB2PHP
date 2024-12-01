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
$eredmeny = $eredmeny2 = $rdate = $currency1 = $currency2 = "";
$dev = $dev2 = $foo = 0.0;
$error = "A kiválasztott devizákra az adott napon nem található adat!";
$er = 0;

if (isset($_POST['datum']) && isset($_POST['penznem']) && isset($_POST['kuld']) && $_POST['datum'] != "" && $_POST['penznem'] != "" && $_POST['penznem2'] != "") {
    $sdate = explode("/", $_POST['datum']);
    $rdate = $sdate[2] . "-" . $sdate[0] . "-" . $sdate[1];
    $currency1 = $_POST['penznem'];
    $currency2 = $_POST['penznem2'];

    if ($currency1 != "HUF" && $currency2 != "HUF") {
        $eredmeny = simplexml_load_string(exc_rates($rdate, $rdate, $currency1));
        $eredmeny2 = simplexml_load_string(exc_rates($rdate, $rdate, $currency2));

        if ($eredmeny->count() != 0 && $eredmeny2->count() != 0) {
            $dev = floatval(str_replace(',', '.', trim($eredmeny->Day->Rate)));
            $dev2 = floatval(str_replace(',', '.', trim($eredmeny2->Day->Rate)));
            $foo = $dev / $dev2;
        } else {
            $er = 1;
        }
    } elseif ($currency1 == "HUF" && $currency2 != "HUF") {
        $eredmeny2 = simplexml_load_string(exc_rates($rdate, $rdate, $currency2));
        if ($eredmeny2->count() != 0) {
            $dev2 = floatval(str_replace(',', '.', trim($eredmeny2->Day->Rate)));
            $foo = 1 / $dev2;
        } else {
            $er = 1;
        }
    } elseif ($currency1 != "HUF" && $currency2 == "HUF") {
        $eredmeny = simplexml_load_string(exc_rates($rdate, $rdate, $currency1));
        if ($eredmeny->count() != 0) {
            $dev = floatval(str_replace(',', '.', trim($eredmeny->Day->Rate)));
            $foo = $dev;
        } else {
            $er = 1;
        }
    } elseif ($currency1 == "HUF" && $currency2 == "HUF") {
        $foo = 1;
    }

    // Fetch rates for the past 30 days for the table
    $exchange_rates_xml1 = simplexml_load_string(exc_rates($start_date_30_days_ago, $rdate, $currency1));
    $exchange_rates_xml2 = simplexml_load_string(exc_rates($start_date_30_days_ago, $rdate, $currency2));
    if ($exchange_rates_xml1->count() > 0 && $exchange_rates_xml2->count() > 0) {
        foreach ($exchange_rates_xml1->Day as $day1) {
            $date = (string)$day1['date'];
            $rate1 = floatval(str_replace(',', '.', (string)$day1->Rate));
            $rate2 = 1; // Default for currency1 as base

            foreach ($exchange_rates_xml2->Day as $day2) {
                if ($day2['date'] == $date) {
                    $rate2 = floatval(str_replace(',', '.', (string)$day2->Rate));
                    break;
                }
            }
            $exchange_rates_data[] = [
                'date' => $date,
                'currency1' => 1,
                'currency2' => number_format($rate1 / $rate2, 4)
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Deviza Árfolyam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>

<body>

    <div class="container">
        <h1 class="text-center mt-4 mb-4">Deviza Árfolyam Megtekintése</h1>

        <form class="mt-4" id="center" method="POST">
            <div class="form-group">
                <label for="datepicker">Dátum:</label>
                <input type="text" class="form-control" name="datum" id="datepicker" required="required">
            </div>

            <div class="form-group">
                <label for="penznem">Az első pénznem:</label>
                <select class="form-control" id="penznem" name="penznem" required="required">
                    <option value="">Válasszon Devizát!</option>
                    <?php foreach (currencies() as $curr) : ?>
                        <option value="<?php echo $curr; ?>"><?php echo $curr; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="penznem2">A második pénznem:</label>
                <select class="form-control" id="penznem2" name="penznem2" required="required">
                    <option value="">Válasszon Devizát!</option>
                    <?php foreach (currencies() as $curr) : ?>
                        <option value="<?php echo $curr; ?>"><?php echo $curr; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-outline-primary btn-lg btn-block" name="kuld">Küld</button>
            </div>
        </form>

        <?php if ($currency1 != "" && $currency2 != "" && $rdate != "" && $er == 0) : ?>
            <h3 class="text-center mt-4">1 <?php echo $currency1; ?> = <?php echo number_format($foo, 4); ?> <?php echo $currency2; ?></h3>

            <?php if (!empty($exchange_rates_data)): ?>
                <div class="table-container">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Dátum</th>
                                <th><?php echo $currency1; ?> (Minden rekord itt 1)</th>
                                <th><?php echo $currency2; ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exchange_rates_data as $row): ?>
                                <tr>
                                    <td><?php echo $row['date']; ?></td>
                                    <td><?php echo $row['currency1']; ?></td>
                                    <td><?php echo $row['currency2']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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

