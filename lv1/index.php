<?php
require_once 'DiplomskiRadovi.php';

$diplomski = new DiplomskiRadovi();

// Fetch data from pages 2 to 6
for ($page = 2; $page <= 6; $page++) {
    echo "<h2>Fetching Page $page</h2>";
    $diplomski->fetchDataFromPage($page);
    sleep(1); // Add a delay to avoid overwhelming the server
}

// Read and display all records
/*
echo "<h2>All Records in Database</h2>";
$records = $diplomski->read();
foreach ($records as $record) {
    echo "<strong>ID:</strong> {$record['id']}<br>";
    echo "<strong>Title:</strong> {$record['naziv_rada']}<br>";
    echo "<strong>Link:</strong> {$record['link_rada']}<br>";
    echo "<strong>OIB:</strong> {$record['oib_tvrtke']}<br>";
    echo "<strong>Text:</strong> {$record['tekst_rada']}<br>";
    echo "<hr>";
}
    */