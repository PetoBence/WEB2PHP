<?php
session_start();


include_once '../includes/db.php';  


// A content-type fejléc beállítása XML-re SOAP-válaszok esetén
header('Content-Type: text/xml; charset=utf-8');

// A 'vizsgazo' táblából történő adatlekérdezés függvénye
function getVizsgazo() {
    global $pdo;
    $stmt = $pdo->query("SELECT azon, nev, osztaly FROM vizsgazo");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// A 'vizsgatargy' táblából való adatlekérdezés függvénye
function getVizsgatargy() {
    global $pdo;
    $stmt = $pdo->query("SELECT azon, nev, szomax, irmax FROM vizsgatargy");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// A 'vizsga' táblából való adatlekérdezés függvénye
function getVizsga() {
    global $pdo;
    $stmt = $pdo->query("SELECT vizsgazoaz, vizsgatargyaz, szobeli, irasbeli FROM vizsga");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// A SOAP-kiszolgáló példány létrehozása
$server = new SoapServer(null, array('uri' => 'http://localhost/soap/soap-server.php'));

// Függvények hozzáadása a SOAP-kiszolgálóhoz
$server->addFunction('getVizsgazo');
$server->addFunction('getVizsgatargy');
$server->addFunction('getVizsga');

// A SOAP kérés kezelése és a válasz kimenete
$server->handle();
?>
