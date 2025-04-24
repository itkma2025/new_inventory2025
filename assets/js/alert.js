var notifikasi = $(".info-data").data("infodata");

if (
  notifikasi == "Disimpan" ||
  notifikasi == "Direfund" ||
  notifikasi == "Dibatalkan" ||
  notifikasi == "Dihapus" ||
  notifikasi == "Diupdate" ||
  notifikasi == "Ditolak" ||
  notifikasi == "Dicancel"
) {
  Swal.fire({
    icon: "success",
    title: "Sukses",
    text: "Data Berhasil " + notifikasi,
    allowOutsideClick: false, 
  });
} else if (
  notifikasi == "Data Gagal Disimpan" ||
  notifikasi == "Data Gagal Dihapus" ||
  notifikasi == "Data Gagal Diupdate" ||
  notifikasi == "Kode Barang Sudah Ada" ||
  notifikasi == "Nama customer sudah ada" ||
  notifikasi == "Nama supplier sudah ada" ||
  notifikasi == "Nama kategori sudah ada" ||
  notifikasi == "No faktur sudah ada" ||
  notifikasi == "Nama user sudah di gunakan" ||
  notifikasi == "Nama role sudah ada" ||
  notifikasi == "Data sudah ada" ||
  notifikasi == "No SPK sudah ada" ||
  notifikasi == "Nama merk sudah ada" ||
  notifikasi == "Nomer rekening sudah ada" ||
  notifikasi == "Token not found" ||
  notifikasi == "Token expired" ||
  notifikasi == "Gagal Validasi Data" ||
  notifikasi == "Jenis file tidak didukung" ||
  notifikasi == "Merk Tidak Boleh Sama"
) {
  Swal.fire({
    icon: "error",
    title: "GAGAL",
    text: "" + notifikasi,
    allowOutsideClick: false, 
  });
} else if (notifikasi == "Data Belum Dipilih") {
  Swal.fire({
    icon: "info",
    title: "Gagal",
    text: "" + notifikasi,
    allowOutsideClick: false, 
  });
} else if (
  notifikasi == "Tidak Ada Perubahan Data" ||
  notifikasi == "No SPK berhasil dibuat" ||
  notifikasi == "Invoice berhasil dibuat" ||
  notifikasi == "No tagihan berhasil dibuat" ||
  notifikasi == "No pembayaran berhasil dibuat" ||
  notifikasi == "No Izin Edar Berhasil Diubah"
) {
  Swal.fire({
    icon: "success",
    title: "Sukses",
    text: "" + notifikasi,
    allowOutsideClick: false, 
  });
} else if (notifikasi == "Silahkan Ulangi Kembali") {
  Swal.fire({
    imageUrl: "img/error-icon.png",
    imageWidth: 100,
    imageHeight: 80,
    imageAlt: "Custom image",
    title: "Terjadi Kesalahan Pada Server",
    text: "" + notifikasi,
    allowOutsideClick: false, 
  });
} else if (notifikasi == "Dilogout") {
  Swal.fire({
    icon: "success",
    title: "Sukses",
    text: "User Berhasil " + notifikasi,
    allowOutsideClick: false, 
  });
} else if (notifikasi == "Gagal Dilogout") {
  Swal.fire({
    icon: "success",
    title: "Sukses",
    text: "User " + notifikasi,
    allowOutsideClick: false, 
  });
}

$(".delete-data").on("click", function (e) {
  e.preventDefault();
  var getLink = $(this).attr("href");

  Swal.fire({
    title: "Anda yakin?",
    text: "Data akan dihapus permanen",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#EB5406",
    cancelButtonColor: "#437C17",
    confirmButtonText: "Ya, Hapus Data",
    allowOutsideClick: false, 
  }).then((result) => {
    if (result.value) {
      window.location.href = getLink;
    }
  });
});

$(".update-data").on("click", function (e) {
  e.preventDefault();
  var getLink = $(this).attr("href");

  Swal.fire({
    title: "Anda yakin?",
    text: "Jenis pengiriman akan di ubah",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#1E90FF",
    cancelButtonColor: "#437C17",
    confirmButtonText: "Ya, Ubah Pengiriman",
    allowOutsideClick: false, 
  }).then((result) => {
    if (result.value) {
      window.location.href = getLink;
    }
  });
});

$(".cancel-data").on("click", function (e) {
  e.preventDefault();
  var form = $(this).closest("form");
  var getAction = form.attr("action");
  var cancelValue = form.find('input[name="id_spk"]').val();

  Swal.fire({
    title: "Anda Yakin Cancel Transaksi Ini ?",
    text: "Transaksi Ini Akan Dicancel Permanen",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#EB5406",
    cancelButtonColor: "#437C17",
    confirmButtonText: "Ya, Cancel Transaksi",
    allowOutsideClick: false, 
  }).then((result) => {
    if (result.value) {
      form.attr("action", getAction);
      form.submit();
    }
  });
});



