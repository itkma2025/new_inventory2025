const myDiv = document.querySelector('#items');
  const children = Array.from(myDiv.children);

  function sortNewest() {
    children.sort((a, b) => b.textContent.localeCompare(a.textContent));
    myDiv.innerHTML = '';
    children.forEach(child => myDiv.appendChild(child));
  }

  function sortOldest() {
    children.sort((a, b) => a.textContent.localeCompare(b.textContent));
    myDiv.innerHTML = '';
    children.forEach(child => myDiv.appendChild(child));
  }

  const sortSelect = document.querySelector('#sort');
  sortSelect.addEventListener('change', () => {
    if (sortSelect.value === 'newest') {
      sortNewest();
    } else if (sortSelect.value === 'oldest') {
      sortOldest();
    }
  });