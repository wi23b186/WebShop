$(document).ready(function () {
    loadCustomers();

    // Funktion zum Laden aller Kunden
    function loadCustomers() {
        $.getJSON('../backend/logic/controllers/customerController.php?action=getCustomers', function (customers) {
            const table = $('#customer-table-body');
            table.empty(); // Vorherige Einträge entfernen

            // Jeden Kunden als Tabellenzeile einfügen
            customers.forEach(c => {
                const activeToggle = `
                    <button class="btn btn-sm ${c.active == 1 ? 'btn-danger' : 'btn-success'} toggle-active" 
                        data-id="${c.id}" data-active="${c.active}">
                        ${c.active == 1 ? 'Deaktivieren' : 'Aktivieren'}
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
                        <td>${c.active == 1 ? '✅' : '❌'}</td>
                        <td>${activeToggle} ${viewOrdersBtn}</td>
                    </tr>
                `);
            });
        });
    }

    // Aktivierungsstatus eines Kunden ändern (Aktivieren / Deaktivieren)
    $(document).on('click', '.toggle-active', function () {
        const userId = $(this).data('id');
        const newStatus = $(this).data('active') == 1 ? 0 : 1;

        $.ajax({
            url: '../backend/logic/controllers/customerController.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'toggleActive',
                user_id: userId,
                active: newStatus
            },
            success: function (response) {
                if (response.success) {
                    loadCustomers(); // Liste neu laden
                } else {
                    alert('Fehler: ' + response.message);
                }
            }
        });
    });

    // Bestellungen eines Kunden anzeigen
    $(document).on('click', '.view-orders', function () {
        const userId = $(this).data('id');
        loadOrders(userId);
    });

    // Funktion zum Laden der Bestellungen eines Kunden
    function loadOrders(userId) {
  $('#orders-section')
        .removeClass('d-none') // Bestellbereich einblenden
        .data('current-user', userId)
        .html('<p>Lade Bestellungen...</p>');

    $.getJSON(`../backend/logic/controllers/customerController.php?action=getOrders&user_id=${userId}`, function (orders) {
        if (orders.length === 0) {
            $('#orders-section').html('<p>Keine Bestellungen gefunden.</p>');
            return;
        }

        // Bestellungen als Karten anzeigen
        let html = '<h3>Bestellungen</h3>';
        orders.forEach(order => {
          html += `<div class="card mb-3"><div class="card-body">
    <h5>Bestellung #${order.id} vom ${order.order_date} – Gesamt: € ${parseFloat(order.total).toFixed(2)}</h5>
    <button class="btn btn-outline-danger btn-sm mb-2 delete-order" data-id="${order.id}">Bestellung verwerfen</button>
    <ul class="list-group">`;

            // Bestellpositionen auflisten
            order.items.forEach(item => {
                html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    ${item.name} – Menge: ${item.quantity} – Preis: € ${parseFloat(item.price).toFixed(2)}
                    <button class="btn btn-sm btn-danger remove-item" data-id="${item.id}">Entfernen</button>
                </li>`;
            });
            html += `</ul></div></div>`;
        });

        $('#orders-section').html(html);
    });
    }

    // Einzelne Bestellung entfernen + neu laden
    $(document).on('click', '.remove-item', function () {
        const itemId = $(this).data('id');
        if (!confirm('Produkt wirklich aus Bestellung entfernen?')) return;

        $.post('../backend/logic/controllers/customerController.php', {
            action: 'removeOrderItem',
            item_id: itemId
        }, function (response) {
            if (response.success) {
                const userId = $('#orders-section').data('current-user');
                loadOrders(userId); // Neu laden
            } else {
                alert('Fehler beim Entfernen: ' + (response.message || 'Unbekannter Fehler'));
            }
        }, 'json');
    });

    // Ganze Bestellung löschen
    $(document).on('click', '.delete-order', function () {
    const orderId = $(this).data('id');
    if (!confirm('Willst du wirklich die gesamte Bestellung löschen?')) return;

    $.post('../backend/logic/controllers/customerController.php', {
        action: 'deleteOrder',
        order_id: orderId
    }, function (response) {
        if (response.success) {
            const userId = $('#orders-section').data('current-user');
            loadOrders(userId); // Aktualisiert die Ansicht
        } else {
            alert('Fehler beim Löschen der Bestellung.');
        }
    }, 'json');
});

});
