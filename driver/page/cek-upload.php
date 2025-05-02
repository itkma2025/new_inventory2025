<script>
        function checkFileName() {
            var file1 = document
                .getElementById('fileku1')
                .value;
            var file2 = document
                .getElementById('fileku2')
                .value;
            var file3 = document
                .getElementById('fileku3')
                .value;

            if (file1 === file2 && file2 !== "") {
                alert("Nama file ke 2 harus berbeda!");
                document
                    .getElementById('fileku2')
                    .value = "";
                document
                    .getElementById('imagePreview2')
                    .innerHTML = "";
            }

            if (file1 === file3 && file3 !== "") {
                alert("Nama file ke 3 harus berbeda!");
                document
                    .getElementById('fileku3')
                    .value = "";
                document
                    .getElementById('imagePreview3')
                    .innerHTML = "";
            }

            if (file2 === file3 && file3 !== "") {
                alert("Nama file ke 3 harus berbeda!");
                document
                    .getElementById('fileku3')
                    .value = "";
                document
                    .getElementById('imagePreview3')
                    .innerHTML = "";
            }
        }
    </script>