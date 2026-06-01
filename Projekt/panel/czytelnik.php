<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Przekieruj jeśli nie zalogowany lub admin
if (!isset($_SESSION['uzytkownik'])) {
    header('Location: ../index.php');
    exit;
}
if ($_SESSION['uzytkownik']['typ'] === 'admin') {
    header('Location: admin.php');
    exit;
}

$uzytkownik = $_SESSION['uzytkownik'];
$imieNazwisko = $uzytkownik['imieNazwisko'] ?? 'Czytelnik';
$typ = ucfirst($uzytkownik['typ'] ?? 'czytelnik');
?>
<!DOCTYPE html>
<html lang="pl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Czytelnika - Biblioteka</title>
    <script src="../../cdn/tailwind.min.js"></script>
    <script src="../../cdn/jquery.js"></script>
    <script src="../../cdn/flowbite.min.js"></script>
    <script src="czytelnik.js"></script>
    <style>
        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #404040;
            border-radius: 4px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #525252;
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-200 min-h-screen">

<!-- ============================================
     LAYOUT: Sidebar + Główna zawartość
     ============================================ -->
<div class="flex min-h-screen">

    <!-- ============================
         SIDEBAR
         ============================ -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen bg-gray-900 border-r border-gray-800 flex flex-col">

        <!-- Logo / Nagłówek -->
        <div class="px-5 py-5 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-amber-500 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <div>
                    <h1 class="text-base font-bold text-white leading-tight">Biblioteka</h1>
                    <p class="text-xs text-gray-400">Panel czytelnika</p>
                </div>
            </div>
        </div>

        <!-- Nawigacja — rośnie by zająć dostępne miejsce -->
        <nav class="flex-1 px-3 py-4 sidebar-scroll overflow-y-auto">
            <ul class="space-y-1" id="sidebarMenu">
                <!-- Książki -->
                <li>
                    <a href="#" data-section="ksiazki"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        <svg class="w-5 h-5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span>Książki</span>
                    </a>
                </li>
                <!-- Rezerwacje -->
                <li>
                    <a href="#" data-section="rezerwacje"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              text-gray-400 hover:bg-gray-800 hover:text-white">
                        <svg class="w-5 h-5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <span>Rezerwacje</span>
                    </a>
                </li>

                <!-- Wypożyczenia -->
                <li>
                    <a href="#" data-section="wypozyczenia"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              text-gray-400 hover:bg-gray-800 hover:text-white">
                        <svg class="w-5 h-5 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Wypożyczenia</span>
                    </a>
                </li>

                <!-- Dane osobowe -->
                <li>
                    <a href="#" data-section="dane"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              text-gray-400 hover:bg-gray-800 hover:text-white">
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>
                        <span>Dane osobowe</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- ============================
             DOLNA SEKCJA — User info + Wyloguj
             ============================ -->
        <div class="border-t border-gray-800 px-4 py-4">
            <!-- Info o userze -->
            <div class="flex items-center gap-3 mb-3">
                <!-- Avatar -->
                <div class="w-9 h-9 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate" title="<?= htmlspecialchars($imieNazwisko) ?>">
                        <?= htmlspecialchars($imieNazwisko) ?>
                    </p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($typ) ?></p>
                </div>
            </div>

            <!-- Przycisk wylogowania -->
            <button id="logoutBtn" type="button"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400
                           rounded-lg border border-gray-700 hover:bg-gray-800 hover:text-white hover:border-gray-600
                           transition-colors">
                <svg class="shrink-0 w-5 h-5 transition duration-75 group-hover:text-fg-brand" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/></svg>
                <span id="logoutBtnText">Wyloguj się</span>
            </button>
        </div>
    </aside>

    <!-- ============================
         GŁÓWNA ZAWARTOŚĆ (prawa strona)
         ============================ -->
    <main class="flex-1 ml-64">

        <!-- Top bar -->
        <header class="sticky top-0 z-30 bg-gray-900/80 backdrop-blur-md border-b border-gray-800">
            <div class="flex items-center justify-between px-6 py-6">
                <div class="flex items-center gap-3">
                    <!-- Hamburger (mobile — na razie ukryty, przyda się później) -->
                    <button id="sidebarToggle" type="button"
                            class="lg:hidden p-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-white transition-colors">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h2 id="pageTitle" class="text-lg font-semibold text-white">Książki</h2>
                </div>
            </div>
        </header>

        <!-- Zawartość sekcji -->
        <div class="p-6">
            <!-- Sekcja: Książki -->
            <div id="section-ksiazki" class="content-section">

                <!-- Alert błędu -->
                <div id="ksiazkiError" class="hidden mb-4 flex items-center p-4 text-sm text-red-400 rounded-lg bg-red-900/30 border border-red-800" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span id="ksiazkiErrorMsg"></span>
                    <button type="button" class="ms-auto -mx-1.5 -my-1.5 rounded-lg focus:ring-2 p-1.5 hover:bg-red-800 inline-flex items-center justify-center h-8 w-8"
                            onclick="$('#ksiazkiError').addClass('hidden')">
                        <span class="sr-only">Zamknij</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>

                <!-- Alert sukcesu -->
                <div id="ksiazkiSuccess" class="hidden mb-4 flex items-center p-4 text-sm text-green-400 rounded-lg bg-green-900/30 border border-green-800" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span id="ksiazkiSuccessMsg"></span>
                </div>

                <!-- Wyszukiwarka -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="ksiazkiSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po tytule, autorze lub wydawnictwie...">
                    </div>
                </div>

                <!-- Tabela książek -->
                <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                    <!-- Loading -->
                    <div id="ksiazkiLoading" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-400">Ładowanie książek...</span>
                    </div>

                    <!-- Tabela -->
                    <table id="ksiazkiTable" class="hidden w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Tytuł</th>
                                <th scope="col" class="px-6 py-3 font-medium">Autor</th>
                                <th scope="col" class="px-6 py-3 font-medium">Wydawnictwo</th>
                                <th scope="col" class="px-6 py-3 font-medium">Rok</th>
                                <th scope="col" class="px-6 py-3 font-medium">Dostępne</th>
                                <th scope="col" class="px-6 py-3 font-medium text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody id="ksiazkiTableBody">
                        </tbody>
                    </table>

                    <!-- Brak danych -->
                    <div id="ksiazkiEmpty" class="hidden py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Brak książek w bibliotece</p>
                    </div>
                </div>
            </div>
            <!-- Sekcja: Rezerwacje -->
            <div id="section-rezerwacje" class="content-section hidden">

                <!-- Wyszukiwarka -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="rezerwacjeSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po tytule lub autorze...">
                    </div>
                </div>

                <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                    <div id="rezerwacjeLoading" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-400">Ładowanie rezerwacji...</span>
                    </div>

                    <table id="rezerwacjeTable" class="hidden w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-medium">Książka</th>
                                <th scope="col" class="px-6 py-3 font-medium">Autor</th>
                                <th scope="col" class="px-6 py-3 font-medium">Data rezerwacji</th>
                                <th scope="col" class="px-6 py-3 font-medium">Status</th>
                            </tr>
                        </thead>
                        <tbody id="rezerwacjeTableBody"></tbody>
                    </table>

                    <div id="rezerwacjeEmpty" class="hidden py-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">Nie masz żadnych rezerwacji</p>
                    </div>
                </div>
            </div>

            <!-- Sekcja: Wypożyczenia -->
            <div id="section-wypozyczenia" class="content-section hidden">

                <!-- Wyszukiwarka -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="wypozyczeniaSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po tytule lub autorze...">
                    </div>
                </div>

                <!-- Loading (jeden dla obu tabel) -->
                <div id="wypozyczeniaLoading" class="flex items-center justify-center py-12">
                    <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="ml-3 text-sm text-gray-400">Ładowanie wypożyczeń...</span>
                </div>

                <!-- ==============================
                     AKTYWNE WYPOŻYCZENIA
                     ============================== -->
                <div id="wypozyczeniaAktywneSection" class="hidden mb-8">
                    <h3 class="text-sm font-semibold text-amber-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Aktywne wypożyczenia
                    </h3>
                    <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                        <table class="w-full text-sm text-left text-gray-300">
                            <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-medium">Książka</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Autor</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Data wypożyczenia</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Termin zwrotu</th>
                                </tr>
                            </thead>
                            <tbody id="wypozyczeniaAktywneBody"></tbody>
                        </table>
                        <div id="wypozyczeniaAktywneEmpty" class="hidden py-8 text-center">
                            <p class="text-sm text-gray-500">Brak aktywnych wypożyczeń</p>
                        </div>
                    </div>
                </div>

                <!-- ==============================
                     STARE WYPOŻYCZENIA
                     ============================== -->
                <div id="wypozyczeniaStareSection" class="hidden">
                    <h3 class="text-sm font-semibold text-gray-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        Zakończone wypożyczenia
                    </h3>
                    <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                        <table class="w-full text-sm text-left text-gray-300">
                            <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 font-medium">Książka</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Autor</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Data wypożyczenia</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Termin zwrotu</th>
                                    <th scope="col" class="px-6 py-3 font-medium">Data zwrotu</th>
                                </tr>
                            </thead>
                            <tbody id="wypozyczeniaStareBody"></tbody>
                        </table>
                        <div id="wypozyczeniaStareEmpty" class="hidden py-8 text-center">
                            <p class="text-sm text-gray-500">Brak zakończonych wypożyczeń</p>
                        </div>
                    </div>
                </div>

                <!-- Empty state (oba puste) -->
                <div id="wypozyczeniaAllEmpty" class="hidden py-12 text-center">
                    <svg class="mx-auto h-10 w-10 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Nie masz żadnych wypożyczeń</p>
                </div>
            </div>
            <!-- Sekcja: Dane osobowe -->
            <div id="section-dane" class="content-section hidden">

                <!-- Alert błędu -->
                <div id="daneError" class="hidden mb-6 flex items-center p-4 text-sm text-red-400 rounded-lg bg-red-900/30 border border-red-800" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span id="daneErrorMsg"></span>
                </div>

                <!-- Alert sukcesu -->
                <div id="daneSuccess" class="hidden mb-6 flex items-center p-4 text-sm text-green-400 rounded-lg bg-green-900/30 border border-green-800" role="alert">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span id="daneSuccessMsg"></span>
                </div>

                <!-- Loading -->
                <div id="daneLoading" class="flex items-center justify-center py-20">
                    <svg class="animate-spin h-8 w-8 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="ml-3 text-sm text-gray-400">Ładowanie danych...</span>
                </div>

                <!-- Zawartość formularza -->
                <div id="daneForm" class="hidden">

                    <!-- Nagłówek profilu -->
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 mb-6">
                        <div class="flex items-center gap-6">
                            <!-- Avatar -->
                            <div class="w-20 h-20 rounded-full bg-amber-500/20 border-2 border-amber-500/30 flex items-center justify-center shrink-0">
                                <svg class="w-10 h-10 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                            </div>
                            <div>
                                <h3 id="daneProfilNazwa" class="text-xl font-bold text-white">—</h3>
                                <p class="text-sm text-gray-400 mt-1">Czytelnik</p>
                            </div>
                        </div>
                    </div>

                    <!-- Formularz w dwóch kolumnach -->
                    <form id="daneFormInner" class="space-y-6">

                        <!-- Kolumna 1: Dane osobowe -->
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                </svg>
                                Dane osobowe
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Imię -->
                                <div>
                                    <label for="daneImie" class="block mb-1.5 text-sm font-medium text-gray-300">Imię</label>
                                    <input type="text" id="daneImie" required
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           placeholder="Podaj imię">
                                </div>

                                <!-- Nazwisko -->
                                <div>
                                    <label for="daneNazwisko" class="block mb-1.5 text-sm font-medium text-gray-300">Nazwisko</label>
                                    <input type="text" id="daneNazwisko" required
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           placeholder="Podaj nazwisko">
                                </div>
                            </div>
                        </div>

                        <!-- Kolumna 2: Dane dokumentu -->
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                                </svg>
                                Dane dokumentu
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <!-- Identyfikator (disabled) -->
                                <div>
                                    <label class="block mb-1.5 text-sm font-medium text-gray-400">Identyfikator</label>
                                    <input type="text" id="daneIdentyfikator" disabled
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800/50 text-gray-500 cursor-not-allowed">
                                    <p class="mt-1.5 text-xs text-gray-600">Identyfikator nie może zostać zmieniony</p>
                                </div>

                                <!-- Nr dokumentu -->
                                <div>
                                    <label for="daneNrDokumentu" class="block mb-1.5 text-sm font-medium text-gray-300">Numer dokumentu</label>
                                    <input type="text" id="daneNrDokumentu" required
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           placeholder="Podaj numer dokumentu">
                                </div>
                            </div>
                        </div>

                        <!-- Kolumna 3: Adres -->
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                Adres zamieszkania
                            </h4>

                            <div>
                                <label for="daneAdres" class="block mb-1.5 text-sm font-medium text-gray-300">Adres</label>
                                <input type="text" id="daneAdres" required
                                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                       placeholder="Ulica, numer, kod pocztowy, miasto">
                            </div>
                        </div>

                        <!-- Kolumna 4: Zmiana hasła -->
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                            <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                                Zmiana hasła
                            </h4>

                            <div class="space-y-4">
                                <div>
                                    <label for="hasloStare" class="block mb-1.5 text-sm font-medium text-gray-300">Aktualne hasło</label>
                                    <input type="password" id="hasloStare"
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           placeholder="Podaj aktualne hasło">
                                </div>

                                <div>
                                    <label for="hasloNowe" class="block mb-1.5 text-sm font-medium text-gray-300">Nowe hasło</label>
                                    <input type="password" id="hasloNowe" minlength="6"
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           placeholder="Minimum 6 znaków">
                                </div>

                                <div>
                                    <label for="hasloPowtorz" class="block mb-1.5 text-sm font-medium text-gray-300">Powtórz nowe hasło</label>
                                    <input type="password" id="hasloPowtorz" minlength="6"
                                           class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                           placeholder="Powtórz nowe hasło">
                                </div>

                                <div id="hasloError" class="hidden flex items-center p-3 text-sm text-red-400 rounded-lg bg-red-900/30 border border-red-800" role="alert">
                                    <span id="hasloErrorMsg"></span>
                                </div>

                                <div id="hasloSuccess" class="hidden flex items-center p-3 text-sm text-green-400 rounded-lg bg-green-900/30 border border-green-800" role="alert">
                                    <span id="hasloSuccessMsg"></span>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" id="hasloSaveBtn"
                                            class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-6 py-2.5 inline-flex items-center justify-center transition">
                                        <svg id="hasloSaveSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                        <span id="hasloSaveBtnText">Zmień hasło</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Przycisk zapisz dane -->
                        <div class="flex justify-end">
                            <button type="submit" id="daneSaveBtn"
                                    class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-8 py-3 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-amber-800 inline-flex items-center justify-center transition">
                                <svg id="daneSaveSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span id="daneSaveBtnText">Zapisz zmiany</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
</div>


</body>
</html>
