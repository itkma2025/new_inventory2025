var inputElement = document.getElementById("numberOnly");
// Menambahkan event listener untuk memeriksa panjang input
inputElement.addEventListener("input", function (event) {
  // Menghapus karakter selain angka
  this.value = this.value.replace(/\D/g, "");

  // Memeriksa panjang input
  //   if (this.value.length < 9) {
  //     inputElement.setCustomValidity("Nomor telepon harus minimal 9 angka.");
  //   } else {
  //     inputElement.setCustomValidity("");
  //   }

  // Memastikan panjang input tidak melebihi 13 karakter
  if (this.value.length > 30) {
    this.value = this.value.slice(0, 30);
  }
});
