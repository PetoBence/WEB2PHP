<?php

include '../includes/db.php'; 

// JSON response
header('Content-Type: application/json');

// GET kérési metódus, ha az Apache nem adja át megfelelően, egy alternatív módszert használ.
$request_method = $_SERVER['REQUEST_METHOD'];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $request_method = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
}


$response = ['message' => 'Invalid request'];


$request_uri = $_SERVER['REQUEST_URI'];
$request_uri_parts = explode('/', $request_uri);
$resource = end($request_uri_parts);


switch ($request_method) {
    case 'GET':
        // Az összes rekord vagy egy adott rekord lekérése, ha „azon” megadva van
        if (isset($_GET['azon'])) {
            $azon = $_GET['azon'];
            $sql = "SELECT * FROM vizsgatargy WHERE azon = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$azon]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($record) {
                $response = ['message' => 'Record found', 'data' => $record];
            } else {
                $response = ['message' => 'Record not found'];
            }
        } else {
            // Minden rekord lekérése
            $sql = "SELECT * FROM vizsgatargy";
            $stmt = $pdo->query($sql);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($records) {
                $response = ['message' => 'Records found', 'data' => $records];
            } else {
                $response = ['message' => 'No records found'];
            }
        }
        break;

    case 'POST':
        // Új rekord hozzáadása
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['nev']) && isset($data['szomax']) && isset($data['irmax'])) {
            $sql = "INSERT INTO vizsgatargy (nev, szomax, irmax) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['nev'], $data['szomax'], $data['irmax']]);
            $response = ['message' => 'Record created successfully'];
        } else {
            $response = ['message' => 'Missing required fields'];
        }
        break;

    case 'PUT':
        // Létező rekord frissítése
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['azon']) && isset($data['nev']) && isset($data['szomax']) && isset($data['irmax'])) {
            $sql = "UPDATE vizsgatargy SET nev = ?, szomax = ?, irmax = ? WHERE azon = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['nev'], $data['szomax'], $data['irmax'], $data['azon']]);
            $response = ['message' => 'Record updated successfully'];
        } else {
            $response = ['message' => 'Missing required fields'];
        }
        break;

    case 'DELETE':
        // Törlés
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['azon'])) {
            $sql = "DELETE FROM vizsgatargy WHERE azon = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$data['azon']]);
            $response = ['message' => 'Record deleted successfully'];
        } else {
            $response = ['message' => 'Missing required fields'];
        }
        break;

    default:
        // Nem megengedett metódus
        $response = ['message' => 'Method Not Allowed'];
        break;
}

// Válasz küldése JSON-ben
echo json_encode($response);
?>


