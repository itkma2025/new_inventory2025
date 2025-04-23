// Untuk menampilkan data notifikasi dengan ajax
$(document).ready(function () {
  loadNotifications(); // Memuat notifikasi saat halaman dimuat

  function loadNotifications() {
    // Memuat notifikasi untuk "Today"
    $.ajax({
      url: "ajax/load-notifikasi-today.php",
      type: "GET",
      dataType: "json",
      success: function (response) {
        // Pastikan respons adalah objek yang valid dan memiliki properti yang diharapkan
        if (
          response &&
          response.notifications !== undefined &&
          response.totalData !== undefined &&
          response.lastRowNumber !== undefined
        ) {
          if (response.totalData > 0) {
            $("#notification-dropdown-today").html(response.notifications);
            $("#no-data-today").hide();
          } else {
            $("#notification-dropdown-today").html("");
            $("#no-data-today").show();
          }

          // Log total data pada database dan total data yang sudah ditampilkan untuk "Today"
          // console.log("Total data pada database (Today):", response.totalData);
          // console.log("Nomor urut terakhir (Today):", response.lastRowNumber);

          // Menyembunyikan tombol "Load More" jika semua data telah dimuat untuk "Today"
          if (
            response.totalData == 0 ||
            response.lastRowNumber >= response.totalData
          ) {
            $("#load-more-today").hide();
          } else {
            $("#load-more-today").show();
          }
        } else {
          console.error("Respons dari server tidak valid:", response);
          $("#notification-dropdown-today").html("");
          $("#no-data-today").show();
          $("#load-more-today").hide();
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", error);
        console.error("Server response:", xhr.responseText);
      },
    });
  }

  // Memuat notifikasi untuk "earlier"
  $.ajax({
    url: "ajax/load-notifikasi-earlier.php",
    type: "GET",
    dataType: "json",
    success: function (response) {
      $("#notification-dropdown-earlier").html(response.notifications);

      // Log total data pada database dan total data yang sudah ditampilkan untuk "earlier"
      // console.log("Total data pada database (earlier):", response.totalData);
      // console.log("Nomor urut terakhir (earlier):", response.lastRowNumber);

      // Menyembunyikan tombol "Load More" jika semua data telah dimuat untuk "earlier"
      if (response.allDataLoaded) {
        $("#load-more-earlier").hide();
      } else {
        $("#load-more-earlier").show();
      }
    },
    error: function (xhr, status, error) {
      console.error(error);
    },
  });

  // Event handler untuk tombol "Load More" di bagian "Today"
  $("#load-more-today").click(function (event) {
    event.stopPropagation();
    loadMoreNotifications("today");
  });

  // Event handler untuk tombol "Load More" di bagian "earlier"
  $("#load-more-earlier").click(function (event) {
    event.stopPropagation();
    loadMoreNotifications("earlier");
  });

  // Fungsi untuk memuat lebih banyak notifikasi
  function loadMoreNotifications(section) {
    var sectionId =
      section === "today"
        ? "#notification-dropdown-today"
        : "#notification-dropdown-earlier";
    var loadMoreId =
      section === "today" ? "#load-more-today" : "#load-more-earlier";

    var totalDisplayedNotifications = $(sectionId).children().length;

    $.ajax({
      url:
        section === "today"
          ? "ajax/load-notifikasi-today.php"
          : "ajax/load-notifikasi-earlier.php",
      type: "GET",
      dataType: "json",
      data: { offset: totalDisplayedNotifications },
      success: function (response) {
        if (response.notifications && response.notifications.length > 0) {
          $(sectionId).append(response.notifications);

          // console.log(
          //   "Total data pada database (" + section + "):",
          //   response.totalData
          // );
          // console.log(
          //   "Nomor urut terakhir (" + section + "):",
          //   response.lastRowNumber
          // );

          if (response.lastRowNumber >= response.totalData) {
            $(loadMoreId).hide();
          }
        } else {
          $(loadMoreId).hide();
        }
      },
      error: function (xhr, status, error) {
        console.error(error);
      },
    });
  }
});
