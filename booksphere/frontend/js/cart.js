$(document).ready(function () {
    let originalTotal = 0;

    updateCartView();
    updateCartCount();
    checkLoginStatus();

    // Menge erhöhen
    $(document).on('click', '.increase-qty', function () {
        const id = $(this).data('id');
        updateQuantity(id, 'increase');
    });

    // Menge verringern
    $(document).on('click', '.decrease-qty', function () {
        const id = $(this).data('id');
        updateQuantity(id, 'decrease');
    });

    // Produkt entfernen
    $(document).on('click', '.remove-item', function () {
        const id = $(this).data('id');
        updateQuantity(id, 'remove');
    });

    // Gutscheincode prüfen bei Eingabe
    $('#voucher').on('input', function () {
        const code = $(this).val().trim();

        if (!code) {
            $('#order-message').html('');
            recalculateTotalWithDiscount(0);
            $('#payment').prop('disabled', false);
            checkOrderButtonState();
            return;
        }

        $.getJSON('../backend/logic/VoucherHandling/validate_voucher.php?code=' + encodeURIComponent(code), function (response) {
            if (response.valid) {
                const discount = parseFloat(response.available);
                const newTotal = Math.max(0, originalTotal - discount);
                const restwert = discount > originalTotal ? (discount - originalTotal).toFixed(2) : null;

                recalculateTotalWithDiscount(discount);

                let message = `<span class="text-success">Gutschein gültig: -€ ${Math.min(discount, originalTotal).toFixed(2)}</span>`;
                if (restwert !== null && restwert > 0) {
                    message += `<br><span class="text-info">Restwert verbleibend: € ${restwert}</span>`;
                }

                $('#order-message').html(message);

                // Zahlungsfeld dynamisch deaktivieren
                if (newTotal === 0) {
                    $('#payment').val('').prop('disabled', true);
                } else {
                    $('#payment').prop('disabled', false);
                }

                checkOrderButtonState();

            } else {
                $('#order-message').html('<span class="text-danger">Ungültiger Gutschein.</span>');
                recalculateTotalWithDiscount(0);
                $('#payment').prop('disabled', false);
                checkOrderButtonState();
            }
        }).fail(function () {
            $('#order-message').html('<span class="text-danger">Ungültiger oder abgelaufener Gutschein.</span>');
            recalculateTotalWithDiscount(0);
            $('#payment').prop('disabled', false);
            checkOrderButtonState();
        });
    });

    // Zahlungsart-Änderung überwachen
    $('#payment').on('change', function () {
        checkOrderButtonState();
    });

    // Bestellung absenden
    $(document).on('click', '#order-button', function () {
        const paymentMethod = $('#payment').val();
        const voucherCode = $('#voucher').val().trim();

        if (!paymentMethod && $('#payment').prop('disabled') !== true) {
            $('#order-message').text("Bitte wählen Sie eine Zahlungsmethode.");
            return;
        }

        $.ajax({
            url: '../backend/logic/OrderHandling/place_order.php',
            method: 'POST',
            data: {
                payment: paymentMethod,
                voucher: voucherCode
            },
            success: function (response) {
                $('#order-message').text(response);
                $('#cart-contents').html('<p>Vielen Dank für Ihre Bestellung!</p>');
                $('#total-price').text('');
                updateCartCount();
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                $('#order-message').text("Fehler beim Absenden der Bestellung.");
            }
        });
    });

    // Warenkorb anzeigen
    function updateCartView() {
        $.getJSON('../backend/logic/OrderHandling/cartHandler.php?action=getItems', function (items) {
            let html = '';
            let total = 0;

            if (items.length === 0) {
                html = '<p>Dein Warenkorb ist leer.</p>';
                $('#order-button').hide();
                $('#total-price').text('');
            } else {
                items.forEach(function (item) {
                    const price = parseFloat(item.price);
                    const subtotal = price * item.quantity;
                    total += subtotal;

                    html += `
                        <div class="cart-item mb-3">
                            <p><strong>${item.name}</strong> – € ${price.toFixed(2)}</p>
                            <p>
                                Menge: ${item.quantity}
                                <button class="btn btn-sm btn-outline-secondary decrease-qty" data-id="${item.id}">−</button>
                                <button class="btn btn-sm btn-outline-secondary increase-qty" data-id="${item.id}">+</button>
                                <button class="btn btn-sm btn-outline-danger remove-item" data-id="${item.id}">Entfernen</button>
                            </p>
                        </div>
                        <hr>`;
                });

                originalTotal = total;
                $('#total-price').text(`Gesamtpreis: € ${total.toFixed(2)}`);
                $('#order-button').show();
            }

            $('#cart-contents').html(html);
            checkOrderButtonState();
        }).fail(function () {
            $('#cart-contents').html('<p>Fehler beim Laden des Warenkorbs.</p>');
        });
    }

    // Anzahl im Header aktualisieren
    function updateCartCount() {
        $.getJSON('../backend/logic/OrderHandling/cartHandler.php?action=getCount', function (data) {
            $('#cart-count').text(data.count);
        });
    }

    // Menge ändern
    function updateQuantity(id, change) {
        $.post('../backend/logic/OrderHandling/cartHandler.php', {
            action: 'updateQuantity',
            product_id: id,
            change: change
        }, function () {
            updateCartView();
            updateCartCount();
        });
    }

    // Loginstatus
    function checkLoginStatus() {
        $.getJSON('../backend/logic/UserManagement/userStatus.php', function (data) {
            if (!data.loggedIn) {
                $('#login-warning').removeClass('d-none');
                $('#order-button').prop('disabled', true);
            }
        });
    }

    // Preis mit Gutschein
    function recalculateTotalWithDiscount(discount) {
        const newTotal = Math.max(0, originalTotal - discount);
        $('#total-price').text(`Gesamtpreis: € ${newTotal.toFixed(2)}`);
    }

    // Bestellbutton aktivieren/deaktivieren
    function checkOrderButtonState() {
        const paymentMethod = $('#payment').val();
        const totalText = $('#total-price').text();
        const preisMatch = totalText.match(/€\s?(\d+[\.,]?\d*)/);
        const total = preisMatch ? parseFloat(preisMatch[1].replace(',', '.')) : originalTotal;

        if (total === 0 || paymentMethod) {
            $('#order-button').prop('disabled', false);
        } else {
            $('#order-button').prop('disabled', true);
        }
    }
});
