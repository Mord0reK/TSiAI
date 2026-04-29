<?php
require_once 'database.php';

$zapisano = '';
$blad = '';
$blad_imie = '';
$blad_nazwisko = '';
$blad_etat = '';
$blad_szef = '';
$blad_zespol = '';
$blad_data_zatrudnienia = '';
$blad_placa_pod = '';
$blad_placa_dod = '';
$etat_data = null;

if (!empty($_POST)) {

    // Sprawdzanie imienia
    if (isset($_POST['imie']) && !empty($_POST['imie']) && mb_strlen($_POST['imie']) > 20)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_imie = "Imię pracownika nie może być dłuższe niż 20 znaków";
    }
    else if (isset($_POST['imie']) && !empty($_POST['imie']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/u', $_POST['imie']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_imie = "W polu znalazły się inne znaki niż litery";
    }
    else if (isset($_POST['imie']) && empty($_POST['imie']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_imie = "Nie podano imienia";
    }
    else if (isset($_POST['imie']) && !empty($_POST['imie']))
    {
        $imie = $_POST['imie'];
    }

    // Sprawdzanie nazwiska
    if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']) && mb_strlen($_POST['nazwisko']) > 15)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwisko = "Nazwisko pracownika nie może być dłuższe niż 15 znaków";
    }
    else if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/u', $_POST['nazwisko']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwisko = "W polu znalazły się inne znaki niż litery";
    }
    else if (isset($_POST['nazwisko']) && empty($_POST['nazwisko']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwisko = "Nie podano nazwiska";
    }
    else if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']))
    {
        $nazwisko = $_POST['nazwisko'];
    }

    // Sprawdzanie etatu
    if (isset($_POST['etat']) && (empty($_POST['etat']) || $_POST['etat'] == "Wybierz etat"))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_etat = "Nie wybrano etatu";
    }
    else if (isset($_POST['etat']))
    {
        $etat = $_POST['etat'];

        $stmt = $pdo->prepare('SELECT PLACA_OD, PLACA_DO FROM etaty WHERE NAZWA = :etat');
        $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
        $stmt->execute();
        $etat_data = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Sprawdzanie szefa
    if (isset($_POST['szef']) && $_POST['szef'] == "0")
    {
        $szef = Null;
    }
    else if (isset($_POST['szef']))
    {
        $szef = $_POST['szef'] === '' ? null : intval($_POST['szef']);
    }

    // Sprawdzanie zespolu
    if (!isset($_POST['zespol']) || $_POST['zespol'] == "0")
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_zespol = "Nie podano zespołu";
    }
    else if (isset($_POST['zespol']))
    {
        $zespol = $_POST['zespol'];
    }

    // Sprawdzanie daty zatrudnienia
    if (isset($_POST['data_zatrudnienia']) && empty($_POST['data_zatrudnienia']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_data_zatrudnienia = "Nie podano daty zatrudnienia";
    }
    else if (isset($_POST['data_zatrudnienia']) && !empty($_POST['data_zatrudnienia']))
    {
        $data = DateTime::createFromFormat('Y-m-d', $_POST['data_zatrudnienia']);
        $dzisiaj = new DateTime();
        $min_data = (clone $dzisiaj)->modify('-100 years');
        $max_data = (clone $dzisiaj)->modify('+100 years');

        if ($data < $min_data || $data > $max_data) {
            $zapisano = "Nie";
            $blad = "Tak";
            $blad_data_zatrudnienia = "Data nie może być o 100 lat do tyłu oraz 100 lat do przodu";
        } else {
            $data_zatrudnienia = $data->format('Y-m-d');
        }
    }

    // Sprawdzanie placy podstawowej
    if (isset($_POST['placa_pod']) && empty($_POST['placa_pod']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_pod = "Nie podano plac podstawowej";
    }
    else if (isset($_POST['placa_pod']) && !is_numeric($_POST['placa_pod']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_pod = "Wprowadzono złą płacę";
    }
    else if (isset($_POST['placa_pod']) && $_POST['placa_pod'] <= 0)
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_pod = "Płaca nie może być mniejsza od 0";
    }
    else if (isset($_POST['placa_pod']))
    {
        $placa_pod = $_POST['placa_pod'];

        if ($etat_data && ($placa_pod < $etat_data['PLACA_OD'] || $placa_pod > $etat_data['PLACA_DO'])) {
            $zapisano = "Nie";
            $blad = "Tak";
            $blad_placa_pod = "Płaca podstawowa musi być w przedziale " . $etat_data['PLACA_OD'] . " - " . $etat_data['PLACA_DO'];
        }
    }

    // Sprawdzanie placy dodatkowej
    if (!empty($_POST['placa_dod']) && !is_numeric($_POST['placa_dod']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_dod = "Wprowadzono złą płacę dodatkową";
    }
    else if (!empty($_POST['placa_dod']) && $_POST['placa_dod'] <= 0)
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_dod = "Płaca dodatkowa nie może być mniejsza od 0";
    }
    else if (!empty($_POST['placa_dod']) && $_POST['placa_dod'] >= $_POST['placa_pod'])
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_dod = "Płaca dodatkowa nie może być większa niż płaca podstawowa";
    }
    else if (!empty($_POST['placa_dod']))
    {
        $placa_dod = $_POST['placa_dod'];
    }
    else
    {
        $placa_dod = NULL;
    }

    if ($zapisano == "")
    {
        $id_pracownika = $pdo->query("SELECT MAX(ID_PRAC) AS ID FROM pracownicy")->fetch(PDO::FETCH_ASSOC)['ID'] + 10;

        $stmt = $pdo->prepare("INSERT INTO pracownicy (ID_PRAC, NAZWISKO, IMIE, ETAT, ID_SZEFA, ZATRUDNIONY, PLACA_POD, PLACA_DOD, ID_ZESP) VALUES (:id_pracownika, :nazwisko, :imie, :etat, :szef, :data_zatrudnienia, :placa_pod, :placa_dod , :id_zesp)");
        $stmt->bindParam(':id_pracownika', $id_pracownika);
        $stmt->bindParam(':nazwisko', $nazwisko);
        $stmt->bindParam(':imie', $imie);
        $stmt->bindParam(':etat', $etat);
        $stmt->bindParam(':szef', $szef);
        $stmt->bindParam(':data_zatrudnienia', $data_zatrudnienia);
        $stmt->bindParam(':placa_pod', $placa_pod);
        $stmt->bindParam(':placa_dod', $placa_dod);
        $stmt->bindParam(':id_zesp', $zespol);
        $stmt->execute();
        $zapisano = "Tak";
    }
}

if ($zapisano === 'Tak') {
    echo '<div class="alert alert-success">Poprawnie dodano pracownika!</div>';
} elseif ($blad !== '') {
    echo '<div class="alert alert-danger">Formularz zawiera błędy</div>';
    echo '<div data-error-for="imie">' . $blad_imie . '</div>';
    echo '<div data-error-for="nazwisko">' . $blad_nazwisko . '</div>';
    echo '<div data-error-for="etat">' . $blad_etat . '</div>';
    echo '<div data-error-for="szef">' . $blad_szef . '</div>';
    echo '<div data-error-for="zespol">' . $blad_zespol . '</div>';
    echo '<div data-error-for="data_zatrudnienia">' . $blad_data_zatrudnienia . '</div>';
    echo '<div data-error-for="placa_pod">' . $blad_placa_pod . '</div>';
    echo '<div data-error-for="placa_dod">' . $blad_placa_dod . '</div>';
}
