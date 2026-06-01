<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Przekieruj jeśli nie zalogowany lub czytelnik
if (!isset($_SESSION['uzytkownik'])) {
    header('Location: ../index.php');
    exit;
}
if ($_SESSION['uzytkownik']['typ'] !== 'admin') {
    header('Location: czytelnik.php');
    exit;
}

$uzytkownik = $_SESSION['uzytkownik'];
$login = $uzytkownik['login'] ?? 'Admin';
$typ = "Administrator";

?>
<!DOCTYPE html>
<html lang="pl" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administratora - Biblioteka</title>
    <script src="../../cdn/tailwind.min.js"></script>
    <script src="../../cdn/jquery.js"></script>
    <script src="../../cdn/flowbite.min.js"></script>
    <script src="admin.js"></script>
    <style>
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #404040; border-radius: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #525252; }
    </style>
</head>
<body class="bg-gray-950 text-gray-200 min-h-screen">

<div class="flex min-h-screen">

    <!-- ============================
         SIDEBAR
         ============================ -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen bg-gray-900 border-r border-gray-800 flex flex-col">
        <div class="px-5 py-5 border-b border-gray-800">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8 text-amber-500 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
                <div>
                    <h1 class="text-base font-bold text-white leading-tight">Biblioteka</h1>
                    <p class="text-xs text-gray-400">Panel administratora</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-3 py-4 sidebar-scroll overflow-y-auto">
            <ul class="space-y-1" id="sidebarMenu">
                <!-- Książki -->
                <li>
                    <a href="#" data-section="ksiazki"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        <span>Wypożyczenia</span>
                    </a>
                </li>
                <!-- Czytelnicy -->
                <li>
                    <a href="#" data-section="czytelnicy"
                       class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors
                              text-gray-400 hover:bg-gray-800 hover:text-white">
                        <svg class="w-5 h-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        <span>Czytelnicy</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- User info + Wyloguj -->
        <div class="border-t border-gray-800 px-4 py-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-amber-500/20 border border-amber-500/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-amber-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate" title="<?= htmlspecialchars($login) ?>"><?= htmlspecialchars($login) ?></p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($typ) ?></p>
                </div>
            </div>
            <button id="logoutBtn" type="button"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-400
                           rounded-lg border border-gray-700 hover:bg-gray-800 hover:text-white hover:border-gray-600 transition-colors">
                <svg class="shrink-0 w-5 h-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H4m12 0-4 4m4-4-4-4m3-4h2a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3h-2"/></svg>
                <span id="logoutBtnText">Wyloguj się</span>
            </button>
        </div>
    </aside>

    <!-- ============================
         GŁÓWNA ZAWARTOŚĆ
         ============================ -->
    <main class="flex-1 ml-64">
        <header class="sticky top-0 z-30 bg-gray-900/80 backdrop-blur-md border-b border-gray-800">
            <div class="flex items-center justify-between px-6 py-6">
                <div class="flex items-center gap-3">
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

        <div class="p-6">

            <!-- ==============================
                 SEKCJA: REZERWACJE
                 ============================== -->
            <div id="section-rezerwacje" class="content-section hidden">
                <!-- Alert -->
                <div id="rezAlert" class="hidden mb-4 flex items-center p-4 text-sm rounded-lg" role="alert">
                    <span id="rezAlertMsg"></span>
                </div>

                <!-- Wyszukiwarka -->
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="rezSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po tytule, autorze lub czytelniku...">
                    </div>
                </div>

                <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                    <div id="rezLoading" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-400">Ładowanie rezerwacji...</span>
                    </div>

                    <table id="rezTable" class="hidden w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                            <tr>
                                <th class="px-6 py-3 font-medium">Książka</th>
                                <th class="px-6 py-3 font-medium">Autor</th>
                                <th class="px-6 py-3 font-medium">Czytelnik</th>
                                <th class="px-6 py-3 font-medium">Data rezerwacji</th>
                                <th class="px-6 py-3 font-medium">Status</th>
                                <th class="px-6 py-3 font-medium text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody id="rezTableBody"></tbody>
                    </table>

                    <div id="rezEmpty" class="hidden py-12 text-center">
                        <p class="text-sm text-gray-500">Brak rezerwacji</p>
                    </div>
                </div>
            </div>

            <!-- ==============================
                 SEKCJA: WYPOŻYCZENIA
                 ============================== -->
            <div id="section-wypozyczenia" class="content-section hidden">
                <div id="wypAlert" class="hidden mb-4 flex items-center p-4 text-sm rounded-lg" role="alert">
                    <span id="wypAlertMsg"></span>
                </div>

                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="wypSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po tytule, autorze lub czytelniku...">
                    </div>
                </div>

                <div id="wypLoading" class="flex items-center justify-center py-12">
                    <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span class="ml-3 text-sm text-gray-400">Ładowanie wypożyczeń...</span>
                </div>

                <!-- Aktywne -->
                <div id="wypAktywneSection" class="hidden mb-8">
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
                                    <th class="px-6 py-3 font-medium">Książka</th>
                                    <th class="px-6 py-3 font-medium">Autor</th>
                                    <th class="px-6 py-3 font-medium">Czytelnik</th>
                                    <th class="px-6 py-3 font-medium">Data wypożyczenia</th>
                                    <th class="px-6 py-3 font-medium">Termin zwrotu</th>
                                    <th class="px-6 py-3 font-medium text-right">Akcja</th>
                                </tr>
                            </thead>
                            <tbody id="wypAktywneBody"></tbody>
                        </table>
                        <div id="wypAktywneEmpty" class="hidden py-8 text-center">
                            <p class="text-sm text-gray-500">Brak aktywnych wypożyczeń</p>
                        </div>
                    </div>
                </div>

                <!-- Zakończone -->
                <div id="wypStareSection" class="hidden">
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
                                    <th class="px-6 py-3 font-medium">Książka</th>
                                    <th class="px-6 py-3 font-medium">Autor</th>
                                    <th class="px-6 py-3 font-medium">Czytelnik</th>
                                    <th class="px-6 py-3 font-medium">Data wypożyczenia</th>
                                    <th class="px-6 py-3 font-medium">Termin zwrotu</th>
                                    <th class="px-6 py-3 font-medium">Data zwrotu</th>
                                </tr>
                            </thead>
                            <tbody id="wypStareBody"></tbody>
                        </table>
                        <div id="wypStareEmpty" class="hidden py-8 text-center">
                            <p class="text-sm text-gray-500">Brak zakończonych wypożyczeń</p>
                        </div>
                    </div>
                </div>

                <div id="wypAllEmpty" class="hidden py-12 text-center">
                    <p class="text-sm text-gray-500">Brak wypożyczeń</p>
                </div>
            </div>

            <!-- ==============================
                 SEKCJA: CZYTELNICY — LISTA
                 ============================== -->
            <div id="section-czytelnicy" class="content-section hidden">
                <div id="czytAlert" class="hidden mb-4 flex items-center p-4 text-sm rounded-lg" role="alert">
                    <span id="czytAlertMsg"></span>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="czytSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po imieniu, nazwisku lub identyfikatorze...">
                    </div>
                    <button id="czytAddBtn" type="button"
                            class="ml-4 text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Dodaj czytelnika
                    </button>
                </div>

                <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                    <div id="czytLoading" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-400">Ładowanie czytelników...</span>
                    </div>

                    <table id="czytTable" class="hidden w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                            <tr>
                                <th class="px-6 py-3 font-medium">Imię i nazwisko</th>
                                <th class="px-6 py-3 font-medium">Identyfikator</th>
                                <th class="px-6 py-3 font-medium">Nr dokumentu</th>
                                <th class="px-6 py-3 font-medium">Adres</th>
                                <th class="px-6 py-3 font-medium text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody id="czytTableBody"></tbody>
                    </table>

                    <div id="czytEmpty" class="hidden py-12 text-center">
                        <p class="text-sm text-gray-500">Brak czytelników</p>
                    </div>
                </div>
            </div>

            <!-- ==============================
                 SEKCJA: CZYTELNICY — FORMULARZ (dodaj/edytuj)
                 ============================== -->
            <div id="section-czyt-form" class="content-section hidden">

                <div id="czytFormAlert" class="hidden mb-6 flex items-center p-4 text-sm rounded-lg" role="alert">
                    <span id="czytFormAlertMsg"></span>
                </div>

                <!-- Nagłówek profilu -->
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-8 mb-6">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-full bg-amber-500/20 border-2 border-amber-500/30 flex items-center justify-center shrink-0">
                            <svg class="w-10 h-10 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                        <div>
                            <h3 id="czytFormTitle" class="text-xl font-bold text-white">Nowy czytelnik</h3>
                            <p id="czytFormSubtitle" class="text-sm text-gray-400 mt-1">Dodaj nowego czytelnika do systemu</p>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="czytFormId">

                <!-- Karty z polami -->
                <div class="space-y-6">

                    <!-- Dane osobowe -->
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                            Dane osobowe
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-300">Imię</label>
                                <input type="text" id="czytFormImie" required
                                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                       placeholder="Podaj imię">
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-300">Nazwisko</label>
                                <input type="text" id="czytFormNazwisko" required
                                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                       placeholder="Podaj nazwisko">
                            </div>
                        </div>
                    </div>

                    <!-- Dane dokumentu -->
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z" />
                            </svg>
                            Dane dokumentu
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-300">Identyfikator</label>
                                <input type="text" id="czytFormIdent" required
                                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                       placeholder="Unikalny identyfikator">
                            </div>
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-300">Numer dokumentu</label>
                                <input type="text" id="czytFormNrDok" required
                                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                       placeholder="Podaj numer dokumentu">
                            </div>
                        </div>
                    </div>

                    <!-- Adres -->
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                            </svg>
                            Adres zamieszkania
                        </h4>
                        <div>
                            <label class="block mb-1.5 text-sm font-medium text-gray-300">Adres</label>
                            <input type="text" id="czytFormAdres" required
                                   class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                   placeholder="Ulica, numer, kod pocztowy, miasto">
                        </div>
                    </div>

                    <!-- Hasło -->
                    <div class="bg-gray-900 border border-gray-800 rounded-xl p-6">
                        <h4 class="text-sm font-semibold text-white mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                            Hasło i uprawnienia
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <label class="block mb-1.5 text-sm font-medium text-gray-300">Hasło</label>
                                <input type="password" id="czytFormHaslo" minlength="6"
                                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                                       placeholder="Minimum 6 znaków">
                                <p id="czytFormHasloHint" class="mt-1.5 text-xs text-gray-600">Wymagane tylko przy dodawaniu</p>
                            </div>
                            <div>
                                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox" id="czytFormZmienHaslo"
                                           class="w-4 h-4 text-amber-600 bg-gray-800 border-gray-600 rounded focus:ring-amber-500 focus:ring-2">
                                    <span class="text-sm text-gray-300">Wymuś zmianę hasła przy najbliższym logowaniu</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Przyciski -->
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" id="czytFormCancel"
                            class="px-6 py-3 text-sm font-medium text-gray-400 rounded-lg border border-gray-700 hover:bg-gray-800 hover:text-white transition-colors">
                        Anuluj
                    </button>
                    <button type="button" id="czytFormSave"
                            class="text-white bg-amber-600 hover:bg-amber-700 focus:ring-4 focus:ring-amber-300 font-medium rounded-lg text-sm px-8 py-3 inline-flex items-center justify-center transition">
                        <svg id="czytFormSpinner" class="hidden animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span id="czytFormSaveText">Zapisz</span>
                    </button>
                </div>
            </div>

            <!-- ==============================
                 SEKCJA: KSIĄŻKI
                 ============================== -->
            <div id="section-ksiazki" class="content-section">
                <div id="ksiazAlert" class="hidden mb-4 flex items-center p-4 text-sm rounded-lg" role="alert">
                    <span id="ksiazAlertMsg"></span>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </div>
                        <input type="text" id="ksiazSearch"
                               class="block w-full p-2.5 pl-10 text-sm text-white rounded-lg border border-gray-700 bg-gray-800 placeholder-gray-500 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors"
                               placeholder="Szukaj po tytule, autorze lub wydawnictwie...">
                    </div>
                    <button id="ksiazAddBtn" type="button"
                            class="ml-4 text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Dodaj książkę
                    </button>
                </div>

                <div class="relative overflow-x-auto shadow-sm rounded-lg border border-gray-800">
                    <div id="ksiazLoading" class="flex items-center justify-center py-12">
                        <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="ml-3 text-sm text-gray-400">Ładowanie książek...</span>
                    </div>

                    <table id="ksiazTable" class="hidden w-full text-sm text-left text-gray-300">
                        <thead class="text-xs text-gray-400 bg-gray-800/50 border-b border-gray-800">
                            <tr>
                                <th class="px-6 py-3 font-medium">Tytuł</th>
                                <th class="px-6 py-3 font-medium">Autor</th>
                                <th class="px-6 py-3 font-medium">Wydawnictwo</th>
                                <th class="px-6 py-3 font-medium">Rok</th>
                                <th class="px-6 py-3 font-medium">Dostępne</th>
                                <th class="px-6 py-3 font-medium text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody id="ksiazTableBody"></tbody>
                    </table>

                    <div id="ksiazEmpty" class="hidden py-12 text-center">
                        <p class="text-sm text-gray-500">Brak książek w bibliotece</p>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- ==============================
     MODAL: Zatwierdzenie rezerwacji
     ============================== -->
<div id="rezModal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-black/60 backdrop-blur-sm">
    <div class="relative w-full max-w-md mx-auto mt-20">
        <div class="relative bg-gray-900 rounded-xl border border-gray-800 shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-white">Zatwierdź rezerwację</h3>
                <button type="button" class="text-gray-400 hover:text-white rounded-lg p-1.5 hover:bg-gray-800 transition-colors"
                        onclick="$('#rezModal').addClass('hidden')">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <input type="hidden" id="rezModalId">
                <p class="text-sm text-gray-400 mb-4">Podaj termin zwrotu książki:</p>
                <label for="rezModalTermin" class="block mb-1.5 text-sm font-medium text-gray-300">Termin zwrotu</label>
                <input type="date" id="rezModalTermin"
                       class="w-full px-4 py-3 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>
            <div class="flex justify-end gap-3 p-5 border-t border-gray-800">
                <button type="button" onclick="$('#rezModal').addClass('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-gray-400 rounded-lg border border-gray-700 hover:bg-gray-800 hover:text-white transition-colors">
                    Anuluj
                </button>
                <button type="button" id="rezModalConfirm"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg id="rezModalSpinner" class="hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span id="rezModalConfirmText">Zatwierdź</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================
     MODAL: Formularz książki (dodaj/edytuj)
     ============================== -->
<div id="ksiazModal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-black/60 backdrop-blur-sm">
    <div class="relative w-full max-w-lg mx-auto mt-10">
        <div class="relative bg-gray-900 rounded-xl border border-gray-800 shadow-2xl">
            <div class="flex items-center justify-between p-5 border-b border-gray-800">
                <h3 id="ksiazModalTitle" class="text-lg font-semibold text-white">Dodaj książkę</h3>
                <button type="button" class="text-gray-400 hover:text-white rounded-lg p-1.5 hover:bg-gray-800 transition-colors"
                        onclick="$('#ksiazModal').addClass('hidden')">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <input type="hidden" id="ksiazModalId">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block mb-1.5 text-sm font-medium text-gray-300">Tytuł</label>
                        <input type="text" id="ksiazModalTytul" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1.5 text-sm font-medium text-gray-300">Autor</label>
                        <input type="text" id="ksiazModalAutor" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div class="col-span-2">
                        <label class="block mb-1.5 text-sm font-medium text-gray-300">Wydawnictwo</label>
                        <input type="text" id="ksiazModalWydawnictwo" required
                               class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-300">Rok wydania</label>
                        <input type="number" id="ksiazModalRok" required min="1000" max="9999"
                               class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block mb-1.5 text-sm font-medium text-gray-300">Ilość egzemplarzy</label>
                        <input type="number" id="ksiazModalIlosc" required min="1"
                               class="w-full px-4 py-2.5 text-sm rounded-lg border border-gray-700 bg-gray-800 text-white focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 p-5 border-t border-gray-800">
                <button type="button" onclick="$('#ksiazModal').addClass('hidden')"
                        class="px-5 py-2.5 text-sm font-medium text-gray-400 rounded-lg border border-gray-700 hover:bg-gray-800 hover:text-white transition-colors">
                    Anuluj
                </button>
                <button type="button" id="ksiazModalSave"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-amber-600 hover:bg-amber-700 rounded-lg transition-colors inline-flex items-center gap-2">
                    <svg id="ksiazModalSpinner" class="hidden animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span id="ksiazModalSaveText">Zapisz</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ==============================
     MODAL: Potwierdzenie usunięcia
     ============================== -->
<div id="confirmModal" tabindex="-1" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-modal md:h-full bg-black/60 backdrop-blur-sm">
    <div class="relative w-full max-w-sm mx-auto mt-32">
        <div class="relative bg-gray-900 rounded-xl border border-gray-800 shadow-2xl">
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 w-12 h-12 text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
                <h3 class="mb-2 text-lg font-semibold text-white">Potwierdź usunięcie</h3>
                <p id="confirmModalMsg" class="text-sm text-gray-400 mb-6">Czy na pewno chcesz usunąć ten element?</p>
                <div class="flex justify-center gap-3">
                    <button type="button" onclick="$('#confirmModal').addClass('hidden')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-400 rounded-lg border border-gray-700 hover:bg-gray-800 hover:text-white transition-colors">
                        Anuluj
                    </button>
                    <button type="button" id="confirmModalYes"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Usuń
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
