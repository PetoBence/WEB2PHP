<?php

require_once('../includes/db.php');
require_once('../tcpdf/tcpdf.php');

if (!isset($_GET['table'])) {
    die('No table selected.');
}

$table = $_GET['table'];
$tableData = [];

// SQL lekérdezés az összes lehetséges táblára
if ($table == 'vizsgazo') {
    $sql = "SELECT azon, nev, osztaly FROM vizsgazo";
} elseif ($table == 'vizsgatargy') {
    $sql = "SELECT azon, nev, szomax, irmax FROM vizsgatargy";
} elseif ($table == 'vizsga') {
    $sql = "SELECT vizsgazoaz, vizsgatargyaz, szobeli, irasbeli FROM vizsga";
} else {
    die("Invalid table selected.");
}

// Lekérdezés megvalósítása PDO-val
try {
    $stmt = $pdo->query($sql);
    if ($stmt->rowCount() > 0) {
        // adatok kiiratása és tömbben tárolása
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tableData[] = $row;
        }
    } else {
        die("No data found for the selected table.");
    }
} catch (PDOException $e) {
    die("Error executing query: " . $e->getMessage());
}

// új .pdf létrehozása TCPDF-el
$pdf = new TCPDF();
$pdf->AddPage();

// Főcím és betűméret/tipus beállitása
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Table Data from ' . ucfirst($table), 0, 1, 'C');
$pdf->Ln(5);


$pdf->SetFont('helvetica', '', 10);


if ($table == 'vizsgazo') {
    $pdf->Cell(30, 10, 'Azon', 1, 0, 'C');
    $pdf->Cell(70, 10, 'Nev', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Osztaly', 1, 1, 'C');
} elseif ($table == 'vizsgatargy') {
    $pdf->Cell(30, 10, 'Azon', 1, 0, 'C');
    $pdf->Cell(70, 10, 'Nev', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Szomax', 1, 0, 'C');
    $pdf->Cell(30, 10, 'Irmax', 1, 1, 'C');
} elseif ($table == 'vizsga') {
    $pdf->Cell(40, 10, 'VizsgazoAz', 1, 0, 'C');
    $pdf->Cell(40, 10, 'VizsgatargyAz', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Szobeli', 1, 0, 'C');
    $pdf->Cell(40, 10, 'Irasbeli', 1, 1, 'C');
}

// Adatsorok megjelenitése 
foreach ($tableData as $row) {
    foreach ($row as $column) {
        $pdf->Cell(40, 10, $column, 1, 0, 'C');
    }
    $pdf->Ln(10);
}

// .pdf output 
$pdf->Output('table_data.pdf', 'I');
exit;
?>
