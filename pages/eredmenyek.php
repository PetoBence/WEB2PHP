<?php
include_once '../includes/db.php';

// 'vizsgazo', 'vizsgatargy', and 'vizsga' táblák adatainak kiiratása
$query = "SELECT v.nev AS student_name, vt.nev AS subject_name, vs.szobeli, vs.irasbeli 
          FROM vizsga vs
          JOIN vizsgazo v ON vs.vizsgazoaz = v.azon
          JOIN vizsgatargy vt ON vs.vizsgatargyaz = vt.azon";
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($results) {
    echo "<table>";
    echo "<tr><th>Student Name</th><th>Subject</th><th>Oral Exam</th><th>Written Exam</th></tr>";
    foreach ($results as $row) {
        echo "<tr><td>" . htmlspecialchars($row['student_name']) . "</td>
                  <td>" . htmlspecialchars($row['subject_name']) . "</td>
                  <td>" . htmlspecialchars($row['szobeli']) . "</td>
                  <td>" . htmlspecialchars($row['irasbeli']) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No results found.";
}
?>
