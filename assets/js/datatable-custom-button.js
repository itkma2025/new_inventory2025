// Custom sorting function for invoice numbers with specific format
$.fn.dataTable.ext.type.order["invoice-number-pre"] = function (data) {
  // Regular expression to match the initial part of the invoice number
  let regex = /^(\d+)/;

  // Extract the number part
  let match = data.match(regex);
  if (match) {
    let number = parseInt(match[1], 10); // Convert the extracted part to an integer

    // Return the number as the sortable value
    return number;
  } else {
    // Return a default value for data that doesn't match the format
    return 0;
  }
};

// Tambahkan ini sebelum inisialisasi DataTables
$.fn.dataTable.ext.type.order["date-ddmmyyyy-pre"] = function (data) {
  // Misalkan format tanggal adalah dd/mm/yyyy
  let dateParts = data.split("/");
  let day = parseInt(dateParts[0], 10);
  let month = parseInt(dateParts[1], 10);
  let year = parseInt(dateParts[2], 10);

  // Kembalikan tanggal dalam format yyyy-mm-dd untuk sorting
  return (
    year +
    "-" +
    (month < 10 ? "0" : "") +
    month +
    "-" +
    (day < 10 ? "0" : "") +
    day
  );
};

// Custom sorting function for numbers with dot as thousand separator
$.fn.dataTable.ext.type.order["dot-separated-number-pre"] = function (data) {
  // Hilangkan semua titik (.) dan konversi ke angka
  let cleanedData = data.replace(/\./g, ""); // Remove dots
  return parseFloat(cleanedData) || 0; // Parse to float or return 0 if invalid
};



$(document).ready(function () {
  // Inisialisasi DataTable
  const table = new DataTable("#tableExportNew", {
    lengthChange: false,
    autoWidth: false,
    paging: true,
    dom: '<"top">rt<"bottom"lp><"clear">',
    columnDefs: [
      { targets: 0, type: "invoice-number" }, // Sorting invoice
      { targets: 1, type: "date-ddmmyyyy" },  // Sorting tanggal
      { targets: [5, 6, 7, 8], type: "dot-separated-number" }, // Sorting angka dengan pemisah titik
    ],
    buttons: [
      {
        extend: "print",
        title: "",
        exportOptions: {
          columns: ":not(:last-child)",
          format: {
            body: function (data, row, column) {
              return column === 0
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 3
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 5
                ? '<div style="text-align:right;">' + data + "</div>"
                : column === 6
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 7
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 8
                ? '<div style="text-align:center;">' + data + "</div>"
                : data;
            },
          },
        },
      },
      {
        extend: "excelHtml5",
        title: "",
        exportOptions: {
          columns: ":not(:last-child)",
        },
        customize: function (xlsx) {
          var sheet = xlsx.xl.worksheets["sheet1.xml"];
          $("row c[r]", sheet).each(function () {
            var cell = $(this);
            var text = cell.text();
            if (text.match(/^\d+(\.\d+)?$/)) {
              cell.attr("t", "inlineStr");
              cell.append("<is><t>" + text + "</t></is>");
              cell.children("v").remove();
            }
          });
        },
      },
      {
        extend: "csvHtml5",
        title: "",
        exportOptions: {
          columns: ":not(:last-child)",
        },
      },
      {
        extend: "pdfHtml5",
        title: "",
        exportOptions: {
          columns: ":not(:last-child)",
          format: {
            body: function (data, row, column) {
              return column === 0
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 3
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 5
                ? '<div style="text-align:right;">' + data + "</div>"
                : column === 6
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 7
                ? '<div style="text-align:center;">' + data + "</div>"
                : column === 8
                ? '<div style="text-align:center;">' + data + "</div>"
                : data;
            },
          },
        },
      },
    ],
  });

  // Attach DataTables buttons to the custom buttons
  $("#btnPrint").on("click", function () {
    table.button(0).trigger();
  });

  $("#btnExcel").on("click", function () {
    table.button(1).trigger();
  });

  $("#btnCsv").on("click", function () {
    table.button(2).trigger();
  });

  $("#btnPdf").on("click", function () {
    table.button(3).trigger();
  });

  // Function to update total data information
  function updateTotalData() {
    const info = table.page.info();
    $("#totalData").html(
      `Showing ${info.start + 1} to ${info.end} of ${
        info.recordsDisplay
      } entries (filtered from ${info.recordsTotal} total entries)`
    );
  }

  // Call updateTotalData initially
  updateTotalData();

  // Search functionality
  $("#cari-data").on("input", function () {
    table.search($(this).val()).draw();
    if ($(this).val().length > 0) {
      $("#resetButton").show();
    } else {
      $("#resetButton").hide();
    }
  });

  $("#resetButton").on("click", function () {
    $("#cari-data").val(""); // Clear input text
    $("#cari-data").focus(); // Optional: focus back on the input
    $(this).hide(); // Hide reset button after clicking
    table.search("").draw(); // Clear the search
  });

  table.on("draw.dt", function () {
    // Mengatur ulang nomor urut setelah menggambar ulang tabel
    table
      .column(0, { search: "applied", order: "applied" })
      .nodes()
      .each(function (cell, i) {
        cell.innerHTML = i + 1;
      });

    // Update total data information
    updateTotalData();

    // Update pagination after draw
    updatePagination();
  });

  // Custom pagination logic
  const paginate = (page) => {
    const info = table.page.info();
    if (page < 0 || page >= info.pages) return;
    table.page(page).draw("page");
  };

  const updatePagination = () => {
    const info = table.page.info();
    const maxPagesToShow = 5; // Number of pages to show at a time
    let startPage = Math.floor(info.page / maxPagesToShow) * maxPagesToShow;
    let endPage = Math.min(startPage + maxPagesToShow, info.pages);

    // Ensure that the next pages are displayed correctly
    if (info.page >= endPage - 1) {
      startPage = Math.max(endPage - maxPagesToShow, 0);
      endPage = Math.min(startPage + maxPagesToShow, info.pages);
    }

    $("#customPagination").html(`
  <li class="page-item ${info.page === 0 ? "disabled" : ""}">
    <a class="page-link" href="#" data-page="prev">&laquo;</a>
  </li>
  ${Array.from(
    { length: endPage - startPage },
    (v, i) => `
    <li class="page-item ${info.page === startPage + i ? "active" : ""}
          <li class="page-item ${info.page === startPage + i ? "active" : ""}">
        <a class="page-link" href="#" data-page="${startPage + i}">${
      startPage + i + 1
    }</a>
      </li>
    `
  ).join("")}
    <li class="page-item ${info.page === info.pages - 1 ? "disabled" : ""}">
      <a class="page-link" href="#" data-page="next">&raquo;</a>
    </li>
    `);

    // Update pagination controls
    $("#customPagination .page-link").on("click", function (e) {
      e.preventDefault();
      const page = $(this).data("page");

      if (page === "prev") {
        paginate(info.page - 1);
      } else if (page === "next") {
        paginate(info.page + 1);
      } else {
        paginate(page);
      }
    });
  };

  // Initialize pagination after the table has been drawn
  table.on("draw", updatePagination);

  // Call updatePagination initially
  updatePagination();
});
