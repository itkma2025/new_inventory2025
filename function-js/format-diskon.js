// Fungsi untuk memformat diskon
function formatDiscount(inputElement) {
    let value = inputElement.value;

    // Menghapus karakter yang tidak berupa angka atau titik (mengizinkan satu titik sebagai desimal)
    value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1'); // Mengizinkan hanya satu titik

    // Membatasi satu angka di belakang titik desimal
    const parts = value.split('.');
    if (parts.length > 1) {
        // Jika ada bagian desimal, ambil hanya satu angka setelah titik
        value = parts[0] + '.' + parts[1].charAt(0);
    }

    // Jika lebih dari 100, set nilai menjadi 100
    if (parseFloat(value) > 100) {
        value = '100';
    }

    // Set nilai kembali ke input
    inputElement.value = value;
}