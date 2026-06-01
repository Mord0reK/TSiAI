// ============================================================
// Logowanie + Zmiana hasła — strona główna (index.php)
// ============================================================
$(function () {

    // ============================================================
    // 1. PRZYCISKI POKAŻ/UKRYJ HASŁO
    // ============================================================
    function setupPasswordToggle(btnSelector, inputSelector, iconSelector, offIconSelector) {
        $(btnSelector).on('click', function () {
            var $input = $(inputSelector);
            var isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');
            $(iconSelector).toggleClass('hidden');
            $(offIconSelector).toggleClass('hidden');
        });
    }

    setupPasswordToggle('#togglePassword',       '#passwordInput',      '#eyeIcon',    '#eyeOffIcon');
    setupPasswordToggle('#toggleNewPassword',    '#newPasswordInput',   '#newEyeIcon', '#newEyeOffIcon');
    setupPasswordToggle('#toggleRepeatPassword',  '#repeatPasswordInput', '#repEyeIcon', '#repEyeOffIcon');

    // ============================================================
    // 2. HELPER: Pokaż / ukryj alert
    // ============================================================
    function showAlert(alertSelector, msgSelector, message) {
        $(msgSelector).text(message);
        $(alertSelector).removeClass('hidden');
    }

    function hideAlert(alertSelector) {
        $(alertSelector).addClass('hidden');
    }

    // ============================================================
    // 3. LOGOWANIE — AJAX (jQuery)
    // ============================================================
    // Blokujemy domyślny submit (Enter w polu tekstowym)
    $('#loginForm').on('submit', function (e) { e.preventDefault(); });

    $('#loginBtn').on('click', function (e) {
        e.preventDefault();

        var $form    = $('#loginForm');
        var $btn     = $('#loginBtn');
        var $spinner = $('#loginSpinner');
        var $btnText = $('#loginBtnText');

        // Czyścimy stan błędu
        clearLoginError();

        // Loading
        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $btnText.text('Logowanie...');

        $.ajax({
            url: 'api/auth/login.php',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json'
        })
            .done(function (data) {
                if (data.status) {
                    if (data.typ === 'admin') {
                        window.location.href = 'panel/admin.php';
                    } else if (data.typ === 'czytelnik') {
                        if (data.zmien_haslo) {
                            $('#loginHeader').addClass('hidden');
                            $('#welcomeHeader').removeClass('hidden');
                            $('#loginForm').addClass('hidden');
                            $('#zmienHasloForm').removeClass('hidden');
                            $('#witajText').text('Witaj ' + (data.imieNazwisko || ''));
                            hideAlert('#zmienHasloErrorAlert');
                            hideAlert('#zmienHasloSuccessAlert');
                        } else {
                            window.location.href = 'panel/czytelnik.php';
                        }
                    }
                } else {
                    showLoginError(data.komunikat || 'Błąd logowania');
                }
            })
            .fail(function (jqXHR) {
                // API zwraca kod błędu HTTP (400/401) + JSON z komunikatem
                if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
                    showLoginError(jqXHR.responseJSON.komunikat);
                } else if (jqXHR.status === 0) {
                    showLoginError('Brak połączenia z serwerem');
                } else if (jqXHR.status >= 500) {
                    showLoginError('Wewnętrzny błąd serwera');
                } else {
                    showLoginError('Błąd połączenia z serwerem');
                }
            })
            .always(function () {
                $btn.prop('disabled', false);
                $spinner.addClass('hidden');
                $btnText.text('Zaloguj się');
            });
    });

    // --- Stan błędu logowania (danger na inputach) ---
    function showLoginError(message) {
        // Ustaw czerwone obramowanie na inputach
        $('#loginInput, #passwordInput')
            .removeClass('border-gray-300 dark:border-gray-600 focus:ring-amber-500 focus:border-amber-500')
            .addClass('border-red-500 dark:border-red-500 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-300 focus:ring-red-500 focus:border-red-500 placeholder-red-400 dark:placeholder-red-400');

        // Pokaż komunikat błędu
        $('#errorMsgText').text(message);
        $('#errorMsg').removeClass('hidden');
    }

    function clearLoginError() {
        // Przywróć normalne kolory inputów
        $('#loginInput, #passwordInput')
            .removeClass('border-red-500 dark:border-red-500 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-300 focus:ring-red-500 focus:border-red-500 placeholder-red-400 dark:placeholder-red-400')
            .addClass('border-gray-300 dark:border-gray-600 focus:ring-amber-500 focus:border-amber-500');

        // Ukryj komunikat błędu
        $('#errorMsg').addClass('hidden');
    }

    // ============================================================
    // 4. ZMIANA HASŁA — AJAX (jQuery)
    // ============================================================
    $('#zmienHasloForm').on('submit', function (e) { e.preventDefault(); });

    $('#zmienHasloBtn').on('click', function (e) {
        e.preventDefault();

        var noweHaslo     = $('#newPasswordInput').val();
        var powtorzHaslo  = $('#repeatPasswordInput').val();
        var $btn          = $('#zmienHasloBtn');
        var $spinner      = $('#zmienHasloSpinner');
        var $btnText      = $('#zmienHasloBtnText');

        hideAlert('#zmienHasloErrorAlert');
        hideAlert('#zmienHasloSuccessAlert');

        // Walidacja
        if (noweHaslo !== powtorzHaslo) {
            showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', 'Hasła nie są identyczne');
            return;
        }
        if (noweHaslo.length < 6) {
            showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', 'Hasło musi mieć co najmniej 6 znaków');
            return;
        }

        // Loading
        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $btnText.text('Zmiana hasła...');

        // Pobierz login i hasło z formularza (w DOM, tylko schowany)
        var loginVal = $('#loginInput').val();
        var hasloVal = $('#passwordInput').val();

        $.ajax({
            url: 'api/auth/login.php',
            method: 'POST',
            data: { login: loginVal, haslo: hasloVal, nowe_haslo: noweHaslo },
            dataType: 'json'
        })
            .done(function (data) {
                if (data.status) {
                    showAlert('#zmienHasloSuccessAlert', '#zmienHasloSuccessMsg', 'Hasło zmienione. Za chwilę nastąpi przekierowanie...');
                    setTimeout(function () {
                        window.location.href = 'panel/czytelnik.php';
                    }, 1500);
                } else {
                    showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', data.komunikat || 'Błąd zmiany hasła');
                }
            })
            .fail(function (jqXHR) {
                if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
                    showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', jqXHR.responseJSON.komunikat);
                } else if (jqXHR.status === 0) {
                    showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', 'Brak połączenia z serwerem');
                } else if (jqXHR.status >= 500) {
                    showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', 'Wewnętrzny błąd serwera');
                } else {
                    showAlert('#zmienHasloErrorAlert', '#zmienHasloErrorMsg', 'Błąd połączenia z serwerem');
                }
            })
            .always(function () {
                $btn.prop('disabled', false);
                $spinner.addClass('hidden');
                $btnText.text('Zmień hasło');
            });
    });

});
