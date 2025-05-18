$(document).ready(function () {
    loadNav();
    loadProducts();

    // 🔍 Live-Search
    $('#searchInput').on('input', function () {
        const query = $(this).val();
        const category = $('#category-filter').val();
        loadProducts(query, category);
    });

    // 🔄 Kategorie-Filter
    $('#category-filter').on('change', function () {
        const category = $(this).val();
        const query = $('#searchInput').val();
        loadProducts(query, category);
    });

    // 🛒 In den Warenkorb
    $(document).on('click', '.add-to-cart', function () {
        const productId = $(this).data('id');
        $.post('../backend/logic/cartHandler.php', {
            action: 'add',
            product_id: productId
        }, function () {
            alert('Produkt wurde zum Warenkorb hinzugefügt!');
            updateCartCount();
        });
    });

    function loadProducts(query = '', category = '') {
        $.ajax({
            url: '../backend/logic/requestHandler.php',
            type: 'GET',
            data: { action: 'searchProducts', query: query, category: category },
            success: function (response) {
                let products = JSON.parse(response);
                let html = '';
                if (products.length === 0) {
                    html = '<p class="no-results">Keine passenden Produkte gefunden.</p>';
                } else {
                    products.forEach(function (prod) {
                        html += `
                            <div class="product-item">
                                <a href="product_detail.html?id=${prod.id}">
                                    <img src="../backend/productpictures/${prod.image}" alt="${prod.name}">
                                </a>
                                <h3>
                                    <a href="product_detail.html?id=${prod.id}" style="text-decoration: none; color: inherit;">
                                        ${prod.name}
                                    </a>
                                </h3>
                                <p>${prod.description}</p>
                                <p>€ ${parseFloat(prod.price).toFixed(2)}</p>
                                <button class="add-to-cart" data-id="${prod.id}">In den Warenkorb</button>
                            </div>
                        `;
                    });
                }
                $('#product-list').html(html);
            }
        });
    }

    function updateCartCount() {
        $.getJSON('../backend/logic/cartHandler.php?action=getCount', function (data) {
            $('#cart-count').text(data.count);
        });
    }

    function loadNav() {
        $.getJSON('../backend/logic/UserManagement/userStatus.php', function (data) {
            let nav = $('#nav-bar');
            nav.empty();
            nav.append('<a href="index.html">Startseite</a>');
            nav.append('<a href="products.html">Produkte</a>');
            nav.append('<a href="cart.html">Warenkorb (<span id="cart-count">0</span>)</a>');

            if (data.loggedIn) {
                if (data.role === 'admin') {
                    nav.append('<a href="edit_products.html">Produkte bearbeiten</a>');
                    nav.append('<a href="manage_customers.html">Kunden bearbeiten</a>');
                    nav.append('<a href="manage_vouchers.html">Gutscheine verwalten</a>');
                } else {
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
    }
});
