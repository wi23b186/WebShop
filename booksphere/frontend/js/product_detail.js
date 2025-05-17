$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    $.getJSON('../backend/logic/requestHandler.php', { action: 'getProductById', id: productId }, function (product) {
        if (!product) {
            $('#product-detail').html('<p>Produkt nicht gefunden.</p>');
            return;
        }

        $('#product-detail').html(`
            <div class="product-detail-box">
                <div class="product-image">
                    <img src="../backend/productpictures/${product.image}" alt="${product.name}">
                </div>
                <div class="product-info">
                    <h2>${product.name}</h2>
                    <p><strong>Kategorie:</strong> ${product.category}</p>
                    <p><strong>Bewertung:</strong> ${product.rating} ⭐</p>
                    <p><strong>Preis:</strong> € ${parseFloat(product.price).toFixed(2)}</p>
                    <p>${product.description}</p>
                    <button class="add-to-cart" data-id="${product.id}">In den Warenkorb</button>
                </div>
            </div>
        `);
    });

    $(document).on('click', '.add-to-cart', function () {
        const productId = $(this).data('id');
        $.post('../backend/logic/cartHandler.php', {
            action: 'add',
            product_id: productId
        }, function () {
            alert('Produkt wurde zum Warenkorb hinzugefügt!');
        });
    });
});
