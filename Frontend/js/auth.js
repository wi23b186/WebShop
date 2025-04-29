// auth.js
$(document).ready(function() {
    // Umschalten zwischen Login- und Registrierungs-Formular
    $('#showLogin').on('click', function() {
      $('#loginContainer').show();
      $('#registrationContainer').hide();
    });
    $('#showRegister').on('click', function() {
      $('#registrationContainer').show();
      $('#loginContainer').hide();
    });
  
    // Live-Prüfung der Passwortfelder im Registrierungsformular
    function checkPasswords() {
      var pass = $('#password_reg').val();
      var confirmPass = $('#confirmPassword').val();
  
      if (pass === "" && confirmPass === "") {
        $('#registerButton').prop('disabled', true);
        $('#passwordError').hide();
        return;
      }
      if (pass !== confirmPass) {
        $('#passwordError').show();
        $('#registerButton').prop('disabled', true);
      } else {
        $('#passwordError').hide();
        $('#registerButton').prop('disabled', false);
      }
    }
    $('#password_reg, #confirmPassword').on('keyup change', function() {
      checkPasswords();
    });
  
    // Login Formular absenden
    $('#loginForm').on('submit', function(e) {
      e.preventDefault();
      var loginData = {
        loginInput: $('#login_email').val(),
        password: $('#login_password').val(),
        remember: $('#remember').is(':checked')
      };
      $.ajax({
        type: 'POST',
        url: window.location.origin + "/WebShop/Backend/config/login.php", // ggf. anpassen
        contentType: 'application/json',
        data: JSON.stringify(loginData),
        dataType: 'json',
        success: function(response) {
          console.log('Login-Antwort:', response);
          alert('Login erfolgreich: ' + response.message);
          // Weiterleitung oder Neuladen
          window.location.href = "index.html";
        },
        error: function(xhr) {
          console.error('Login-Fehler:', xhr.responseText);
          alert('Login fehlgeschlagen: ' + xhr.responseText);
        }
      });
    });
  
    // Registrierungsformular absenden
    $('#registrationForm').on('submit', function(e) {
      e.preventDefault();
  
      // HTML5-Validierung
      if (!this.checkValidity()) {
        this.reportValidity();
        return;
      }
  
      // Abschließende Passwort-Prüfung
      var pass = $('#password_reg').val();
      var confirmPass = $('#confirmPassword').val();
      if (pass !== confirmPass) {
        alert('Die Passwörter stimmen nicht überein.');
        return;
      }
  
      var formData = {
        title: $('#title').val(),
        firstName: $('#firstName').val(),
        lastName: $('#lastName').val(),
        address: $('#address').val(),
        zip: $('#zip').val(),
        city: $('#city').val(),
        email: $('#email').val(),
        username: $('#username_reg').val(),
        password: pass,
        confirmPassword: confirmPass,
        paymentInfo: $('#paymentInfo').val()
      };
      $.ajax({
        type: 'POST',
        url: window.location.origin + "/WebShop/Backend/config/login.php", // ggf. anpassen
        contentType: 'application/json',
        data: JSON.stringify(formData),
        dataType: 'json',
        success: function(response) {
          console.log('Registrierungs-Antwort:', response);
          alert('Registrierung erfolgreich: ' + response.message);
        },
        error: function(xhr, status, error) {
          console.error('Fehler bei Registrierung:', xhr.responseText);
          alert('Ein technischer Fehler ist aufgetreten: ' + xhr.responseText);
        }
      });
    });
  });
  