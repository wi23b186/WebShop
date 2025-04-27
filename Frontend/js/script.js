$(document).ready(function () {
  // Bücher vom Server laden und in "books" speichern
  $.ajax({
    url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/getbooks.php',
    method: 'GET',
    dataType: 'json',
    success: function (responseBooks) {
      window.books = responseBooks;
      console.log("✅ Bücher geladen:", books.length);
      renderBooks(books); // Anzeige aller Bücher
    },
    error: function () {
      $('#books-container').html('<p>❌ Fehler beim Laden der Bücher.</p>');
    }
  });
  
  // Funktion zum Rendern der Bücher im Grid
  function renderBooks(bookList) {
    const container = $('#books-container');
    container.empty();

    if (!bookList || bookList.length === 0) {
      container.append('<p>❗ Keine Bücher gefunden.</p>');
      return;
    }

    bookList.forEach((book) => {
      // Annahme: Jeder Datensatz hat eine eindeutige "id"
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
              <button class="btn btn-primary add-to-cart">In den Warenkorb</button>
            </div>
          </div>
        </div>
      `;
      container.append(bookHtml);
    });

    // Mache die Buchkarten per jQuery UI "draggable"
    $(".book-card").draggable({
      revert: "invalid",
      helper: "clone"
    });
  }

  // **Einmaliger Klick-Event für den "In den Warenkorb"-Button**
  $(document).on('click', '.add-to-cart', function (event) {
    event.preventDefault();
    const productCard = $(this).closest('.book-card');
    const productId = productCard.data('book-id');

    $.ajax({
      type: 'POST',
      url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/addToCart.php',
      contentType: 'application/json',
      data: JSON.stringify({ id: productId }),
      dataType: 'json',
      success: function (response) {
        console.log('Produkt hinzugefügt', response);
        // Aktualisiere den Header-Zähler über den Gesamtwert aus der Session
        updateHeaderCartCount();
      },
      error: function (xhr) {
        console.error('Fehler beim Hinzufügen zum Warenkorb', xhr.responseText);
      }
    });
  });

  // Drag & Drop: Das Warenkorb-Symbol als Droppable definieren
  $("#cart-icon").droppable({
    accept: ".book-card",
    drop: function(event, ui) {
      const productId = ui.draggable.data('book-id');
      $.ajax({
        type: 'POST',
        url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/addToCart.php',
        contentType: 'application/json',
        data: JSON.stringify({ id: productId }),
        dataType: 'json',
        success: function (response) {
          console.log('Produkt per Drag & Drop hinzugefügt', response);
          updateHeaderCartCount();
        },
        error: function (xhr) {
          console.error('Fehler beim Hinzufügen per Drag & Drop', xhr.responseText);
        }
      });
    }
  });
  
  // Beim Laden der Seite sofort den Header-Zähler aktualisieren
  //updateHeaderCartCount();

  /**
 * Aktualisiert den Warenkorb-Zähler im Header, indem er den aktuellen totalCount von getCart.php abruft.
 */

});


// * Aktualisiert den Warenkorb-Zähler im Header, indem er den aktuellen totalCount von getCart.php abruft.
 /*
function updateHeaderCartCount() {
  $.ajax({
    url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/getCart.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      // Erwartet wird, dass response.totalCount den korrekten Gesamtwert enthält
      $('#cart-count').text(response.totalCount);
      console.log('Warenkorb-Zähler aktualisiert:', response.totalCount);
    },
    error: function(xhr, status, error) {
      console.error("Fehler beim Aktualisieren des Warenkorb-Zählers:", error);
      console.log('Fehler ... alisiert:', response.totalCount);
    }
  });
}
*/

