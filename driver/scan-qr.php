<?php
require_once "../akses.php";
$page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Inventory KMA</title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <?php include "page/head.php"; ?>
  <style>
    #my-qr-reader {
            padding: 20px !important;
            border: 1.5px solid #b2b2b2 !important;
            border-radius: 8px;
        }
        
        #my-qr-reader img[alt="Info icon"] {
            display: none;
        }
        
        #my-qr-reader img[alt="Camera based scan"] {
            width: 100px !important;
            height: 100px !important;
        }
        #html5-qrcode-anchor-scan-type-change {
            text-decoration: none !important;
            color: #1d9bf0;
        }
        video {
            width: 100% !important;
            border: 1px solid #b2b2b2 !important;
            border-radius: 0.25em;
        }

         
        button {
            padding: 10px 20px;
            border: 1px solid #b2b2b2;
            outline: none;
            border-radius: 0.25em;
            color: white;
            font-size: 15px;
            cursor: pointer;
            margin-top: 15px;
            margin-bottom: 10px;
            background-color: #008000ad;
            transition: 0.3s background-color;
        }
        
        button:hover {
            background-color: #008000;
        }
  </style>
</head>

<body>
  <!-- nav header -->
  <?php include "page/nav-header.php" ?>
  <!-- end nav header -->

  <!-- sidebar  -->
  <?php include "page/sidebar.php"; ?>
  <!-- end sidebar -->

  <main id="main" class="main">
    <section class="section dashboard">
    <?php
        $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>
    <div class="container">
        <h1>Scan QR Codes</h1>
        <div class="section">
            <div id="my-qr-reader">
            </div>
        </div>
        <div id="hasilScan"></div>
    </div>
        <script
            src="https://unpkg.com/html5-qrcode">
        </script>
        <script>
            var url = "<?php echo $url; ?>";
            // Function to execute when the DOM is fully loaded
            function domReady(fn) {
                if (
                    document.readyState === "complete" ||
                    document.readyState === "interactive"
                ) {
                    setTimeout(fn, 1000); // If the document is already ready, execute the function after 1 second
                } else {
                    document.addEventListener("DOMContentLoaded", fn); // Otherwise, wait for the DOMContentLoaded event
                }
            }

            // Execute the following code when the DOM is ready
            domReady(function () {

                // Function to handle successful QR code scan
                function onScanSuccess(decodeText, decodeResult) {
                    // Remove the specified part from the URL
                    var trimmedText = decodeText.replace(url, "");
                    console.log("Trimmed Text: " + trimmedText);
                    
                    // Ensure the resulting URL is absolute by adding the correct domain only if necessary
                    var finalUrl;
                    if (trimmedText.startsWith("karsa-inventory.mandirialkesindo.co.id/")) {
                        finalUrl = "https://" + trimmedText; // No need to add domain again
                    } else {
                        finalUrl = "https://karsa-inventory.mandirialkesindo.co.id/" + trimmedText;
                    }

                    // Show a confirmation dialog with the decoded text
                    if (confirm("Your QR is: " + decodeText + "\n\nDo you want to open this URL?")) {
                        window.location.href = finalUrl; // Redirect to the modified URL
                    }
                }

                // Create a new instance of Html5QrcodeScanner with enhanced settings
                let htmlscanner = new Html5QrcodeScanner(
                    "my-qr-reader", // ID of the HTML element where the QR code scanner will be rendered
                    {
                        fps: 20, // Increased frames per second
                        qrbox: { width: 250, height: 250 }, // Size of the scanning box
                        aspectRatio: 1.0, // Aspect ratio of the video feed
                        disableFlip: true // Disable mirrored scanning
                    }
                );

                // Render the QR code scanner and set the callback function for successful scans
                htmlscanner.render(onScanSuccess);
            });
        </script>
    </section>
  </main><!-- End #main -->
  <!-- Footer -->
  <?php include "page/footer.php" ?>
  <!-- End Footer -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <?php include "page/script.php"; ?>
</body>
</html>