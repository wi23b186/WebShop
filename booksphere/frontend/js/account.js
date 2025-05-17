// js/account.js

$(document).ready(function () {
    let userData = {};

    function renderViewMode(data) {
        $('#account-form').html(`
            <p><strong>Anrede:</strong> ${data.salutation}</p>
            <p><strong>Name:</strong> ${data.firstname} ${data.lastname}</p>
            <p><strong>Adresse:</strong> ${data.address}, ${data.postalcode} ${data.city}</p>
            <p><strong>E-Mail:</strong> ${data.email}</p>
            <p><strong>Zahlungsinfo:</strong> ${data.payment_info}</p>
        `);
    }

    function renderEditMode(data) {
        $('#account-form').html(`
            <label>Adresse:<br><input type="text" id="address" value="${data.address}" class="large-input"></label><br><br>
            <label>PLZ:<br><input type="text" id="postalcode" value="${data.postalcode}" class="large-input"></label><br><br>
            <label>Stadt:<br><input type="text" id="city" value="${data.city}" class="large-input"></label><br><br>
            <label>Zahlungsinfo:<br><input type="text" id="payment_info" value="${data.payment_info}" class="large-input"></label><br><br>
            <label>Benutzername:<br><input type="text" id="username" value="${data.username}" class="large-input"></label><br><br>
        `);
    }

    $.getJSON('../backend/logic/getAccountData.php', function (data) {
        if (!data.loggedIn || data.role !== 'customer') {
            window.location.href = 'login.html';
            return;
        }

        userData = data;
        renderViewMode(userData);
        loadOrders();
    });

    $('#edit-btn').on('click', function () {
        renderEditMode(userData);
        $('#edit-btn').hide();
        $('#save-btn').show();
    });

    $('#save-btn').on('click', function () {
        const updated = {
            address: $('#address').val(),
            postalcode: $('#postalcode').val(),
            city: $('#city').val(),
            payment_info: $('#payment_info').val(),
            username: $('#username').val()
        };

        $.post('../backend/logic/updateAccountData.php', updated, function (response) {
            if (response.success) {
                userData = Object.assign(userData, updated);
                renderViewMode(userData);
                $('#save-btn').hide();
                $('#edit-btn').show();
                alert('Daten wurden erfolgreich gespeichert!');
            } else {
                alert('Fehler: ' + response.message);
            }
        }, 'json');
    });

    function loadOrders() {
        $.getJSON('../backend/logic/get_orders.php', function (orders) {
            let html = '';
            let index = 0;

            orders.sort((a, b) => new Date(a.order_date) - new Date(b.order_date));

            orders.forEach(order => {
                const orderId = order.id;
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading${index}">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                                Bestellung #${order.id} – ${order.order_date} – Gesamt: € ${order.total}
                            </button>
                        </h2>
                        <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#order-history">
                            <div class="accordion-body">
                                ${order.voucher_code ? `<p><strong>Verwendeter Gutscheincode:</strong> ${order.voucher_code}</p>` : ''}
                                <ul>
                                    ${order.items.map(item => `
                                        <li>${item.name} – Menge: ${item.quantity} – Preis: € ${item.price}</li>
                                    `).join('')}
                                </ul>
                                 <button class="btn btn-sm btn-outline-secondary" onclick="window.open('../backend/logic/invoice.php?order_id=${orderId}', '_blank')">
                                    Rechnung generieren
                                </button>
                            </div>
                        </div>
                    </div>`;
                index++;
            });

            $('#order-history').html(html);
        });
    }
});
