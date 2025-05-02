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
                    var targetWidth = 500;
                    var targetHeight = 500;
                    var width = img.width;
                    var height = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = width / height;

                    // Menentukan dimensi yang akan digunakan untuk memangkas gambar
                    var trimWidth = width;
                    var trimHeight = height;
                    if (aspectRatio > 1) {
                        trimWidth = Math.min(width, height * aspectRatio);
                        trimHeight = trimWidth / aspectRatio;
                    } else {
                        trimHeight = Math.min(height, width / aspectRatio);
                        trimWidth = trimHeight * aspectRatio;
                    }

                    // Menghitung koordinat pemangkasan
                    var trimX = (width - trimWidth) / 2;
                    var trimY = (height - trimHeight) / 2;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, trimX, trimY, trimWidth, trimHeight, 0, 0, targetWidth, targetHeight);

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
                }
            }
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
                    var targetWidth = 500;
                    var targetHeight = 500;
                    var width = img.width;
                    var height = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = width / height;

                    // Menentukan dimensi yang akan digunakan untuk memangkas gambar
                    var trimWidth = width;
                    var trimHeight = height;
                    if (aspectRatio > 1) {
                        trimWidth = Math.min(width, height * aspectRatio);
                        trimHeight = trimWidth / aspectRatio;
                    } else {
                        trimHeight = Math.min(height, width / aspectRatio);
                        trimWidth = trimHeight * aspectRatio;
                    }

                    // Menghitung koordinat pemangkasan
                    var trimX = (width - trimWidth) / 2;
                    var trimY = (height - trimHeight) / 2;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, trimX, trimY, trimWidth, trimHeight, 0, 0, targetWidth, targetHeight);

                    // Mengubah gambar menjadi data URL
                    var compressedImageData = canvas.toDataURL('image/jpeg', 1.0);
                    var compressedFileSize = compressedImageData.length * 0.75;

                    // Membuat elemen <img> untuk pratinjau gambar
                    var previewElement = document.createElement('img');
                    previewElement.src = compressedImageData;
                    previewElement.classList.add('preview-image');

                    // Menampilkan pratinjau gambar
                    var imagePreview2 = document.getElementById('imagePreview2');
                    imagePreview2.innerHTML = '';
                    imagePreview2.appendChild(previewElement);

                    console.log("Ukuran gambar setelah kompresi:", targetWidth, "x", targetHeight);
                    console.log("Ukuran memori gambar setelah kompresi:", formatBytes2(compressedFileSize));
                }
            }
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
                    var targetWidth = 500;
                    var targetHeight = 500;
                    var width = img.width;
                    var height = img.height;

                    // Menghitung aspek rasio gambar
                    var aspectRatio = width / height;

                    // Menentukan dimensi yang akan digunakan untuk memangkas gambar
                    var trimWidth = width;
                    var trimHeight = height;
                    if (aspectRatio > 1) {
                        trimWidth = Math.min(width, height * aspectRatio);
                        trimHeight = trimWidth / aspectRatio;
                    } else {
                        trimHeight = Math.min(height, width / aspectRatio);
                        trimWidth = trimHeight * aspectRatio;
                    }

                    // Menghitung koordinat pemangkasan
                    var trimX = (width - trimWidth) / 2;
                    var trimY = (height - trimHeight) / 2;

                    // Menggambar gambar yang dipangkas ke dalam canvas
                    canvas.width = targetWidth;
                    canvas.height = targetHeight;
                    ctx.drawImage(img, trimX, trimY, trimWidth, trimHeight, 0, 0, targetWidth, targetHeight);

                    // Mengubah gambar menjadi data URL
                    var compressedImageData = canvas.toDataURL('image/jpeg', 1.0);
                    var compressedFileSize = compressedImageData.length * 0.75;

                    // Membuat elemen <img> untuk pratinjau gambar
                    var previewElement = document.createElement('img');
                    previewElement.src = compressedImageData;
                    previewElement.classList.add('preview-image');

                    // Menampilkan pratinjau gambar
                    var imagePreview3 = document.getElementById('imagePreview3');
                    imagePreview3.innerHTML = '';
                    imagePreview3.appendChild(previewElement);

                    console.log("Ukuran gambar setelah kompresi:", targetWidth, "x", targetHeight);
                    console.log("Ukuran memori gambar setelah kompresi:", formatBytes3(compressedFileSize));
                }
            }
            reader.readAsDataURL(file);
        }
</script>