$(document).ready(function () {
    const pathPrefix = window.location.origin + "/WebShop/Backend/config/getUserStatus.php";
  
    $.ajax({
      url: pathPrefix,
      method: "GET",
      xhrFields: {
        withCredentials: true // wichtig für Cookies/Sessions
      },
      dataType: "json",
      success: function (data) {
        const statusContainer = $('#user-status');
        const menuContainer = $('#main-menu');
        let statusText = 'Nicht eingeloggt (Gast)';
        let menu = `
          <ul>
            <li><a href="index.html">Home</a></li>
            <li>
              <a href="cart.html" class="nav-link" style="position: relative;">
                <i class="bi bi-cart-fill" style="font-size: 1.5rem; vertical-align: middle;"></i>
                <span id="cart-count" style="
                    position: absolute;
                    top: -5px;
                    right: -10px;
                    background: red;
                    color: white;
                    border-radius: 50%;
                    width: 20px;
                    height: 20px;
                    font-size: 12px;
                    text-align: center;
                    line-height: 20px;"></span>
              </a>
            </li>
          </ul>
        `;
  
        if (data.loggedIn) {
          const username = data.username || 'Unbekannt';
          const role = data.role || 'user';
          const isAdmin = role === 'admin';
  
          statusText = `Eingeloggt als ${username}` + (isAdmin ? ' (admin)' : '');
  
          if (isAdmin) {
            menu = `
              <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="produkteBearbeiten.html">Produkte bearbeiten</a></li>
                <li><a href="kundenBearbeiten.html">Kunden bearbeiten</a></li>
                <li><a href="gutscheineVerwalten.html">Gutscheine verwalten</a></li>
              </ul>
            `;
          } else {
            menu = `
              <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="meinKonto.html">Mein Konto</a></li>
                <li>
                  <a href="cart.html" class="nav-link" style="position: relative;">
                    <i class="bi bi-cart-fill" style="font-size: 1.5rem; vertical-align: middle;"></i>
                    <span id="cart-count" style="
                        position: absolute;
                        top: -5px;
                        right: -10px;
                        background: red;
                        color: white;
                        border-radius: 50%;
                        width: 20px;
                        height: 20px;
                        font-size: 12px;
                        text-align: center;
                        line-height: 20px;"></span>
                  </a>
                </li>
              </ul>
            `;
          }
  
          statusText += ` <button id="logout-btn">Logout</button>`;
        } else {
          statusText += ` | <a href="registerAndLogin.html">Login</a> / <a href="registerAndLogin.html">Registrieren</a>`;
        }
  
        statusContainer.html(statusText);
        menuContainer.html(menu);
  
        $('#logout-btn').on('click', function () {
          $.post("../../Backend/config/logout.php", function () {
            location.reload();
          });
        });
      },
      error: function (xhr, status, error) {
        console.error("Fehler beim Abrufen des Benutzerstatus:", xhr.responseText);
      }
    });
  });
  