// js/cart.js
console.log("cart.js wurde geladen");

$(function() {
  // Beim Laden Zähler und Warenkorb holen
  updateHeaderCartCount();
  loadCart();

  // Delegierter Klick-Handler für Entfernen
  $('#cart-content').on('click', '.remove-item', function() {
    const row    = $(this).closest('tr');
    const prodId = row.data('product-id');
    $.ajax({
      type: 'POST',
      url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/removeFromCart.php',
      contentType: 'application/json',
      data: JSON.stringify({ id: prodId }),
      dataType: 'json',
      xhrFields: { withCredentials: true },
      crossDomain: true,
      success: function() {
        row.remove();
        recalcTotal();
        updateHeaderCartCount();
        if ($('#cart-content table tbody tr').length === 0) {
          $('#cart-content').html('<p>Dein Warenkorb ist leer.</p>');
        }
      },
      error: function(xhr) {
        console.error('Fehler beim Entfernen:', xhr.responseText);
      }
    });
  });

  // Delegierter Change-Handler für die Mengen-Inputs
  $('#cart-content').on('change', '.quantity-input', function() {
    const input  = $(this);
    let   qty    = parseInt(input.val(), 10);
    const row    = input.closest('tr');
    const prodId = row.data('product-id');

    if (isNaN(qty) || qty < 1) {
      qty = 1;
      input.val(1);
    }

    $.ajax({
      type: 'POST',
      url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/updateCart.php',
      contentType: 'application/json',
      data: JSON.stringify({ id: prodId, quantity: qty }),
      dataType: 'json',
      xhrFields: { withCredentials: true },
      crossDomain: true,
      success: function() {
        // Zwischensumme aktualisieren
        const price    = parseFloat(row.find('td').eq(1).text());
        const subtotal = (price * qty).toFixed(2) + ' €';
        row.find('.item-subtotal').text(subtotal);

        // Gesamtpreis und Zähler updaten
        recalcTotal();
        updateHeaderCartCount();
      },
      error: function(xhr) {
        console.error('Fehler beim Aktualisieren der Menge:', xhr.responseText);
      }
    });
  });
});

/**
 * Zähler im Header aktuell halten
 */
function updateHeaderCartCount() {
  $.getJSON('http://localhost/ProjektWebshop/WebShop/Backend/config/getCart.php', function(resp) {
    $('#cart-count').text(resp.totalCount || 0);
  });
}

/**
 * Lädt per AJAX den Warenkorb und rendert ihn
 */
function loadCart() {
  $.getJSON('http://localhost/ProjektWebshop/WebShop/Backend/config/getCart.php')
    .done(function(data) {
      renderCart(data);
    })
    .fail(function() {
      $('#cart-content').html('<p class="text-danger">Fehler beim Laden des Warenkorbs.</p>');
    });
}

/**
 * Baut die Tabelle mit Mengen-Inputs
 */
function renderCart(data) {
  if (!data.items || data.items.length === 0) {
    $('#cart-content').html('<p>Dein Warenkorb ist leer.</p>');
    return;
  }

  let html = '<table class="table table-bordered">';
  html += '<thead><tr>'
       + '<th>Produkt</th>'
       + '<th>Preis</th>'
       + '<th>Anzahl</th>'
       + '<th>Zwischensumme</th>'
       + '<th>Aktion</th>'
       + '</tr></thead><tbody>';

  data.items.forEach(item => {
    const price    = parseFloat(item.price);
    const subtotal = parseFloat(item.subtotal);
    html += `
    <tr data-product-id="${item.id}">
        <td>${item.title}</td>
        <td>${price.toFixed(2)} €</td>
        <td>
          <div class="input-group quantity-group">
            <button class="btn btn-outline-secondary btn-sm quantity-decrease" type="button">–</button>
            <input type="text"
                   class="form-control text-center quantity-input"
                   value="${item.quantity}"
                   readonly>
            <button class="btn btn-outline-secondary btn-sm quantity-increase" type="button">+</button>
          </div>
        </td>
        <td class="item-subtotal">${subtotal.toFixed(2)} €</td>
        <td>
          <button class="btn btn-danger btn-sm remove-item">Entfernen</button>
        </td>
      </tr>`;
  });

  const total = parseFloat(data.totalPrice);
  html += `</tbody></table>
           <div class="text-right">
             <strong id="cart-total">Gesamtpreis: ${total.toFixed(2)} €</strong>
           </div>`;

  $('#cart-content').html(html);
}

/**
 * Rechnet den Gesamtpreis aus allen Zeilen erneut
 */
function recalcTotal() {
  let sum = 0;
  $('#cart-content table tbody tr').each(function() {
    const val = parseFloat(
      $(this).find('.item-subtotal').text().replace(' €','')
    );
    sum += val;
  });
  $('#cart-total').text('Gesamtpreis: ' + sum.toFixed(2) + ' €');
}
// Klick auf Plus- und Minus-Buttons
$('#cart-content').on('click', '.quantity-increase, .quantity-decrease', function() {
  const isIncrease = $(this).hasClass('quantity-increase');
  const row        = $(this).closest('tr');
  const input      = row.find('.quantity-input');
  let   qty        = parseInt(input.val(), 10);

  qty = isIncrease ? qty + 1 : qty - 1;
  if (qty < 1) qty = 1;
  input.val(qty);

  // triggere dann denselben AJAX-Update-Flow wie beim direkten Eingeben
  input.trigger('change');
});