<?php 
    // Kode untuk sanitasi input
    $config = HTMLPurifier_Config::createDefault();
    // Hanya izinkan URL yang menggunakan skema 'http' dan 'https'
    $config->set('URI.AllowedSchemes', array('http' => true, 'https' => true));

    // Menonaktifkan sumber daya eksternal untuk gambar dan media lainnya
    $config->set('URI.DisableExternalResources', true);

    // Izinkan tag <img> hanya jika atribut src valid
    $config->set('HTML.Allowed', 'p,b,a[href],i,img[src]');

    // Membatasi sumber iframe, jika perlu (misal, YouTube atau Vimeo)
    $config->set('HTML.SafeIframe', true);
    $config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube\.com/embed/|player\.vimeo\.com/video/)%');

    $sanitasi_input = new HTMLPurifier($config);
    // Fungsi untuk sanitasi global
    function sanitizeInput($data) {
        global $sanitasi_input; // Akses variabel sanitasi_input dari luar
        $sanitized_data = [];

        foreach ($data as $key => $value) {
            // Cek apakah nilai adalah array
            if (is_array($value)) {
                // Jika nilai adalah array, sanitasi setiap elemen
                foreach ($value as $sub_value) {
                    $sanitized_data[$key][] = $sanitasi_input->purify($sub_value);
                }
            } else {
                // Jika bukan array, sanitasi nilai langsung
                $sanitized_data[$key] = $sanitasi_input->purify($value);
            }
        }

        return $sanitized_data;
    }
?>