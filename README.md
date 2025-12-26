# Planta

Helps you take care of your houseplants!

# UI Design

<img src="public/images/Planta_UI_Design_page-0001.jpg" alt=""/>
<img src="public/images/Planta_UI_Design_page-0002.jpg" alt=""/>
<img src="public/images/Planta_UI_Design_page-0003.jpg" alt=""/>
<img src="public/images/Planta_UI_Design_page-0004.jpg" alt=""/>
<img src="public/images/Planta_UI_Design_page-0005.jpg" alt=""/>
<img src="public/images/Planta_UI_Design_page-0006.jpg" alt="" width="300"/>
<img src="public/images/Planta_UI_Design_page-0007.jpg" alt="" width="300"/>
<img src="public/images/Planta_UI_Design_page-0008.jpg" alt="" width="300"/>
<img src="public/images/Planta_UI_Design_page-0009.jpg" alt="" width="300"/>
<img src="public/images/Planta_UI_Design_page-0010.jpg" alt="" width="300"/>

# Bingo

Ochrona przed SQL injection (prepared statements / brak konkatenacji SQL)
Nie zdradzam, czy email istnieje – komunikat typu „Email lub hasło niepoprawne”
Walidacja formatu email po stronie serwera
UserRepository zarządzany jako singleton
Logowanie i rejestracja dostępne tylko przez HTTPS
Metoda login/register przyjmuje dane tylko na POST, GET tylko renderuje widok
CSRF token w formularzu logowania
CSRF token w formularzu rejestracji
Ograniczam długość wejścia (email, hasło, imię…)
Hasła przechowywane jako hash (bcrypt/Argon2, password_hash)
Hasła nigdy nie są logowane w logach / errorach
Po poprawnym logowaniu regeneruję ID sesji
Cookie sesyjne ma flagę HttpOnly
Cookie sesyjne ma flagę Secure
Cookie ma ustawione SameSite (np. Lax/Strict)
Limit prób logowania / blokada czasowa / CAPTCHA po wielu nieudanych próbach
Waliduję złożoność hasła (min. długość itd.)
Przy rejestracji sprawdzam, czy email jest już w bazie
Dane wyświetlane w widokach są escapowane (ochrona przed XSS)
W produkcji nie pokazuję stack trace / surowych błędów użytkownikowi
Zwracam sensowne kody HTTP (np. 400/401/403 przy błędach)
Hasło nie jest przekazywane do widoków ani echo/var_dump
Z bazy pobieram tylko minimalny zestaw danych o użytkowniku
Mam poprawne wylogowanie – niszczę sesję użytkownika
Loguję nieudane próby logowania (bez haseł) do audytu

# Wymagania

- [ ] Dokumentacja w README.md
- [ ] Architektura aplikacji MVC/front-backend, inna
- [ ] Kod napisany obiektowo (część backendowa)
- [ ] Diagram ERD
- [ ] Git
- [ ] Realizacja tematu
- [ ] HTML
- [ ] PostgreSQL
- [ ] Złożoność bazy danych
- [ ] Eksport bazy do pliku .sql
- [ ] PHP
- [ ] Java Script
- [ ] Fetch API (Ajax)
- [ ] Design
- [ ] Responsywność
- [ ] Logowanie
- [ ] Sesja użytkownika
- [ ] Uprawnienia użytkowników
- [ ] Role użytkowników - co najmniej dwie
- [ ] Wylogowywanie
- [ ] Widoki, wyzwalacze, funkcje, transakcje
- [ ] Akcje na referencjach (joiny w bazie danych)
- [ ] Bezpieczeństwo
- [ ] Brak replikacji kodu
- [ ] Czystość i przejrzystość kodu

## ERD

<img src="public/images/ERD.png" alt="" width="300"/>
