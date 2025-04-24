// select Kategori Produk
$(document).on("click", "#table3 tbody tr", function (e) {
  $("#idKatProduk").val($(this).data("idkat"));
  $("#namaKatProduk").val($(this).data("namakatprod"));
  $("#modalkatprod").modal("hide");
});
