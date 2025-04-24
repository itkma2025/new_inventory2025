// select lokasi
$(document).on("click", "#table2 tbody tr", function (e) {
  $("#id_lokasi").val($(this).data("id"));
  $("#nama_lokasi").val($(this).data("nama"));
  $("#no_lantai").val($(this).data("lantai"));
  $("#area").val($(this).data("area"));
  $("#no_rak").val($(this).data("rak"));
  $("#modalLokasi").modal("hide");
});
