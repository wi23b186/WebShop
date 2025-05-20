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
        $.post('../backend/logic/OrderHandling/cartHandler.php', {
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
                html = '<div class="row g-4">';
                products.forEach(function (prod) {
                    html += `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm product-item" draggable="true">
                                <a href="product_detail.html?id=${prod.id}">
                                    <img src="../backend/productpictures/${prod.image}" 
                                         class="card-img-top" 
                                         alt="${prod.name}" 
                                         style="object-fit: cover; aspect-ratio: 3/4;">
                                </a>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <a href="product_detail.html?id=${prod.id}" 
                                           class="text-decoration-none text-dark">
                                           ${prod.name}
                                        </a>
                                    </h5>
                                    <p class="card-text small">${prod.description}</p>
                                    <p class="fw-bold text-success">€ ${parseFloat(prod.price).toFixed(2)}</p>
                                    <button class="btn btn-dark mt-auto add-to-cart" data-id="${prod.id}">
                                        <i class="bi bi-cart-plus"></i> In den Warenkorb
                                    </button>
                                </div>
                            </div>
<<<<<<< HEAD
                        </div>
                    `;
                });
                html += '</div>';
=======
                        `;
                    });
                }
                $('#product-list').html(html);
                initDragAndDrop();
>>>>>>> e62d31d535de5c171550664e5fbb8f331617f11a
            }

            $('#product-list').html(html);
            initDragAndDrop();
        }
    });


    }



    function updateCartCount() {
        $.getJSON('../backend/logic/OrderHandling/cartHandler.php?action=getCount', function (data) {
            $('#cart-count').text(data.count);
        });
    }

   


    function loadNav() {
        $.getJSON('../backend/logic/UserManagement/userStatus.php', function (data) {
            let nav = $('#nav-bar');
            nav.empty();
            nav.append('<a href="index.html">Startseite</a>');
            nav.append('<a href="products.html">Produkte</a>');
            nav.append(`
<<<<<<< HEAD
                <a href="cart.html" id="cart-icon" class="position-relative">
                    <i class="bi bi-cart4" style="font-size: 1.3rem;"></i>
                    (<span id="cart-count">0</span>)
                </a>
            `);
=======
  <a href="cart.html" id="cart-icon">
    <i class="bi bi-cart4" style="font-size: 1.3rem;"></i>
    (<span id="cart-count">0</span>)
  </a>
`);
>>>>>>> e62d31d535de5c171550664e5fbb8f331617f11a

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

<<<<<<< HEAD
    function initDragAndDrop() {
        console.log('Drag & Drop initialisiert');
=======

     function initDragAndDrop() {
    console.log('Drag & Drop initialisiert');
>>>>>>> e62d31d535de5c171550664e5fbb8f331617f11a

        document.querySelectorAll('.product-item').forEach(item => {
            item.setAttribute('draggable', true);

            item.addEventListener('dragstart', function (e) {
                const button = this.querySelector('.add-to-cart');
                const productId = button.dataset.id;
                e.dataTransfer.setData('text/plain', productId);
            });
        });

        const cartIcon = document.querySelector('#cart-icon');

        if (cartIcon) {
            cartIcon.addEventListener('dragover', function (e) {
                e.preventDefault();
                this.classList.add('drag-hover');
            });

            cartIcon.addEventListener('dragleave', function () {
                this.classList.remove('drag-hover');
            });

            cartIcon.addEventListener('drop', function (e) {
                e.preventDefault();
                this.classList.remove('drag-hover');
                const productId = e.dataTransfer.getData('text/plain');
                if (productId) {
                    $.post('../backend/logic/OrderHandling/cartHandler.php', {
                        action: 'add',
                        product_id: productId
                    }, function () {
                        alert('Produkt wurde per Drag & Drop zum Warenkorb hinzugefügt!');
                        updateCartCount();
                    });
                }
            });
        }
    }
});
