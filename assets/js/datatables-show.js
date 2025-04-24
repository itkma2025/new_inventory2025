$(document).ready(function () {
  const table = new DataTable("#tableExport", {
    lengthChange: false,
    autoWidth: false,
    layout: {
      topStart: {
        buttons: [
          {
            extend: "print",
            title: "",
            text: '<i class="bi bi-printer-fill"></i>',
            titleAttr: "Print",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
          {
            extend: "excelHtml5",
            title: "",
            text: '<i class="bi bi-file-earmark-excel"></i>',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
            customize: function (xlsx) {
              var sheet = xlsx.xl.worksheets["sheet1.xml"];
              $("row c[r]", sheet).each(function () {
                var cell = $(this);
                var text = cell.text();
                if (text.match(/^\d+(\.\d+)?$/)) {
                  // Memeriksa apakah teks dalam sel adalah angka atau angka desimal
                  cell.attr("t", "inlineStr"); // Mengatur tipe data sebagai inlineStr untuk teks
                  cell.append("<is><t>" + text + "</t></is>");
                  cell.children("v").remove(); // Menghapus nilai lama
                }
              });
            },
          },
          {
            extend: "csvHtml5",
            title: "",
            text: '<i class="bi bi-file-text"></i>',
            titleAttr: "CSV",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
          {
            extend: "pdfHtml5",
            title: "",
            text: '<i class="bi bi-file-pdf"></i>',
            titleAttr: "PDF",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
        ],
      },
    },
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });

  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#table1", {
    lengthChange: false,
    responsive: true,
    autoWidth: true,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#tableKartuStock", {
    ordering: false, // Menonaktifkan fitur sorting
    layout: {
      topStart: {
        buttons: [
          {
            extend: "print",
            title: "",
            text: '<i class="bi bi-printer-fill"></i>',
            titleAttr: "Print",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
          {
            extend: "excelHtml5",
            title: "",
            text: '<i class="bi bi-file-earmark-excel"></i>',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
          {
            extend: "excelHtml5",
            title: "",
            text: '<i class="bi bi-file-earmark-excel"></i>',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
            customize: function (xlsx) {
              var sheet = xlsx.xl.worksheets["sheet1.xml"];
              $("row c[r]", sheet).each(function () {
                var cell = $(this);
                var text = cell.text();
                if (text.match(/^\d+(\.\d+)?$/)) {
                  // Memeriksa apakah teks dalam sel adalah angka atau angka desimal
                  cell.attr("t", "inlineStr"); // Mengatur tipe data sebagai inlineStr untuk teks
                  cell.append("<is><t>" + text + "</t></is>");
                  cell.children("v").remove(); // Menghapus nilai lama
                }
              });
            },
          },
          {
            extend: "pdfHtml5",
            title: "",
            text: '<i class="bi bi-file-pdf"></i>',
            titleAttr: "PDF",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
        ],
      },
    },
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
});

$(document).ready(function () {
  const table = new DataTable("#tableExport2", {
    layout: {
      topStart: {
        buttons: [
          {
            extend: "print",
            title: "",
            text: '<i class="bi bi-printer-fill"></i>',
            titleAttr: "Print",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
          {
            extend: "excelHtml5",
            title: "",
            text: '<i class="bi bi-file-earmark-excel"></i>',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
            customize: function (xlsx) {
              var sheet = xlsx.xl.worksheets["sheet1.xml"];
              $("row c[r]", sheet).each(function () {
                var cell = $(this);
                var text = cell.text();
                if (text.match(/^\d+(\.\d+)?$/)) {
                  // Memeriksa apakah teks dalam sel adalah angka atau angka desimal
                  cell.attr("t", "inlineStr"); // Mengatur tipe data sebagai inlineStr untuk teks
                  cell.append("<is><t>" + text + "</t></is>");
                  cell.children("v").remove(); // Menghapus nilai lama
                }
              });
            },
          },
          {
            extend: "csvHtml5",
            title: "",
            text: '<i class="bi bi-file-text"></i>',
            titleAttr: "CSV",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
          {
            extend: "pdfHtml5",
            title: "",
            text: '<i class="bi bi-file-pdf"></i>',
            titleAttr: "PDF",
            exportOptions: {
              columns: ":not(:last-child)", // Mengabaikan kolom terakhir
            },
          },
        ],
      },
    },
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });

  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#tableExportNoAction", {
    layout: {
      topStart: {
        buttons: [
          {
            extend: "print",
            title: "",
            text: '<i class="bi bi-printer-fill"></i>',
            titleAttr: "Print",
          },
          {
            extend: "excelHtml5",
            title: "",
            text: '<i class="bi bi-file-earmark-excel"></i>',
            titleAttr: "Excel",
          },
          {
            extend: "csvHtml5",
            title: "",
            text: '<i class="bi bi-file-text"></i>',
            titleAttr: "CSV",
          },
          {
            extend: "pdfHtml5",
            title: "",
            text: '<i class="bi bi-file-pdf"></i>',
            titleAttr: "PDF",
          },
        ],
      },
    },
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });

  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#tableSiapKirim", {
    lengthChange: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(1, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#table2", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#table3", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#table4", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

$(document).ready(function () {
  const table = new DataTable("#table5", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
});

function filter_nonppn() {
  const table = new DataTable("#filter_nonppn", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
}

function filter_ppn() {
  const table = new DataTable("#filter_ppn", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
}

function filter_bum() {
  const table = new DataTable("#filter_bum", {
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
  });
  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });
  });
}
