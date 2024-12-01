<?php
// Include necessary files
require_once('../includes/db.php');
require_once('../includes/header.php');

// Változó hibaüzenet tárolásához
$error = "";

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['table'])) {
    $table = $_POST['table'];
    
    // Redirect to generate_pdf.php with the table parameter
    header("Location: ../includes/generate_pdf.php?table=$table");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Generator</title>
</head>
<body>

<h2>Kiválasztott táblából .pdf generálás</h2>

<!-- Form to select the table -->
<form action="pdf.php" method="POST">
    <label for="table">Kérem válasszon táblát:</label>
    <select name="table" id="table" required>
        <option value="">Válasszon táblát!</option>
        <option value="vizsgazo">vizsgazo</option>
        <option value="vizsgatargy">vizsgatargy</option>
        <option value="vizsga">vizsga</option>
    </select>

    <button type="submit">PDF generálása</button>
</form>

<?php
// Display error message if no data is found
if ($error != "") {
    echo "<p style='color: red;'>$error</p>";
}
?>

</body>
</html>





