<!-- Index.html file -->
<!DOCTYPE html>
<html lang="en">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory KMA</title>
    <style>
        /* style.css file*/
        body {
            display: flex;
            justify-content: center;
            margin: 0;
            padding: 0;
            height: 100vh;
            box-sizing: border-box;
            text-align: center;
            background: rgb(128 0 0 / 66%);
        }
        .container {
            width: 100%;
            max-width: 500px;
            margin: 5px;
        }
        
        .container h1 {
            color: #ffffff;
        }
        
        .section {
            background-color: #ffffff;
            padding: 50px 30px;
            border: 1.5px solid #b2b2b2;
            border-radius: 0.25em;
            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);
        }
        
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
        
        #html5-qrcode-anchor-scan-type-change {
            text-decoration: none !important;
            color: #1d9bf0;
        }
        
        video {
            width: 100% !important;
            border: 1px solid #b2b2b2 !important;
            border-radius: 0.25em;
        }
    </style>
</head>
 
<body>
    <?php
        $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    ?>
    <div class="container">
        <h1>Scan QR Codes</h1>
        <div class="section">
            <div id="my-qr-reader">
            </div>
        </div>
    </div>
    <script src="assets/js/scan-qr.js"></script>
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
                if (trimmedText.startsWith(decodeText)) {
                    finalUrl = "https://" + trimmedText; // No need to add domain again
                } else {
                    finalUrl = decodeText + trimmedText;
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
</body>
</html>