function compressImage() {
  var file = document.querySelector("#formFile").files[0];
  var reader = new FileReader();
  var consoleOutput = document.getElementById("console-output");

  // Empty the console output
  consoleOutput.innerHTML = "";

  reader.onload = function () {
    var img = new Image();
    img.src = reader.result;

    img.onload = function () {
      var canvas = document.createElement("canvas");
      var ctx = canvas.getContext("2d");
      var maxWidth = 650;
      var maxHeight = 650;
      var width = img.width;
      var height = img.height;

      // Calculate new dimensions
      if (width > height) {
        if (width > maxWidth) {
          height *= maxWidth / width;
          width = maxWidth;
        }
      } else {
        if (height > maxHeight) {
          width *= maxHeight / height;
          height = maxHeight;
        }
      }

      // Set canvas dimensions
      canvas.width = width;
      canvas.height = height;

      // Compress image
      ctx.drawImage(img, 0, 0, width, height);
      canvas.toBlob(function (blob) {
        // Get compressed file size
        var compressedSize = blob.size / 1024; // convert to KB
        // console.log('Compressed file size:', compressedSize + ' KB');

        // Get original file size
        var originalSize = file.size / 1024; // convert to KB
        // console.log('Original file size:', originalSize + ' KB');

        // Display console log output in HTML
        var consoleOutput = document.getElementById("console-output");
        consoleOutput.innerHTML +=
          "File size: " + compressedSize.toFixed(2) + " KB<br>";
        // consoleOutput.innerHTML += 'Original file size: ' + originalSize.toFixed(2) + ' KB<br>';

        // Set compressed image preview
        var preview = document.querySelector("#imagePreview");
        preview.src = URL.createObjectURL(blob);
        preview.style.display = "block";
        preview.style.width = "300px";
        preview.style.height = "300px";
      }, file.type);
    };
  };

  if (file) {
    reader.readAsDataURL(file);
  }
}
