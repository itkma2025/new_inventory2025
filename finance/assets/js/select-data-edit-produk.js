var id_lokasi_original = $('#id_lokasi').val();

function checkButtonStatus() {
  if ($('#id_lokasi').val() !== id_lokasi_original) {
    $('#ubahData').removeAttr('disabled');
  } else {
    $('#ubahData').attr('disabled', true);
  }
}

$(document).on('click', '#table2 tbody tr', function (e) {
  var id_lokasi_before = $('#id_lokasi').val(); // nilai sebelumnya
  console.log("ID Lokasi (Before): " + id_lokasi_before);
  
  var id_lokasi = $(this).data('id');
  console.log("ID Lokasi (After): " + id_lokasi);
  
  if (id_lokasi_before !== id_lokasi) { // jika data berbeda
    $('#id_lokasi').val(id_lokasi);
    $('#nama_lokasi').val($(this).data('nama'));
    $('#no_lantai').val($(this).data('lantai'));
    $('#area').val($(this).data('area'));
    $('#no_rak').val($(this).data('rak'));
    $('#modal2').modal('hide');
    checkButtonStatus();
  } else { // jika data sama
    $('#ubahData').attr('disabled', true); // menambahkan atribut disabled pada button
    return false;
  }
});
$(document).on('keyup change', '#id_lokasi, #nama_lokasi, #no_lantai, #area, #no_rak', function (e) {
  checkButtonStatus();
});



  
  





// select Kategori Produk
// $(document).on('click', '#table3 tbody tr', function (e) {
//   $('#idKatProduk').val($(this).data('idkat'));
//   $('#namaKatProduk').val($(this).data('namakatprod'));
//   $('#modalkatprod').modal('hide');
// });