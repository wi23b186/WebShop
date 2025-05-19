$(document).ready(function() {
    $.getJSON('../backend/logic/UserManagement/userStatus.php', function(data) {
        let nav = $('#nav-bar');
        nav.empty();
        nav.append('<a href="index.html">Startseite</a>');
        nav.append('<a href="products.html">Produkte</a>');
        nav.append(`
  <a href="cart.html" id="cart-icon">
    <i class="bi bi-cart4" style="font-size: 1.3rem;"></i>
    (<span id="cart-count">0</span>)
  </a>
`);

        if (data.loggedIn) {
            if (data.role === 'admin') {
                nav.append('<a href="edit_products.html">Produkte bearbeiten</a>');
                nav.append('<a href="manage_customers.html">Kunden bearbeiten</a>');
                nav.append('<a href="manage_vouchers.html">Gutscheine verwalten</a>');
            } else if (data.role === 'customer') {
                nav.append('<a href="account.html">Mein Konto</a>');
                nav.append('<a href="change_password.html">Passwort ändern</a>');
            }
            nav.append('<a href="../backend/logic/UserManagement/logout.php">Logout (' + data.username + ')</a>');
        } else {
            nav.append('<a href="register.html">Registrieren</a>');
            nav.append('<a href="login.html">Login</a>');
        }

        updateCartCount();
    });

    function updateCartCount() {
        $.getJSON('../backend/logic/OrderHandling/cartHandler.php?action=getCount', function(data) {
            $('#cart-count').text(data.count);
        });
    }

});
