let books = [];

$(document).ready(function () {
  // Bücher vom Server laden
  $.ajax({
    // url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/getbooks.php', -- David
    url: 'http://localhost/WebShop/Backend/config/getbooks.php', // -- Armin
    method: 'GET',
    dataType: 'json',
    success: function (responseBooks) {
      books = responseBooks;
      console.log("✅ Bücher geladen:", books.length);
      renderBooks(books); // zuerst alle anzeigen
    },
    error: function () {
      $('#books-container').html('<p>❌ Fehler beim Laden der Bücher.</p>');
    }
  });

  // Nur bei Buttonklick filtern
  $('#applyFilterBtn').on('click', applyFilters);

  // Klick auf Buchkarte → Modal anzeigen
  $(document).on('click', '.book-card', function () {
    const bookId = $(this).data('book-id');
    const book = books.find(b => b.id == bookId);
  
    if (!book) {
      console.error('❌ Buchdaten nicht gefunden für ID:', bookId);
      return;
    }
  
    $('#modalBookImage').attr('src', book.image);
    $('#modalBookTitle').text(book.title);
    $('#modalBookAuthor').text(book.author);
    $('#modalBookGenre').text(book.genre);
    $('#modalBookDate').text(book.publishedDate);
    $('#modalBookPrice').text(book.price);
    $('#modalBookLanguage').text(book.language);
    $('#modalBookDescription').text(book.description);
    $('#bookDetailModal').modal('show');
  });  
});

// Bücher anzeigen
function renderBooks(bookList) {
  const container = $('#books-container');
  container.empty();

  if (!bookList || bookList.length === 0) {
    container.append('<p>❗ Keine Bücher gefunden.</p>');
    return;
  }

  bookList.forEach((book) => {
    const bookHtml = `
      <div class="col-md-3 mb-4">
        <div class="card book-card h-100" data-book-id="${book.id}" style="cursor:pointer">
          <img src="${book.image}" class="card-img-top book-img" alt="${book.title}">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">${book.title}</h5>
            <p class="card-text"><strong>Autor:</strong> ${book.author}</p>
            <p class="card-text"><strong>Genre:</strong> ${book.genre}</p>
            <p class="card-text"><strong>Sprache:</strong> ${book.language}</p>
            <p class="card-text mt-auto"><strong>Preis:</strong> ${book.price} €</p>
            <button class="btn btn-primary btn-block mt-2">In den Warenkorb</button>
          </div>
        </div>
      </div>
    `;
    container.append(bookHtml);
  });
}

// Filterfunktion im Frontend (lokal)
function applyFilters() {
  const title = $('#filter-title').val().toLowerCase();
  const author = $('#filter-author').val().toLowerCase();
  const genre = $('#filter-genre').val().toLowerCase();
  const language = $('#filter-language').val().toLowerCase();
  const maxPrice = parseFloat($('#filter-max-price').val());

  const filtered = books.filter(book => {
    return (
      (title === '' || (book.title || '').toLowerCase().includes(title)) &&
      (author === '' || (book.author || '').toLowerCase().includes(author)) &&
      (genre === '' || (book.genre || '').toLowerCase().includes(genre)) &&
      (language === '' || (book.language || '').toLowerCase().includes(language)) &&
      (isNaN(maxPrice) || parseFloat(book.price) <= maxPrice)
    );
  });

  console.log(`🔎 Gefiltert: ${filtered.length} Buch(er) gefunden.`);
  renderBooks(filtered);
}
