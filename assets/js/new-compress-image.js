// function compressImage(event, modalType) {
//   const fileInput = event.target;
//   const file = fileInput.files[0];

//   // Tentukan ID berdasarkan tipe modal
//   let imagePreviewId, imageSizeId, fileInputId;

//   if (modalType === "Edit" || modalType === "Reupload") {
//     imagePreviewId = "imagePreviewEdit";
//     imageSizeId = "imageSizeEdit";
//     fileInputId = "formFileEdit";
//   } else {
//     imagePreviewId = "imagePreviewAdd";
//     imageSizeId = "imageSizeAdd";
//     fileInputId = "formFileAdd";
//   }

//   // Menampilkan ukuran file asli dalam KB
//   const imageSizeElement = document.getElementById(imageSizeId);
//   if (file) {
//     const fileSizeInKB = (file.size / 1024).toFixed(2); // Ukuran file dalam KB
//     console.log("Original File Size:", fileSizeInKB + " KB");
//     imageSizeElement.textContent = `File Size: ${fileSizeInKB} KB`;
//     imageSizeElement.style.display = "none";
//   }

//   if (file.size > 1 * 1024 * 1024) {
//     // Jika ukuran file lebih dari 1 MB
//     const reader = new FileReader();

//     reader.onload = function (e) {
//       console.log("File Data URL:", e.target.result);

//       const img = new Image();
//       img.src = e.target.result;

//       img.onload = function () {
//         console.log("Image Width:", img.width);
//         console.log("Image Height:", img.height);

//         const canvas = document.createElement("canvas");
//         const ctx = canvas.getContext("2d");

//         // Gunakan ukuran asli gambar
//         canvas.width = img.width;
//         canvas.height = img.height;

//         ctx.drawImage(img, 0, 0, img.width, img.height);

//         canvas.toBlob(
//           function (blob) {
//             // Buat file terkompresi
//             const compressedFile = new File([blob], file.name, {
//               type: file.type,
//             });

//             const compressedFileSizeInKB = (blob.size / 1024).toFixed(2);
//             console.log("Compressed File Size:", compressedFileSizeInKB + " KB");

//             // Ganti file input dengan file terkompresi
//             const dataTransfer = new DataTransfer();
//             dataTransfer.items.add(compressedFile);
//             fileInput.files = dataTransfer.files;

//             // Tampilkan preview
//             const previewURL = URL.createObjectURL(compressedFile);
//             console.log("Compressed File Preview URL:", previewURL);
//             document.getElementById(imagePreviewId).src = previewURL;
//             document.getElementById(imagePreviewId).style.display = "block";
//           },
//           file.type,
//           0.7 // 70% quality
//         );
//       };
//     };
//     reader.readAsDataURL(file);
//   } else {
//     // Jika file kecil, langsung tampilkan preview
//     const previewURL = URL.createObjectURL(file);
//     console.log("Original File Preview URL:", previewURL);
//     document.getElementById(imagePreviewId).src = previewURL;
//     document.getElementById(imagePreviewId).style.display = "block";
//   }
// }

function compressImage(event, modalType) {
  const fileInput = event.target;
  const file = fileInput.files[0];

  // Tentukan ID berdasarkan tipe modal
  let imagePreviewId, imageSizeId, fileInputId;
  if (modalType === "Edit") {
    imagePreviewId = "imagePreviewEdit";
    imageSizeId = "imageSizeEdit";
    fileInputId = "formFileEdit";
  } else if (modalType === "Add") {
    imagePreviewId = "imagePreviewAdd";
    imageSizeId = "imageSizeAdd";
    fileInputId = "formFileAdd";
  } else if (modalType === "Reupload") {
    imagePreviewId = "imagePreviewReupload";
    imageSizeId = "imageSizeReupload";
    fileInputId = "formFileReupload";
  }

  const imageSizeElement = document.getElementById(imageSizeId);
  if (file) {
    const fileSizeInKB = (file.size / 1024).toFixed(2);
    console.log("Original File Size:", fileSizeInKB + " KB");
    imageSizeElement.textContent = `File Size: ${fileSizeInKB} KB`;
    imageSizeElement.style.display = "none";
  }

  if (file.size > 1 * 1024 * 1024) {
    const reader = new FileReader();

    reader.onload = function (e) {
      const img = new Image();
      img.src = e.target.result;

      img.onload = function () {
        const canvas = document.createElement("canvas");
        const ctx = canvas.getContext("2d");

        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0, img.width, img.height);

        canvas.toBlob(
          function (blob) {
            const compressedFile = new File([blob], file.name, {
              type: file.type,
            });

            const compressedFileSizeInKB = (blob.size / 1024).toFixed(2);
            console.log("Compressed File Size:", compressedFileSizeInKB + " KB");

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(compressedFile);
            fileInput.files = dataTransfer.files;

            const previewURL = URL.createObjectURL(compressedFile);
            console.log("Compressed File Preview URL:", previewURL);
            document.getElementById(imagePreviewId).src = previewURL;
            document.getElementById(imagePreviewId).style.display = "block";
          },
          file.type,
          0.7
        );
      };
    };
    reader.readAsDataURL(file);
  } else {
    const previewURL = URL.createObjectURL(file);
    console.log("Original File Preview URL:", previewURL);
    document.getElementById(imagePreviewId).src = previewURL;
    document.getElementById(imagePreviewId).style.display = "block";
  }
}

