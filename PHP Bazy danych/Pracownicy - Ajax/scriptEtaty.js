$(document).ready(function(){
    initModals();
    initForms();
    initReset();
    initFilter();
    initDeleteClickBlocker();
    getEtaty();
})

function initModals() {
    $('#modalAddEtat').on('show.bs.modal', function() {
        loadFormEtat();
    });
    
    $('#modalEditEtat').on('show.bs.modal', function(e) {
        var editNazwa = $(e.relatedTarget).data('edit-nazwa');
        loadFormEditEtat(editNazwa);
    });
}

function initForms() {
    $(document).on('click', '#btnSaveEtat', function() {
        saveEtat();
    });
    
    $(document).on('click', '#btnSaveEditEtat', function() {
        saveEditEtat();
    });
    
    $(document).on('keypress', '#formAddEtat', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEtat();
        }
    });
    
    $(document).on('keypress', '#formEditEtat', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEditEtat();
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

function getEtaty(){
    renderList({
        url: "get/getEtaty.php",
        $table: $('#etaty'),
        colspan: 4,
        errorText: 'Błąd podczas ładowania danych'
    });
}

function initReset() {
    $('#reset').off('click').on('click', function(){
        $('#search').val('');
        getEtaty();
    });
}

function initFilter(){
    $('#form').off('submit').on('submit',function(e){
        e.preventDefault();
        renderList({
            url: "get/getEtatyFiltr.php",
            $table: $('#etaty'),
            colspan: 4,
            data: { search: $('#search').val() },
            errorText: 'Błąd podczas wyszukiwania'
        });
    })
}

function initDelete() {
    $(document).off('click', '.btn-confirm-delete-etat').on('click', '.btn-confirm-delete-etat', function() {
        var deleteNazwa = $(this).data('delete-nazwa');
        
        $.ajax({
            url: "delete/deleteEtat.php",
            method: 'POST',
            data: {
                delete_nazwa: deleteNazwa
            }
        })
        .done(function( response )
        {
            showAlert('success', $(response).filter('.alert-success').text() || 'Etat został usunięty!');

            var popoverEl = document.querySelector('[data-delete-nazwa="' + deleteNazwa + '"]');
            var popover = popoverEl ? bootstrap.Popover.getInstance(popoverEl) : null;
            if (popover) popover.hide();

            setTimeout(function() {
                getEtaty();
            }, 1000);
        })
        .fail(function() {
            showAlert('danger', 'Błąd podczas usuwania etatu');
        })
    })
}

function initDeleteClickBlocker() {
    $(document).on('click', '.btn-delete-etat', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
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

function showAlert(type, message) {
    var alertHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>';
    
    // Wstaw alert na górze tabeli
    $('.table').before(alertHTML);
    
    // Auto-close po 5 sekundach
    setTimeout(function() {
        $('.alert').fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
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

// ======================== DODAWANIE ETATU ========================

function loadFormEtat() {
    $.ajax({
        url: "get/getFormEtat.php",
        method: 'GET'
    })
    .done(function(data) {
        $('#formEtatContainer').html(data);
    })
    .fail(function() {
        $('#formEtatContainer').html('<div class="alert alert-danger">Błąd podczas ładowania formularza</div>');
    })
}

function saveEtat() {
    var form = $('#formAddEtat');
    var formData = form.serializeArray();
    
    applyFieldErrors('');
    $.ajax({
        url: "add/addEtat.php",
        method: 'POST',
        data: $.param(formData)
    })
    .done(function(response) {
        if ($(response).filter('.alert-success').length) {
            showAlert('success', $(response).filter('.alert-success').text());
            
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalAddEtat'));
            modal.hide();

            setTimeout(function() {
                getEtaty();
            }, 1000);
        } else {
            applyFieldErrors(response);
        }
    })
     .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}

// ======================== EDYTOWANIE ETATU ========================

function loadFormEditEtat(editNazwa) {
    $.ajax({
        url: "get/getFormEtatEdit.php?nazwa=" + encodeURIComponent(editNazwa),
        method: 'GET'
    })
    .done(function(data) {
        $('#formEtatEditContainer').html(data);
    })
    .fail(function() {
        $('#formEtatEditContainer').html('<div class="alert alert-danger">Błąd podczas ładowania formularza</div>');
    })
}

function saveEditEtat() {
    var form = $('#formEditEtat');
    var formData = form.serializeArray();
    
    applyFieldErrors('');
    $.ajax({
        url: "add/editEtat.php",
        method: 'POST',
        data: $.param(formData)
    })
    .done(function(response) {
        if ($(response).filter('.alert-success').length) {
            showAlert('success', $(response).filter('.alert-success').text());
            
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditEtat'));
            modal.hide();

            setTimeout(function() {
                getEtaty();
            }, 1000);
        } else {
            applyFieldErrors(response);
        }
    })
    .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}
