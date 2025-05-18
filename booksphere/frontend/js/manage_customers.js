$(document).ready(function () {
    loadCustomers();

    function loadCustomers() {
        $.getJSON('../backend/logic/manageCustomers.php?action=getCustomers', function (customers) {
            const table = $('#customer-table-body');
            table.empty();

            customers.forEach(c => {
                const activeToggle = `
                    <button class="btn btn-sm ${c.active ? 'btn-danger' : 'btn-success'} toggle-active" 
                        data-id="${c.id}" data-active="${c.active}">
                        ${c.active ? 'Deaktivieren' : 'Aktivieren'}
                    </button>`;

                const viewOrdersBtn = `
                    <button class="btn btn-sm btn-info view-orders" data-id="${c.id}">
                        Bestellungen
                    </button>`;

                table.append(`
                    <tr>
                        <td>${c.id}</td>
                        <td>${c.username}</td>
                        <td>${c.email}</td>
                        <td>${c.active ? '✅' : '❌'}</td>
                        <td>${activeToggle} ${viewOrdersBtn}</td>
                    </tr>
                `);
            });
        });
    }

    $(document).on('click', '.toggle-active', function () {
        const userId = $(this).data('id');
        const newStatus = $(this).data('active') ? 0 : 1;

        $.post('../backend/logic/manageCustomers.php', {
            action: 'toggleActive',
            user_id: userId,
            active: newStatus
        }, function () {
            loadCustomers();
        });
    });

    $(document).on('click', '.view-orders', function () {
        const userId = $(this).data('id');
        $('#orders-section').html('<p>Lade Bestellungen...</p>');

        $.getJSON(`../backend/logic/manageCustomers.php?action=getOrders&user_id=${userId}`, function (orders) {
            if (orders.length === 0) {
                $('#orders-section').html('<p>Keine Bestellungen gefunden.</p>');
                return;
            }

            let html = '<h3>Bestellungen</h3>';
            orders.forEach(order => {
                html += `<div class="card mb-3"><div class="card-body">
                    <h5>Bestellung #${order.id} vom ${order.order_date}</h5>
                    <ul class="list-group">`;
                order.items.forEach(item => {
                    html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        ${item.name} (x${item.quantity})
                        <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">Entfernen</button>
                    </li>`;
                });
                html += `</ul></div></div>`;
            });

            $('#orders-section').html(html);
        });
    });

    $(document).on('click', '.remove-item', function () {
        const itemId = $(this).data('id');
        if (!confirm('Produkt wirklich aus Bestellung entfernen?')) return;

        $.post('../backend/logic/manageCustomers.php', {
            action: 'removeOrderItem',
            item_id: itemId
        }, function () {
            $('.view-orders:visible').click(); // neu laden
        });
    });
});
