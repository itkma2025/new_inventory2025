<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Menampilkan nilai latitude dan longitude
        echo "Latitude: " . $latitude . "<br>";
        echo "Longitude: " . $longitude . "<br>";

        // Memanggil Nominatim API dengan User-Agent
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude&zoom=18&addressdetails=1";
        $options = [
            "http" => [
                "header" => "User-Agent: MyGeolocationApp/1.0 (myemail@example.com)\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            die('Error occurred while fetching location details.');
        }

        $response = json_decode($response, true);

        // Debug output
        // echo "<pre>";
        // print_r($response);
        // echo "</pre>";

        if (isset($response['address'])) {
            $address = $response['address'];
            // $_SESSION['kelurahan'] = isset($address['village']) ? $address['village'] : '';
            // $_SESSION['kecamatan'] = isset($address['suburb']) ? $address['suburb'] : '';
            // $_SESSION['kota'] = isset($address['city']) ? $address['city'] : '';
            // $_SESSION['provinsi'] = isset($address['state']) ? $address['state'] : '';
            // $_SESSION['kode_pos'] = isset($address['postcode']) ? $address['postcode'] : '';
            // $_SESSION['kecamatan'] = isset($address['county']) ? $address['county'] : '';
            $_SESSION['display_name'] = isset($response['display_name']) ? $response['display_name'] : '';

            // Menampilkan data lokasi
            // echo "Kota: " . $_SESSION['kota'] . "<br>";
            // echo "Kecamatan: " . $_SESSION['kecamatan'] . "<br>";
            // echo "Kelurahan: " . $_SESSION['kelurahan'] . "<br>";
            // echo "Provinsi: " . $_SESSION['provinsi'] . "<br>";
            // echo "Kode Pos: " . $_SESSION['kode_pos'] . "<br>";
            // echo "Display Name: " . $_SESSION['display_name'] . "<br>";
        } else {
            echo "Unable to get location details.";
        }
    } else {
        echo "Latitude and Longitude must be provided.";
    }
} else {
    echo "Invalid request method.";
}
?>
