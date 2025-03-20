<?php
/* Skripta za parsiranje XML-a i prikaz profila */
$xml = simplexml_load_file('LV2.xml') or die("Ne mogu učitati LV2.xml");

echo "<h1>Popis osoba</h1>";
foreach ($xml->record as $person) {
    $id = $person->id;
    $ime = $person->ime;
    $prezime = $person->prezime;
    $email = $person->email;
    $spol = $person->spol;
    $slika = $person->slika;
    $zivotopis = $person->zivotopis;

    echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
    echo "<img src='$slika' alt='Slika osobe' style='width: 50px; height: 50px;'>";
    echo "<h2>$ime $prezime</h2>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Spol:</strong> $spol</p>";
    echo "<p><strong>Životopis:</strong> $zivotopis</p>";
    echo "</div>";
}
?>