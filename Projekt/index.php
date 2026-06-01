<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['uzytkownik'])) {
    if ($_SESSION['uzytkownik']['typ'] === 'admin') {
        header('Location: panel/admin.php');
    } else {
        header('Location: panel/czytelnik.php');
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteka - Logowanie</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
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
        <div class="p-6 sm:p-8 bg-white rounded-xl shadow-xl dark:bg-gray-800 border border-gray-200 dark:border-gray-700/50 border-t-4 border-t-amber-500">

            <!-- Nagłówek — logowanie (widoczny domyślnie) -->
            <div id="loginHeader" class="mb-8">
                <div class="flex items-center gap-3 mb-1">
                    <svg class="w-10 h-10 shrink-0 text-amber-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 19V4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v13H7a2 2 0 0 0-2 2Zm0 0a2 2 0 0 0 2 2h12M9 3v14m0 0h10"/>
                    </svg>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Logowanie
                    </h1>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Zaloguj się do systemu zarządzania biblioteką</p>
            </div>

            <!-- Nagłówek — powitanie (ukryty domyślnie) -->
            <div id="welcomeHeader" class="hidden mb-8">
                <div class="flex items-center gap-3">
                    <svg class="w-10 h-10 shrink-0 text-amber-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <div>
                        <p class="text-base font-semibold text-gray-900 dark:text-white" id="witajText">Witaj</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Aby kontynuować, ustaw silne hasło</p>
                    </div>
                </div>
            </div>

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
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500"
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
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 pr-10"
                               placeholder="••••••••">
                        <button type="button" id="togglePassword" tabindex="-1"
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
                        <span id="errorMsgText"></span>
                    </p>
                </div>

                <!-- Przycisk logowania -->
                <button type="submit" id="loginBtn"
                        class="w-full text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800 inline-flex items-center justify-center transition">
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
                <!-- Nowe hasło -->
                <div>
                    <label for="newPasswordInput"
                           class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Nowe hasło
                    </label>
                    <div class="relative">
                        <input type="password" name="nowe_haslo" id="newPasswordInput" required minlength="6"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 pr-10"
                               placeholder="••••••••">
                        <button type="button" id="toggleNewPassword" tabindex="-1"
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
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-amber-500 dark:focus:border-amber-500 pr-10"
                               placeholder="••••••••">
                        <button type="button" id="toggleRepeatPassword" tabindex="-1"
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
                        class="w-full text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-amber-600 dark:hover:bg-amber-700 dark:focus:ring-amber-800 inline-flex items-center justify-center transition">
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

<script src="script.js"></script>

</body>
</html>