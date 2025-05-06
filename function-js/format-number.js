function formatNumber(inputElement) {
    // Periksa apakah inputElement didefinisikan dan merupakan elemen input
    if (!inputElement || !(inputElement instanceof HTMLInputElement)) {
        return; // Jika inputElement tidak valid, keluar dari fungsi
    }

    let inputVal = inputElement.value;

    // Hapus semua karakter non-digit dari input
    inputVal = inputVal.replace(/[^0-9]/g, '');

    // Konversi input menjadi angka
    let numValue = parseInt(inputVal) || 0;

    // Batas maksimal 1 triliyun
    const maxValue = 1000000000000;

    // Jika nilai lebih dari 1 triliyun, set ke maxValue
    if (numValue > maxValue) {
        numValue = maxValue;
    }

    // Format angka dengan pemisah titik
    let formattedValue = numValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    // Masukkan nilai yang diformat kembali ke dalam input field
    inputElement.value = formattedValue;
}
