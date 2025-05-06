<script>
  function validateInput(input) {
    // Regex pattern untuk mencari karakter yang tidak diinginkan
    var invalidChars = /[~!@#$%^&*()_+\-=[\]{}|\\/,.<>?;:'"]/g;

    // Menghapus karakter yang tidak diinginkan dari input
    input.value = input.value.replace(invalidChars, '');
  }
</script>
