<?php
/* Skripta za dekriptiranje i prikaz linkova */
session_start();

$upload_dir = 'uploads/';
$encryption_key = md5('tajni_kljuc_256'); // Isti ključ kao za kriptiranje
$cipher = 'AES-128-CTR';

$files = glob($upload_dir . '*.enc');
if (empty($files)) {
    echo "Nema kriptiranih dokumenata.";
    exit;
}

echo "<h2>Dokumenti za preuzimanje:</h2><ul>";
foreach ($files as $file) {
    $filename = basename($file);
    $iv = base64_decode($_SESSION['ivs'][$filename] ?? '');
    if (!$iv) {
        echo "<li>$filename - IV nedostupan, dekripcija nije moguća.</li>";
        continue;
    }

    $encrypted_content = file_get_contents($file);
    $decrypted_content = openssl_decrypt($encrypted_content, $cipher, $encryption_key, 0, $iv);

    // Spremanje dekriptirane datoteke
    $decrypted_file = $upload_dir . 'decrypted_' . $filename . '.dec';
    file_put_contents($decrypted_file, $decrypted_content);

    echo "<li><a href='$decrypted_file' download>Dohvati $filename</a></li>";
}
echo "</ul>";
?>