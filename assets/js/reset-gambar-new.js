function resetForm(modalType) {
  // Tentukan ID berdasarkan tipe modal
  const fileInputId = modalType === "Edit" ? "formFileEdit" : "formFileAdd";
  const imagePreviewId =
    modalType === "Edit" ? "imagePreviewEdit" : "imagePreviewAdd";
  const imageSizeId = modalType === "Edit" ? "imageSizeEdit" : "imageSizeAdd";

  document.getElementById(fileInputId).value = "";
  var preview = document.getElementById(imagePreviewId);
  var size = document.getElementById(imageSizeId);

  preview.style.display = "none";
  size.style.display = "none";
  preview.src = "#";
}

document
  .querySelector("#resetButtonEdit")
  .addEventListener("click", function () {
    resetForm("Edit");
  });

document
  .querySelector("#resetButtonAdd")
  .addEventListener("click", function () {
    resetForm("Add");
  });
