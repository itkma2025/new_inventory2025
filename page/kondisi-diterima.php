<script>
    // Mengambil referensi ke textarea
    var textarea = document.getElementById('catatan');

    // Mengambil referensi ke elemen yang akan menampilkan jumlah karakter
    var hitungKarakter = document.getElementById('hitungKarakter');

    // Menambahkan event listener untuk menghitung karakter saat textarea berubah
    textarea.addEventListener('input', function() {
        // Mengambil teks dari textarea
        var teks = textarea.value;

        // Menghitung jumlah karakter
        var jumlahKarakter = teks.length;

        // Memeriksa apakah jumlah karakter melebihi batas maksimum (150)
        if (jumlahKarakter > 150) {
            // Jika melebihi, potong teks menjadi 150 karakter
            teks = teks.substring(0, 150);
            textarea.value = teks; // Set ulang nilai textarea
            jumlahKarakter = 150; // Set ulang jumlah karakter
        }

        // Memperbarui teks pada elemen yang menampilkan jumlah karakter
        hitungKarakter.textContent = jumlahKarakter;
    });
</script>
<script>
    const kat_komplain = document.getElementById('kat_komplain');
    const retur_ya = document.getElementById('retur_ya');
    const retur_tidak = document.getElementById('retur_tidak');
    const refundDana = document.getElementById('refundDana');
    const refund_ya = document.getElementById('refund_ya');
    const refund_tidak = document.getElementById('refund_tidak');
    const jenisOngkir = document.getElementById('jenis_ongkir');
    const kondisi_pesanan0 = document.getElementById('kondisi_pesanan0');
    const kondisi_pesanan1 = document.getElementById('kondisi_pesanan1');
    const kondisi_pesanan2 = document.getElementById('kondisi_pesanan2');
    const kondisi_pesanan3 = document.getElementById('kondisi_pesanan3');
    const kondisi_pesanan4 = document.getElementById('kondisi_pesanan4');
    const kondisi_pesanan5 = document.getElementById('kondisi_pesanan5');
    const dropdown_input = document.getElementById('dropdown-input');
    const resi = document.getElementById('resi');
    const ongkir = document.getElementById('ongkir');
   
    // Tambahkan event listener untuk menangani perubahan pada radio button "Transfer"
    retur_ya.addEventListener('change', function() {
        if (retur_ya.checked) {
            refundDana.style.display = 'block'; // Menampilkan Form Input
            refund_ya.setAttribute('required', 'true');
            refund_tidak.setAttribute('required', 'true');
        }
    });

    retur_tidak.addEventListener('change', function() {
        if (retur_tidak.checked) {
            refundDana.style.display = 'none'; // Menampilkan Form Input
            refund_ya.removeAttribute('required');
            refund_tidak.removeAttribute('required');
        }
    });


    const cancelKomplain = document.getElementById('cancelKomplain');
    if (cancelKomplain) {
        cancelKomplain.addEventListener('click', function () {
            location.reload();
        });
    }


    
    function formatNumber(input) {
        // Menghapus semua karakter selain angka dan titik desimal
        let formattedValue = input.value.replace(/[^\d.]/g, '');

        // Memisahkan angka ribuan dengan titik
        formattedValue = formattedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

        // Mengatur nilai input dengan angka yang diformat
        input.value = formattedValue;
    }
</script>