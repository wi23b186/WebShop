$(document).ready(function() {
  // AJAX-Request an checkLoginStatus.php
  $.ajax({
    // Achte hier auf den korrekten Pfad zu deiner PHP-Datei!
    // Beispiel: "http://localhost/ProjektWebshop/WebShop/backend/config/checkLoginStatus.php"
    url: 'http://localhost/ProjektWebshop/WebShop/Backend/config/checkLoginStatus.php',
    method: 'GET',
    dataType: 'json',
    xhrFields: {
      withCredentials: true // sorgt dafür, dass Cookies gesendet/angenommen werden
    },
    success: function(response) {
      if (response.loggedIn) {
        // Eingeloggt
        $('#myLoginStatus').text('Logged in');
      } else {
        // Nicht eingeloggt
        $('#myLoginStatus').text('Not logged in');
      }
    },
    error: function() {
      console.error('Fehler beim Abfragen des Login-Status');
      $('#myLoginStatus').text('Status konnte nicht ermittelt werden');
    }
  });
});
