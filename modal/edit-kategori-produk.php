<div class="modal fade" id="modalEdit" tabindex="-1"  data-bs-backdrop="static" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetailLabel">Edit Kategori Produk</h5>
            </div>
            <div class="modal-body" id="editContent">
                <!-- Data akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>

<!-- Script Untuk Modal Edit -->
<script>
  $(document).ready(function () {
    $(document).on('click', '.btnEdit', function() {
        var id = $(this).data("id");

        $.ajax({
            url: "ajax/edit-kat-produk.php", 
            type: "POST",
            data: { id: id },
            success: function (response) {
                $("#editContent").html(response);
            },
            error: function () {
                $("#editContent").html('<p class="text-danger">Gagal mengambil data.</p>');
            }
        });
    });
  });
</script>