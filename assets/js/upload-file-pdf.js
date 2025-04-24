const dropZone = document.getElementById("dropZone");
const fileInput = document.getElementById("fileInput");
const resetButton = document.getElementById("resetButton");
const uploadButton = document.getElementById("uploadButton");
const pdfEmbed = document.getElementById("pdfEmbed");

const MAX_FILE_SIZE_MB = 5;
const MAX_COMPRESSION_RATIO = 0.8;

// Handle File Input
fileInput.addEventListener("change", async (event) => {
    let file = event.target.files[0];
    if (!file) return;
    handleFile(file);
});

// Drag & Drop Events
dropZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZone.classList.add("dragover");
});

dropZone.addEventListener("dragleave", () => {
    dropZone.classList.remove("dragover");
});

dropZone.addEventListener("drop", (e) => {
    e.preventDefault();
    dropZone.classList.remove("dragover");

    let file = e.dataTransfer.files[0];
    if (file) {
        fileInput.files = e.dataTransfer.files;
        handleFile(file);
    }
});

// Fungsi Menangani File
async function handleFile(file) {
    let fileSizeMB = file.size / (1024 * 1024);
    let fileType = file.type;

    console.log("File dipilih:", file.name, "| Size:", fileSizeMB.toFixed(2), "MB");

    if (fileSizeMB > MAX_FILE_SIZE_MB) {
        if (fileType === "application/pdf") {
            let compressedFile = await forceCompressPDF(file);
            updateFileInput(compressedFile, file.name);
        } else {
            alert("File terlalu besar dan tidak bisa dikompresi!");
        }
    } else {
        updateFileInput(file, file.name);
    }
}

// Fungsi Perbarui File di Input
function updateFileInput(file, originalName) {
    fileInput.files = createFileList(file);
    displayFile(file, originalName);
}

// Fungsi Menampilkan File yang Dipilih
function displayFile(file) {
    let fileSizeMB = file.size / (1024 * 1024);

    // Buat URL Blob dengan nama file yang benar
    let blob = new Blob([file], { type: file.type });
    let fileURL = URL.createObjectURL(blob);

    // Buat ID unik berdasarkan timestamp
    let uniqueId = Date.now(); // Contoh: 1700000000000
    let uniqueFileName = `${uniqueId}_${file.name}`;

    dropZone.innerHTML = `<p>
        <a href="${fileURL}" data-fancybox="pdf" data-type="iframe" data-title="${uniqueFileName}" id="pdfLink">
            📂 ${file.name} (${fileSizeMB.toFixed(2)} MB)
        </a>
    </p>`;

    // Set PDF embed dengan URL yang sesuai
    pdfEmbed.src = fileURL;

    if (resetButton) resetButton.style.display = "inline-block";
    if (uploadButton) uploadButton.style.display = "inline-block";
}

// Event listener untuk memastikan setiap Fancybox memiliki nama unik
document.addEventListener("DOMContentLoaded", function () {
    Fancybox.bind("[data-fancybox]", {
        beforeLoad: (instance, slide) => {
            let link = slide.$trigger;
            let uniqueName = link.dataset.title;

            // Set judul Fancybox agar sesuai dengan nama file unik
            slide.caption = uniqueName;
        }
    });
});




// Fungsi Buat FileList dari File yang Dikompresi
function createFileList(file) {
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(file);
    return dataTransfer.files;
}

// Fungsi Paksa Kompresi PDF Hingga 10%
async function forceCompressPDF(file) {
    try {
        let currentFile = file;
        let originalSize = file.size;
        let targetSize = originalSize * MAX_COMPRESSION_RATIO;

        while (currentFile.size > targetSize) {
            let compressedFile = await compressPDF(currentFile, file.name);
            if (compressedFile.size >= currentFile.size) {
                console.warn("Kompresi tambahan tidak efektif, menggunakan hasil terbaik.");
                break;
            }
            currentFile = compressedFile;
        }

        return currentFile;
    } catch (error) {
        console.error("Kompresi PDF gagal:", error);
        alert("Terjadi kesalahan saat mengompresi PDF!");
        return file;
    }
}

// Fungsi Kompresi PDF (Gunakan PDF-LIB)
async function compressPDF(file, originalName) {
    const reader = new FileReader();
    reader.readAsArrayBuffer(file);

    return new Promise((resolve) => {
        reader.onloadend = async function () {
            const pdfBytes = reader.result;
            const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);

            pdfDoc.setTitle("");
            pdfDoc.setAuthor("");
            pdfDoc.setSubject("");

            const pages = pdfDoc.getPages();
            pages.forEach((page) => {
                let { width, height } = page.getSize();
                page.scale(0.8, 0.8);
            });

            const compressedPdfBytes = await pdfDoc.save({
                useObjectStreams: false,
                compress: true,
            });

            const compressedFile = new File([compressedPdfBytes], originalName, { type: "application/pdf" });

            console.log("PDF dikompresi:", compressedFile.name, "| Size:", (compressedFile.size / (1024 * 1024)).toFixed(2), "MB");

            resolve(compressedFile);
        };
    });
}

// Fungsi Reset Form
resetButton.addEventListener("click", (e) => {
    e.preventDefault();
    resetUpload();
});

function resetUpload() {
    fileInput.value = "";
    dropZone.innerHTML = `<i class="bi bi-cloud-upload"></i>
        <p>Drag and Drop here</p>
        <p>or</p>
        <label class="btn-upload" for="fileInput">Select file</label>`;

    pdfEmbed.src = ""; // Hapus preview PDF
    if (resetButton) resetButton.style.display = "none";
    if (uploadButton) uploadButton.style.display = "none";
}