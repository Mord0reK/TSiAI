$(document).ready(function(){
    initModals();
    initForms();
    initReset();
    initFilter();
    initDeleteClickBlocker();
    getZespoly();
})

function initModals() {
    $('#modalAddZespol').on('show.bs.modal', function() {
        loadFormZespol();
    });
    
    $('#modalEditZespol').on('show.bs.modal', function(e) {
        var editId = $(e.relatedTarget).data('edit-id');
        loadFormEditZespol(editId);
    });
}

function initForms() {
    $(document).on('click', '#btnSaveZespol', function() {
        saveZespol();
    });
    
    $(document).on('click', '#btnSaveEditZespol', function() {
        saveEditZespol();
    });
    
    $(document).on('keypress', '#formAddZespol', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveZespol();
        }
    });
    
    $(document).on('keypress', '#formEditZespol', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEditZespol();
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

function getZespoly(){
    renderList({
        url: "get/getZespoly.php",
        $table: $('#zespoly'),
        colspan: 4,
        errorText: 'Błąd podczas ładowania danych'
    });
}

function initReset() {
    $('#reset').off('click').on('click', function(){
        $('#search').val('');
        getZespoly();
    });
}

function initFilter(){
    $('#form').off('submit').on('submit',function(e){
        e.preventDefault();
        renderList({
            url: "get/getZespolyFiltr.php",
            $table: $('#zespoly'),
            colspan: 4,
            data: { search: $('#search').val() },
            errorText: 'Błąd podczas wyszukiwania'
        });
    })
}

function initDelete() {
    $(document).off('click', '.btn-confirm-delete-zespol').on('click', '.btn-confirm-delete-zespol', function() {
        var deleteId = $(this).data('delete-id');
        
        $.ajax({
            url: "delete/deleteZespol.php",
            method: 'POST',
            data: {
                delete_id: deleteId
            }
        })
        .done(function( response )
        {
            showAlert('success', $(response).filter('.alert-success').text() || 'Zespół został usunięty!');

            var popoverEl = document.querySelector('[data-delete-id="' + deleteId + '"]');
            var popover = popoverEl ? bootstrap.Popover.getInstance(popoverEl) : null;
            if (popover) popover.hide();

            setTimeout(function() {
                getZespoly();
            }, 1000);
        })
        .fail(function() {
            showAlert('danger', 'Błąd podczas usuwania zespołu');
        })
    })
}

function initDeleteClickBlocker() {
    $(document).on('click', '.btn-delete-zespol', function(e) {
        e.preventDefault();
        e.stopPropagation();
    });
}

function showAlert(type, message) {
    var alertHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
        message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>';

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

// ======================== DODAWANIE ZESPOLU ========================

function loadFormZespol() {
    $.ajax({
        url: "get/getFormZespol.php",
        method: 'GET'
    })
    .done(function(data) {
        $('#formZespolContainer').html(data);
    })
    .fail(function() {
        $('#formZespolContainer').html('<div class="alert alert-danger">Błąd podczas ładowania formularza</div>');
    })
}

function saveZespol() {
    var form = $('#formAddZespol');
    var formData = form.serializeArray();
    
    applyFieldErrors('');
    $.ajax({
        url: "add/addZespol.php",
        method: 'POST',
        data: $.param(formData)
    })
    .done(function(response) {
        if ($(response).filter('.alert-success').length) {
            showAlert('success', $(response).filter('.alert-success').text());
            
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalAddZespol'));
            modal.hide();

            setTimeout(function() {
                getZespoly();
            }, 1000);
        } else {
            applyFieldErrors(response);
        }
    })
    .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}

// ======================== EDYTOWANIE ZESPOLU ========================

function loadFormEditZespol(editId) {
    $.ajax({
        url: "get/getFormZespolEdit.php?id=" + editId,
        method: 'GET'
    })
    .done(function(data) {
        $('#formZespolEditContainer').html(data);
    })
    .fail(function() {
        $('#formZespolEditContainer').html('<div class="alert alert-danger">Błąd podczas ładowania formularza</div>');
    })
}

function saveEditZespol() {
    var form = $('#formEditZespol');
    var formData = form.serializeArray();
    
    applyFieldErrors('');
    $.ajax({
        url: "add/editZespol.php",
        method: 'POST',
        data: $.param(formData)
    })
    .done(function(response) {
        if ($(response).filter('.alert-success').length) {
            showAlert('success', $(response).filter('.alert-success').text());
            
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditZespol'));
            modal.hide();

            setTimeout(function() {
                getZespoly();
            }, 1000);
        } else {
            applyFieldErrors(response);
        }
    })
    .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}
