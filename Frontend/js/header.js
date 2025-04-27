// Beim Laden der Seite den Warenkorb-Zähler aktualisieren


console.log("header.js wurde geladen");


$(document).ready(function() {
    $("#header-placeholder").load("../includes/header.html", function() {
      // Sobald der Header geladen ist, können wir den Zähler aktualisieren.
      updateHeaderCartCount();
    });
  });
  
/**
 * Ruft per AJAX den aktuellen Warenkorbinhalt aus der Session ab und
 * aktualisiert den Zähler (#cart-count) im Header.
 * */

function updateHeaderCartCount() {
    $.ajax({
        url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/getCart.php',
        type: 'GET',
        dataType: 'json',
        // Wichtig: Mit Credentials (Cookies/Session) senden
        xhrFields: { withCredentials: true },
        success: function(response) {
            const count = (response && response.totalCount) ? response.totalCount : 0;
            $('#cart-count').text(count);
          },
        error: function (xhr, status, error) {
            console.error("Fehler beim Aktualisieren des Warenkorb-Zählers kommt von headerjs datei:", error);
        }
    });
}

 