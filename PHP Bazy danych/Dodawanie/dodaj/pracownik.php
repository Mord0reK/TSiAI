<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dodawanie Pracownika</title>
</head>
<body>

    <div class="container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" href="../index.php ">Pracownicy</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../etaty.php">Etaty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../zespoly.php">Zespoły</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../polaczone.php">Połączone</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-12">
                <?php
                    $stmt = $pdo->prepare('SELECT NAZWA FROM etaty');
                    $stmt->execute();
                    $etaty = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT CONCAT(IMIE, " ", NAZWISKO) AS NAZWA FROM pracownicy');
                    $stmt->execute();
                    $szefy = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT NAZWA FROM zespoly');
                    $stmt->execute();
                    $zespoły = $stmt->fetchAll();
                ?>
                <form class="mt-4" method="post" action="../index.php">
                    <div class="form-floating mb-3">
                        <input type="imie" class="form-control" id="floatingInputImie" placeholder="Jan">
                        <label for="floatingInputImie">Imię</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="Nazwisko" class="form-control" id="floatingInputNazwisko" placeholder="Kowalski">
                        <label for="floatingInputNazwisko">Nazwisko</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" id="floatingSelectEtat" aria-label="Default select example">
                            <option value="" selected>Wybierz etat</option>
                            <?php
                                foreach ($etaty as $etat) {
                                    echo '<option value="' . htmlspecialchars($etat['NAZWA']) . '">' . htmlspecialchars($etat['NAZWA']) . '</option>';
                                }
                            ?>      
                        </select>
                        <label for ="floatingSelectEtat">Etat</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" id="floatingSelectSzef" aria-label="Default select example">
                            <option value="0" selected>Brak szefa</option>
                            <?php
                            foreach ($szefy as $szef) {
                                echo '<option value="' . htmlspecialchars($szef['NAZWA']) . '">' . htmlspecialchars($szef['NAZWA']) . '</option>';
                            }
                        ?>
                        </select>
                        <label for="floatingSelectSzef">Szef</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="floatingInputPłaca" placeholder="1000" min="0">
                        <label for="floatingInputPłaca">Płaca podstawowa</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0">
                        <label for="floatingInputPłacaDodatkowa">Płaca dodatkowa</label>
                    </div>
                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" id="floatingSelectZespol" aria-label="Default select example">
                            <option value="0" selected>Brak Zespołu</option>
                            <?php
                            foreach ($zespoły as $zespol) {
                                echo '<option value="' . htmlspecialchars($zespol['NAZWA']) . '">' . htmlspecialchars($zespol['NAZWA']) . '</option>';
                            }
                        ?>
                        </select>
                        <label for="floatingSelectZespol">Zespół</label>
                    </div>
                    <button type="submit" class="btn btn-success">Zapisz dane</button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html> 