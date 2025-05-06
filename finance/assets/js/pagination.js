const itemsPerPage = 1; // number of items per page
let currentPage = 1; // default current page

// select the items container and all the items
const itemsContainer = document.getElementById('items');
const allItems = itemsContainer.getElementsByClassName('card item');

// function to display items for a specific page
function displayItems(page) {
  const startIndex = (page - 1) * itemsPerPage; // calculate the start index
  const endIndex = startIndex + itemsPerPage; // calculate the end index

  // loop through all items and show/hide based on the page
  for (let i = 0; i < allItems.length; i++) {
    if (i >= startIndex && i < endIndex) {
      allItems[i].style.display = 'block';
    } else {
      allItems[i].style.display = 'none';
    }
  }
}

// function to create pagination buttons
function createPaginationButtons() {
  const totalPages = Math.ceil(allItems.length / itemsPerPage); // calculate the total number of pages

  // select the pagination container and remove any existing buttons
  const paginationContainer = document.getElementById('pagination');
  paginationContainer.innerHTML = '';

  // create and append the previous button
  const previousButton = document.createElement('li');
  previousButton.classList.add('page-item');
  const previousLink = document.createElement('a');
  previousLink.classList.add('page-link');
  previousLink.innerText = 'Previous';
  previousLink.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      displayItems(currentPage);
      updateActiveButton();
    }
  });
  previousButton.appendChild(previousLink);

  // create and append the page buttons
  for (let i = 1; i <= totalPages; i++) {
    const pageButton = document.createElement('li');
    pageButton.classList.add('page-item');
    const pageLink = document.createElement('a');
    pageLink.classList.add('page-link');
    pageLink.innerText = i.toString();
    pageLink.addEventListener('click', () => {
      currentPage = i;
      displayItems(currentPage);
      updateActiveButton();
    });
    pageButton.appendChild(pageLink);
    paginationContainer.appendChild(pageButton);
  }

  // create and append the next button
  const nextButton = document.createElement('li');
  nextButton.classList.add('page-item');
  const nextLink = document.createElement('a');
  nextLink.classList.add('page-link');
  nextLink.innerText = 'Next';
  nextLink.addEventListener('click', () => {
    if (currentPage < totalPages) {
      currentPage++;
      displayItems(currentPage);
      updateActiveButton();
    }
  });
  
  updateActiveButton();
}

// function to update the active page button
function updateActiveButton() {
  const pageLinks = document.getElementsByClassName('page-link');
  for (let i = 0; i < pageLinks.length; i++) {
    if (parseInt(pageLinks[i].innerText) === currentPage) {
      pageLinks[i].parentNode.classList.add('active');
    } else {
      pageLinks[i].parentNode.classList.remove('active');
    }
  }
}

// call the functions to initialize the pagination buttons and display the initial page
createPaginationButtons();
displayItems(currentPage);