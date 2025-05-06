// Select Customer
$(document).ready(function(){
  // $('#select-cs').DataTable();
  
  $(document).on('click', '#data-cs', function (e) {
    document.getElementById("pelanggan").value = $(this).attr('pelanggan');
    document.getElementById("alamat").value = $(this).attr('alamat');
    $('#modal-select-cs').modal('hide');
  }); 
});

// select lokasi
$(document).on('click', '#table2 tbody tr', function (e) {
  $('#id_lokasi').val($(this).data('id'));
  $('#nama_lokasi').val($(this).data('nama'));
  $('#no_lantai').val($(this).data('lantai'));
  $('#area').val($(this).data('area'));
  $('#no_rak').val($(this).data('rak'));
  $('#modalLokasi').modal('hide');
});


// select Kategori Produk
$(document).on('click', '#table3 tbody tr', function (e) {
  $('#idKatProduk').val($(this).data('idkat'));
  $('#namaKatProduk').val($(this).data('namakatprod'));
  $('#modalkatprod').modal('hide');
});