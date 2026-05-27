-- =============================================================
-- Schemat bazy danych - System zarządzania biblioteką
-- TSiAI Projekt
-- =============================================================

CREATE DATABASE IF NOT EXISTS TSiAI_Projekt
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE TSiAI_Projekt;

-- ---------------------------------------------------------
-- Administratorzy systemu
-- ---------------------------------------------------------
CREATE TABLE adminy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL COMMENT 'bcrypt hash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------
-- Czytelnicy
-- ---------------------------------------------------------
CREATE TABLE czytelnicy (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imie VARCHAR(100) NOT NULL,
    nazwisko VARCHAR(100) NOT NULL,
    adres VARCHAR(255) NOT NULL,
    nr_dokumentu VARCHAR(50) NOT NULL,
    identyfikator VARCHAR(50) NOT NULL UNIQUE,
    haslo VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
    zmien_haslo TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = wymagana zmiana przy logowaniu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------
-- Książki
-- ---------------------------------------------------------
CREATE TABLE ksiazki (
    id INT AUTO_INCREMENT PRIMARY KEY,
    autor VARCHAR(200) NOT NULL,
    tytul VARCHAR(200) NOT NULL,
    wydawnictwo VARCHAR(200) NOT NULL,
    rok_wydania YEAR NOT NULL,
    ilosc_calkowita INT NOT NULL DEFAULT 1,
    ilosc_dostepnych INT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------
-- Rezerwacje książek (dokonywane przez czytelnika)
-- ---------------------------------------------------------
CREATE TABLE rezerwacje (
    id INT AUTO_INCREMENT PRIMARY KEY,
    czytelnik_id INT NOT NULL,
    ksiazka_id INT NOT NULL,
    data_rezerwacji DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('aktywna', 'zrealizowana', 'anulowana') NOT NULL DEFAULT 'aktywna',
    FOREIGN KEY (czytelnik_id) REFERENCES czytelnicy(id) ON DELETE CASCADE,
    FOREIGN KEY (ksiazka_id) REFERENCES ksiazki(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------
-- Wypożyczenia (realizowane przez administratora)
-- ---------------------------------------------------------
CREATE TABLE wypozyczenia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    czytelnik_id INT NOT NULL,
    ksiazka_id INT NOT NULL,
    data_wypozyczenia DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    termin_zwrotu DATE NOT NULL,
    data_zwrotu DATETIME DEFAULT NULL,
    status ENUM('aktywne', 'zwrocone') NOT NULL DEFAULT 'aktywne',
    FOREIGN KEY (czytelnik_id) REFERENCES czytelnicy(id) ON DELETE CASCADE,
    FOREIGN KEY (ksiazka_id) REFERENCES ksiazki(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ---------------------------------------------------------
-- Indeksy pomocnicze
-- ---------------------------------------------------------
CREATE INDEX idx_rezerwacje_czytelnik ON rezerwacje(czytelnik_id);
CREATE INDEX idx_rezerwacje_ksiazka ON rezerwacje(ksiazka_id);
CREATE INDEX idx_wypozyczenia_czytelnik ON wypozyczenia(czytelnik_id);
CREATE INDEX idx_wypozyczenia_ksiazka ON wypozyczenia(ksiazka_id);
CREATE INDEX idx_ksiazki_autor ON ksiazki(autor);
CREATE INDEX idx_ksiazki_tytul ON ksiazki(tytul);
