<!-- Template Main JS File -->
<script src="../assets/js/main.js"></script>

<!-- jquery 3.6.3 -->
<script src="../assets/js/jquery.min.js"></script>

<!-- Selectize JS -->
<script src="../assets/selectize-js/dist/js/selectize.min.js"></script>

<script type="text/javascript">
	$('.selectize-js').selectize({
        plugins: ["clear_button"],
    });
</script>
<script type="text/javascript">
    $('.selectize-js-2').selectize({
        plugins: ["clear_button"],
        onChange: function(value) {
            // Menampilkan data yang dipilih ke dalam console log
            // console.log('Selected value:', value);
        }
    });
</script>
<!-- End Selectize JS -->

<!-- date picker with flatpick -->
<script type="text/javascript">
    flatpickr("#date", {
        dateFormat: "d/m/Y", 
        defaultDate: new Date()
    });
</script>
<!-- end date picker -->

<!-- DataTables Bootstrap 5 -->
<script src="../assets/DataTables/js/dataTables.js"></script>
<script src="../assets/DataTables/js/dataTables.bootstrap5.js"></script>
<script src="../assets/DataTables/js/dataTables.buttons.js"></script>
<script src="../assets/DataTables/js/buttons.bootstrap5.js"></script>
<script src="../assets/DataTables/js/jszip.min.js"></script>
<script src="../assets/DataTables/js/pdfmake.min.js"></script>
<script src="../assets/DataTables/js/vfs_fonts.js"></script>
<script src="../assets/DataTables/js/buttons.html5.min.js"></script>
<script src="../assets/DataTables/js/buttons.print.min.js"></script>
<script src="../assets/DataTables/js/buttons.colVis.min.js"></script>
<script src="../assets/js/datatables-show.js"></script>
<!-- End Datatable Bootstraps 5 -->

<!-- Alert -->
<script src="../assets/js/alert.js"></script>

<!-- animate loading -->
<script src="../assets/js/load.js"></script>

<!-- format number -->
<script src="../assets/js/function-format-number.js"></script>
