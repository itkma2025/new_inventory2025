flatpickr("#date", {
    dateFormat: "d/m/Y",
});

flatpickr("#tempo", {
    dateFormat: "d/m/Y",
});

// untuk menampilkan tanggal hari ini
var dateInput = document.getElementById('date');

// Membuat objek tanggal hari ini
var today = new Date();

// Mendapatkan hari, bulan, dan tahun dari tanggal hari ini
var day = String(today.getDate()).padStart(2, '0');
var month = String(today.getMonth() + 1).padStart(2, '0');
var year = today.getFullYear();

// Mengatur nilai default input dengan format yang diinginkan
dateInput.value = day + '/' + month + '/' + year;

// Edit dengan diskon
$('#edit-diskon').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var idTrx = button.data('id');
    var namaProduk = button.data('nama');
    var harga = button.data('hargadisc');
    var diskon = button.data('diskon');
    var qty = button.data('qty');

    $('#id_trxdisc').val(idTrx);
    $('#nama_produk_disc').val(namaProduk);
    $('#harga_produk_disc').val(harga);
    $('#disc').val(diskon);
    $('#qtydisc').val(qty);
});

// Edit tanpa diskon
$('#edit').on('show.bs.modal', function(event) {
    var button = $(event.relatedTarget);
    var idTrx = button.data('id');
    var namaProduk = button.data('nama');
    var harga = button.data('harga');
    var qty = button.data('qty');

    $('#id_trx').val(idTrx);
    $('#nama_produk').val(namaProduk);
    $('#harga_produk').val(harga);
    $('#qty').val(qty);
});

// Js Dikirim
function checkFileName() {
    var file1 = document.getElementById('fileku1').value;
    var file2 = document.getElementById('fileku2').value;
    var file3 = document.getElementById('fileku3').value;

    if (file1 === file2 && file2 !== "") {
        alert("Nama file ke 2 harus berbeda!");
        document.getElementById('fileku2').value = "";
        document.getElementById('imagePreview2').innerHTML = "";
    }

    if (file1 === file3 && file3 !== "") {
        alert("Nama file ke 3 harus berbeda!");
        document.getElementById('fileku3').value = "";
        document.getElementById('imagePreview3').innerHTML = "";
    }

    if (file2 === file3 && file3 !== "") {
        alert("Nama file ke 3 harus berbeda!");
        document.getElementById('fileku3').value = "";
        document.getElementById('imagePreview3').innerHTML = "";
    }
}

$(document).ready(function () {
    let jenisPengiriman = $("#jenisPengiriman"); // Pilihan jenis pengiriman
    let driverDiv = $("#driver");
    let divEkspedisi = $("#ekspedisi");
    let kernetStatus = $("input[name='kernet']");
    let divKernet = $("#divKernet");
    let pilihKernet = $("#pilihKernet");
    let pilihKernetSelectize = $("#pilihKernet-selectized")
    let divDriver = $("#divDriver");
    let pilihDriver = $("#pilihDriver");
    let pilihDriverSelectize = $("#pilihDriver-selectized")
    let pilihEkspedisi = $("#pilihEkspedisi");
    let pilihEkspedisiSelectize = $("#pilihEkspedisi-selectized");
    let dikirimOleh = $("#dikirimOleh");
    let pj = $("#pj");

    // Event listener untuk select jenis pengiriman
    jenisPengiriman.on("change", function () {
        let selectedValue = $(this).val(); // Ambil value dari select

        // Tampilkan div berdasarkan pilihan
        if (selectedValue === "Driver") {
            divEkspedisi.addClass("d-none");
            pilihEkspedisiSelectize.attr('required', false);
            pilihEkspedisi.attr('required', false);
            pilihEkspedisi[0].selectize.clear(); // Kosongkan selectize kernet
            driverDiv.removeClass("d-none");
            kernetStatus.prop('required', true); // Untuk membuat radio button menjadi required
            dikirimOleh.val('');
            dikirimOleh.attr('required', false);
            pj.val('');
            pj.attr('required', false);

            // Event listener untuk radio button kernet
            kernetStatus.on("change", function () {
                let selectedKernet = $(this).val();
                if (selectedKernet === '1') {
                    // Kernet
                    divKernet.removeClass('d-none');
                    pilihKernet.attr('required', true);

                    // Driver
                    divDriver.removeClass('d-none');
                    pilihDriver.attr('required', true);
                } else {
                    // Kernet
                    divKernet.addClass('d-none');
                    pilihKernet[0].selectize.clear(); // Kosongkan selectize kernet
                    pilihKernet.attr('required', false); // Hapus required dari input
                    pilihKernetSelectize.attr('required', false); // Hapus required dari input
                    
                    // Driver
                    divDriver.removeClass('d-none');
                }
            });

        } else if (selectedValue === "Ekspedisi") {
            // kernet
            driverDiv.addClass("d-none");
            kernetStatus.prop('required', false); // Untuk membuat radio button menjadi required

            // Hapus required jika pindah ke Ekspedisi
            pilihKernet[0].selectize.clear(); // Kosongkan selectize kernet
            pilihKernetSelectize.attr('required', false); // Hapus required dari input
            pilihKernet.attr('required', false);

            pilihDriver[0].selectize.clear(); // Kosongkan selectize driver
            pilihDriverSelectize.attr('required', false); // Hapus required dari input
            pilihDriver.attr('required', false);

            // Form expedisi
            divEkspedisi.removeClass("d-none");
            pilihEkspedisi.attr('required', true);
            dikirimOleh.attr('required', true);
            pj.attr('required', true);
        } else if (selectedValue === "Diambil Langsung") {
            divEkspedisi.addClass("d-none");
            pilihEkspedisiSelectize.attr('required', false);
            pilihEkspedisi[0].selectize.clear(); // Kosongkan selectize kernet
            pilihEkspedisi.attr('required', false);

            dikirimOleh.val('');
            dikirimOleh.attr('required', false);
            pj.val('');
            pj.attr('required', false);

            // kernet
            driverDiv.addClass("d-none");
            kernetStatus.prop('required', false); // Untuk membuat radio button menjadi required

           // Hapus required jika pindah ke Driver
           pilihKernet[0].selectize.clear(); // Kosongkan selectize kernet
           pilihKernetSelectize.attr('required', false); // Hapus required dari input
           pilihKernet.attr('required', false);

           pilihDriver[0].selectize.clear(); // Kosongkan selectize driver
           pilihDriverSelectize.attr('required', false); // Hapus required dari input
           pilihDriver.attr('required', false);
        }
    });
});



// Ajax Edit Produk
$(document).ready(function() {
    // Gunakan event delegation untuk mengikat event handler pada elemen dinamis
    $(document).on('click', '#editProduk', function() {
        var id_trx = $(this).data('idtrx');
        var id_inv = $(this).data('idinv');
        var jenis_inv = $(this).data('jenisinv');
        var kat_inv = $(this).data('kat');
        var jenis_cb = $(this).data('jeniscb');
        var cs = $(this).data('cs');
        var noinv = $(this).data('noinv');
        console.log(noinv);
        $.ajax({
            type: 'POST',
            url: 'ajax/edit-produk-proforma.php', // Pastikan file PHP ini benar
            data: { 
                id_trx: id_trx,
                id_inv: id_inv,
                jenis_inv : jenis_inv,
                kat_inv: kat_inv,
                jenis_cb : jenis_cb,
                cs: cs,
                noinv: noinv
            },
            cache: false, // Ini mencegah browser menyimpan cache
            success: function(response) {
                // console.log("Raw Response:", response);
                $('#editDataProdukShow').html(response); // Isi modal body dengan HTML dari response
                $('#modalEditProduk').modal('show'); // Tampilkan modal
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    });
});


$(document).ready(function() {
    // Gunakan event delegation untuk mengikat event handler pada elemen dinamis
    $(document).on('click', '#referensiHarga', function() {
        var id = $(this).data('id');
        var cs = $(this).data('cs');
        var noinv = $(this).data('noinv');
        console.log(id);
        $.ajax({
            type: 'POST',
            url: 'ajax/referensi-harga-produk.php', // Pastikan file PHP ini benar
            data: { 
                id: id,
                cs: cs,
                noinv: noinv
            },
            cache: false, // Ini mencegah browser menyimpan cache
            success: function(response) {
                console.log("Raw Response:", response);
                $('#referensiHargaProdukShow').html(response); // Isi modal body dengan HTML dari response
                $('#referensiHargaProduk').modal('show'); // Tampilkan modal
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
            }
        });
    });
});


