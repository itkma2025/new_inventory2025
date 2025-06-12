<?php
function testGetMerk($jenis, $expectedContains, $shouldContain = true) {
    $url = "http://localhost:8080/ajax/get-merk.php?jenis=" . urlencode($jenis);
    $output = @file_get_contents($url);

    if ($output === false) {
        echo "❌ Gagal request ke $url\n";
        return;
    }

    $found = strpos($output, $expectedContains) !== false;
    $status = ($shouldContain && $found) || (!$shouldContain && !$found) ? "✅" : "❌";

    echo "$status Test jenis='$jenis' → ";
    echo $shouldContain ? "Harus mengandung '$expectedContains'" : "Tidak boleh mengandung '$expectedContains'";
    echo "\n";
}

// Jalankan test case
testGetMerk('localss', 'Pilih Merk...');
testGetMerk('import', 'Pilih Merk...');
testGetMerk('local', 'Pilih Merk...', true);
testGetMerk("local' OR 1=1 --", 'Pilih Merk...', false);
testGetMerk('<script>', 'alert(', false);
