$(document).ready(function(){
    getZespoly();
    getZespolyFiltr();
    
    // Ładuj formularz do modalu na otwarcie - ADD
    $('#modalAddZespol').on('show.bs.modal', function() {
        loadFormZespol();
    });
    
    // Ładuj formularz do modalu na otwarcie - EDIT
    $('#modalEditZespol').on('show.bs.modal', function(e) {
        var editId = $(e.relatedTarget).data('edit-id');
        loadFormEditZespol(editId);
    });
    
    // Obsłuż klik na przycisk Zapisz - ADD
    $(document).on('click', '#btnSaveZespol', function() {
        saveZespol();
    });
    
    // Obsłuż klik na przycisk Zapisz - EDIT
    $(document).on('click', '#btnSaveEditZespol', function() {
        saveEditZespol();
    });
    
    // Obsłuż Enter w formularzu - ADD
    $(document).on('keypress', '#formAddZespol', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveZespol();
        }
    });
    
    // Obsłuż Enter w formularzu - EDIT
    $(document).on('keypress', '#formEditZespol', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            saveEditZespol();
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

function getZespoly(){
    // Pokaż loader
    $('#zespoly').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-3 text-muted">Ładowanie danych...</p></td></tr>');

    $.ajax({
        url: "get/getZespoly.php",
        method: 'POST'
    })
    .done(function( data )
    {
        $('#zespoly').html(data);
        initializePopovers();
        attachDeleteHandlers();
    })
    .fail(function() {
        $('#zespoly').html('<tr><td colspan="4" class="text-center text-danger">Błąd podczas ładowania danych</td></tr>');
    })

    $('#reset').on('click',function(){

        $('#search').val('');
        // Pokaż loader
        $('#zespoly').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-3 text-muted">Ładowanie danych...</p></td></tr>');

        $.ajax({
            url: "get/getZespoly.php",
            method: 'POST'
        })
        .done(function( data )
        {
            $('#zespoly').html(data);
            initializePopovers();
            attachDeleteHandlers();
        })
        .fail(function() {
            $('#zespoly').html('<tr><td colspan="4" class="text-center text-danger">Błąd podczas ładowania danych</td></tr>');
        })
    })
}

function getZespolyFiltr(){
    $('#form').on('submit',function(e){
        e.preventDefault();
        
        // Pokaż loader
        $('#zespoly').html('<tr><td colspan="4" class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-3 text-muted">Ładowanie danych...</p></td></tr>');
        
        $.ajax({
            url: "get/getZespolyFiltr.php",
            method: 'POST',
            data: {
                search: $('#search').val(),
            }
        })
        .done(function( data ){
            $('#zespoly').html(data);
            initializePopovers();
            attachDeleteHandlers();
        })
        .fail(function() {
            $('#zespoly').html('<tr><td colspan="4" class="text-center text-danger">Błąd podczas wyszukiwania</td></tr>');
        })
    })
}

function attachDeleteHandlers() {
    $(document).off('click', '.btn-confirm-delete-zespol').on('click', '.btn-confirm-delete-zespol', function() {
        var deleteId = $(this).data('delete-id');
        
        $.ajax({
            url: "delete/deleteZespol.php",
            method: 'POST',
            data: {
                delete_id: deleteId
            },
            dataType: 'json'
        })
        .done(function( response )
        {
            if (response.success) {
                // Pokaż alert sukcesu
                showAlert('success', response.message);
                
                // Zamknij popover
                var popover = bootstrap.Popover.getInstance('[data-delete-id="' + deleteId + '"]');
                if (popover) popover.hide();
                
                // Odśwież tabelę
                setTimeout(function() {
                    getZespoly();
                }, 1500);
            } else {
                showAlert('danger', response.message);
            }
        })
        .fail(function() {
            showAlert('danger', 'Błąd podczas usuwania zespołu');
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
    
    // Wyczyść poprzednie błędy
    $('.invalid-feedback').html('');
    $('.form-control, .form-select').removeClass('is-invalid');
    
    $.ajax({
        url: "add/addZespol.php",
        method: 'POST',
        data: $.param(formData),
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            // Pokaż sukces
            showAlert('success', response.message);
            
            // Zamknij modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalAddZespol'));
            modal.hide();
            
            // Odśwież tabelę
            setTimeout(function() {
                getZespoly();
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
    
    // Wyczyść poprzednie błędy
    $('.invalid-feedback').html('');
    $('.form-control, .form-select').removeClass('is-invalid');
    
    $.ajax({
        url: "add/editZespol.php",
        method: 'POST',
        data: $.param(formData),
        dataType: 'json'
    })
    .done(function(response) {
        if (response.success) {
            // Pokaż sukces
            showAlert('success', response.message);
            
            // Zamknij modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditZespol'));
            modal.hide();
            
            // Odśwież tabelę
            setTimeout(function() {
                getZespoly();
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
