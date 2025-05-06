<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fancybox PDF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css">
    <style>
        /* Atur ukuran maksimal untuk Fancybox */
        .fancybox__container {
            width: 100vw !important;
            height: 100vh !important;
        }
        .fancybox__content {
            width: 90vw !important;
            height: 90vh !important;
        }
        #pdf-container {
            width: 90vw;
            height: 90vh;
        }
        #pdf-container embed {
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>

<!-- Tombol untuk membuka PDF -->
<a data-fancybox data-src="#pdf-container" href="javascript:;">Lihat PDF</a>

<!-- Kontainer PDF -->
<div style="display: none;">
    <div id="pdf-container">
        <embed src="files/aaccvvv173941541167ad5f7343d70.pdf" type="application/pdf" />
    </div>
</div>

<!-- Fancybox -->
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>


</body>
</html>
