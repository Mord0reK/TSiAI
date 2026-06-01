<!DOCTYPE html>
<html lang="pl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteka - Logowanie</title>
    <script src="../cdn/tailwind.min.js"></script>
    <script src="../cdn/jquery.js"></script>
    <script src="../cdn/flowbite.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    <!-- ============================
         Tło — rozmazane zdjęcie biblioteki
         ============================ -->
    <div class="fixed inset-0 -z-10">
        <img src="../cdn/obrazy/biblioteka.jpg" alt="Tło biblioteki"
             class="w-full h-full object-cover blur-sm">
        <div class="absolute inset-0 bg-black/60"></div>
    </div>

    <!-- ============================
         Główna zawartość — Card + Formularz
         ============================ -->
    <main class="flex items-center justify-center min-h-screen pt-16 px-4">
        <div class="w-full max-w-md">
            <!-- Card — Flowbite -->
            <div class="p-6 sm:p-8 bg-white/80 backdrop-blur-lg rounded-xl shadow-lg dark:bg-gray-900/80 border border-white/20 dark:border-gray-700">

                <!-- Nagłówek karty -->
                <h1 class="text-2xl font-bold text-center mb-8 text-gray-900 dark:text-white">
                    Panel logowania
                </h1>

                <!-- ==========================================
                     Formularz logowania
                     ========================================== -->
                <form id="loginForm" class="space-y-6">
                    <!-- Pole: Login -->
                    <div id="loginField">
                        <label for="loginInput"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Identyfikator użytkownika
                        </label>
                        <input type="text" name="login" id="loginInput" required
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="Identyfikator">
                    </div>

                    <!-- Pole: Hasło z przyciskiem pokaż/ukryj -->
                    <div id="passwordField">
                        <label for="passwordInput"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Hasło
                        </label>
                        <div class="relative">
                            <input type="password" name="haslo" id="passwordInput" required
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 pr-10"
                                   placeholder="••••••••">
                            <button type="button" id="togglePassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    aria-label="Pokaż/ukryj hasło">
                                <svg id="eyeIcon" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 3.5c-4.5 0-7.8 4.2-9 5.5 1.2 1.3 4.5 5.5 9 5.5s7.8-4.2 9-5.5c-1.2-1.3-4.5-5.5-9-5.5Z"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                <svg id="eyeOffIcon" class="w-5 h-5 hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.933 13.909A10.944 10.944 0 0 1 1.05 9.495c1.2-1.343 4.5-5.5 9-5.5a10.032 10.032 0 0 1 4.068.932M6.336 6.336A3 3 0 0 0 9.5 10.5M5.5 14.5A9.14 9.14 0 0 0 10 15.5c4.479 0 7.8-4.2 9-5.5a11.56 11.56 0 0 0-2.225-3.075M1 1l18 18"/>
                                </svg>
                            </button>
                        </div>
                        <!-- Komunikat błędu pod hasłem -->
                        <p id="errorMsg" class="mt-2 text-sm text-red-600 dark:text-red-400 hidden">
                            <span class="font-medium">Błąd!</span> <span id="errorMsgText"></span>
                        </p>
                    </div>

                    <!-- Przycisk logowania -->
                    <button type="submit" id="loginBtn"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 inline-flex items-center justify-center transition">
                        <svg id="loginSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span id="loginBtnText">Zaloguj się</span>
                    </button>
                </form>

                <!-- ==========================================
                     Formularz zmiany hasła (ukryty domyślnie)
                     ========================================== -->
                <form id="zmienHasloForm" class="hidden space-y-6 mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                        Musisz zmienić hasło przed kontynuacją.
                    </p>

                    <!-- Nowe hasło -->
                    <div>
                        <label for="newPasswordInput"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nowe hasło
                        </label>
                        <div class="relative">
                            <input type="password" name="nowe_haslo" id="newPasswordInput" required minlength="6"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 pr-10"
                                   placeholder="••••••••">
                            <button type="button" id="toggleNewPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    aria-label="Pokaż/ukryj hasło">
                                <svg id="newEyeIcon" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 3.5c-4.5 0-7.8 4.2-9 5.5 1.2 1.3 4.5 5.5 9 5.5s7.8-4.2 9-5.5c-1.2-1.3-4.5-5.5-9-5.5Z"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                <svg id="newEyeOffIcon" class="w-5 h-5 hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.933 13.909A10.944 10.944 0 0 1 1.05 9.495c1.2-1.343 4.5-5.5 9-5.5a10.032 10.032 0 0 1 4.068.932M6.336 6.336A3 3 0 0 0 9.5 10.5M5.5 14.5A9.14 9.14 0 0 0 10 15.5c4.479 0 7.8-4.2 9-5.5a11.56 11.56 0 0 0-2.225-3.075M1 1l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Powtórz hasło -->
                    <div>
                        <label for="repeatPasswordInput"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Powtórz hasło
                        </label>
                        <div class="relative">
                            <input type="password" name="powtorz_haslo" id="repeatPasswordInput" required minlength="6"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 pr-10"
                                   placeholder="••••••••">
                            <button type="button" id="toggleRepeatPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                                    aria-label="Pokaż/ukryj hasło">
                                <svg id="repEyeIcon" class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 3.5c-4.5 0-7.8 4.2-9 5.5 1.2 1.3 4.5 5.5 9 5.5s7.8-4.2 9-5.5c-1.2-1.3-4.5-5.5-9-5.5Z"/>
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                </svg>
                                <svg id="repEyeOffIcon" class="w-5 h-5 hidden" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 18">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.933 13.909A10.944 10.944 0 0 1 1.05 9.495c1.2-1.343 4.5-5.5 9-5.5a10.032 10.032 0 0 1 4.068.932M6.336 6.336A3 3 0 0 0 9.5 10.5M5.5 14.5A9.14 9.14 0 0 0 10 15.5c4.479 0 7.8-4.2 9-5.5a11.56 11.56 0 0 0-2.225-3.075M1 1l18 18"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Alert błędu zmiany hasła — Flowbite -->
                    <div id="zmienHasloErrorAlert" class="hidden flex items-center p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-700 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span id="zmienHasloErrorMsg"></span>
                        <button type="button"
                                class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-700 dark:text-red-400 dark:hover:bg-gray-600"
                                data-dismiss-target="#zmienHasloErrorAlert" aria-label="Zamknij">
                            <span class="sr-only">Zamknij</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Alert sukcesu zmiany hasła — Flowbite -->
                    <div id="zmienHasloSuccessAlert" class="hidden flex items-center p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-700 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                        </svg>
                        <span id="zmienHasloSuccessMsg"></span>
                        <button type="button"
                                class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-700 dark:text-green-400 dark:hover:bg-gray-600"
                                data-dismiss-target="#zmienHasloSuccessAlert" aria-label="Zamknij">
                            <span class="sr-only">Zamknij</span>
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                        </button>
                    </div>

                    <button type="submit" id="zmienHasloBtn"
                            class="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800 inline-flex items-center justify-center transition">
                        <svg id="zmienHasloSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span id="zmienHasloBtnText">Zmień hasło</span>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
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
                                $('#loginForm').addClass('hidden');
                                $('#zmienHasloForm').removeClass('hidden');
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
                    .removeClass('border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500')
                    .addClass('border-red-500 dark:border-red-500 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-300 focus:ring-red-500 focus:border-red-500 placeholder-red-400 dark:placeholder-red-400');

                // Pokaż komunikat błędu
                $('#errorMsgText').text(message);
                $('#errorMsg').removeClass('hidden');
            }

            function clearLoginError() {
                // Przywróć normalne kolory inputów
                $('#loginInput, #passwordInput')
                    .removeClass('border-red-500 dark:border-red-500 bg-red-50 dark:bg-red-900/20 text-red-900 dark:text-red-300 focus:ring-red-500 focus:border-red-500 placeholder-red-400 dark:placeholder-red-400')
                    .addClass('border-gray-300 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500');

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

                $.ajax({
                    url: 'api/auth/zmien_haslo.php',
                    method: 'POST',
                    data: { nowe_haslo: noweHaslo },
                    dataType: 'json'
                })
                .done(function (data) {
                    if (data.status) {
                        showAlert('#zmienHasloSuccessAlert', '#zmienHasloSuccessMsg', 'Hasło zmienione. Za chwilę wrócisz do logowania...');
                        setTimeout(function () {
                            // Wracamy do formularza logowania
                            backToLoginForm();
                        }, 2000);
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

            // --- Powrót do formularza logowania po zmianie hasła ---
            function backToLoginForm() {
                // Ukryj formularz zmiany hasła
                $('#zmienHasloForm').addClass('hidden');
                // Wyczyść pola
                $('#newPasswordInput, #repeatPasswordInput').val('');
                hideAlert('#zmienHasloErrorAlert');
                hideAlert('#zmienHasloSuccessAlert');
                // Pokaż formularz logowania
                $('#loginForm').removeClass('hidden');
                clearLoginError();
                $('#loginInput').val('');
                $('#passwordInput').val('');
            }
        });
    </script>

</body>
</html>
