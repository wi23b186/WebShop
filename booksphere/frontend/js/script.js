
/*

$(document).ready(function(){
    $('#view-products').click(function(e){
        e.preventDefault();
        $.ajax({
            url: '../backend/logic/requestHandler.php',
            type: 'GET',
            data: { action: 'getProducts' },
            success: function(response) {
                let products = JSON.parse(response);
                let html = '';
                products.forEach(function(prod) {
                    html += '<div class="product-item">';
                    html += '<img src="../backend/productpictures/' + prod.image + '" alt="' + prod.name + '">';
                    html += '<h3>' + prod.name + '</h3>';
                    html += '<p>' + prod.description + '</p>';
                    html += '<p>€' + prod.price + '</p>';
                    html += '<button class="add-to-cart" data-id="' + prod.id + '">Add to Cart</button>';
                    html += '</div>';
                });
                $('#product-list').html(html);
            }
        });
    });
});

*/
