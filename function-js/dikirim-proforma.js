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

const jenisPengirimanSelect = document.getElementById('jenis-pengiriman');
const pengirimSelect = document.getElementById('pengirim');
const labelDriver = document.getElementById('labelDriver');
const ekspedisiSelect = document.getElementById('ekspedisi');
const ekspedisiSelectize = document.getElementById('pilihEkspedisi');
const labelDikirimOleh = document.getElementById('labelDikirimOleh');
const dikirimOleh = document.getElementById('dikirim_oleh');
const pjLabel = document.getElementById('labelPj');
const penanggungJawab = document.getElementById('penanggung_jawab');
const divKernet = document.getElementById('divKernet');
let isModalShown = false;

jenisPengirimanSelect.addEventListener('change', function() {
    if (this.value === 'Driver') {
        labelDriver.style.display = 'block'; // Menampilkan form input
        pengirimSelect.style.display = 'block'; // Menampilkan form input
        pengirimSelect.setAttribute('required', 'true');
        ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
        ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
        ekspedisiSelectize.removeAttribute('required');
        ekspedisiSelectize.classList.remove('selectize-js');
        labelDikirimOleh.style.display = 'none';
        dikirimOleh.style.display = 'none';
        dikirimOleh.value = '';
        dikirimOleh.removeAttribute('required');
        pjLabel.style.display = 'none';
        penanggungJawab.style.display = 'none';
        penanggungJawab.removeAttribute('required');
        penanggungJawab.value = '';
        dikirim.disabled = false;
        divKernet.style.display = 'block';
    } else if (this.value === 'Ekspedisi') {
        pengirimSelect.value = ''; // Mengatur ulang nilai menjadi kosong
        labelDriver.style.display = 'none'; // Menyembunyikan form input
        pengirimSelect.style.display = 'none'; // Menyembunyikan form input
        pengirimSelect.removeAttribute('required');
        ekspedisiSelect.style.display = 'block'; // Menampilkan form input
        ekspedisiSelectize.classList.add('selectize');
        ekspedisiSelectize.classList.add('selectize-js');
        ekspedisiSelectize.required = true;
        labelDikirimOleh.style.display = 'block';
        dikirimOleh.style.display = 'block';
        dikirimOleh.setAttribute('required', 'true');
        labelPj.style.display = 'block';
        penanggungJawab.style.display = 'block';
        penanggungJawab.setAttribute('required' , 'true');
        dikirim.disabled = false;       
    } else if (this.value === 'Diambil Langsung') {
        labelDriver.style.display = 'none'; // Menampilkan form input
        pengirimSelect.style.display = 'none'; // Menampilkan form input
        pengirimSelect.removeAttribute('required');
        ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
        ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
        ekspedisiSelectize.removeAttribute('required');
        ekspedisiSelectize.classList.remove('selectize-js');
        labelDikirimOleh.style.display = 'none';
        dikirimOleh.style.display = 'none';
        dikirimOleh.value = '';
        dikirimOleh.removeAttribute('required');
        pjLabel.style.display = 'none';
        penanggungJawab.style.display = 'none';
        penanggungJawab.removeAttribute('required');
        penanggungJawab.value = '';
        dikirim.disabled = false;
    } else if (this.value === '') {
        ekspedisiSelectize.removeAttribute('required');
        ekspedisiSelectize.classList.remove('selectize-js');
        pengirimSelect.value = ''; // Mengatur ulang nilai menjadi kosong
        ekspedisiSelectize.value = ''; // Mengatur ulang nilai menjadi kosong
        dikirimOleh.value = '';
        penanggungJawab.value = '';
        labelDriver.style.display = 'none'; // Menyembunyikan form input
        pengirimSelect.style.display = 'none'; // Menyembunyikan form input
        ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
        labelDikirimOleh.style.display = 'none' // Menyembunyikan form input
        labelPj.style.display = 'none' // Menyembunyikan form input
        file1.style.display = 'none'; // Menyembunyikan form input
        file2.style.display = 'none'; // Menyembunyikan form input
        file3.style.display = 'none'; // Menyembunyikan form input
        pengirimSelect.style.display = 'none'; // Menyembunyikan form input
        dikirimOleh.style.display = 'none';
        penanggungJawab.style.display = 'none';
        dikirim.disabled = true;
        document.getElementById('form-kirim').reset();
    }
    dikirim.addEventListener('shown.bs.modal', function() {
        dikirim.disabled = true;
    });
    // Refresh halaman modal
    if (isModalShown) {
        $('#Dikirim').modal('hide'); // Menyembunyikan modal
        location.reload(); // Melakukan refresh halaman
        $('#Dikirim').modal('show')
    }

    // Mendapatkan tombol "Cancel" berdasarkan ID
    const cancelButton = document.getElementById('cancelDikirim');

    // Fungsi untuk mengatur ulang input teks dan tombol
    // Event listener saat tombol "Cancel" ditekan
    cancelButton.addEventListener('click', function() {
        dikirimOleh.value = '';
        penanggungJawab.value = '';
        jenisPengirimanSelect.value = '';
        pengirimSelect.value = ''; // Mengatur ulang nilai menjadi kosong
        ekspedisiSelect.value = ''; // Mengatur ulang nilai menjadi kosong
        ekspedisiSelect.style.display = 'none'; // Menyembunyikan form input
        labelDriver.style.display = 'none'; // Menyembunyikan form input
        pengirimSelect.style.display = 'none'; // Menyembunyikan form input
        labelDikirimOleh.style.display = 'none';
        dikirimOleh.style.display = 'none';
        pjLabel.style.display = 'none';
        penanggungJawab.style.display = 'none';
        dikirim.disabled = true;
    });
});