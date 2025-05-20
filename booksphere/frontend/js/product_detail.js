$(document).ready(function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    $.getJSON('../backend/logic/requestHandler.php', { action: 'getProductById', id: productId }, function (product) {
        if (!product) {
            $('#product-detail').html('<div class="alert alert-danger">Produkt nicht gefunden.</div>');
            return;
        }

        $('#product-detail').html(`
    <div class="row g-5 align-items-start">
        <div class="col-md-5">
            <div class="border rounded shadow-sm overflow-hidden bg-white">
                <img src="../backend/productpictures/${product.image}" 
                     alt="${product.name}" 
                     class="img-fluid w-100 d-block" 
                     style="object-fit: cover; aspect-ratio: 3/4;">
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm p-4">
                <h2 class="card-title mb-3">${product.name}</h2>
                <p><strong>Kategorie:</strong> ${product.category}</p>
                <p><strong>Bewertung:</strong> ${product.rating} ⭐</p>
                <p><strong>Preis:</strong> <span class="fw-bold text-success">€ ${parseFloat(product.price).toFixed(2)}</span></p>
                <p class="mt-3">${product.description}</p>
                <button class="btn btn-dark mt-4 add-to-cart" data-id="${product.id}">
                    <i class="bi bi-cart-plus"></i> In den Warenkorb
                </button>
            </div>
        </div>
    </div>
`);
    });

    $(document).on('click', '.add-to-cart', function () {
        const productId = $(this).data('id');
        $.post('../backend/logic/OrderHandling/cartHandler.php', {
            action: 'add',
            product_id: productId
        }, function () {
            alert('Produkt wurde zum Warenkorb hinzugefügt!');
        });
    });
});
