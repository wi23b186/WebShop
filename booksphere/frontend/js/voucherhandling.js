$(document).ready(function () {
    checkAdminAccess();
    loadVouchers();

    // Klick auf "Gutschein erstellen"
  $('#create-voucher').on('click', function () {
       // const code = $('#voucher-code').val().trim().toUpperCase(); // leer? → PHP generiert
        const value = parseFloat($('#voucher-value').val());
        const date = $('#voucher-date').val();

        // Eingabefehler prüfen
        if (!value || value <= 0) {
            alert('Bitte einen gültigen Gutscheinwert eingeben.');
            return;
        }

        if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
            alert('Bitte ein gültiges Datum im Format YYYY-MM-DD angeben.');
            return;
        }

        // Gutschein anlegen (PHP generiert den Code)
        $.post('../backend/logic/VoucherHandling/create_voucher.php', {
            value: value,
            date: date
        }, function (res) {
            if (res.success) {
                alert('Gutschein erfolgreich erstellt!');
                // $('#voucher-code').val(res.code); // Gutschein-Code wird vom PHP generiert
                $('#voucher-value').val('');
                $('#voucher-date').val('');
                loadVouchers();
            } else {
                alert('Fehler: ' + res.message);
            }
        }, 'json');
    });

    // Funktion: Gutscheine vom Server laden und anzeigen
    function loadVouchers() {
        $.getJSON('../backend/logic/VoucherHandling/list_vouchers.php', function (data) {
            const tbody = $('#voucher-table tbody');
            tbody.empty();

            const today = new Date().toISOString().split('T')[0];

            // Für jeden Gutschein eine Tabellenzeile erstellen
            data.forEach(v => {
            const used = parseFloat(v.used_value);
            const total = parseFloat(v.value);
            const today = new Date().setHours(0,0,0,0);
            const expiry = new Date(v.expiry_date).setHours(0,0,0,0);

            // Statuslogik
            let status = '🟢 Aktiv';
            if (expiry < today) {
                status = '❌ Abgelaufen';
            } else if (used >= total) {
                status = '✅ Verbraucht';
            }
            else if (expiry - today <= 7 * 24 * 60 * 60 * 1000) {
                status = '🟡 Bald ablaufend';
            }

            // Zeile hinzufügen
            tbody.append(`
                <tr>
                    <td>${v.code}</td>
                    <td>€ ${total.toFixed(2)}</td>
                    <td>€ ${used.toFixed(2)}</td>
                        <td>${v.expiry_date}</td>
                        <td>${status}</td>
                        <td><button class="btn btn-sm btn-danger delete-btn" data-id="${v.id}">🗑️</button></td>
                    </tr>
                `);
            });
        });
    }

    // Klick auf Papierkorb: Gutschein löschen
    $(document).on('click', '.delete-btn', function () {
        const id = $(this).data('id');
        if (confirm('Gutschein wirklich löschen?')) {
            $.post('../backend/logic/VoucherHandling/delete_voucher.php', { id }, function (res) {
                if (res.success) {
                    loadVouchers();
                } else {
                    alert('Fehler beim Löschen');
                }
            }, 'json');
        }
    });

    // Zugriffsschutz für Adminseite
    function checkAdminAccess() {
        $.getJSON('../backend/logic/UserManagement/userStatus.php', function (data) {
            if (!data.loggedIn || data.role !== 'admin') {
                alert('Zugriff verweigert.');
                window.location.href = 'login.html';
            }
        });
    }
});
