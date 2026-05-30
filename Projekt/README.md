# Projekt Biblioteka

## Treść z moodle

Zadanie będzie polegało na napisaniu skryptu php z wykorzystaniem technologii Ajax (bez przeładowywania strony),
która będzie wspomagała pracę biblioteki.

Aplikacja powinna umożliwiaż:

1. Dodawanie nowej książki do zasobów – przez administratora
2. Usuwanie książki z zasobów – przez administratora
3. Udostępnid informację o liczbie dostępnych egzemplarzy danej książki
4. Umożliwid dodawanie nowego użytkownika – przez administratora
5. Umożliwid usuwanie czytelnika – przez administratora
6. Czytelnik ma przydzielony numer identyfikacyjny i hasło. Za pomocą tych danych może się zalogowad na
   swoje konto.
7. Po zalogowaniu czytelnik może:

   * Modyfikować swoje dane 
   * Rezerwować książkę do wypożyczenia (wypożyczanie odbywa się przez administratora)
   * Wyświetlić listę zarezerwowanych książek 
   * Wyświetlić listę wypożyczonych książek

Należy pamiętać żeby aktualizować ilość dostępnych egzemplarzy po każdej rezerwacji i zwróceniu książki.

Dane powinny byd przechowywane w bazie danych.
Dane książki:
   * Imię i nazwisko autora
   * Tytuł
   * Wydawnictwo
   * Rok wydania
   * Ilość dostępnych egzemplarzy 

Dane czytelnika:
   * Imię i nazwisko 
   * Adres zamieszkania 
   * Numer dokumentu tożsamości 
   * Identyfikator 
   * Hasło (do zmiany przy pierwszym logowaniu)