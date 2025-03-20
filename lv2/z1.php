<?php

$db_name = 'lv2baza'; 
$dir = "backup/$db_name"; // Direktorij za backup
$time = time(); // Trenutno vrijeme za jedinstveni naziv datoteke

// Kreiranje direktorija ako ne postoji
if (!is_dir($dir)) {
    if (!@mkdir($dir, 0777, true)) {
        die("Ne možemo stvoriti direktorij $dir.");
    }
}

// Spajanje na bazu podataka
$dbc = @mysqli_connect('localhost', 'root', '', $db_name) or die("Ne možemo se spojiti na bazu $db_name.");

// Dohvaćanje svih tablica
$result = mysqli_query($dbc, 'SHOW TABLES');
if (mysqli_num_rows($result) > 0) {
    echo "Backup za bazu podataka '$db_name' u tijeku...<br>";

    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        $table = $row[0];
        $file = "$dir/{$table}_{$time}.txt.gz"; // Sažeta datoteka

        // Dohvaćanje strukture tablice
        $columns_result = mysqli_query($dbc, "SHOW COLUMNS FROM $table");
        $columns = [];
        while ($col = mysqli_fetch_array($columns_result)) {
            $columns[] = $col['Field'];
        }
        $columns_list = implode(', ', $columns);

        // Dohvaćanje podataka
        $data_result = mysqli_query($dbc, "SELECT * FROM $table");
        if (mysqli_num_rows($data_result) > 0) {
            if ($fp = gzopen($file, 'w9')) {
                while ($data = mysqli_fetch_array($data_result, MYSQLI_NUM)) {
                    $values = array_map('addslashes', $data);
                    $values = array_map(function($val) { return "'$val'"; }, $values);
                    $values_list = implode(', ', $values);
                    gzwrite($fp, "INSERT INTO $table ($columns_list) VALUES ($values_list);\n");
                }
                gzclose($fp);
                echo "Tablica '$table' je sažeta i pohranjena u $file.<br>";
            } else {
                echo "Ne možemo otvoriti datoteku $file.<br>";
            }
        }
    }
} else {
    echo "Baza $db_name ne sadrži tablice.";
}

mysqli_close($dbc);
?>