ClassicEditor.create(document.querySelector("#deskripsi"))
  .then((editor) => {
    const MAX_CHARACTERS = 2000;
    const charCountElement = document.getElementById("charCount");

    // Fungsi untuk memperbarui jumlah karakter
    const updateCharCount = () => {
      const text = editor.getData().replace(/<[^>]*>/g, ""); // Menghapus tag HTML untuk mendapatkan teks murni
      const textLength = text.length;

      charCountElement.textContent = `${Math.min(
        textLength,
        MAX_CHARACTERS
      )} / ${MAX_CHARACTERS}`;
    };

    // Memanggil fungsi updateCharCount segera setelah editor diinisialisasi
    updateCharCount();

    // Event listener untuk perubahan data pada editor
    editor.model.document.on("change:data", () => {
      updateCharCount(); // Perbarui jumlah karakter saat terjadi perubahan data
    });

    // Batasi input hingga MAX_CHARACTERS karakter
    editor.model.document.on("change:data", () => {
      const text = editor.getData().replace(/<[^>]*>/g, ""); // Menghapus tag HTML untuk mendapatkan teks murni
      const textLength = text.length;

      if (textLength > MAX_CHARACTERS) {
        const truncatedText = text.substring(0, MAX_CHARACTERS); // Ambil MAX_CHARACTERS karakter pertama
        editor.setData(truncatedText); // Terapkan teks yang dipotong ke editor
      }
    });

    // Ambil data awal dari atribut data-deskripsi pada elemen <script>
    const deskripsiScript = document.getElementById("deskripsi");
    const initialData = deskripsiScript.getAttribute("data-deskripsi");

    // Setel data editor dengan data awal dari server dan panggil updateCharCount lagi
    editor.setData(initialData).then(() => {
      updateCharCount();
    });
  })
  .catch((error) => {
    console.error(error);
  });
