const video = document.getElementById("video");
const canvas = document.getElementById("canvas");
const photo = document.getElementById("photo");
const captureButton = document.getElementById("capture");
const uploadBuktiButton = document.getElementById("upload-bukti");
const loading = document.getElementById("loading");
const progressBarInner = document.querySelector(".progress-bar-inner");
const percentageDisplay = document.querySelector(".percentage");
const context = canvas.getContext("2d");
const imageInput = document.getElementById("image");
const timestampInput = document.getElementById("timestamp");
let stream;

// Fungsi untuk memulai kamera
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
      captureButton.style.display = "block";
    })
    .catch((error) => {
      console.error("Error accessing the camera", error);
    });
}

// Fungsi untuk menghentikan kamera
function stopCamera() {
  if (stream) {
    stream.getTracks().forEach((track) => track.stop());
  }
  video.style.display = "none";
  captureButton.style.display = "none";
}

// Fungsi untuk menyesuaikan ukuran canvas dan video
function resizeCanvasAndVideo() {
  const modal = document.getElementById("Diterima");
  const modalContent = modal.querySelector(".modal-content");

  canvas.width = modalContent.clientWidth;
  canvas.height = modalContent.clientHeight;
  video.width = modalContent.clientWidth - 40;
  video.height = modalContent.clientHeight - 40;
}

// Fungsi untuk mengecek orientasi layar
function isLandscape() {
  return window.innerWidth > window.innerHeight;
}

// Fungsi untuk memformat timestamp
function formatTimestamp(date) {
  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  const seconds = String(date.getSeconds()).padStart(2, "0");
  return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
}

// Fungsi untuk menambahkan timestamp ke canvas
function addTimestampToCanvas() {
  const currentDate = new Date();
  const currentTimestamp = formatTimestamp(currentDate);
  timestampInput.value = currentTimestamp;

  context.font = "20px Arial";
  context.fillStyle = "white";
  context.strokeStyle = "black";
  context.lineWidth = 2;

  context.strokeText(`Timestamp: ${currentTimestamp}`, 10, canvas.height - 30);
  context.fillText(`Timestamp: ${currentTimestamp}`, 10, canvas.height - 30);
}

// Event listener untuk tombol capture
captureButton.addEventListener("click", () => {
  resizeCanvasAndVideo();
  if (isLandscape()) {
    canvas.width = window.innerHeight;
    canvas.height = window.innerWidth;
    context.save();
    context.translate(canvas.width / 2, canvas.height / 2);
    context.rotate((-90 * Math.PI) / 180);
    context.drawImage(
      video,
      -video.videoHeight / 2,
      -video.videoWidth / 2,
      video.videoHeight,
      video.videoWidth
    );
    context.restore();
  } else {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
  }
  addTimestampToCanvas();
  const dataUrl = canvas.toDataURL("image/png", 1.0);
  photo.src = dataUrl;
  photo.style.width = canvas.width + "px";
  photo.style.height = canvas.height + "px";
  imageInput.value = dataUrl;
  photo.style.display = "block";
  stopCamera();
  uploadBuktiButton.innerText = "Ambil Gambar Ulang";
});

// Event listener untuk tombol upload bukti
uploadBuktiButton.addEventListener("click", () => {
  startCamera();
  photo.style.display = "none";
});

// Event listener untuk meresize canvas dan video saat ukuran window berubah
window.addEventListener("resize", resizeCanvasAndVideo);

// Event listener untuk submit form
document
  .getElementById("uploadForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    loading.style.display = "block";

    let progress = 0;
    const interval = setInterval(() => {
      progress += 1;
      progressBarInner.style.width = `${progress}%`;
      percentageDisplay.textContent = `${progress}%`;

      if (progress >= 100) {
        clearInterval(interval);
        // Simulasi pengiriman formulir setelah loading selesai
        event.target.submit();
      }
    }, 35);
  });

// Event listener untuk menangani pembukaan modal dan memulai kamera
const diterimaModal = new bootstrap.Modal(document.getElementById("Diterima"));

document
  .getElementById("Diterima")
  .addEventListener("shown.bs.modal", startCamera);
document
  .getElementById("Diterima")
  .addEventListener("hidden.bs.modal", stopCamera);
