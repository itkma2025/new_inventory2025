<!-- kode untuk kompress gambar sebelum upload -->
<!-- Bukti 1 -->
<script>
    function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function compressAndPreviewImage(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    var originalWidth = img.width;
                    var originalHeight = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = originalWidth / originalHeight;

                    // Menentukan persentase lebar yang diinginkan (contoh: 65%)
                    var targetWidthPercentage = 65;

                    // Menghitung targetWidth berdasarkan persentase
                    var targetWidth = (originalWidth * targetWidthPercentage) / 100;

                    // Menghitung targetHeight berdasarkan aspek rasio
                    var targetHeight = targetWidth / aspectRatio;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, 0, 0, originalWidth, originalHeight, 0, 0, targetWidth, targetHeight);

                    // Mengubah gambar menjadi data URL
                    var compressedImageData = canvas.toDataURL('image/jpeg', 1.0);
                    var compressedFileSize = compressedImageData.length * 0.75;

                    // Membuat elemen <img> untuk pratinjau gambar
                    var previewElement = document.createElement('img');
                    previewElement.src = compressedImageData;
                    previewElement.classList.add('preview-image');

                    // Menampilkan pratinjau gambar
                    var imagePreview = document.getElementById('imagePreview');
                    imagePreview.innerHTML = '';
                    imagePreview.appendChild(previewElement);

                    console.log("Ukuran gambar setelah kompresi:", targetWidth, "x", targetHeight);
                    console.log("Ukuran memori gambar setelah kompresi:", formatBytes(compressedFileSize));
                };
            };
            reader.readAsDataURL(file);
        }



    function checkIfFileNameExists(fileName) {
        // Simulasikan pengecekan jika nama file sudah ada sebelumnya
        // Misalnya, Anda dapat menggunakan AJAX untuk memeriksa dengan server
        // Berikut ini hanya contoh sederhana
        var existingFileNames = ['image1.jpg', 'image2.jpg', 'image3.jpg'];

        return existingFileNames.includes(fileName);
    }
</script>

<script>
    function formatBytes2(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function compressAndPreviewImage2(event) {
        var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    var originalWidth = img.width;
                    var originalHeight = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = originalWidth / originalHeight;

                    // Menentukan persentase lebar yang diinginkan (contoh: 65%)
                    var targetWidthPercentage = 65;

                    // Menghitung targetWidth berdasarkan persentase
                    var targetWidth = (originalWidth * targetWidthPercentage) / 100;

                    // Menghitung targetHeight berdasarkan aspek rasio
                    var targetHeight = targetWidth / aspectRatio;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, 0, 0, originalWidth, originalHeight, 0, 0, targetWidth, targetHeight);

                    // Mengubah gambar menjadi data URL
                    var compressedImageData = canvas.toDataURL('image/jpeg', 1.0);
                    var compressedFileSize = compressedImageData.length * 0.75;

                    // Membuat elemen <img> untuk pratinjau gambar
                    var previewElement = document.createElement('img');
                    previewElement.src = compressedImageData;
                    previewElement.classList.add('preview-image');

                    // Menampilkan pratinjau gambar
                    var imagePreview = document.getElementById('imagePreview');
                    imagePreview.innerHTML = '';
                    imagePreview.appendChild(previewElement);

                    console.log("Ukuran gambar setelah kompresi:", targetWidth, "x", targetHeight);
                    console.log("Ukuran memori gambar setelah kompresi:", formatBytes(compressedFileSize));
                };
            };
            reader.readAsDataURL(file);
        }

    function checkIfFileNameExists(fileName) {
        // Simulasikan pengecekan jika nama file sudah ada sebelumnya
        // Misalnya, Anda dapat menggunakan AJAX untuk memeriksa dengan server
        // Berikut ini hanya contoh sederhana
        var existingFileNames = ['image1.jpg', 'image2.jpg', 'image3.jpg'];

        return existingFileNames.includes(fileName);
    }
</script>

<script>
    function formatBytes3(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function compressAndPreviewImage3(event) {
        var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    var originalWidth = img.width;
                    var originalHeight = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = originalWidth / originalHeight;

                    // Menentukan persentase lebar yang diinginkan (contoh: 65%)
                    var targetWidthPercentage = 65;

                    // Menghitung targetWidth berdasarkan persentase
                    var targetWidth = (originalWidth * targetWidthPercentage) / 100;

                    // Menghitung targetHeight berdasarkan aspek rasio
                    var targetHeight = targetWidth / aspectRatio;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, 0, 0, originalWidth, originalHeight, 0, 0, targetWidth, targetHeight);

                    // Mengubah gambar menjadi data URL
                    var compressedImageData = canvas.toDataURL('image/jpeg', 1.0);
                    var compressedFileSize = compressedImageData.length * 0.75;

                    // Membuat elemen <img> untuk pratinjau gambar
                    var previewElement = document.createElement('img');
                    previewElement.src = compressedImageData;
                    previewElement.classList.add('preview-image');

                    // Menampilkan pratinjau gambar
                    var imagePreview = document.getElementById('imagePreview');
                    imagePreview.innerHTML = '';
                    imagePreview.appendChild(previewElement);

                    console.log("Ukuran gambar setelah kompresi:", targetWidth, "x", targetHeight);
                    console.log("Ukuran memori gambar setelah kompresi:", formatBytes(compressedFileSize));
                };
            };
            reader.readAsDataURL(file);
        }
</script>

<script>
    function formatBytes2(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    function compressAndPreviewImageCb(event) {
        var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = new Image();
                img.src = e.target.result;
                img.onload = function() {
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');

                    var originalWidth = img.width;
                    var originalHeight = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = originalWidth / originalHeight;

                    // Menentukan persentase lebar yang diinginkan (contoh: 65%)
                    var targetWidthPercentage = 65;

                    // Menghitung targetWidth berdasarkan persentase
                    var targetWidth = (originalWidth * targetWidthPercentage) / 100;

                    // Menghitung targetHeight berdasarkan aspek rasio
                    var targetHeight = targetWidth / aspectRatio;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, 0, 0, originalWidth, originalHeight, 0, 0, targetWidth, targetHeight);

                    // Mengubah gambar menjadi data URL
                    var compressedImageData = canvas.toDataURL('image/jpeg', 1.0);
                    var compressedFileSize = compressedImageData.length * 0.75;

                    // Membuat elemen <img> untuk pratinjau gambar
                    var previewElement = document.createElement('img');
                    previewElement.src = compressedImageData;
                    previewElement.classList.add('preview-image');

                    // Menampilkan pratinjau gambar
                    var imagePreview = document.getElementById('imagePreviewCb');
                    imagePreview.innerHTML = '';
                    imagePreview.appendChild(previewElement);

                    console.log("Ukuran gambar setelah kompresi:", targetWidth, "x", targetHeight);
                    console.log("Ukuran memori gambar setelah kompresi:", formatBytes(compressedFileSize));
                };
            };
            reader.readAsDataURL(file);
        }

    function checkIfFileNameExists(fileName) {
        // Simulasikan pengecekan jika nama file sudah ada sebelumnya
        // Misalnya, Anda dapat menggunakan AJAX untuk memeriksa dengan server
        // Berikut ini hanya contoh sederhana
        var existingFileNames = ['image1.jpg', 'image2.jpg', 'image3.jpg'];

        return existingFileNames.includes(fileName);
    }
</script>