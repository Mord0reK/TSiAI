$(document).ready(function () {
    initNawigacja();
    initWylogowywanie();
    initKsiazki();
    initDaneOsobowe();
    initZmianaHasla();
    initWyszukiwarki();
    zaladujKsiazki();
});

// ============================================================
// NAWIGACJA SIDEBAR
// ============================================================
function initNawigacja() {
    var $links = $('.sidebar-link');
    var $sections = $('.content-section');
    var $pageTitle = $('#pageTitle');

    var titles = {
        'ksiazki': 'Książki',
        'rezerwacje': 'Rezerwacje',
        'wypozyczenia': 'Wypożyczenia',
        'dane': 'Dane osobowe'
    };

    $links.on('click', function (e) {
        e.preventDefault();
        var section = $(this).data('section');

        $links
            .removeClass('bg-amber-500/10 text-amber-400 border border-amber-500/20')
            .addClass('text-gray-400 hover:bg-gray-800 hover:text-white');

        $(this)
            .removeClass('text-gray-400 hover:bg-gray-800 hover:text-white')
            .addClass('bg-amber-500/10 text-amber-400 border border-amber-500/20');

        $sections.addClass('hidden');
        $('#section-' + section).removeClass('hidden');

        $pageTitle.text(titles[section] || section);

        // Załaduj dane przy przejściu do sekcji
        if (section === 'rezerwacje') zaladujRezerwacje();
        if (section === 'wypozyczenia') zaladujWypozyczenia();
    });
}

// ============================================================
// WYLOGOWANIE
// ============================================================
function initWylogowywanie() {
    $('#logoutBtn').on('click', function () {
        var $btn = $(this);
        var $btnText = $('#logoutBtnText');

        $btn.prop('disabled', true);
        $btnText.text('Trwa wylogowywanie...');

        $.ajax({
            url: '../api/auth/logout.php',
            method: 'POST',
            dataType: 'json'
        })
        .done(function () {
            window.location.href = '../index.php';
        })
        .fail(function () {
            window.location.href = '../index.php';
        });
    });
}

// ============================================================
// KSIĄŻKI — ładowanie listy
// ============================================================
function initKsiazki() {
    $(document).on('click', '.rez-btn', function () {
        var $btn = $(this);
        var ksiazkaId = $btn.data('id');

        if ($btn.hasClass('rez-loading')) return;

        $btn.addClass('rez-loading');
        var origText = $btn.text();
        $btn.text('...');

        $.ajax({
            url: '../api/rezerwacje/dodaj.php',
            method: 'POST',
            data: { ksiazka_id: ksiazkaId },
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                zaladujKsiazki($('#ksiazkiSearch').val());
                pokazAlert('success', data.komunikat || 'Pomyślnie zarezerwowano książkę');
            } else {
                pokazAlert('danger', data.komunikat || 'Nie udało się utworzyć rezerwacji');
                $btn.text(origText).removeClass('rez-loading');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
                msg = jqXHR.responseJSON.komunikat;
            }
            pokazAlert('danger', msg);
            $btn.text(origText).removeClass('rez-loading');
        });
    });
}

function zaladujKsiazki(szukaj) {
    var $loading = $('#ksiazkiLoading');
    var $table = $('#ksiazkiTable');
    var $tbody = $('#ksiazkiTableBody');
    var $empty = $('#ksiazkiEmpty');

    $loading.removeClass('hidden');
    $table.addClass('hidden');
    $empty.addClass('hidden');

    var dane = {};
    if (szukaj && szukaj.trim() !== '') {
        dane.szukaj = szukaj.trim();
    }

    $.ajax({
        url: '../api/ksiazki/list.php',
        method: 'GET',
        data: dane,
        dataType: 'json'
    })
    .done(function (data) {
        $loading.addClass('hidden');

        if (data.status && data.ksiazki && data.ksiazki.length > 0) {
            $tbody.empty();

            $.each(data.ksiazki, function (i, k) {
                var dostepne = k.ilosc_dostepnych;
                var badge = dostepne > 0
                    ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-900/40 text-green-400 border border-green-800/50">' + dostepne + ' / ' + k.ilosc_calkowita + '</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-900/40 text-red-400 border border-red-800/50">Brak</span>';

                var btn = dostepne > 0
                    ? '<button class="rez-btn px-3 py-1.5 text-xs font-medium text-amber-400 border border-amber-500/30 rounded-lg hover:bg-amber-500/10 transition-colors" data-id="' + k.id + '">Zarezerwuj</button>'
                    : '<span class="text-xs text-gray-600">Niedostępna</span>';

                $tbody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(k.tytul) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(k.autor) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(k.wydawnictwo) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + escapeHtml(String(k.rok_wydania)) + '</td>' +
                        '<td class="px-6 py-4">' + badge + '</td>' +
                        '<td class="px-6 py-4 text-right">' + btn + '</td>' +
                    '</tr>'
                );
            });

            $table.removeClass('hidden');
        } else {
            $empty.removeClass('hidden');
        }
    })
    .fail(function (jqXHR) {
        $loading.addClass('hidden');
        var wiadomosc = 'Nie udało się załadować książek';
        if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
            wiadomosc = jqXHR.responseJSON.komunikat;
        }
        pokazAlert('ostrzezenie', wiadomosc);
    });
}

// ============================================================
// WYSZUKIWARKI — Enter
// ============================================================
function initWyszukiwarki() {
    $('#ksiazkiSearch').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            zaladujKsiazki($(this).val());
        }
    });

    $('#rezerwacjeSearch').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            wyswietlRezerwacje($(this).val());
        }
    });

    $('#wypozyczeniaSearch').on('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            wyswietlWypozyczenia($(this).val());
        }
    });
}

// ============================================================
// REZERWACJE — ładowanie listy
// ============================================================
var rezerwacjeDane = [];

function zaladujRezerwacje(szukaj) {
    var $loading = $('#rezerwacjeLoading');
    var $table = $('#rezerwacjeTable');
    var $tbody = $('#rezerwacjeTableBody');
    var $empty = $('#rezerwacjeEmpty');

    $loading.removeClass('hidden');
    $table.addClass('hidden');
    $empty.addClass('hidden');

    $.ajax({
        url: '../api/rezerwacje/list.php',
        method: 'GET',
        dataType: 'json'
    })
    .done(function (data) {
        $loading.addClass('hidden');

        if (data.status && data.rezerwacje && data.rezerwacje.length > 0) {
            rezerwacjeDane = data.rezerwacje;
            wyswietlRezerwacje(szukaj || '');
        } else {
            $empty.removeClass('hidden');
        }
    })
    .fail(function (jqXHR) {
        $loading.addClass('hidden');
        var msg = 'Nie udało się załadować rezerwacji';
        if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
            msg = jqXHR.responseJSON.komunikat;
        }
        pokazAlert('danger', msg);
    });
}

function wyswietlRezerwacje(szukaj) {
    var $tbody = $('#rezerwacjeTableBody');
    var $table = $('#rezerwacjeTable');
    var $empty = $('#rezerwacjeEmpty');
    var filtr = szukaj.toLowerCase();

    var wyniki = filtr
        ? rezerwacjeDane.filter(function (r) {
            return r.ksiazka_tytul.toLowerCase().includes(filtr) ||
                   r.ksiazka_autor.toLowerCase().includes(filtr);
        })
        : rezerwacjeDane;

    $tbody.empty();

    if (wyniki.length > 0) {
        $.each(wyniki, function (i, r) {
            var statusBadge = badgeStatus(r.status, 'rezerwacja');
            $tbody.append(
                '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                    '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(r.ksiazka_tytul) + '</td>' +
                    '<td class="px-6 py-4 text-gray-300">' + escapeHtml(r.ksiazka_autor) + '</td>' +
                    '<td class="px-6 py-4 text-gray-400">' + formatData(r.data_rezerwacji) + '</td>' +
                    '<td class="px-6 py-4">' + statusBadge + '</td>' +
                '</tr>'
            );
        });
        $table.removeClass('hidden');
        $empty.addClass('hidden');
    } else {
        $table.addClass('hidden');
        $empty.removeClass('hidden');
    }
}

// ============================================================
// WYPOŻYCZENIA — ładowanie listy (aktywne + stare)
// ============================================================
var wypozyczeniaDane = [];

function zaladujWypozyczenia(szukaj) {
    var $loading = $('#wypozyczeniaLoading');
    var $aktywneSection = $('#wypozyczeniaAktywneSection');
    var $stareSection = $('#wypozyczeniaStareSection');
    var $allEmpty = $('#wypozyczeniaAllEmpty');

    $loading.removeClass('hidden');
    $aktywneSection.addClass('hidden');
    $stareSection.addClass('hidden');
    $allEmpty.addClass('hidden');

    $.ajax({
        url: '../api/wypozyczenia/list.php',
        method: 'GET',
        dataType: 'json'
    })
    .done(function (data) {
        $loading.addClass('hidden');

        if (!data.status || !data.wypozyczenia || data.wypozyczenia.length === 0) {
            $allEmpty.removeClass('hidden');
            return;
        }

        wypozyczeniaDane = data.wypozyczenia;
        wyswietlWypozyczenia(szukaj || '');
    })
    .fail(function (jqXHR) {
        $loading.addClass('hidden');
        var msg = 'Nie udało się załadować wypożyczeń';
        if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
            msg = jqXHR.responseJSON.komunikat;
        }
        pokazAlert('danger', msg);
    });
}

function wyswietlWypozyczenia(szukaj) {
    var filtr = szukaj.toLowerCase();

    var wyniki = filtr
        ? wypozyczeniaDane.filter(function (w) {
            return w.ksiazka_tytul.toLowerCase().includes(filtr) ||
                   w.ksiazka_autor.toLowerCase().includes(filtr);
        })
        : wypozyczeniaDane;

    var aktywne = [];
    var stare = [];

    $.each(wyniki, function (i, w) {
        if (w.status === 'aktywne') {
            aktywne.push(w);
        } else {
            stare.push(w);
        }
    });

    // --- Aktywne ---
    var $aktywneSection = $('#wypozyczeniaAktywneSection');
    var $aktywneBody = $('#wypozyczeniaAktywneBody');
    var $aktywneEmpty = $('#wypozyczeniaAktywneEmpty');
    $aktywneBody.empty();

        if (aktywne.length > 0) {
            $.each(aktywne, function (i, w) {
                $aktywneBody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(w.ksiazka_tytul) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(w.ksiazka_autor) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.data_wypozyczenia) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.termin_zwrotu) + '</td>' +
                    '</tr>'
                );
            });
        $aktywneSection.removeClass('hidden');
        $aktywneEmpty.addClass('hidden');
    } else {
        $aktywneSection.removeClass('hidden');
        $aktywneEmpty.removeClass('hidden');
    }

    // --- Stare ---
    var $stareSection = $('#wypozyczeniaStareSection');
    var $stareBody = $('#wypozyczeniaStareBody');
    var $stareEmpty = $('#wypozyczeniaStareEmpty');
    $stareBody.empty();

    if (stare.length > 0) {
        $.each(stare, function (i, w) {
            $stareBody.append(
                '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                    '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(w.ksiazka_tytul) + '</td>' +
                    '<td class="px-6 py-4 text-gray-300">' + escapeHtml(w.ksiazka_autor) + '</td>' +
                    '<td class="px-6 py-4 text-gray-400">' + formatData(w.data_wypozyczenia) + '</td>' +
                    '<td class="px-6 py-4 text-gray-400">' + formatData(w.termin_zwrotu) + '</td>' +
                    '<td class="px-6 py-4 text-gray-400">' + formatData(w.data_zwrotu) + '</td>' +
                '</tr>'
            );
        });
        $stareSection.removeClass('hidden');
        $stareEmpty.addClass('hidden');
    } else {
        $stareSection.removeClass('hidden');
        $stareEmpty.removeClass('hidden');
    }

    // Ukryj oba jeśli nic nie pasuje
    if (aktywne.length === 0 && stare.length === 0) {
        $aktywneSection.addClass('hidden');
        $stareSection.addClass('hidden');
        $('#wypozyczeniaAllEmpty').removeClass('hidden');
    }
}

// ============================================================
// DANE OSOBOWE — ładowanie i edycja
// ============================================================
function zaladujDane() {
    var $loading = $('#daneLoading');
    var $form = $('#daneForm');

    $loading.removeClass('hidden');
    $form.addClass('hidden');
    $('#daneError').addClass('hidden');
    $('#daneSuccess').addClass('hidden');

    $.ajax({
        url: '../api/czytelnicy/profil.php',
        method: 'GET',
        dataType: 'json'
    })
    .done(function (data) {
        $loading.addClass('hidden');

        if (data.status && data.czytelnik) {
            var c = data.czytelnik;
            $('#daneProfilNazwa').text(c.imie + ' ' + c.nazwisko);
            $('#daneIdentyfikator').val(c.identyfikator || '');
            $('#daneImie').val(c.imie || '');
            $('#daneNazwisko').val(c.nazwisko || '');
            $('#daneNrDokumentu').val(c.nr_dokumentu || '');
            $('#daneAdres').val(c.adres || '');
            $form.removeClass('hidden');
        } else {
            pokazDaneAlert('danger', 'Nie udało się załadować danych');
        }
    })
    .fail(function (jqXHR) {
        $loading.addClass('hidden');
        var msg = 'Nie udało się załadować danych';
        if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
            msg = jqXHR.responseJSON.komunikat;
        }
        pokazDaneAlert('danger', msg);
    });
}

function initDaneOsobowe() {
    $(document).on('click', '[data-section="dane"]', function () {
        zaladujDane();
    });

    $('#daneFormInner').on('submit', function (e) { e.preventDefault(); });

    $('#daneSaveBtn').on('click', function (e) {
        e.preventDefault();

        var $btn = $(this);
        var $spinner = $('#daneSaveSpinner');
        var $btnText = $('#daneSaveBtnText');

        var imie = $('#daneImie').val().trim();
        var nazwisko = $('#daneNazwisko').val().trim();
        var nrDokumentu = $('#daneNrDokumentu').val().trim();
        var adres = $('#daneAdres').val().trim();

        $('#daneError').addClass('hidden');
        $('#daneSuccess').addClass('hidden');

        if (!imie || !nazwisko || !nrDokumentu || !adres) {
            pokazDaneAlert('danger', 'Wszystkie pola są wymagane');
            return;
        }

        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $btnText.text('Zapisywanie...');

        $.ajax({
            url: '../api/czytelnicy/edytuj.php',
            method: 'POST',
            data: {
                imie: imie,
                nazwisko: nazwisko,
                nr_dokumentu: nrDokumentu,
                adres: adres
            },
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                // Aktualizuj nagłówek profilu
                $('#daneProfilNazwa').text(imie + ' ' + nazwisko);
                pokazDaneAlert('success', data.komunikat || 'Dane zostały zaktualizowane');
            } else {
                pokazDaneAlert('danger', data.komunikat || 'Nie udało się zapisać danych');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
                msg = jqXHR.responseJSON.komunikat;
            }
            pokazDaneAlert('danger', msg);
        })
        .always(function () {
            $btn.prop('disabled', false);
            $spinner.addClass('hidden');
            $btnText.text('Zapisz zmiany');
        });
    });
}

function pokazDaneAlert(typ, wiadomosc) {
    var id = typ === 'success' ? 'daneSuccess' : 'daneError';
    var msgId = typ === 'success' ? 'daneSuccessMsg' : 'daneErrorMsg';

    $('#' + msgId).text(wiadomosc);
    $('#' + id).removeClass('hidden');

    if (typ === 'success') {
        setTimeout(function () { $('#' + id).addClass('hidden'); }, 3000);
    }
}

// ============================================================
// ZMIANA HASŁA (w sekcji Dane osobowe)
// ============================================================
function initZmianaHasla() {
    $('#hasloSaveBtn').on('click', function (e) {
        e.preventDefault();

        var stare = $('#hasloStare').val();
        var nowe = $('#hasloNowe').val();
        var powtorz = $('#hasloPowtorz').val();

        $('#hasloError').addClass('hidden');
        $('#hasloSuccess').addClass('hidden');

        if (!stare || !nowe || !powtorz) {
            pokazHasloAlert('danger', 'Wypełnij wszystkie pola');
            return;
        }

        if (nowe.length < 6) {
            pokazHasloAlert('danger', 'Nowe hasło musi mieć co najmniej 6 znaków');
            return;
        }

        if (nowe !== powtorz) {
            pokazHasloAlert('danger', 'Hasła nie są identyczne');
            return;
        }

        var $btn = $(this);
        var $spinner = $('#hasloSaveSpinner');
        var $btnText = $('#hasloSaveBtnText');

        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $btnText.text('Zmienianie...');

        $.ajax({
            url: '../api/czytelnicy/zmien_haslo.php',
            method: 'POST',
            data: { stare_haslo: stare, nowe_haslo: nowe },
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                pokazHasloAlert('success', data.komunikat || 'Hasło zostało zmienione');
                $('#hasloStare').val('');
                $('#hasloNowe').val('');
                $('#hasloPowtorz').val('');
            } else {
                pokazHasloAlert('danger', data.komunikat || 'Nie udało się zmienić hasła');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) {
                msg = jqXHR.responseJSON.komunikat;
            }
            pokazHasloAlert('danger', msg);
        })
        .always(function () {
            $btn.prop('disabled', false);
            $spinner.addClass('hidden');
            $btnText.text('Zmień hasło');
        });
    });
}

function pokazHasloAlert(typ, wiadomosc) {
    var id = typ === 'success' ? 'hasloSuccess' : 'hasloError';
    var msgId = typ === 'success' ? 'hasloSuccessMsg' : 'hasloErrorMsg';

    $('#' + msgId).text(wiadomosc);
    $('#' + id).removeClass('hidden');

    if (typ === 'success') {
        setTimeout(function () { $('#' + id).addClass('hidden'); }, 4000);
    }
}

// ============================================================
// HELPERY
// ============================================================
function pokazAlert(typ, wiadomosc) {
    var id = typ === 'success' ? 'ksiazkiSuccess' : 'ksiazkiError';
    var msgId = typ === 'success' ? 'ksiazkiSuccessMsg' : 'ksiazkiErrorMsg';

    $('#' + msgId).text(wiadomosc);
    $('#' + id).removeClass('hidden');

    if (typ === 'success') {
        setTimeout(function () { $('#' + id).addClass('hidden'); }, 3000);
    }
}

function ukryjAlert() {
    $('#ksiazkiError').addClass('hidden');
    $('#ksiazkiSuccess').addClass('hidden');
}

function badgeStatus(status, typ) {
    var kolory = {
        'aktywna':      'bg-amber-900/40 text-amber-400 border-amber-800/50',
        'zrealizowana': 'bg-green-900/40 text-green-400 border-green-800/50',
        'anulowana':    'bg-red-900/40 text-red-400 border-red-800/50',
        'aktywne':      'bg-amber-900/40 text-amber-400 border-amber-800/50',
        'zwrocone':     'bg-green-900/40 text-green-400 border-green-800/50'
    };

    var etykiety = {
        'aktywna':      'Aktywna',
        'zrealizowana': 'Zrealizowana',
        'anulowana':    'Anulowana',
        'aktywne':      'Aktywne',
        'zwrocone':     'Zwrócone'
    };

    var kolor = kolory[status] || 'bg-gray-900/40 text-gray-400 border-gray-800/50';
    var etykieta = etykiety[status] || status;

    return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ' + kolor + '">' + etykieta + '</span>';
}

function formatData(data) {
    if (!data) return '—';
    // MySQL DATETIME: "2026-06-01 18:30:00" — parsujemy jako czas lokalny (nie UTC)
    var czesci = data.split(/[\sT]/);
    if (czesci.length < 2) return data;
    var daty = czesci[0].split('-');
    var czas = czesci[1].split(':');
    if (daty.length < 3 || czas.length < 2) return data;
    var d = new Date(
        parseInt(daty[0], 10),
        parseInt(daty[1], 10) - 1,
        parseInt(daty[2], 10),
        parseInt(czas[0], 10),
        parseInt(czas[1], 10),
        czas.length > 2 ? parseInt(czas[2], 10) : 0
    );
    if (isNaN(d.getTime())) return data;
    var dzien = String(d.getDate()).padStart(2, '0');
    var miesiac = String(d.getMonth() + 1).padStart(2, '0');
    var rok = d.getFullYear();
    var godzina = String(d.getHours()).padStart(2, '0');
    var minuty = String(d.getMinutes()).padStart(2, '0');
    return dzien + '.' + miesiac + '.' + rok + ' ' + godzina + ':' + minuty;
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
