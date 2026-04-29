$(document).ready(function(){
    initModals();
    initForms();
    initReset();
    initFilter();
    getPracownicy();
})

function initModals() {
    $('#modalAddPracownik').on('show.bs.modal', function() {
        loadFormPracownik();
    });

    $('#modalEditPracownik').on('show.bs.modal', function(e) {
        var editId = $(e.relatedTarget).data('edit-id');
        loadFormEditPracownik(editId);
    });
}

function initForms() {
    $(document).on('click', '#btnSavePracownik', function() {
        savePracownik();
    });

    $(document).on('click', '#btnSaveEditPracownik', function() {
        saveEditPracownik();
    });

    $(document).on('keypress', '#formAddPracownik', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            savePracownik();
        }
    });

    $(document).on('keypress', '#formEditPracownik', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEditPracownik();
        }
    });
}

function initPopovers() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'focus',
            html: true,
            sanitize: false
        });
    });
}

function loaderDelay(callback) {
    setTimeout(callback, 350);
}

function getPracownicy(){
    renderList({
        url: "get/getPracownicy.php",
        $table: $('#pracownicy'),
        colspan: 11,
        errorText: 'Błąd podczas ładowania danych'
    });
}

function initReset() {
    $('#reset').off('click').on('click', function(){
        $('#search').val('');
        getPracownicy();
    });
}

function initFilter(){
    $('#form').off('submit').on('submit',function(e){
        e.preventDefault();
        renderList({
            url: "get/getPracownicyFiltr.php",
            $table: $('#pracownicy'),
            colspan: 11,
            data: { search: $('#search').val() },
            errorText: 'Błąd podczas wyszukiwania'
        });
    })
}

function renderTableLoader($table, colspan) {
    $table.html('<tr><td colspan="' + colspan + '" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-3 text-muted">Ładowanie danych...</p></td></tr>');
}

function renderTableError($table, colspan, message) {
    $table.html('<tr><td colspan="' + colspan + '" class="text-center text-danger">' + message + '</td></tr>');
}

function renderList(options) {
    renderTableLoader(options.$table, options.colspan);
    $.ajax({
        url: options.url,
        method: 'POST',
        data: options.data || undefined
    })
    .done(function(data) {
        loaderDelay(function() {
            options.$table.html(data);
            initPopovers();
            initDelete();
        });
    })
    .fail(function() {
        renderTableError(options.$table, options.colspan, options.errorText);
    });
}

function initDelete() {
    $(document).off('click', '.btn-confirm-delete').on('click', '.btn-confirm-delete', function() {
        var deleteId = $(this).data('delete-id');
        
        $.ajax({
            url: "delete/deletePracownik.php",
            method: 'POST',
            data: {
                delete_id: deleteId
            }
        })
        .done(function( response )
        {
            if ($(response).filter('.alert-success').length) {
                showAlert('success', $(response).filter('.alert-success').text());

                var popoverEl = document.querySelector('[data-delete-id="' + deleteId + '"]');
                var popover = popoverEl ? bootstrap.Popover.getInstance(popoverEl) : null;
                if (popover) popover.hide();

                setTimeout(function() {
                    getPracownicy();
                }, 1500);
            } else {
                showAlert('danger', $(response).filter('.alert-danger').text() || 'Błąd podczas usuwania pracownika');
            }
        })
        .fail(function() {
            showAlert('danger', 'Błąd podczas usuwania pracownika');
        })
    })
}

function showAlert(type, message) {
    var alertHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>';
    
    $('.table').before(alertHTML);

    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

function loadFormPracownik() {
    $.ajax({
        url: "get/getFormPracownik.php",
        method: 'GET'
    })
    .done(function(data) {
        $('#formPracownikContainer').html(data);
    })
    .fail(function() {
        $('#formPracownikContainer').html('<div class="alert alert-danger">Błąd podczas ładowania formularza</div>');
    })
}

function applyFieldErrors(responseHtml) {
    $('.invalid-feedback').html('');
    $('.form-control, .form-select').removeClass('is-invalid');

    var wrapper = $('<div>').html(responseHtml);
    wrapper.find('[data-error-for]').each(function() {
        var field = $(this).data('error-for');
        var message = $(this).text().trim();
        if (message) {
            $('#' + field).addClass('is-invalid');
            $('#' + field + '-error').html(message);
        }
    });
}

function savePracownik() {
    var form = $('#formAddPracownik');
    var formData = form.serializeArray();
    
    applyFieldErrors('');
    $.ajax({
        url: "add/addPracownik.php",
        method: 'POST',
        data: $.param(formData)
    })
    .done(function(response) {
        if ($(response).filter('.alert-success').length) {
            showAlert('success', $(response).filter('.alert-success').text());

            var modal = bootstrap.Modal.getInstance(document.getElementById('modalAddPracownik'));
            modal.hide();

            setTimeout(function() {
                getPracownicy();
            }, 1000);
            return;
        }

        applyFieldErrors(response);
    })
    .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}

function loadFormEditPracownik(editId) {
    $.ajax({
        url: "get/getFormPracownikEdit.php?id=" + editId,
        method: 'GET'
    })
    .done(function(data) {
        $('#formPracownikEditContainer').html(data);
    })
    .fail(function() {
        $('#formPracownikEditContainer').html('<div class="alert alert-danger">Błąd podczas ładowania formularza</div>');
    })
}

function saveEditPracownik() {
    var form = $('#formEditPracownik');
    var formData = form.serializeArray();
    
    applyFieldErrors('');
    $.ajax({
        url: "add/editPracownik.php",
        method: 'POST',
        data: $.param(formData)
    })
    .done(function(response) {
        if ($(response).filter('.alert-success').length) {
            showAlert('success', $(response).filter('.alert-success').text());

            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditPracownik'));
            modal.hide();

            setTimeout(function() {
                getPracownicy();
            }, 1000);
            return;
        }

        applyFieldErrors(response);
    })
    .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}
