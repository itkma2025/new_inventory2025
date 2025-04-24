// Fungsi untuk memfilter elemen di dalam div
function filterByValue() {
    var value = document.getElementById('filter').value.toLowerCase();
    var items = document.getElementsByClassName('item');

    Array.prototype.forEach.call(items, function(item) {
      if (item.innerText.toLowerCase().indexOf(value) > -1) {
        item.style.display = 'block';
      } else {
        item.style.display = 'none';
      }
    });
    
    // Check if value is empty
    if (value === '') {
        // Display all items
        Array.prototype.forEach.call(items, function(item) {
            item.style.display = 'block';
        });
        
        // Reset pagination
        currentPage = 1;
        displayItems(currentPage);
        createPaginationButtons();
    }
  }

  // Event listener untuk input filter
  document.getElementById('filter').addEventListener('keyup', filterByValue);