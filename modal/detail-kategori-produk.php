<div class="modal fade" id="modalDetail" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Detail Kategori Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <!-- Data akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>

<!-- Script Untuk Modal Detail -->
<script>
  $(document).ready(function () {
    $(document).on('click', '.btnDetail', function() {
        var id = $(this).data("id");

        $.ajax({
            url: "ajax/detail-kat-produk.php", 
            type: "POST",
            data: { id: id },
            success: function (response) {
                $("#detailContent").html(response);
            },
            error: function () {
                $("#detailContent").html('<p class="text-danger">Gagal mengambil data.</p>');
            }
        });
    });
  });
</script>