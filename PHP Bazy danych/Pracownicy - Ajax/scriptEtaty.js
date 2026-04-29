$(document).ready(function(){
    getEtaty();
    getEtatyFiltr();
    
    // Ładuj formularz do modalu na otwarcie - ADD
    $('#modalAddEtat').on('show.bs.modal', function() {
        loadFormEtat();
    });
    
    // Ładuj formularz do modalu na otwarcie - EDIT
    $('#modalEditEtat').on('show.bs.modal', function(e) {
        var editNazwa = $(e.relatedTarget).data('edit-nazwa');
        loadFormEditEtat(editNazwa);
    });
    
    // Obsłuż klik na przycisk Zapisz - ADD
    $(document).on('click', '#btnSaveEtat', function() {
        saveEtat();
    });
    
    // Obsłuż klik na przycisk Zapisz - EDIT
    $(document).on('click', '#btnSaveEditEtat', function() {
        saveEditEtat();
    });
    
    // Obsłuż Enter w formularzu - ADD
    $(document).on('keypress', '#formAddEtat', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEtat();
        }
    });
    
    // Obsłuż Enter w formularzu - EDIT
    $(document).on('keypress', '#formEditEtat', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEditEtat();
        }
    });
})

function initializePopovers() {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'focus',
            html: true,
            sanitize: false
        });
    });
}

function getEtaty(){

    $.ajax({
        url: "get/getEtaty.php",
        method: 'POST'
    })
    .done(function( data )
    {
        $('#etaty').html(data);
        initializePopovers();
        attachDeleteHandlers();
    })
    .fail(function() {
        $('#etaty').html('<tr><td colspan="4" class="text-center text-danger">Błąd podczas ładowania danych</td></tr>');
    })

    $('#reset').on('click',function(){

        $('#search').val('');
        // Pokaż loader
        $('#etaty').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-3 text-muted">Ładowanie danych...</p></td></tr>');

        $.ajax({
            url: "get/getEtaty.php",
            method: 'POST'
        })
        .done(function( data )
        {
            $('#etaty').html(data);
            initializePopovers();
            attachDeleteHandlers();
        })
        .fail(function() {
            $('#etaty').html('<tr><td colspan="4" class="text-center text-danger">Błąd podczas ładowania danych</td></tr>');
        })
    })
}

function getEtatyFiltr(){
    $('#form').on('submit',function(e){
        e.preventDefault();
        
        // Pokaż loader
        $('#etaty').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-3 text-muted">Ładowanie danych...</p></td></tr>');
        
        $.ajax({
            url: "get/getEtatyFiltr.php",
            method: 'POST',
            data: {
                search: $('#search').val(),
            }
        })
        .done(function( data ){
            $('#etaty').html(data);
            initializePopovers();
            attachDeleteHandlers();
        })
        .fail(function() {
            $('#etaty').html('<tr><td colspan="4" class="text-center text-danger">Błąd podczas wyszukiwania</td></tr>');
        })
    })
}

function attachDeleteHandlers() {
    $(document).off('click', '.btn-confirm-delete-etat').on('click', '.btn-confirm-delete-etat', function() {
        var deleteNazwa = $(this).data('delete-nazwa');
        
        $.ajax({
            url: "delete/deleteEtat.php",
            method: 'POST',
            data: {
                delete_nazwa: deleteNazwa
            },
            dataType: 'json'
        })
        .done(function( response )
        {
            if (response.success) {
                // Pokaż alert sukcesu
                showAlert('success', response.message);
                
                // Zamknij popover
                var popover = bootstrap.Popover.getInstance('[data-delete-nazwa="' + deleteNazwa + '"]');
                if (popover) popover.hide();
                
                // Odśwież tabelę
                setTimeout(function() {
                    getEtaty();
                }, 1500);
            } else {
                showAlert('danger', response.message);
            }
        })
        .fail(function() {
            showAlert('danger', 'Błąd podczas usuwania etatu');
        })
    })
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
    
    // Wyczyść poprzednie błędy
    $('.invalid-feedback').html('');
    $('.form-control, .form-select').removeClass('is-invalid');
    
    $.ajax({
        url: "add/addEtat.php",
        method: 'POST',
        data: $.param(formData),
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            // Pokaż sukces
            showAlert('success', response.message);
            
            // Zamknij modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalAddEtat'));
            modal.hide();
            
            // Odśwież tabelę
            setTimeout(function() {
                getEtaty();
            }, 1000);
        } else {
            // Pokaż błędy
            if (response.errors && Object.keys(response.errors).length > 0) {
                $.each(response.errors, function(field, error) {
                    var $field = $('#' + field);
                    if ($field.length) {
                        $field.addClass('is-invalid');
                        $('#' + field + '-error').html(error);
                    }
                });
                showAlert('danger', response.message);
            } else {
                showAlert('danger', response.message);
            }
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
    
    // Wyczyść poprzednie błędy
    $('.invalid-feedback').html('');
    $('.form-control, .form-select').removeClass('is-invalid');
    
    $.ajax({
        url: "add/editEtat.php",
        method: 'POST',
        data: $.param(formData),
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            // Pokaż sukces
            showAlert('success', response.message);
            
            // Zamknij modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditEtat'));
            modal.hide();
            
            // Odśwież tabelę
            setTimeout(function() {
                getEtaty();
            }, 1000);
        } else {
            // Pokaż błędy
            if (response.errors && Object.keys(response.errors).length > 0) {
                $.each(response.errors, function(field, error) {
                    var $field = $('#' + field);
                    if ($field.length) {
                        $field.addClass('is-invalid');
                        $('#' + field + '-error').html(error);
                    }
                });
                showAlert('danger', response.message);
            } else {
                showAlert('danger', response.message);
            }
        }
    })
    .fail(function() {
        showAlert('danger', 'Błąd podczas wysyłania formularza');
    })
}
