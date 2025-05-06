<script>
        var input = document.getElementById('resi');

        input.addEventListener('input', function () {
            var sanitizedValue = input
                .value
                .replace(/[^A-Za-z0-9]/g, '');
            input.value = sanitizedValue;
        });
</script>
<script>
    const jenisPenerima = <?php echo json_encode($jenis_pengiriman); ?> ;
    const labeljenisPenerima = document.getElementById('labelJenisPenerima');
    const jenisPenerimaSelect = document.getElementById('jenis-penerima');
    const labelPenerima = document.getElementById('labelPenerima');
    const penerima = document.getElementById('penerima');
    const labelEkspedisi = document.getElementById('clear-search');
    const ekspedisiSelect = document.getElementById('dropdown-input');
    const labelResi = document.getElementById('labelResi');
    const resi = document.getElementById('resi');
    const labelBukti1 = document.getElementById('labelBukti1');
    const labelBukti2 = document.getElementById('labelBukti2');
    const labelBukti3 = document.getElementById('labelBukti3');
    const file1 = document.getElementById('fileku1');
    const file2 = document.getElementById('fileku2');
    const file3 = document.getElementById('fileku3');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreview2 = document.getElementById('imagePreview2');
    const imagePreview3 = document.getElementById('imagePreview3');
    const diterima = document.getElementById('diterima');

    if (jenisPenerima === 'Driver') {
        labelJenisPenerima.style.display = 'block'; // Menampilkan Form Input
        jenisPenerimaSelect.style.display = 'block'; // Menampilkan Form Input
        jenisPenerimaSelect.setAttribute('required', 'true');
        diterima.disabled = false;

        //Membuat event listener saat select data
        jenisPenerimaSelect.addEventListener('change', function () {
            if (this.value === 'Customer') {
                labelPenerima.style.display = 'block'; // Menampilkan Form Input
                penerima.style.display = 'block'; // Menampilkan Form Input
                penerima.setAttribute('required', 'true'); // Membuat Atribut Required
                labelEkspedisi.style.display = 'none'; // Menyembunyikan Form Input
                ekspedisiSelect.style.display = 'none'; // Menyembunyikan Form Input
                ekspedisiSelect.value = ''; // Reset Value
                labelResi.style.display = 'none'; // Menyembunyikan Form Input
                resi.style.display = 'none'; // Menyembunyikan Form Input
                resi.value = ''; // Reset Value
                labelBukti1.style.display = 'block'; // Menampilkan form input
                labelBukti2.style.display = 'block'; // Menampilkan form input
                labelBukti3.style.display = 'block'; // Menampilkan form input
                file1.style.display = 'block'; // Menampilkan form input
                file1.setAttribute('required', 'true'); // Membuat Atribut Required
                file2.style.display = 'block'; // Menampilkan form input
                file3.style.display = 'block'; // Menampilkan form input
                imagePreview.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview"
                imagePreview2.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview2"
                imagePreview3.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview3"
                file1.value = ''; // Mengatur ulang nilai menjadi kosong
                file2.value = ''; // Mengatur ulang nilai menjadi kosong
                file3.value = ''; // Mengatur ulang nilai menjadi kosong
                imagePreview.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview"
                imagePreview2.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview2"
                imagePreview3.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview3"
                ekspedisiSelect.removeAttribute('required', 'true'); // Membuat Atribut Required
                resi.removeAttribute('required', 'true'); // Membuat Atribut Required
            } else if (this.value === 'Ekspedisi') {
                labelPenerima.style.display = 'none'; // Menyembunyikan Form Input
                penerima.style.display = 'none'; // Menyembunyikan Form Input
                penerima.value = ''; // Reset Value
                penerima.removeAttribute('required', 'true'); // Membuat Atribut Required
                labelEkspedisi.style.display = 'block'; // Menampilkan Form Input
                ekspedisiSelect.style.display = 'block'; // Menampilkan Form Input
                ekspedisiSelect.setAttribute('required', 'true'); // Membuat Atribut Required
                labelResi.style.display = 'block'; // Menampilkan Form Input
                resi.style.display = 'block'; // Menampilkan Form Input
                resi.setAttribute('required', 'true'); // Membuat Atribut Required
                labelBukti1.style.display = 'block'; // Menampilkan form input
                labelBukti2.style.display = 'block'; // Menampilkan form input
                labelBukti3.style.display = 'block'; // Menampilkan form input
                file1.style.display = 'block'; // Menampilkan form input
                file1.setAttribute('required', 'true'); // Membuat Atribut Required
                file2.style.display = 'block'; // Menampilkan form input
                file3.style.display = 'block'; // Menampilkan form input
                imagePreview.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview"
                imagePreview2.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview2"
                imagePreview3.style.display = 'block'; // Menampilkan konten di dalam elemen "imagePreview3"
            } else if (this.value === '') {
                labelPenerima.style.display = 'none'; // Menyembunyikan Form Input
                penerima.style.display = 'none'; // Menyembunyikan Form Input
                penerima.value = ''; // Reset Value
                labelEkspedisi.style.display = 'none'; // Menyembunyikan Form Input
                ekspedisiSelect.style.display = 'none'; // Menyembunyikan Form Input
                ekspedisiSelect.value = ''; // Reset Value
                labelResi.style.display = 'none'; // Menyembunyikan Form Input
                resi.style.display = 'none'; // Menyembunyikan Form Input
                resi.value = ''; // Reset Value
            }
        });

    } else if (jenisPenerima === 'Ekspedisi') {
        labelPenerima.style.display = 'block'; // Menampilkan Form Input
        penerima.style.display = 'block'; // Menampilkan Form Input
        penerima.setAttribute('required', 'true'); // Membuat Atribut Required
    } else {
        console.log("Nilai jenis Penerima tidak valid");
    }

    // membuat refresh halaman modal tanpa menutup modal dialog
    let isModalShown = false;
    // Refresh halaman modal
    if (isModalShown) {
        $('#Diterima').modal('hide'); // Menyembunyikan modal
        location.reload(); // Melakukan refresh halaman
        $('#Diterima').modal('show'); // Menampilkan modal kembali
    }

    // Mendapatkan tombol "Cancel" berdasarkan ID
    const cancelDriver = document.getElementById('cancelDriver');
    if (cancelDriver) {
        cancelDriver.addEventListener('click', function () {
            jenisPenerimaSelect.value = ''; // Mengatur ulang nilai menjadi kosong
            penerima.value = ''; // Mengatur ulang nilai menjadi kosong
            ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
            resi.value = ''; // Mengatur ulang nilai menjadi kosong
            file1.value = ''; // Mengatur ulang nilai menjadi kosong
            file2.value = ''; // Mengatur ulang nilai menjadi kosong
            file3.value = ''; // Mengatur ulang nilai menjadi kosong
            imagePreview.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview"
            imagePreview2.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview2"
            imagePreview3.innerHTML = ''; // Menghapus konten di dalam elemen "imagePreview3"
            labelPenerima.style.display = 'none'; // Menyembunyikan form input
            penerima.style.display = 'none'; // Menyembunyikan form input
            labelEkspedisi.style.display = 'none'; // Menyembunyikan form input
            ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
            labelResi.style.display = 'none'; // Menyembunyikan form input
            resi.style.display = 'none'; // Menyembunyikan form input
            labelBukti1.style.display = 'none'; // Menyembunyikan form input
            labelBukti2.style.display = 'none'; // Menyembunyikan form input
            labelBukti3.style.display = 'none'; // Menyembunyikan form input
            file1.style.display = 'none'; // Menyembunyikan form input
            file2.style.display = 'none'; // Menyembunyikan form input
            file3.style.display = 'none'; // Menyembunyikan form input
        });
    } else {
        console.log("Button Cancel Driver Sedang Aktif");
    }

    // Mendapatkan tombol "Cancel" berdasarkan ID
    const cancelEkspedisi = document.getElementById('cancelEkspedisi');
    if (cancelEkspedisi) {
        cancelEkspedisi.addEventListener('click', function () {
            penerima.value = ''; // Mengatur ulang nilai menjadi kosong
        });
    } else {
        console.log("Button Cancel Ekspedisi Sedang Aktif");
    }
</script>