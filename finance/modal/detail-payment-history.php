<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Modal utama History -->
<div class="modal fade" id="history" tabindex="-1" aria-labelledby="historyLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="historyLabel">History Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detail_history"></div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal utama History -->
<!-- Modal gambar History -->
<div class="modal fade" id="bukti" data-bs-keyboard="false" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Bukti Transfer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detail_bukti"></div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal gambar History -->
</body>
</html>

<!-- Script Untuk Modal History -->
<script>
    $('#bukti').on('hidden.bs.modal', function () {
        $('#history').modal('show'); // Tampilkan kembali modal utama saat modal gambar disembunyikan
    });
</script>
<!-- ============================================= -->
<!-- Untuk menampilkan data Histori pad amodal -->
<script>
    $(document).ready(function(){
        $('.view_data').click(function(event){
            // Mencegah perilaku default dari tombol yang dipilih
            event.preventDefault();  
            var paymentId = $(this).data('id');
            
            // Mengirimkan data ke server menggunakan AJAX
            $.ajax({
                url: "ajax/detail-payment-history.php",
                method: "POST",
                data: {paymentId: paymentId},
                success: function(data){
                    // Menampilkan respons di konsol browser
                    // console.log(data);
                    $("#detail_history").html(data)
                    new DataTable("#table3", {
                        lengthChange: false,
                        autoWidth: false,
                        language: {
                        searchPlaceholder: "Cari data",
                        search: "",
                        },
                    });
                }
            });
        });
    });
</script>
<!--End Script Untuk Modal History -->

