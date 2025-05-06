<?php
// Fungsi untuk mengatur logging error
function initializeErrorLogging($logFile = '../error_log_localhost.txt', $timezone = 'Asia/Jakarta') {
    // Mengaktifkan error logging
    ini_set('log_errors', 1);
    // Menentukan lokasi file error log
    ini_set('error_log', __DIR__ . '/' . $logFile);
    // Mengatur tingkat pelaporan error (semua jenis error)
    error_reporting(E_ALL);

    // Menyimpan konfigurasi di variabel global untuk akses di handler
    global $globalErrorConfig;
    $globalErrorConfig = [
        'logFile' => $logFile,
        'timezone' => $timezone,
    ];

    // Menetapkan custom error handler
    set_error_handler("customErrorHandler");
}

// Fungsi untuk menangani error
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    global $globalErrorConfig; // Mengakses konfigurasi global

    // Format timestamp sesuai zona waktu
    $date = new DateTime('', new DateTimeZone($globalErrorConfig['timezone']));
    $timestamp = $date->format('d-M-Y H:i:s');

    // Mendapatkan tipe error berdasarkan kode
    $errorType = getErrorType($errno);

    // Format pesan error
    $errorMessage = "[$timestamp] PHP $errorType: $errstr in $errfile on line $errline" . PHP_EOL;

    // Menulis error ke file log
    error_log($errorMessage, 3, __DIR__ . '/' . $globalErrorConfig['logFile']);
    
    // Jika ingin menampilkan error ke layar, uncomment baris berikut
    // echo $errorMessage;
}

// Fungsi untuk mendapatkan tipe error
function getErrorType($errno) {
    switch ($errno) {
        case E_ERROR: return "Error";
        case E_WARNING: return "Warning";
        case E_NOTICE: return "Notice";
        case E_PARSE: return "Parse Error";
        case E_DEPRECATED: return "Deprecated";
        default: return "Unknown Error";
    }
}

// Panggil fungsi untuk mengatur error logging
initializeErrorLogging();

// Contoh untuk menghasilkan error
// echo $undefined_variable; // Variabel tidak terdefinisi, akan menulis error ke file log
?>
