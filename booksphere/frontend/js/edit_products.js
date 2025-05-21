$(document).ready(function () {
    loadProducts();

    // Neues Produkt erstellen
    $('#new-product-form').on('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        formData.append('action', 'create');

        $.ajax({
            url: '../backend/logic/adminProducts.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function () {
                alert('Produkt erfolgreich erstellt!');
                $('#new-product-form')[0].reset();
                loadProducts();
            }
        });
    });

    // Produkt löschen
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (confirm('Wirklich löschen?')) {
            $.post('../backend/logic/adminProducts.php', { action: 'delete', id: id }, function () {
                loadProducts();
            });
        }
    });

    // Felder aktualisieren
    $(document).on('click', '.update-btn', function () {
        const row = $(this).closest('tr');
        const id = $(this).data('id');
        const fields = ['name', 'price', 'category', 'rating'];

        let updates = fields.map(field => {
            return $.post('../backend/logic/adminProducts.php', {
                action: 'update',
                id: id,
                field: field,
                value: row.find('.' + field).val()
            });
        });

        Promise.all(updates).then(() => {
            alert('Produkt aktualisiert!');
            loadProducts();
        });
    });

    // Produkte laden
    function loadProducts() {
        $.getJSON('../backend/logic/adminProducts.php?action=getAll', function (products) {
            let rows = '';
            products.forEach(prod => {
                rows += `
                <tr>
                    <td>${prod.id}</td>
                    <td><input class="form-control form-control-sm name" value="${prod.name}"></td>
                    <td><input class="form-control form-control-sm price" type="number" step="0.01" value="${prod.price}"></td>
                    <td><input class="form-control form-control-sm category" value="${prod.category}"></td>
                    <td><input class="form-control form-control-sm rating" type="number" step="0.1" value="${prod.rating}"></td>
                    <td><img src="../backend/productpictures/${prod.image}" width="50" class="rounded shadow-sm"></td>
                    <td class="d-flex gap-2">
                        <button class="btn btn-sm btn-dark update-btn" data-id="${prod.id}">
                            <i class="bi bi-save"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-btn" data-id="${prod.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                `;
            });
            $('#product-table tbody').html(rows);
        });
    }
});
