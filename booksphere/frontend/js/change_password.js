$(document).ready(function () {
    // Beim Absenden des Passwortformulars
    $('#password-change-form').submit(function (e) {
        e.preventDefault(); // Verhindert das Neuladen der Seite

        // Eingabewerte aus dem Formular holen
        const current = $('#current-password').val().trim();
        const neu = $('#new-password').val().trim();
        const confirm = $('#confirm-password').val().trim();

        // Prüfen, ob neue Passwörter übereinstimmen
        if (neu !== confirm) {
            $('#password-message').text('❌ Die neuen Passwörter stimmen nicht überein.').css('color', 'red');
            return;
        }

        // AJAX-Request an das Backend zum Ändern des Passworts
        $.post('../backend/logic/UserManagement/changePassword.php', {
            current_password: current,
            new_password: neu
        }, function (response) {
            if (response.success) {
                // Erfolgreiche Änderung: Erfolgsmeldung anzeigen und Formular zurücksetzen
                $('#password-message').text('✅ Passwort wurde erfolgreich geändert!').css('color', 'green');
                $('#password-change-form')[0].reset();
            } else {
                // Fehlerfall: Fehlermeldung vom Server anzeigen
                $('#password-message').text('❌ ' + response.message).css('color', 'red');
            }
        }, 'json');
    });
});
