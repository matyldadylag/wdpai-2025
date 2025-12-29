# Planta

## Opis

Planta to aplikacja webowa służąca do zarządzania roślinami oraz planowania i monitorowania zadań pielęgnacyjnych z nimi związanych. Umożliwia użytkownikom dodawanie własnych roślin oraz bieżące śledzenie zadań takich jak podlewanie, nawożenie czy przycinanie.

### Główne funkcjonalności aplikacji:

- Rejestracja i logowanie użytkowników.
- Dodawanie, edycja oraz usuwanie roślin przypisanych do użytkownika.
- Automatyczne planowanie harmonogramu zadań pielęgnacyjnych dla każdej z roślin użytkownika.
- Wyświetlanie harmonogramu zadań pielęgnacyjnych przy pomocy kalendarza.
- Interaktywna lista zadań, umożliwiająca oznaczanie zadań jako wykonane.
- Widok Dashboard, prezentujący zwięzłe podsumowanie najważniejszych zadań na dany dzień.
- Panel administracyjny dostępny wyłącznie dla upoważnionych, umożliwiający zarządzanie użytkownikami aplikacji.

Aplikacja Planta wspiera użytkowników w regularnej i świadomej pielęgnacji roślin, ułatwiając planowanie oraz realizację zadań pielęgnacyjnych.

## Opis implementacji wybranych wymagań

### Architektura aplikacji MVC/front-backend, inna

Struktura aplikacji jest podzielona na warstwy:

- Model: komunikacja z bazą danych, logika zarządzania danymi. Pliki w folderze /src/repository (np. PlantsRepository.php).
- View: prezentacja danych. Pliki w folderze /public/views (np. my-plants.html)
- Controller: zarządza zapytaniami. Pliki w folderze /src/controllers (np. MyPlantsController.php).

### Diagram ERD

<img src="public/images/ERD.png" alt="" width="300"/>

### Git

[Link do repozytorium GitHub](https://github.com/matyldadylag/wdpai-2025).

### Fetch API (AJAX)

W [calendar.js](public/scripts/calendar.js):

Użyty jest Fetch API, który wysyła żądanie POST z danymi w postacji JSON.

```
const res = await fetch("/calendar/mark-task-done", {
    method: "POST",
    headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "fetch",
    },
    body: JSON.stringify({ plant_id: plantId, task_id: taskId }),
});
```

W dalszej części kodu odpowiedź z serwera jest odbierana, parsowana jako JSON, sprawdzany jest status operacji (data.ok), interfejs użytkownika jest aktualizowany.

```
const data = await res.json().catch(() => null);

if (!res.ok || !data || !data.ok) {
    throw new Error((data && data.error) || "Request failed");
}

item.classList.add("calendar-task-done");
cb.checked = true;
window.location.reload();
```

### Design

<img src="public/images/login.png" alt="" width="600"/>
<img src="public/images/register.png" alt="" width="600"/>
<img src="public/images/dashboard.png" alt="" width="600"/>
<img src="public/images/my-plants.png" alt="" width="600"/>
<img src="public/images/calendar.png" alt="" width="600"/>
<img src="public/images/admin-users.png" alt="" width="600"/>

### Responsywność

<div>
<img src="public/images/login-mobile.png" alt="" width="300"/>
<img src="public/images/register-mobile.png" alt="" width="300"/>
<img src="public/images/dashboard-mobile.png" alt="" width="300"/>
<img src="public/images/my-plants-mobile.png" alt="" width="300"/>
<img src="public/images/calendar-mobile.png" alt="" width="300"/>
<img src="public/images/admin-users-mobile.png" alt="" width="300"/>
</div>

### Wylogowywanie

```
public function logout()
{
    // Start session if not started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Unset all session variables
    session_unset();

    // Destroy the session
    session_destroy();

    // Delete session cookie (important for full logout)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    $this->redirect('login');
}
```

### Widoki, wyzwalacze, funkcje, transakcje

### Bezpieczeństwo

1. Ochrona przed SQL injection (prepared statements / brak konkatenacji SQL)
2. Nie zdradzam, czy email istnieje – komunikat typu „Email lub hasło niepoprawne”
3. Walidacja formatu email po stronie serwera
4. UserRepository zarządzany jako singleton
5. Hasła przechowywane jako hash (bcrypt/Argon2, password_hash)
6. Hasła nigdy nie są logowane w logach / errorach
7. Waliduję złożoność hasła (min. długość itd.)
8. Przy rejestracji sprawdzam, czy email jest już w bazie
9. Z bazy pobieram tylko minimalny zestaw danych o użytkowniku
10. Hasło nie jest przekazywane do widoków ani echo/var_dump
11. Metoda login/register przyjmuje dane tylko na POST, GET tylko renderuje widok
12. Zwracam sensowne kody HTTP (np. 400/401/403 przy błędach)
13. Po poprawnym logowaniu regeneruję ID sesji
14. Mam poprawne wylogowanie – niszczę sesję użytkownika
15. Limit prób logowania / blokada czasowa / CAPTCHA po wielu nieudanych próbach
