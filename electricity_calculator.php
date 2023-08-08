<?php
function calculateElectricityCost($voltage, $current, $rate) {
    $power = ($voltage * $current) / 1000; // Moving the decimal point three times to the left for kWh unit conversion
    $energy = $power * 1 * 1000; // Assuming 1 hour
    $totalCharge = $energy * ($rate / 100);

    // Convert rate to MYR (sen/kWh)
    $ratePerHour = ($totalCharge / $energy) * 100; // Rate per kWh
    $ratePerDay = $ratePerHour * 24; // Rate per day

    $hourlyCharges = array();
    for ($hour = 1; $hour <= 24; $hour++) {
        $hourlyEnergy = $energy * $hour;
        $hourlyCharge = $hourlyEnergy * ($rate / 100000); // conversion from sen to RM
        $hourlyCharges[] = array(
            'hour' => $hour,
            'hourlyEnergy' => $hourlyEnergy / 1000,
            'hourlyCharge' => $hourlyCharge,
        );
    }

    return array(
        'power' => $energy / 1000,
        'ratePerHour' => $ratePerHour / 100,
        'hourlyCharges' => $hourlyCharges,
    );
}

$voltage = isset($_POST["voltage"]) ? $_POST["voltage"] : "";
$current = isset($_POST["current"]) ? $_POST["current"] : "";
$rate = isset($_POST["rate"]) ? $_POST["rate"] : "";

$results = array();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($voltage) && !empty($current) && !empty($rate)) {
        $results = calculateElectricityCost($voltage, $current, $rate);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Electricity Calculator</title>
    <style>
        body {
            padding: 20px;
            font-family: Times New Roman;
        }
        .container {
            max-width: 800px;
            margin: auto;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #3498db; /* Bold Blue Color */
            color: #ffffff; /* Text Color for Bold Blue */
            font-weight: bold;
        }
        td {
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
        }
        tr:nth-child(even) td {
            background-color: #f2f2f2;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Electricity Calculator</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="voltage">Voltage (V):</label>
                <input type="number" step="any" class="form-control" id="voltage" name="voltage" value="<?php echo $voltage; ?>" required>
            </div>
            <div class="form-group">
                <label for="current">Current (A):</label>
                <input type="number" step="any" class="form-control" id="current" name="current" value="<?php echo $current; ?>" required>
            </div>
            <div class="form-group">
                <label for="rate">Current Rate (sen/kWh):</label>
                <input type="number" step="any" class="form-control" id="rate" name="rate" value="<?php echo $rate; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Calculate</button>
        </form>

        <?php if (!empty($results)) { ?>
            <p>POWER: <?php echo number_format($results['power'], 5); ?> kW</p>
            <p>RATE: <?php echo number_format($results['ratePerHour'], 3); ?> RM</p>
            <table>
                <tr>
                    <th># Hour</th>
                    <th>Energy (kWh)</th>
                    <th>TOTAL (RM)</th>
                </tr>
                <?php
                foreach ($results['hourlyCharges'] as $hourlyCharge) {
                    echo "<tr>";
                    echo "<td>{$hourlyCharge['hour']}</td>";
                    echo "<td>" . number_format($hourlyCharge['hourlyEnergy'], 5) . "</td>";
                    echo "<td>" . number_format($hourlyCharge['hourlyCharge'], 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        <?php } ?>
    </div>
</body>
</html>
