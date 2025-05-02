<!-- Search Ekspedisi -->
<script>
    // Data untuk dropdown
    const options = <?php
    include "koneksi.php";

    $sql_ekspedisi = mysqli_query($connect, "SELECT * FROM ekspedisi");
    $option_values = array();
    while ($data_ekspedisi = mysqli_fetch_array($sql_ekspedisi)) {
        $option_values[] = $data_ekspedisi['nama_ekspedisi'];
    }
    echo json_encode($option_values); 
    ?> ;

        const dropdownInput = document.getElementById('dropdown-input');
        const dropdownList = document.getElementById('dropdown-list');
        const clearSearch = document.getElementById('clear-search');
    
        dropdownInput.addEventListener('click', function() {
        dropdownList.style.display = 'block'; // Display the dropdown list when the input is clicked
        populateDropdownList(options.slice(0, 5));
        });
    
        dropdownInput.addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const filteredOptions = options.filter(function(option) {
            return option.toLowerCase().indexOf(searchValue) > -1;
        });
    
        populateDropdownList(filteredOptions.slice(0, 5));
        });
    
        clearSearch.addEventListener('click', function() {
        dropdownInput.value = '';
        dropdownList.innerHTML = '';
        });
    
        document.addEventListener('click', function(event) {
        const targetElement = event.target;
        if (!dropdownInput.contains(targetElement) && !dropdownList.contains(targetElement)) {
            dropdownList.style.display = 'none'; // Hide the dropdown list when clicking outside the input and the list
        }
        });
    
        function populateDropdownList(options) {
        dropdownList.innerHTML = '';
    
        if (options.length > 0) {
            options.forEach(function(option) {
            const optionElement = document.createElement('div');
            optionElement.textContent = option;
            optionElement.classList.add('dropdown-item');
    
            optionElement.addEventListener('click', function() {
                dropdownInput.value = option;
                dropdownList.innerHTML = '';
                dropdownList.style.display = 'none'; // Hide the dropdown list after an option is selected
            });
    
            dropdownList.appendChild(optionElement);
            });
        } else {
            const noResultElement = document.createElement('div');
            noResultElement.textContent = 'Tidak ada hasil';
            noResultElement.classList.add('dropdown-item');
            dropdownList.appendChild(noResultElement);
        }
    }
</script>