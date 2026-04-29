<?php
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Bazy danych - Etaty</title>
</head>
<body>

<div class="container">
    <ul class="nav nav-tabs mt-2">
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Pracownicy</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">Etaty</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="zespoly.php">Zespoły</a>
        </li>
    </ul>
    <form action="" method="post" id="form">
        <div class="row my-5">
            <div class="col-md-4 input-group" style="width: 40%;" >
                <input type="text" class="form-control" name="search" id="search" />
                <button class="btn btn-primary" type="submit" name="submit">Szukaj</button>
            </div>
            <div class="col-md-1 text-left">
                <button type="submit" class="btn btn-danger" name="reset" id="reset">Resetuj</button>
            </div>
            <div class="col-md-6 text-left d-flex justify-content-end">
                <button type="button" class="btn btn-success" id="btnAddEtat" data-bs-toggle="modal" data-bs-target="#modalAddEtat">Dodaj nowy etat</button>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-12">
            <table class="table">
                <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Placa od</th>
                    <th>Placa do</th>
                    <th>Akcje</th>
                </tr>
                </thead>
                <tbody id="etaty">
                    <!-- Loader na start -->
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Ładowanie...</span>
                            </div>
                            <p class="mt-3 text-muted">Ładowanie danych...</p>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Modal Dodaj Etat -->
<div class="modal fade" id="modalAddEtat" tabindex="-1" aria-labelledby="modalAddEtatLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddEtatLabel">Dodaj nowy etat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="formEtatContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="button" class="btn btn-success" id="btnSaveEtat">Zapisz</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edytuj Etat -->
<div class="modal fade" id="modalEditEtat" tabindex="-1" aria-labelledby="modalEditEtatLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditEtatLabel">Edytuj etat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="formEtatEditContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                <button type="button" class="btn btn-success" id="btnSaveEditEtat">Zapisz zmiany</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="../../../cdn/jquery.js"></script>
<script src="scriptEtaty.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'focus',
                html: true,
                sanitize: false
            });
        });
    });
</script>
</body>
</html>