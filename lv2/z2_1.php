<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];

    // Provjera tipa datoteke
    if (!in_array($file['type'], $allowed_types)) {
        die("Dozvoljeni formati: PDF, JPEG, PNG.");
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $encryption_key = md5('tajni_kljuc_256'); // 256-bitni ključ
    $cipher = 'AES-128-CTR';
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = random_bytes($iv_length);

    // Čitanje sadržaja datoteke
    $content = file_get_contents($file['tmp_name']);
    $encrypted_content = openssl_encrypt($content, $cipher, $encryption_key, 0, $iv);

    // Spremanje kriptirane datoteke
    $encrypted_file = $upload_dir . uniqid() . '.enc';
    file_put_contents($encrypted_file, $encrypted_content);

    // Spremanje IV-a za dekripciju
    $_SESSION['ivs'][basename($encrypted_file)] = base64_encode($iv);

    echo "Dokument uspješno kriptiran i spremljen kao $encrypted_file.";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="document" accept=".pdf,.jpeg,.jpg,.png" required>
    <button type="submit">Upload</button>
</form>