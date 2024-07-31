# Pet Store Application

Aplikacja Laravel komunikująca się z API Petstore, umożliwiająca zarządzanie zwierzętami (dodawanie, edycję, usuwanie i wyświetlanie szczegółów).

## Instalacja

1. Sklonuj repozytorium:

    ```bash
    git clone https://github.com/n4tek-dev/laravel-petstore.git
    cd laravel-petstore
    ```

2. Skonfiguruj plik `.env`:

    Skopiuj plik `.env.example` do `.env`:

    ```bash
    cp .env.example .env
    ```

    W pliku `.env` skonfiguruj inne ustawienia środowiskowe.

3. Uruchom kontenery Docker:

    ```bash
    docker-compose up -d
    ```

4. Wejdź do kontenera aplikacji:

    ```bash
    docker-compose exec app bash
    ```

5. Zainstaluj zależności za pomocą Composer:

    ```bash
    composer install
    ```

6. Wygeneruj klucz aplikacji:

    ```bash
    php artisan key:generate
    ```

## Uruchomienie aplikacji

1. Otwórz przeglądarkę i przejdź do adresu:

    ```
    http://localhost:8000
    ```

## Testowanie

Aby uruchomić testy jednostkowe, użyj następującego polecenia w kontenerze:

```bash
php artisan test
