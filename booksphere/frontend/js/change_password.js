$(document).ready(function () {
    $('#password-change-form').submit(function (e) {
        e.preventDefault();

        const current = $('#current-password').val().trim();
        const neu = $('#new-password').val().trim();
        const confirm = $('#confirm-password').val().trim();

        if (neu !== confirm) {
            $('#password-message').text('❌ Die neuen Passwörter stimmen nicht überein.').css('color', 'red');
            return;
        }

        $.post('../backend/logic/UserManagement/changePassword.php', {
            current_password: current,
            new_password: neu
        }, function (response) {
            if (response.success) {
                $('#password-message').text('✅ Passwort wurde erfolgreich geändert!').css('color', 'green');
                $('#password-change-form')[0].reset();
            } else {
                $('#password-message').text('❌ ' + response.message).css('color', 'red');
            }
        }, 'json');
    });
});
