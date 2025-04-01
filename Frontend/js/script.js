let books = [];

$(document).ready(function () {
  // 📦 Bücher vom Server laden
  $.ajax({
    url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/getbooks.php',
    method: 'GET',
    dataType: 'json',
    success: function (responseBooks) {
      books = responseBooks;
      console.log("✅ Bücher geladen:", books.length);
      renderBooks(books); // Alle anzeigen
    },
    error: function () {
      $('#books-container').html('<p>❌ Fehler beim Laden der Bücher.</p>');
    }
  });

  // 🔍 Filter bei Eingabe (live)
  $('#filter-title, #filter-author, #filter-genre, #filter-language, #filter-date, #filter-max-price')
    .on('input', applyFilters);

  // 🔎 Filter per Button
  $('#applyFilterBtn').on('click', applyFilters);

  // 📘 Klick auf Buchkarte → Modal anzeigen
  $(document).on('click', '.book-card', function () {
    const index = $(this).data('index');
    const book = books[index];

    if (!book) {
      console.error('❌ Buchdaten fehlen für Index:', index);
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

  // 👤 Login-Status abfragen
  $.ajax({
    url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/getUserStatus.php',
    method: 'GET',
    dataType: 'json',
    success: function (response) {
      const role = response.role;
      const username = response.username || '';

      if (role === 'guest') {
        $('#loginButtonArea').show();
        $('#userDropdown').hide();
        $('#guestText').show();
      } else if (role === 'user') {
        $('#loginButtonArea').hide();
        $('#userDropdown').show();
        $('#usernameDisplay').text(username);
        $('#userText').show();
      } else if (role === 'admin') {
        $('#loginButtonArea').hide();
        $('#userDropdown').show();
        $('#usernameDisplay').text(username + " (Admin)");
        $('#adminText').show();
      }
    },
    error: function (xhr) {
      console.error("❌ Fehler beim Rollen-Check:", xhr.responseText);
      $('#loginButtonArea').show();
      $('#userDropdown').hide();
    }
  });

  // 🚪 Logout
  $(document).on('click', '#logoutBtn', function (e) {
    e.preventDefault();
    $.ajax({
      url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/logout.php',
      method: 'POST',
      dataType: 'json',
      xhrFields: {
        withCredentials: true
      },
      success: function (resp) {
        alert(resp.message || 'Logout erfolgreich!');
        window.location.reload();
      },
      error: function (xhr) {
        console.error('❌ Logout fehlgeschlagen:', xhr.responseText);
      }
    });
  });
});


// 📚 Bücher anzeigen
function renderBooks(bookList) {
  const container = $('#books-container');
  container.empty();

  if (!bookList || bookList.length === 0) {
    container.append('<p>❗ Keine Bücher gefunden.</p>');
    return;
  }

  bookList.forEach((book, index) => {
    const bookHtml = `
      <div class="col-md-3 mb-4">
        <div class="card book-card h-100" data-index="${index}" style="cursor:pointer">
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


// 🔍 Filterfunktion
function applyFilters() {
  const title = $('#filter-title').val().toLowerCase();
  const author = $('#filter-author').val().toLowerCase();
  const genre = $('#filter-genre').val().toLowerCase();
  const language = $('#filter-language').val().toLowerCase();
  const date = $('#filter-date').val();
  const maxPrice = parseFloat($('#filter-max-price').val());

  const filtered = books.filter(book => {
    return (
      (title === '' || (book.title || '').toLowerCase().includes(title)) &&
      (author === '' || (book.author || '').toLowerCase().includes(author)) &&
      (genre === '' || (book.genre || '').toLowerCase().includes(genre)) &&
      (language === '' || (book.language || '').toLowerCase().includes(language)) &&
      (date === '' || (book.publishedDate || '').includes(date)) &&
      (isNaN(maxPrice) || parseFloat(book.price) <= maxPrice)
    );
  });

  console.log(`🔎 Filter angewendet: ${filtered.length} Buch(er) gefunden.`);
  renderBooks(filtered);
}
