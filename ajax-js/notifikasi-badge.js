$(document).ready(function () {
  function loadNotificationBadge() {
    $.ajax({
      url: "ajax/load-notifikasi-badge.php", // Ganti dengan path yang benar ke file PHP Anda
      type: "GET",
      dataType: "json",
      success: function (data) {
        // console.log("Total data:", data.total); // Log the total data to the console

        if (data.total != 0) {
          $("#notification-badge").text(data.total); // Show the badge and set the text
        } else {
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.error(
          "Error loading notification badge:",
          textStatus,
          errorThrown
        );
      },
    });
  }

  // Call the function when the page is fully loaded
  loadNotificationBadge();

  // Optionally, refresh the badge every few seconds (e.g., every 120 seconds)
  setInterval(loadNotificationBadge, 120000); // Refresh every 120 seconds
});
