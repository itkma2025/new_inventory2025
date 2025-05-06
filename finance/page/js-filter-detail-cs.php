 <script>
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
        var selectedItem = $('#filterJenisInv').val();
        var statusTagihan = data[4]; // Use the correct column index (index starts from 0)

        if (selectedItem === "all") {
            return true; // Show all rows if the option is "all"
        } else if (selectedItem === "Non PPN" && statusTagihan === "Non PPN") {
            return true; // Show rows with status "Non PPN"
        } else if (selectedItem === "PPN" && statusTagihan === "PPN") {
            return true; // Show rows with status "PPN"
        } else if (selectedItem === "BUM" && statusTagihan === "BUM") {
            return true; // Show rows with status "BUM"
        }

        return false; // Hide other rows
    });

        // Event handler for dropdown filter
    $("#filterJenisInv").change(function () {
        var table = $('#tableExportNew').DataTable();
        table.draw();
    });
</script>

<script>
    // Gunakan ekstensi DataTables bawaan untuk menyaring baris berdasarkan nilai pada kolom "Status Tagihan"
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var selectedItem = $('#filterStatusTrx').val();
        var statusTagihan = data[6]; // Gunakan indeks kolom yang benar (indeks dimulai dari 0)

        console.log("Selected item:", selectedItem);
        console.log("Status tagihan:", statusTagihan);

        if (selectedItem === "all") {
        return true; // Show all rows if the option is "all"
        } else if (selectedItem === "Transaksi Selesai" && statusTagihan == "Transaksi Selesai") {
            return true; // Show rows with status "Transaksi Selesai"
        } else if (selectedItem === "Komplain Selesai" && statusTagihan == "Komplain Selesai") {
            return true;  // Show rows with status "Komplain Selesai"
        } else if (selectedItem === "Belum Dikirim" && statusTagihan === "Belum Dikirim") {
            return true; // Show rows with status "Belum Dikirim"
        } else if (selectedItem === "Dikirim" && statusTagihan === "Dikirim") {
            return true; // Show rows with status "Dikirim"
        } else if (selectedItem === "Diterima" && statusTagihan === "Diterima") {
            return true; // Show rows with status "Diterima"
        }
            return false; // Sembunyikan baris lainnya
    });

    // Atur event change pada dropdown filter untuk menggambar ulang tabel setiap kali pengguna memilih nilai baru
    $("#filterStatusTrx").change(function (e) {
        var table = $('#tableExportNew').DataTable();
        table.draw();
    });
</script>

<script>
    // Gunakan ekstensi DataTables bawaan untuk menyaring baris berdasarkan nilai pada kolom "Status Tagihan"
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var selectedItem = $('#filterStatusPembayaran').val();
        var statusTagihan = data[8]; // Gunakan indeks kolom yang benar (indeks dimulai dari 0)

        console.log("Selected item:", selectedItem);
        console.log("Status tagihan:", statusTagihan);

        if (selectedItem === "all") {
            return true; // Show all rows if the option is "all"
        } else if (selectedItem === "Belum Bayar" && statusTagihan == "Belum Bayar") {
            return true; // Show all rows if the option is "Belum Bayar"
        } else if (selectedItem === "Sudah Bayar" && statusTagihan === "Sudah Bayar") {
            return true; // Show all rows if the option is "Sudah Bayar"
        }
            return false; // Sembunyikan baris lainnya
    });

    // Atur event change pada dropdown filter untuk menggambar ulang tabel setiap kali pengguna memilih nilai baru
    $("#filterStatusPembayaran").change(function (e) {
        var table = $('#tableExportNew').DataTable();
        table.draw();
    });
</script>

<script>
    // Gunakan ekstensi DataTables bawaan untuk menyaring baris berdasarkan nilai pada kolom "Status Pelunasan"
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var selectedItem = $('#filterStatusLunas').val();
        var statusPelunasan = data[10]; // Gunakan indeks kolom yang benar (indeks dimulai dari 0)
        
        console.log("Selected item:", selectedItem);
        console.log("Status pelunasan:", statusPelunasan);

        if (selectedItem === "all") {
            return true; // Show all rows if the option is "all"
        } else if (selectedItem === "Lunas" && statusPelunasan === "Lunas") {
            return true; // Show all rows if the option is "Lunas"
        } else if (selectedItem === "Belum Lunas" && statusPelunasan === "Belum Lunas") {
            return true; // Show all rows if the option is "Belum Lunas"
        }
        
        return false; // Sembunyikan baris lainnya
    });

    // Atur event change pada dropdown filter untuk menggambar ulang tabel setiap kali pengguna memilih nilai baru
    $("#filterStatusLunas").change(function (e) {
        var table = $('#tableExportNew').DataTable();
        table.draw();
    });
</script>

<script>
    // Gunakan ekstensi DataTables bawaan untuk menyaring baris berdasarkan nilai pada kolom "Status Tagihan"
    $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var selectedItem = $('#filterStatusTagihan').val();
        var statusTagihan = data[12]; // Gunakan indeks kolom yang benar (indeks dimulai dari 0)

        // console.log("Selected item:", selectedItem);
        // console.log("Status tagihan:", statusTagihan);

        if (selectedItem === "all") {
            return true; // Show all rows if the option is "all"
        } else if (selectedItem === "Sudah Ditagih" && statusTagihan != "Belum Dibuat") {
            return true; // Show all rows if the option is "Sudah Ditagih"
        } else if (selectedItem === "Belum Dibuat" && statusTagihan === "Belum Dibuat") {
            return true; // Show all rows if the option is "Belum Dibuat"
        }

        return false; // Sembunyikan baris lainnya
    });

    // Atur event change pada dropdown filter untuk menggambar ulang tabel setiap kali pengguna memilih nilai baru
    $("#filterStatusTagihan").change(function (e) {
        var table = $('#tableExportNew').DataTable();
        table.draw();
    });
</script>

<script>
    function submitForm(action) {
        document.getElementById("invoiceForm").action = action;
        document.getElementById("invoiceForm").submit();
    }
</script>