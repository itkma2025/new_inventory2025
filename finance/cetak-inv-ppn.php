<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Invoice Nonppn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous">
    <style>
        /* Gaya untuk animasi loading */
        #loading {
            display: none;
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        .button {
            background-color: #04AA6D; /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
        }

        .button-outline {
            background-color: white; 
            color: black; 
            border: 2px solid #008CBA;
        }

        .button-outline:hover {
            background-color: #008CBA;
            color: white;
        }

        .button-outline.active {
            background-color: #008CBA;
            color: white;
        }
    </style>
</head>
<body>
    <?php  
        $id = $_GET['id'];
    ?>
    <div style="text-align: center; margin-top: 20px; margin-bottom:20px;" id="printButton">
        <button class="button button-outline" id="ppn-old">
            <i class="fas fa-file"></i> PPN Lama
        </button>
        <button class="button button-outline" id="ppn-new">
            <i class="fas fa-file"></i> PPN Baru
        </button>
    </div>
    <div id="loading">Memuat data...</div>
    <!-- Area untuk menampilkan file yang dimuat -->
    <div id="content-area"></div>
</body>
</html>
<!-- jquery 3.6.3 -->
<script src="assets/js/jquery.min.js"></script>
<script>
$(document).ready(function() {
    let autoReload; // Variabel untuk menyimpan interval

    function loadContentOld() {
        $("#content-area").load("cetak-inv-ppn-reg-old.php?id=<?php echo $id ?>&t=" + Date.now());
    }

    function loadContentNew() {
        $("#content-area").load("cetak-inv-ppn-reg.php?id=<?php echo $id ?>&t=" + Date.now());
    }

    function setActiveButton(buttonId) {
        $(".button").removeClass("active"); // Hapus class active dari semua button
        $("#" + buttonId).addClass("active"); // Tambahkan class active ke tombol yang diklik
    }

    $("#ppn-old").click(function() {
        clearInterval(autoReload); // Hentikan auto reload sebelumnya
        setActiveButton("ppn-old");
        loadContentOld();
        autoReload = setInterval(loadContentOld, 200); // Perbarui setiap 0.2 detik setelah diklik
    });

    $("#ppn-new").click(function() {
        clearInterval(autoReload); // Hentikan auto reload sebelumnya
        setActiveButton("ppn-new");
        loadContentNew();
        autoReload = setInterval(loadContentNew, 200); // Perbarui setiap 0.2 detik setelah diklik
        console.log(autoReload);
    });
});
</script>




