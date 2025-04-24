//Membuat realtime permintaan refresh browser jika CSRF token sudah expired
// Fungsi untuk mendapatkan tanggal hari ini dengan format yang diinginkan
function getCurrentDateTime() {
  var now = new Date();

  var year = now.getFullYear();
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var day = ("0" + now.getDate()).slice(-2);

  var hours = ("0" + now.getHours()).slice(-2);
  var minutes = ("0" + now.getMinutes()).slice(-2);
  var seconds = ("0" + now.getSeconds()).slice(-2);

  var formattedDateTime =
    year +
    "-" +
    month +
    "-" +
    day +
    " " +
    hours +
    ":" +
    minutes +
    ":" +
    seconds;

  return formattedDateTime;
}

// Fungsi untuk memeriksa waktu kedaluwarsa token
function checkTokenExpiration() {
  var csrfTokenExpired = document
    .getElementById("expiryDate")
    .getAttribute("data-expiry");
  var currentDateTime = getCurrentDateTime();
  // console.log(currentDateTime);
  // console.log(csrfTokenExpired);
  if (currentDateTime >= csrfTokenExpired) {
    // console.log("Expired");
    clearInterval(intervalID); // Hentikan interval saat token kedaluwarsa
    // Tampilkan sweet alert
    // Tampilkan sweet alert
    Swal.fire({
      title: "Token Expired",
      text: "Your session has expired. Please refresh the page.",
      icon: "warning",
      confirmButtonText: "OK",
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.reload(); // Refresh halaman saat tombol "OK" diklik
      }
    });
  }
}
// Menjalankan fungsi checkTokenExpiration() setiap 1 menit
var intervalID = setInterval(checkTokenExpiration, 1000); // 1000 milidetik = 1 detik
