$(function () {
  $("#table1")
    .DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      buttons: ["csv", "excel", "pdf", "print"],
    })
    .buttons()
    .container()
    .appendTo("#table1_wrapper .col-md-6:eq(0)");
});

$(document).ready(function () {
  $("#table2").DataTable({
    responsive: true,
    lengthChange: false,
    autoWidth: false,
  });
});

$(document).ready(function () {
  $("#table3").DataTable({
    responsive: true,
    lengthChange: false,
    autoWidth: false,
  });
});

$(document).ready(function () {
  $("#table4").DataTable({
    responsive: true,
    lengthChange: false,
    autoWidth: false,
  });
});

$(function () {
  $("#table5")
    .DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      buttons: ["csv", "excel", "pdf", "print"],
    })
    .buttons()
    .container()
    .appendTo("#table1_wrapper .col-md-6:eq(0)");

  $("#example2").DataTable({
    paging: true,
    lengthChange: false,
    searching: false,
    ordering: true,
    info: true,
    autoWidth: false,
    responsive: true,
  });
});
