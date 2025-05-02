<script>
    const form = document.getElementById("form");
    const jenisPenerima = document.getElementById('jenis-penerima');
    const formCustomer = document.getElementById('formCustomer');
    const formExpedisi = document.getElementById('formExpedisi');
    const jenisOngkir = document.getElementById('jenis_ongkir');
    const resi = document.getElementById('resi');
    const ongkir = document.getElementById('ongkir');
    const penerima = document.getElementById('penerima');
    const selectEkspedisi = document.getElementById('selectEkspedisi');
    const errorMessageEx = document.getElementById('errorMessageEx');
    const errorMessage = document.getElementById("errorMessage");
    const imageInput = document.getElementById("image");

    // Event listener untuk memantau perubahan pada select "jenisPenerima"
    jenisPenerima.addEventListener('change', function () {
        if (this.value === 'Customer') {
            // Menampilkan form untuk Customer dan menonaktifkan form untuk Ekspedisi
            formCustomer.style.display = 'block';
            formExpedisi.style.display = 'none';
            selectEkspedisi.selectize.clear();

            // Menjadikan semua input di dalam formCustomer menjadi required
            Array.from(formCustomer.querySelectorAll('input, select')).forEach(input => {
                input.setAttribute('required', 'true');
            });

            // Menghapus required dari input di formExpedisi jika sebelumnya di-set
            resi.removeAttribute('required');
            jenisOngkir.removeAttribute('required');
            ongkir.removeAttribute('required');

            // Menghapus event listener submit dari form
            form.removeEventListener('submit', expedisiSubmitHandler);
        } else if (this.value === 'Ekspedisi') {
            // Menampilkan form untuk Ekspedisi dan menonaktifkan form untuk Customer
            formCustomer.style.display = 'none';
            formExpedisi.style.display = 'block';

            // Menambahkan event listener submit untuk validasi Ekspedisi
            form.addEventListener('submit', expedisiSubmitHandler);

            // Menjadikan input yang relevan di formExpedisi menjadi required
            resi.setAttribute('required', 'true');
            jenisOngkir.setAttribute('required', 'true');

            // Menonaktifkan required dari ongkir jika jenis ongkir "COD"
            if (jenisOngkir.value === '1') {
                ongkir.removeAttribute('required');
            } else {
                ongkir.setAttribute('required', 'true');
            }

            // Menghapus required dari input di formCustomer jika sebelumnya di-set
            Array.from(formCustomer.querySelectorAll('input, select')).forEach(input => {
                input.removeAttribute('required');
            });
        } else {
            // Menyembunyikan kedua form jika tidak ada pilihan yang dipilih
            formCustomer.style.display = 'none';
            formExpedisi.style.display = 'none';

            // Menghapus required dari semua input di kedua form
            Array.from(formCustomer.querySelectorAll('input, select')).forEach(input => {
                input.removeAttribute('required');
            });
            resi.removeAttribute('required');
            jenisOngkir.removeAttribute('required');
            ongkir.removeAttribute('required');

            // Menghapus event listener submit dari form
            form.removeEventListener('submit', expedisiSubmitHandler);
        }
    });

    // Event listener untuk memantau perubahan pada select "jenisOngkir"
    jenisOngkir.addEventListener('change', function () {
        if (this.value === '1') {
            // Jika jenis ongkir adalah "COD", maka ongkir tidak wajib diisi
            ongkir.removeAttribute('required');
            ongkir.setAttribute('readonly', 'true');
            ongkir.style.backgroundColor = '#f3f3f3';
            ongkir.value = '';
        } else {
            // Jika bukan "COD", maka ongkir wajib diisi
            ongkir.setAttribute('required', 'true');
            ongkir.removeAttribute('readonly');
            ongkir.style.backgroundColor = '';
        }
    });

    // Fungsi untuk memformat input ongkir menjadi angka yang diformat
    function formatNumber(input) {
        let formattedValue = input.value.replace(/[^\d.]/g, ''); // Hanya angka dan titik
        formattedValue = formattedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Format ribuan dengan koma
        input.value = formattedValue; // Set nilai input yang diformat
    }

    // Handler untuk validasi Ekspedisi pada event submit
    function expedisiSubmitHandler(event) {
        // Cek apakah nilai yang dipilih pada selectEkspedisi masih kosong
        if (selectEkspedisi.value === '') {
            event.preventDefault(); // Mencegah pengiriman formulir
            errorMessageEx.style.display = 'block'; // Tampilkan pesan kesalahan
        } else {
            errorMessageEx.style.display = 'none'; // Sembunyikan pesan kesalahan jika valid
        }
    }

    // Event listener untuk form submission
    document.addEventListener("DOMContentLoaded", function () {
    form.addEventListener("submit", function (event) {
        if (!imageInput.value) {
        // Jika nilai input tidak ada
        event.preventDefault(); // Mencegah pengiriman formulir
        errorMessage.style.display = "block"; // Tampilkan pesan kesalahan
        }
    });
    });
</script>
