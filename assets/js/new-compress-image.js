function compressImage(event, modalType) {
  const fileInput = event.target;
  const file = fileInput.files[0];

  // Tentukan ID berdasarkan tipe modal
  const imagePreviewId =
    modalType === "Edit" ? "imagePreviewEdit" : "imagePreviewAdd";
  const imageSizeId = modalType === "Edit" ? "imageSizeEdit" : "imageSizeAdd";
  const fileInputId = modalType === "Edit" ? "formFileEdit" : "formFileAdd";

  // Menampilkan ukuran file asli dalam KB
  const imageSizeElement = document.getElementById(imageSizeId);
  if (file) {
    const fileSizeInKB = (file.size / 1024).toFixed(2); // Ukuran file dalam KB
    console.log("Original File Size:", fileSizeInKB + " KB"); // Log ukuran file asli
    imageSizeElement.textContent = `File Size: ${fileSizeInKB} KB`;
    imageSizeElement.style.display = "none";
  }

  if (file.size > 1 * 1024 * 1024) {
    // Check if file size > 1 MB
    const reader = new FileReader();

    reader.onload = function (e) {
      console.log("File Data URL:", e.target.result); // Log data URL gambar

      const img = new Image();
      img.src = e.target.result;

      img.onload = function () {
        console.log("Image Width:", img.width); // Log lebar gambar
        console.log("Image Height:", img.height); // Log tinggi gambar

        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        // Menggunakan dimensi gambar asli
        canvas.width = img.width;
        canvas.height = img.height;

        ctx.drawImage(img, 0, 0, img.width, img.height);

        canvas.toBlob(
          function (blob) {
            // Membuat file terkompresi
            const compressedFile = new File([blob], file.name, {
              type: file.type,
            });

            // Menampilkan ukuran file terkompresi dalam KB
            const compressedFileSizeInKB = (blob.size / 1024).toFixed(2); // Ukuran blob dalam KB
            console.log(
              "Compressed File Size:",
              compressedFileSizeInKB + " KB"
            ); // Log ukuran file terkompresi

            // Memperbarui file input dengan file terkompresi
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(compressedFile);
            fileInput.files = dataTransfer.files;

            // Update preview
            const previewURL = URL.createObjectURL(compressedFile);
            console.log("Compressed File Preview URL:", previewURL); // Log URL preview file terkompresi
            document.getElementById(imagePreviewId).src = previewURL;
            document.getElementById(imagePreviewId).style.display = "block";
          },
          file.type,
          0.7
        ); // Compress at 70% quality
      };
    };
    reader.readAsDataURL(file);
  } else {
    // Untuk file yang lebih kecil dari 1 MB, langsung update preview
    const previewURL = URL.createObjectURL(file);
    console.log("Original File Preview URL:", previewURL); // Log URL preview file asli
    document.getElementById(imagePreviewId).src = previewURL;
    document.getElementById(imagePreviewId).style.display = "block";
  }
}
