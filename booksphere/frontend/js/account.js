$(document).ready(function () {
    let userData = {};

    function renderViewMode(data) {
        $('#account-form').html(`
            <div class="row gy-2">
                <div class="col-12">
                    <p><strong>Anrede:</strong> ${data.salutation}</p>
                </div>
                <div class="col-12">
                    <p><strong>Name:</strong> ${data.firstname} ${data.lastname}</p>
                </div>
                <div class="col-12">
                    <p><strong>Adresse:</strong> ${data.address}, ${data.postalcode} ${data.city}</p>
                </div>
                <div class="col-12">
                    <p><strong>E-Mail:</strong> ${data.email}</p>
                </div>
                <div class="col-12">
                    <p><strong>Zahlungsinfo:</strong> ${data.payment_info}</p>
                </div>
            </div>
        `);
    }

    function renderEditMode(data) {
        $('#account-form').html(`
            <div class="row gy-3">
                <div class="col-md-6">
                    <label for="address" class="form-label">Adresse</label>
                    <input type="text" id="address" value="${data.address}" class="form-control form-control-lg rounded-3 shadow-sm">
                </div>
                <div class="col-md-3">
                    <label for="postalcode" class="form-label">PLZ</label>
                    <input type="text" id="postalcode" value="${data.postalcode}" class="form-control form-control-lg rounded-3 shadow-sm">
                </div>
                <div class="col-md-3">
                    <label for="city" class="form-label">Stadt</label>
                    <input type="text" id="city" value="${data.city}" class="form-control form-control-lg rounded-3 shadow-sm">
                </div>
                <div class="col-md-6">
                    <label for="payment_info" class="form-label">Zahlungsinfo</label>
                    <input type="text" id="payment_info" value="${data.payment_info}" class="form-control form-control-lg rounded-3 shadow-sm">
                </div>
                <div class="col-md-6">
                    <label for="username" class="form-label">Benutzername</label>
                    <input type="text" id="username" value="${data.username}" class="form-control form-control-lg rounded-3 shadow-sm">
                </div>
                <div class="col-md-12">
                    <label for="current_password" class="form-label">Aktuelles Passwort</label>
                    <input type="password" id="current_password" class="form-control form-control-lg rounded-3 shadow-sm" required>
                </div>
            </div>
        `);
    }

    $.getJSON('../backend/logic/UserManagement/getAccountData.php', function (data) {
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
            username: $('#username').val(),
            current_password: $('#current_password').val()
        };

        if (!updated.current_password) {
            alert("Bitte gib dein aktuelles Passwort ein.");
            return;
        }

        $.post('../backend/logic/UserManagement/updateAccountData.php', updated, function (response) {
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
        $.getJSON('../backend/logic/OrderHandling/get_orders.php', function (orders) {
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
                                <ul class="mb-3">
                                    ${order.items.map(item => `
                                        <li>${item.name} – Menge: ${item.quantity} – Preis: € ${item.price}</li>
                                    `).join('')}
                                </ul>
                                <button class="btn btn-sm btn-outline-secondary" onclick="window.open('../backend/logic/Services/invoice.php?order_id=${orderId}', '_blank')">
                                    <i class="bi bi-file-earmark-pdf"></i> Rechnung generieren
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
