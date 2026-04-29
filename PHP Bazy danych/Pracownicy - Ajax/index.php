<?php
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Bazy danych - Pracownicy</title>
</head>
<body>

    <?php
//        if(isset($_POST['submit']) && $_POST['search']!=''){
//            $stmt = $pdo->prepare("SELECT * FROM pracownicy WHERE IMIE LIKE :imie OR NAZWISKO LIKE :nazwisko");
//            $stmt -> bindValue(':imie', '%'.$_POST['search'].'%', PDO::PARAM_STR);
//            $stmt -> bindValue(':nazwisko', '%'.$_POST['search'].'%', PDO::PARAM_STR);
//            $stmt->execute();
//        }
//        else if (isset($_POST['reset'])){
//            $stmt = $pdo->query('SELECT * FROM pracownicy');
//        }
//        else
//        {
//            $stmt = $pdo->query('SELECT * FROM pracownicy');
//        }
    ?>

    <div class="container">
        <ul class="nav nav-tabs mt-2">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Pracownicy</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="etaty.php">Etaty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="zespoly.php">Zespoły</a>
            </li>
        </ul>
        <form action="" method="post" id="form">
            <div class="row my-5">
                <div class="col-md-4 input-group" style="width: 40%;" >
                    <input type="text" class="form-control" name="search" id="search" value="<?php echo isset($_POST['reset']) ? '' : (isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''); ?>" />
                    <button class="btn btn-primary" type="submit" name="submit">Szukaj</button>
                </div>
                <div class="col-md-1 text-left">
                    <button type="submit" class="btn btn-danger" name="reset" id="reset">Resetuj</button>
                </div>
                <div class="col-md-6 text-left d-flex justify-content-end">
                    <button type="button" class="btn btn-success" id="btnAddPracownik" data-bs-toggle="modal" data-bs-target="#modalAddPracownik">Dodaj nowego pracownika</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <!-- Tabela -->
                <table class="table" id="dataTable">
                    <thead>
                    <tr>
                        <th>Id prac</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Etat</th>
                        <th>Id szefa</th>
                        <th>Zatrudniony</th>
                        <th>Placa pod</th>
                        <th>Placa dod</th>
                        <th>id zesp</th>
                        <th colspan="2">Akcje</th>
                    </tr>
                    </thead>
                    <tbody id="pracownicy">
                        <!-- Loader na start -->
                        <tr>
                            <td colspan="11" class="text-center py-5">
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

    <!-- Modal Dodaj Pracownika -->
    <div class="modal fade" id="modalAddPracownik" tabindex="-1" aria-labelledby="modalAddPracownikLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddPracownikLabel">Dodaj nowego pracownika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="formPracownikContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success" id="btnSavePracownik">Zapisz</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edytuj Pracownik -->
    <div class="modal fade" id="modalEditPracownik" tabindex="-1" aria-labelledby="modalEditPracownikLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditPracownikLabel">Edytuj pracownika</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="formPracownikEditContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                    <button type="button" class="btn btn-success" id="btnSaveEditPracownik">Zapisz zmiany</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="../../../cdn/jquery.js"></script>
    <script src="script.js"></script>
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