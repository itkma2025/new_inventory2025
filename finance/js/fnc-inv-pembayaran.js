// Checkbox Create Payment
const checkboxes = document.querySelectorAll(
  'input[type="checkbox"][name="id_inv_pembelian[]"]'
);
const createPayment = document.getElementById("createPayment");

function updateButtonState() {
  const checkedCheckboxes = Array.from(checkboxes).filter(
    (checkbox) => checkbox.checked
  );
  const selectedCustomers = new Set(
    checkedCheckboxes.map((checkbox) => checkbox.getAttribute("data-supplier"))
  );
  const selectedJenis = new Set(
    checkedCheckboxes.map((checkbox) => checkbox.getAttribute("data-jenis"))
  );

  // console.log("Data Supplier yang Dipilih:");
  // selectedCustomers.forEach(customer => {
  //     console.log(customer);
  // });

  if (checkedCheckboxes.length <= 20 && selectedCustomers.size === 1) {
    // Check if "nonppn" and "bum" are selected together, and "ppn" is not selected
    const isNonPPNSelected = selectedJenis.has("Non PPN");
    const isPPNSelected = selectedJenis.has("PPN");

    if (isNonPPNSelected && !isPPNSelected) {
      createPayment.disabled = false;
    } else if (isPPNSelected && !isNonPPNSelected) {
      createPayment.disabled = false;
    } else {
      createPayment.disabled = true;
    }
  } else {
    createPayment.disabled = true;
  }
}

function checkInitialCheckbox() {
  checkboxes.forEach((checkbox) => {
    if (checkbox.getAttribute("data-supplier") === "") {
      checkbox.checked = true;
    }
  });
  updateButtonState();
}

checkboxes.forEach((checkbox) => {
  checkbox.addEventListener("change", function () {
    // Limit selection to a maximum of 20 checkboxes
    const checkedCount = Array.from(checkboxes).filter(
      (checkbox) => checkbox.checked
    ).length;
    if (checkedCount > 20) {
      this.checked = false;
    }

    updateButtonState();
  });
});

checkInitialCheckbox();

// Update input value
function updateInputValue(checkbox) {
  var inputValue = document.getElementById("idInv").value;
  var checkboxValue = encodeURIComponent(checkbox.value);
  var maxData = 20;

  // Cek apakah checkbox dicentang atau tidak
  if (checkbox.checked) {
    // Jika dicentang, tambahkan nilai checkbox ke nilai input jika belum mencapai batas
    var values = inputValue.split(",");
    if (values.length < maxData) {
      if (inputValue.length > 0) {
        inputValue += "," + checkboxValue;
      } else {
        inputValue = checkboxValue;
      }
    } else {
      // Jika sudah mencapai batas, batasi pemilihan checkbox
      checkbox.checked = false;
      Swal.fire({
        title: "<strong>Anda Sudah Mencapai Batas Maksimum Pemilihan</strong>",
        icon: "info",
        html: "Anda hanya dapat memilih maksimal 20 data.",
        confirmButtonText: "OK",
      });
    }
  } else {
    // Jika tidak dicentang, hapus nilai checkbox dari nilai input
    inputValue = inputValue
      .split(",")
      .filter(function (value) {
        return value !== checkboxValue;
      })
      .join(",");
  }

  // Perbarui nilai input
  document.getElementById("idInv").value = inputValue;
}

// Function untuk submit form create payment
function submitForm(action) {
  document.getElementById("invoiceForm").action = action;
  document.getElementById("invoiceForm").submit();
}
// End Checkbox Create Payment

// Datatables
$(document).ready(function () {
  // Inisialisasi DataTable dan simpan objek DataTable dalam variabel
  var table = $("#tableInv").DataTable({
    lengthChange: false,
    ordering: false,
    autoWidth: false,
    language: {
      searchPlaceholder: "Cari data",
      search: "",
    },
    layout: {
      topStart: {
        buttons: [
          {
            extend: "print",
            title: "",
            text: '<i class="bi bi-printer-fill"></i>',
            titleAttr: "Print",
            exportOptions: {
              columns: ":not(:nth-child(1), :nth-child(2), :last-child)", // Mengabaikan 1, 2 dan kolom terakhir
            },
          },
          {
            extend: "excelHtml5",
            title: "",
            text: '<i class="bi bi-file-earmark-excel"></i>',
            titleAttr: "Excel",
            exportOptions: {
              columns: ":not(:nth-child(1), :nth-child(2), :last-child)", // Mengabaikan 1, 2 dan kolom terakhir
            },
          },
          {
            extend: "csvHtml5",
            title: "",
            text: '<i class="bi bi-file-text"></i>',
            titleAttr: "CSV",
            exportOptions: {
              columns: ":not(:nth-child(1), :nth-child(2), :last-child)", // Mengabaikan 1, 2 dan kolom terakhir
            },
          },
          {
            extend: "pdfHtml5",
            title: "",
            text: '<i class="bi bi-file-pdf"></i>',
            titleAttr: "PDF",
            exportOptions: {
              ccolumns: ":not(:nth-child(1), :nth-child(2), :last-child)", // Mengabaikan 1, 2 dan kolom terakhir
            },
          },
        ],
      },
    },
  });
  // // Tempatkan tombol eksport di dalam div dengan id exportData
  // $('#exportData').html($('.dt-buttons'));
  // // Tangkap perubahan input pada elemen pencarian
  // $('#searchInput').on('keyup', function () {
  //     // Terapkan nilai pencarian ke DataTables
  //     table.search(this.value).draw();
  // });
});
//  End Datatables

// Filter jenis Invoice
$(document).ready(function () {
  var table = $("#tableInv").DataTable();

  // Gunakan ekstensi DataTables bawaan untuk menyaring baris berdasarkan nilai pada kolom "Status Tagihan"
  $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
    var selectedItem = $("#filterJenisInv").val();
    var statusTagihan = data[4]; // Gunakan indeks kolom yang benar (indeks dimulai dari 0)

    console.log("Selected item:", selectedItem);
    console.log("Status tagihan:", statusTagihan);

    if (selectedItem === "all") {
      return true; // Tampilkan semua baris jika pilihan adalah "Semua"
    } else if (selectedItem === "Non PPN" && statusTagihan == "Non PPN") {
      return true; // Tampilkan baris dengan status "Sudah Ditagih" jika status tagihan berisi "BILL"
    } else if (selectedItem === "PPN" && statusTagihan === "PPN") {
      return true; // Tampilkan baris dengan status "Belum Dibuat" jika status tagihan adalah "Belum Dibuat"
    }
    return false; // Sembunyikan baris lainnya
  });

  // Atur event change pada dropdown filter untuk menggambar ulang tabel setiap kali pengguna memilih nilai baru
  $("#filterJenisInv").change(function (e) {
    table.draw();
  });

  table.draw(); // Gambar tabel pada saat memuat halaman
});
// End filter Jenis Invoice
