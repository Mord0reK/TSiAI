$(function () {

    // ============================================================
    // NAWIGACJA SIDEBAR
    // ============================================================
    var $links = $('.sidebar-link');
    var $sections = $('.content-section');
    var $pageTitle = $('#pageTitle');

    var titles = {
        'rezerwacje': 'Rezerwacje',
        'wypozyczenia': 'Wypożyczenia',
        'czytelnicy': 'Czytelnicy',
        'ksiazki': 'Książki'
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

        // Ładowanie danych przy przejściu do sekcji
        if (section === 'rezerwacje') zaladujRezerwacje();
        if (section === 'wypozyczenia') zaladujWypozyczenia();
        if (section === 'czytelnicy') zaladujCzytelnikow();
        if (section === 'ksiazki') zaladujKsiazki();
    });

    // ============================================================
    // WYLOGOWANIE
    // ============================================================
    $('#logoutBtn').on('click', function () {
        var $btn = $(this);
        var $btnText = $('#logoutBtnText');
        $btn.prop('disabled', true);
        $btnText.text('Wylogowywanie...');

        $.ajax({
            url: '../api/auth/logout.php',
            method: 'POST',
            dataType: 'json'
        })
        .done(function () { window.location.href = '../index.php'; })
        .fail(function () { window.location.href = '../index.php'; });
    });

    // ============================================================
    // REZERWACJE
    // ============================================================
    var rezerwacjeDane = [];

    function zaladujRezerwacje(szukaj) {
        var $loading = $('#rezLoading');
        var $table = $('#rezTable');
        var $tbody = $('#rezTableBody');
        var $empty = $('#rezEmpty');

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
            pokazAlert('rez', 'danger', 'Nie udało się załadować rezerwacji');
        });
    }

    function wyswietlRezerwacje(szukaj) {
        var filtr = szukaj.toLowerCase();
        var wyniki = filtr
            ? rezerwacjeDane.filter(function (r) {
                return r.ksiazka_tytul.toLowerCase().includes(filtr) ||
                       r.ksiazka_autor.toLowerCase().includes(filtr) ||
                       (r.czytelnik_nazwa && r.czytelnik_nazwa.toLowerCase().includes(filtr));
            })
            : rezerwacjeDane;

        var $tbody = $('#rezTableBody');
        var $table = $('#rezTable');
        var $empty = $('#rezEmpty');
        $tbody.empty();

        if (wyniki.length > 0) {
            $.each(wyniki, function (i, r) {
                var statusBadge = badgeStatus(r.status);
                var akcja = '';
                if (r.status === 'aktywna') {
                    akcja = '<button class="rez-zatwierdz px-3 py-1.5 text-xs font-medium text-green-400 border border-green-500/30 rounded-lg hover:bg-green-500/10 transition-colors mr-2" data-id="' + r.id + '">Zatwierdź</button>' +
                            '<button class="rez-anuluj px-3 py-1.5 text-xs font-medium text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/10 transition-colors" data-id="' + r.id + '">Anuluj</button>';
                }
                $tbody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(r.ksiazka_tytul) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(r.ksiazka_autor) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(r.czytelnik_nazwa || '—') + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(r.data_rezerwacji) + '</td>' +
                        '<td class="px-6 py-4">' + statusBadge + '</td>' +
                        '<td class="px-6 py-4 text-right">' + akcja + '</td>' +
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

    // Zatwierdzenie rezerwacji — otwórz modal
    $(document).on('click', '.rez-zatwierdz', function () {
        var id = $(this).data('id');
        $('#rezModalId').val(id);
        // Domyślny termin: 14 dni od dziś
        var dzisiaj = new Date();
        dzisiaj.setDate(dzisiaj.getDate() + 14);
        var dd = String(dzisiaj.getDate()).padStart(2, '0');
        var mm = String(dzisiaj.getMonth() + 1).padStart(2, '0');
        var yyyy = dzisiaj.getFullYear();
        $('#rezModalTermin').val(yyyy + '-' + mm + '-' + dd);
        $('#rezModal').removeClass('hidden');
    });

    $('#rezModalConfirm').on('click', function () {
        var id = $('#rezModalId').val();
        var termin = $('#rezModalTermin').val();
        if (!termin) {
            pokazAlert('rez', 'danger', 'Podaj termin zwrotu');
            return;
        }

        var $btn = $(this);
        var $spinner = $('#rezModalSpinner');
        var $text = $('#rezModalConfirmText');
        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $text.text('Zatwierdzanie...');

        $.ajax({
            url: '../api/wypozyczenia/dodaj.php',
            method: 'POST',
            data: { rezerwacja_id: id, termin_zwrotu: termin },
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                $('#rezModal').addClass('hidden');
                pokazAlert('rez', 'success', data.komunikat || 'Rezerwacja zatwierdzona');
                zaladujRezerwacje($('#rezSearch').val());
            } else {
                pokazAlert('rez', 'danger', data.komunikat || 'Nie udało się zatwierdzić');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
            pokazAlert('rez', 'danger', msg);
        })
        .always(function () {
            $btn.prop('disabled', false);
            $spinner.addClass('hidden');
            $text.text('Zatwierdź');
        });
    });

    // Anulowanie rezerwacji
    $(document).on('click', '.rez-anuluj', function () {
        var id = $(this).data('id');
        if (!confirm('Czy na pewno chcesz anulować tę rezerwację?')) return;

        $.ajax({
            url: '../api/rezerwacje/edytuj.php',
            method: 'POST',
            data: { id: id, status: 'anulowana' },
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                pokazAlert('rez', 'success', data.komunikat || 'Rezerwacja anulowana');
                zaladujRezerwacje($('#rezSearch').val());
            } else {
                pokazAlert('rez', 'danger', data.komunikat || 'Nie udało się anulować');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
            pokazAlert('rez', 'danger', msg);
        });
    });

    $('#rezSearch').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); wyswietlRezerwacje($(this).val()); }
    });

    // ============================================================
    // WYPOŻYCZENIA
    // ============================================================
    var wypozyczeniaDane = [];

    function zaladujWypozyczenia(szukaj) {
        var $loading = $('#wypLoading');
        var $aktywne = $('#wypAktywneSection');
        var $stare = $('#wypStareSection');
        var $empty = $('#wypAllEmpty');

        $loading.removeClass('hidden');
        $aktywne.addClass('hidden');
        $stare.addClass('hidden');
        $empty.addClass('hidden');

        $.ajax({
            url: '../api/wypozyczenia/list.php',
            method: 'GET',
            dataType: 'json'
        })
        .done(function (data) {
            $loading.addClass('hidden');
            if (data.status && data.wypozyczenia && data.wypozyczenia.length > 0) {
                wypozyczeniaDane = data.wypozyczenia;
                wyswietlWypozyczenia(szukaj || '');
            } else {
                $empty.removeClass('hidden');
            }
        })
        .fail(function (jqXHR) {
            $loading.addClass('hidden');
            pokazAlert('wyp', 'danger', 'Nie udało się załadować wypożyczeń');
        });
    }

    function wyswietlWypozyczenia(szukaj) {
        var filtr = szukaj.toLowerCase();
        var wyniki = filtr
            ? wypozyczeniaDane.filter(function (w) {
                return w.ksiazka_tytul.toLowerCase().includes(filtr) ||
                       w.ksiazka_autor.toLowerCase().includes(filtr) ||
                       (w.czytelnik_nazwa && w.czytelnik_nazwa.toLowerCase().includes(filtr));
            })
            : wypozyczeniaDane;

        var aktywne = [], stare = [];
        $.each(wyniki, function (i, w) {
            if (w.status === 'aktywne') aktywne.push(w);
            else stare.push(w);
        });

        // Aktywne
        var $aBody = $('#wypAktywneBody');
        var $aEmpty = $('#wypAktywneEmpty');
        $aBody.empty();
        if (aktywne.length > 0) {
            $.each(aktywne, function (i, w) {
                $aBody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(w.ksiazka_tytul) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(w.ksiazka_autor) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(w.czytelnik_nazwa || '—') + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.data_wypozyczenia) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.termin_zwrotu) + '</td>' +
                        '<td class="px-6 py-4 text-right">' +
                            '<button class="wyp-zwrot px-3 py-1.5 text-xs font-medium text-green-400 border border-green-500/30 rounded-lg hover:bg-green-500/10 transition-colors" data-id="' + w.id + '">Zwróć</button>' +
                        '</td>' +
                    '</tr>'
                );
            });
            $('#wypAktywneSection').removeClass('hidden');
            $aEmpty.addClass('hidden');
        } else {
            $('#wypAktywneSection').removeClass('hidden');
            $aEmpty.removeClass('hidden');
        }

        // Stare
        var $sBody = $('#wypStareBody');
        var $sEmpty = $('#wypStareEmpty');
        $sBody.empty();
        if (stare.length > 0) {
            $.each(stare, function (i, w) {
                $sBody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(w.ksiazka_tytul) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(w.ksiazka_autor) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(w.czytelnik_nazwa || '—') + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.data_wypozyczenia) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.termin_zwrotu) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + formatData(w.data_zwrotu) + '</td>' +
                    '</tr>'
                );
            });
            $('#wypStareSection').removeClass('hidden');
            $sEmpty.addClass('hidden');
        } else {
            $('#wypStareSection').removeClass('hidden');
            $sEmpty.removeClass('hidden');
        }

        if (aktywne.length === 0 && stare.length === 0) {
            $('#wypAktywneSection').addClass('hidden');
            $('#wypStareSection').addClass('hidden');
            $('#wypAllEmpty').removeClass('hidden');
        }
    }

    // Zwrot książki
    $(document).on('click', '.wyp-zwrot', function () {
        var $btn = $(this);
        var id = $btn.data('id');
        if ($btn.hasClass('loading')) return;

        $btn.addClass('loading');
        var origText = $btn.text();
        $btn.text('...');

        $.ajax({
            url: '../api/wypozyczenia/zwrot.php',
            method: 'POST',
            data: { id: id },
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                pokazAlert('wyp', 'success', data.komunikat || 'Zwrot zarejestrowany');
                zaladujWypozyczenia($('#wypSearch').val());
            } else {
                pokazAlert('wyp', 'danger', data.komunikat || 'Nie udało się zarejestrować zwrotu');
                $btn.text(origText).removeClass('loading');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
            pokazAlert('wyp', 'danger', msg);
            $btn.text(origText).removeClass('loading');
        });
    });

    $('#wypSearch').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); wyswietlWypozyczenia($(this).val()); }
    });

    // ============================================================
    // CZYTELNICY
    // ============================================================
    var czytelnicyDane = [];

    // Przełączanie między listą a formularzem
    function pokazformeCzytelnika() {
        $('#section-czytelnicy').addClass('hidden');
        $('#section-czyt-form').removeClass('hidden');
    }

    function pokazListeCzytelnikow() {
        $('#section-czyt-form').addClass('hidden');
        $('#section-czytelnicy').removeClass('hidden');
        zaladujCzytelnikow($('#czytSearch').val());
    }

    function zaladujCzytelnikow(szukaj) {
        var $loading = $('#czytLoading');
        var $table = $('#czytTable');
        var $empty = $('#czytEmpty');

        $loading.removeClass('hidden');
        $table.addClass('hidden');
        $empty.addClass('hidden');

        $.ajax({
            url: '../api/czytelnicy/list.php',
            method: 'GET',
            dataType: 'json'
        })
        .done(function (data) {
            $loading.addClass('hidden');
            if (data.status && data.czytelnicy && data.czytelnicy.length > 0) {
                czytelnicyDane = data.czytelnicy;
                wyswietlCzytelnikow(szukaj || '');
            } else {
                $empty.removeClass('hidden');
            }
        })
        .fail(function (jqXHR) {
            $loading.addClass('hidden');
            pokazAlert('czyt', 'danger', 'Nie udało się załadować czytelników');
        });
    }

    function wyswietlCzytelnikow(szukaj) {
        var filtr = szukaj.toLowerCase();
        var wyniki = filtr
            ? czytelnicyDane.filter(function (c) {
                return (c.imie + ' ' + c.nazwisko).toLowerCase().includes(filtr) ||
                       c.identyfikator.toLowerCase().includes(filtr);
            })
            : czytelnicyDane;

        var $tbody = $('#czytTableBody');
        var $table = $('#czytTable');
        var $empty = $('#czytEmpty');
        $tbody.empty();

        if (wyniki.length > 0) {
            $.each(wyniki, function (i, c) {
                $tbody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(c.imie + ' ' + c.nazwisko) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(c.identyfikator) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(c.nr_dokumentu) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400 max-w-[200px] truncate">' + escapeHtml(c.adres) + '</td>' +
                        '<td class="px-6 py-4 text-right">' +
                            '<button class="czyt-edit px-3 py-1.5 text-xs font-medium text-amber-400 border border-amber-500/30 rounded-lg hover:bg-amber-500/10 transition-colors mr-2" data-id="' + c.id + '">Edytuj</button>' +
                            '<button class="czyt-delete px-3 py-1.5 text-xs font-medium text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/10 transition-colors" data-id="' + c.id + '" data-name="' + escapeHtml(c.imie + ' ' + c.nazwisko) + '">Usuń</button>' +
                        '</td>' +
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

    // Dodaj czytelnika — pokaż formularz
    $('#czytAddBtn').on('click', function () {
        $('#czytFormId').val('');
        $('#czytFormTitle').text('Nowy czytelnik');
        $('#czytFormSubtitle').text('Dodaj nowego czytelnika do systemu');
        $('#czytFormImie').val('');
        $('#czytFormNazwisko').val('');
        $('#czytFormAdres').val('');
        $('#czytFormNrDok').val('');
        $('#czytFormIdent').val('');
        $('#czytFormHaslo').val('').prop('required', true);
        $('#czytFormHasloHint').text('Wymagane tylko przy dodawaniu');
        $('#czytFormZmienHaslo').prop('checked', false);
        $('#czytFormAlert').addClass('hidden');
        pokazformeCzytelnika();
    });

    // Edytuj czytelnika — pokaż formularz z danymi
    $(document).on('click', '.czyt-edit', function () {
        var id = $(this).data('id');
        var c = czytelnicyDane.find(function (x) { return x.id == id; });
        if (!c) return;

        $('#czytFormId').val(c.id);
        $('#czytFormTitle').text(c.imie + ' ' + c.nazwisko);
        $('#czytFormSubtitle').text('Edytuj dane czytelnika');
        $('#czytFormImie').val(c.imie);
        $('#czytFormNazwisko').val(c.nazwisko);
        $('#czytFormAdres').val(c.adres);
        $('#czytFormNrDok').val(c.nr_dokumentu);
        $('#czytFormIdent').val(c.identyfikator);
        $('#czytFormHaslo').val('').prop('required', false);
        $('#czytFormHasloHint').text('Pozostaw puste aby nie zmieniać');
        $('#czytFormZmienHaslo').prop('checked', c.zmien_haslo == 1);
        $('#czytFormAlert').addClass('hidden');
        pokazformeCzytelnika();
    });

    // Anuluj — wróć do listy
    $('#czytFormCancel').on('click', function () {
        pokazListeCzytelnikow();
    });

    // Zapisz czytelnika
    $('#czytFormSave').on('click', function () {
        var id = $('#czytFormId').val();
        var imie = $('#czytFormImie').val().trim();
        var nazwisko = $('#czytFormNazwisko').val().trim();
        var adres = $('#czytFormAdres').val().trim();
        var nrDok = $('#czytFormNrDok').val().trim();
        var ident = $('#czytFormIdent').val().trim();
        var haslo = $('#czytFormHaslo').val();
        var zmienHaslo = $('#czytFormZmienHaslo').is(':checked') ? 1 : 0;

        $('#czytFormAlert').addClass('hidden');

        if (!imie || !nazwisko || !adres || !nrDok || !ident) {
            pokazFormAlert('czyt', 'danger', 'Wypełnij wszystkie wymagane pola');
            return;
        }

        // Walidacja długości pól tekstowych (zgodnie z VARCHAR w bazie)
        var limity = { imie: 100, nazwisko: 100, adres: 255, 'nr dokumentu': 50, identyfikator: 50 };
        var pola = { imie: imie, nazwisko: nazwisko, adres: adres, 'nr dokumentu': nrDok, identyfikator: ident };
        for (var nazwa in pola) {
            if (pola[nazwa].length > limity[nazwa]) {
                pokazFormAlert('czyt', 'danger', nazwa.charAt(0).toUpperCase() + nazwa.slice(1) + ' może mieć maksymalnie ' + limity[nazwa] + ' znaków');
                return;
            }
        }

        var dane = {
            imie: imie, nazwisko: nazwisko, adres: adres,
            nr_dokumentu: nrDok, identyfikator: ident,
            zmien_haslo: zmienHaslo
        };

        if (id) {
            dane.id = id;
            if (haslo) dane.haslo = haslo;
        } else {
            if (!haslo) {
                pokazFormAlert('czyt', 'danger', 'Hasło jest wymagane');
                return;
            }
            dane.haslo = haslo;
        }

        var $btn = $(this);
        var $spinner = $('#czytFormSpinner');
        var $text = $('#czytFormSaveText');
        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $text.text('Zapisywanie...');

        var url = id ? '../api/czytelnicy/edytuj.php' : '../api/czytelnicy/dodaj.php';

        $.ajax({
            url: url,
            method: 'POST',
            data: dane,
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                pokazListeCzytelnikow();
                pokazAlert('czyt', 'success', data.komunikat || 'Zapisano');
            } else {
                pokazFormAlert('czyt', 'danger', data.komunikat || 'Nie udało się zapisać');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
            pokazFormAlert('czyt', 'danger', msg);
        })
        .always(function () {
            $btn.prop('disabled', false);
            $spinner.addClass('hidden');
            $text.text('Zapisz');
        });
    });

    function pokazFormAlert(prefiks, typ, wiadomosc) {
        var $el = $('#' + prefiks + 'FormAlert');
        var $msg = $('#' + prefiks + 'FormAlertMsg');
        var kolory = {
            'success': 'text-green-300 bg-green-900/90 border border-green-700',
            'danger':  'text-red-300 bg-red-900/90 border border-red-700'
        };
        $el.removeClass('text-green-300 bg-green-900/90 border border-green-700 text-red-300 bg-red-900/90 border border-red-700');
        $el.addClass(kolory[typ] || kolory['danger']);
        $msg.text(wiadomosc);
        $el.removeClass('hidden');
    }

    // Usuń czytelnika
    $(document).on('click', '.czyt-delete', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#confirmModalMsg').text('Czy na pewno chcesz usunąć czytelnika "' + name + '"?');
        $('#confirmModal').removeClass('hidden');

        $('#confirmModalYes').off('click').on('click', function () {
            $.ajax({
                url: '../api/czytelnicy/usun.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json'
            })
            .done(function (data) {
                $('#confirmModal').addClass('hidden');
                if (data.status) {
                    pokazAlert('czyt', 'success', data.komunikat || 'Usunięto');
                    zaladujCzytelnikow($('#czytSearch').val());
                } else {
                    pokazAlert('czyt', 'danger', data.komunikat || 'Nie udało się usunąć');
                }
            })
            .fail(function (jqXHR) {
                $('#confirmModal').addClass('hidden');
                var msg = 'Błąd połączenia z serwerem';
                if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
                pokazAlert('czyt', 'danger', msg);
            });
        });
    });

    $('#czytSearch').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); wyswietlCzytelnikow($(this).val()); }
    });

    // ============================================================
    // KSIĄŻKI
    // ============================================================
    var ksiazkiDane = [];
    var aktualnyRok = new Date().getFullYear();

    // Przełączanie między listą a formularzem
    function pokazformeKsiazki() {
        $('#section-ksiazki').addClass('hidden');
        $('#section-ksiaz-form').removeClass('hidden');
    }

    function pokazListeKsiazek() {
        $('#section-ksiaz-form').addClass('hidden');
        $('#section-ksiazki').removeClass('hidden');
        zaladujKsiazki($('#ksiazSearch').val());
    }

    function zaladujKsiazki(szukaj) {
        var $loading = $('#ksiazLoading');
        var $table = $('#ksiazTable');
        var $empty = $('#ksiazEmpty');

        $loading.removeClass('hidden');
        $table.addClass('hidden');
        $empty.addClass('hidden');

        var dane = {};
        if (szukaj && szukaj.trim() !== '') dane.szukaj = szukaj.trim();

        $.ajax({
            url: '../api/ksiazki/list.php',
            method: 'GET',
            data: dane,
            dataType: 'json'
        })
        .done(function (data) {
            $loading.addClass('hidden');
            if (data.status && data.ksiazki && data.ksiazki.length > 0) {
                ksiazkiDane = data.ksiazki;
                wyswietlKsiazki(szukaj || '');
            } else {
                $empty.removeClass('hidden');
            }
        })
        .fail(function (jqXHR) {
            $loading.addClass('hidden');
            pokazAlert('ksiaz', 'danger', 'Nie udało się załadować książek');
        });
    }

    function wyswietlKsiazki(szukaj) {
        var filtr = szukaj.toLowerCase();
        var wyniki = filtr
            ? ksiazkiDane.filter(function (k) {
                return k.tytul.toLowerCase().includes(filtr) ||
                       k.autor.toLowerCase().includes(filtr) ||
                       k.wydawnictwo.toLowerCase().includes(filtr);
            })
            : ksiazkiDane;

        var $tbody = $('#ksiazTableBody');
        var $table = $('#ksiazTable');
        var $empty = $('#ksiazEmpty');
        $tbody.empty();

        if (wyniki.length > 0) {
            $.each(wyniki, function (i, k) {
                var dostepne = k.ilosc_dostepnych;
                var badge = dostepne > 0
                    ? '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-900/40 text-green-400 border border-green-800/50">' + dostepne + ' / ' + k.ilosc_calkowita + '</span>'
                    : '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-900/40 text-red-400 border border-red-800/50">Brak</span>';

                $tbody.append(
                    '<tr class="bg-gray-900 border-b border-gray-800 hover:bg-gray-800/50 transition-colors">' +
                        '<td class="px-6 py-4 font-medium text-white whitespace-nowrap">' + escapeHtml(k.tytul) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(k.autor) + '</td>' +
                        '<td class="px-6 py-4 text-gray-300">' + escapeHtml(k.wydawnictwo) + '</td>' +
                        '<td class="px-6 py-4 text-gray-400">' + escapeHtml(String(k.rok_wydania)) + '</td>' +
                        '<td class="px-6 py-4">' + badge + '</td>' +
                        '<td class="px-6 py-4 text-right">' +
                            '<button class="ksiaz-edit px-3 py-1.5 text-xs font-medium text-amber-400 border border-amber-500/30 rounded-lg hover:bg-amber-500/10 transition-colors mr-2" data-id="' + k.id + '">Edytuj</button>' +
                            '<button class="ksiaz-delete px-3 py-1.5 text-xs font-medium text-red-400 border border-red-500/30 rounded-lg hover:bg-red-500/10 transition-colors" data-id="' + k.id + '" data-name="' + escapeHtml(k.tytul) + '">Usuń</button>' +
                        '</td>' +
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

    // Dodaj książkę — pokaż formularz
    $('#ksiazAddBtn').on('click', function () {
        $('#ksiazFormId').val('');
        $('#ksiazFormTitle').text('Nowa książka');
        $('#ksiazFormSubtitle').text('Dodaj nową książkę do zbiorów biblioteki');
        $('#ksiazFormTytul').val('');
        $('#ksiazFormAutor').val('');
        $('#ksiazFormWydawnictwo').val('');
        // Domyślnie ustawiamy aktualny rok, by ułatwić wpis
        $('#ksiazFormRok').val(aktualnyRok).attr('max', aktualnyRok);
        $('#ksiazFormIlosc').val('1');
        $('#ksiazFormAlert').addClass('hidden');
        pokazformeKsiazki();
    });

    // Edytuj książkę — pokaż formularz z danymi
    $(document).on('click', '.ksiaz-edit', function () {
        var id = $(this).data('id');
        var k = ksiazkiDane.find(function (x) { return x.id == id; });
        if (!k) return;

        $('#ksiazFormId').val(k.id);
        $('#ksiazFormTitle').text(k.tytul);
        $('#ksiazFormSubtitle').text('Edytuj dane książki');
        $('#ksiazFormTytul').val(k.tytul);
        $('#ksiazFormAutor').val(k.autor);
        $('#ksiazFormWydawnictwo').val(k.wydawnictwo);
        $('#ksiazFormRok').val(k.rok_wydania).attr('max', aktualnyRok);
        $('#ksiazFormIlosc').val(k.ilosc_calkowita);
        $('#ksiazFormAlert').addClass('hidden');
        pokazformeKsiazki();
    });

    // Anuluj — wróć do listy
    $('#ksiazFormCancel').on('click', function () {
        pokazListeKsiazek();
    });

    // Zapisz książkę
    $('#ksiazFormSave').on('click', function () {
        var id = $('#ksiazFormId').val();
        var tytul = $('#ksiazFormTytul').val().trim();
        var autor = $('#ksiazFormAutor').val().trim();
        var wydawnictwo = $('#ksiazFormWydawnictwo').val().trim();
        var rok = parseInt($('#ksiazFormRok').val());
        var ilosc = parseInt($('#ksiazFormIlosc').val());

        $('#ksiazFormAlert').addClass('hidden');

        // Walidacja pól wymaganych
        if (!tytul || !autor || !wydawnictwo || !rok || !ilosc) {
            pokazFormAlert('ksiaz', 'danger', 'Wypełnij wszystkie wymagane pola');
            return;
        }

        // Walidacja długości pól tekstowych (max 100 znaków)
        if (tytul.length > 100) {
            pokazFormAlert('ksiaz', 'danger', 'Tytuł może mieć maksymalnie 100 znaków');
            return;
        }
        if (autor.length > 100) {
            pokazFormAlert('ksiaz', 'danger', 'Autor może mieć maksymalnie 100 znaków');
            return;
        }
        if (wydawnictwo.length > 100) {
            pokazFormAlert('ksiaz', 'danger', 'Wydawnictwo może mieć maksymalnie 100 znaków');
            return;
        }

        // Walidacja zakresu roku (1901..aktualny rok, zgodnie z typem YEAR w MySQL)
        if (rok < 1901 || rok > aktualnyRok) {
            pokazFormAlert('ksiaz', 'danger', 'Rok wydania musi być w zakresie 1901 - ' + aktualnyRok);
            return;
        }

        // Walidacja ilości
        if (ilosc < 1) {
            pokazFormAlert('ksiaz', 'danger', 'Ilość egzemplarzy musi wynosić co najmniej 1');
            return;
        }

        var dane = {
            tytul: tytul, autor: autor, wydawnictwo: wydawnictwo,
            rok_wydania: rok, ilosc: ilosc
        };
        if (id) dane.id = id;

        var $btn = $(this);
        var $spinner = $('#ksiazFormSpinner');
        var $text = $('#ksiazFormSaveText');
        $btn.prop('disabled', true);
        $spinner.removeClass('hidden');
        $text.text('Zapisywanie...');

        var url = id ? '../api/ksiazki/edytuj.php' : '../api/ksiazki/dodaj.php';

        $.ajax({
            url: url,
            method: 'POST',
            data: dane,
            dataType: 'json'
        })
        .done(function (data) {
            if (data.status) {
                pokazListeKsiazek();
                pokazAlert('ksiaz', 'success', data.komunikat || 'Zapisano');
            } else {
                pokazFormAlert('ksiaz', 'danger', data.komunikat || 'Nie udało się zapisać');
            }
        })
        .fail(function (jqXHR) {
            var msg = 'Błąd połączenia z serwerem';
            if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
            pokazFormAlert('ksiaz', 'danger', msg);
        })
        .always(function () {
            $btn.prop('disabled', false);
            $spinner.addClass('hidden');
            $text.text('Zapisz');
        });
    });

    // Usuń książkę
    $(document).on('click', '.ksiaz-delete', function () {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#confirmModalMsg').text('Czy na pewno chcesz usunąć książkę "' + name + '"?');
        $('#confirmModal').removeClass('hidden');

        $('#confirmModalYes').off('click').on('click', function () {
            $.ajax({
                url: '../api/ksiazki/usun.php',
                method: 'POST',
                data: { id: id },
                dataType: 'json'
            })
            .done(function (data) {
                $('#confirmModal').addClass('hidden');
                if (data.status) {
                    pokazAlert('ksiaz', 'success', data.komunikat || 'Usunięto');
                    zaladujKsiazki($('#ksiazSearch').val());
                } else {
                    pokazAlert('ksiaz', 'danger', data.komunikat || 'Nie udało się usunąć');
                }
            })
            .fail(function (jqXHR) {
                $('#confirmModal').addClass('hidden');
                var msg = 'Błąd połączenia z serwerem';
                if (jqXHR.responseJSON && jqXHR.responseJSON.komunikat) msg = jqXHR.responseJSON.komunikat;
                pokazAlert('ksiaz', 'danger', msg);
            });
        });
    });

    $('#ksiazSearch').on('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); zaladujKsiazki($(this).val()); }
    });

    // ============================================================
    // HELPERY
    // ============================================================
    function pokazAlert(prefiks, typ, wiadomosc) {
        var id = prefiks + 'Alert';
        var msgId = prefiks + 'AlertMsg';
        var $el = $('#' + id);
        var kolory = {
            'success': 'text-green-300 bg-green-900/90 border border-green-700',
            'danger':  'text-red-300 bg-red-900/90 border border-red-700'
        };
        $el.removeClass('text-green-300 bg-green-900/90 border border-green-700 text-red-300 bg-red-900/90 border border-red-700');
        $el.addClass(kolory[typ] || kolory['danger']);
        $('#' + msgId).text(wiadomosc);
        $el.removeClass('hidden');
        if (typ === 'success') {
            setTimeout(function () { $el.addClass('hidden'); }, 4000);
        }
    }

    function badgeStatus(status) {
        var kolory = {
            'aktywna':      'bg-amber-900/40 text-amber-400 border-amber-800/50',
            'zrealizowana': 'bg-green-900/40 text-green-400 border-green-800/50',
            'anulowana':    'bg-red-900/40 text-red-400 border-red-800/50'
        };
        var etykiety = {
            'aktywna': 'Aktywna', 'zrealizowana': 'Zrealizowana', 'anulowana': 'Anulowana'
        };
        var kolor = kolory[status] || 'bg-gray-900/40 text-gray-400 border-gray-800/50';
        var etykieta = etykiety[status] || status;
        return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border ' + kolor + '">' + etykieta + '</span>';
    }

    function formatData(data) {
        if (!data) return '—';
        var czesci = data.split(/[\sT]/);
        if (czesci.length < 2) return data;
        var daty = czesci[0].split('-');
        var czas = czesci[1].split(':');
        if (daty.length < 3 || czas.length < 2) return data;
        var d = new Date(
            parseInt(daty[0], 10), parseInt(daty[1], 10) - 1, parseInt(daty[2], 10),
            parseInt(czas[0], 10), parseInt(czas[1], 10),
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

    // Załaduj książki na start (domyślna sekcja)
    zaladujKsiazki();

});
