document.addEventListener("DOMContentLoaded", function () {
  const video = document.getElementById("videoModal");
  const captureButton = document.getElementById("captureModalButton");
  const photo = document.getElementById("photo");
  const imageInput = document.getElementById("image");
  const uploadBuktiButton = document.getElementById("upload-bukti");
  const captureModal = new bootstrap.Modal(
    document.getElementById("captureModal")
  );
  let stream;

  function startCamera() {
    navigator.mediaDevices
      .getUserMedia({
        video: {
          facingMode: { ideal: "environment" },
        },
      })
      .then((s) => {
        stream = s;
        video.srcObject = stream;
        video.style.display = "block";
        captureButton.style.display = "block"; // Tampilkan tombol capture setelah kamera berhasil diakses
      })
      .catch((error) => {
        console.error("Error accessing the camera", error);
      });
  }

  function stopCamera() {
    if (stream) {
      stream.getTracks().forEach((track) => track.stop());
    }
    video.style.display = "none";
    captureButton.style.display = "none"; // Sembunyikan tombol capture saat kamera dimatikan
  }

  captureButton.addEventListener("click", () => {
    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d");

    const videoWidth = video.videoWidth;
    const videoHeight = video.videoHeight;

    canvas.width = videoWidth;
    canvas.height = videoHeight;

    context.drawImage(video, 0, 0, videoWidth, videoHeight);

    const dataUrl = canvas.toDataURL("image/png", 1.0);

    photo.src = dataUrl;
    photo.style.display = "block";

    imageInput.value = dataUrl;

    stopCamera();

    captureModal.hide();

    uploadBuktiButton.innerText = "Ambil Gambar Ulang";
  });

  $("#captureModal").on("shown.bs.modal", function () {
    startCamera();
  });

  $("#captureModal").on("hidden.bs.modal", function () {
    stopCamera();
  });
});
