// Reset gambar
function resetForm() {
  document.getElementById("formFile").value = "";
  var preview = document.querySelector("#imagePreview");
  var console = document.querySelector("#console-output");
  preview.style.display = "none";
  console.style.display = "block";
  preview.src = "#";
  console.innerHTML = "";
}

document.querySelector("#resetButton").addEventListener("click", resetForm);
