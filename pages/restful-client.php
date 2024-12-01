<?php

// API URL
$api_url = "http://localhost/your_project_folder/restful-server.php"; // Update the URL as needed

// Function to make a GET request
function makeGetRequest($api_url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    $response = curl_exec($ch);
    curl_close($ch);
    echo $response;
}

// Function to make a POST request
function makePostRequest($api_url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    echo $response;
}

// Function to make a PUT request
function makePutRequest($api_url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    echo $response;
}

// Function to make a DELETE request
function makeDeleteRequest($api_url, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    $response = curl_exec($ch);
    curl_close($ch);
    echo $response;
}

// Example usage:
// GET request (fetch all records)
echo "GET request: \n";
makeGetRequest($api_url . "\n");

// POST request (create a new record)
$data = ['nev' => 'Matematika', 'szomax' => 5, 'irmax' => 10];
echo "POST request: \n";
makePostRequest($api_url, $data);

// PUT request (update a record)
$data = ['azon' => 1, 'nev' => 'Matematika Updated', 'szomax' => 6, 'irmax' => 12];
echo "PUT request: \n";
makePutRequest($api_url, $data);

// DELETE request (delete a record)
$data = ['azon' => 1];
echo "DELETE request: \n";
makeDeleteRequest($api_url, $data);
?>
