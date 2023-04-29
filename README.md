# Przelewy24 PHP API

Projekt Przelewy24 PHP API jest gotowym rozwiązaniem dla programistów PHP, którzy chcą połączyć swoją aplikację z systemem płatności Przelewy24. API pozwala na bezproblemowe przesyłanie zapytań do serwera Przelewy24 i odbieranie odpowiedzi.

## Wymagania

- PHP w wersji 7.4 lub nowszej
- Zainstalowany cURL na serwerze

## Pliki w projekcie

- `api`
- - `Przelewy24_API.php` - główny plik API
- - `return.php` - plik do którego przekierowywane są dane z serwera Przelewy24 po zakończeniu płatności
- `pay.php` - plik do którego odwołuje się formularz płatności i który przekierowuje do stron płatności Przelewy24
- `payment.html` - formularz płatności
- `thank-you.php` - strona na którą użytkownik zostanie przekierowany po zakończeniu płatności

## Konfiguracja

- `Przelewy24_API.php` - w tym pliku należy ustawić dane dostępowe do konta Przelewy24

```php
define('PRZELEWY24_MERCHANT_ID', 'TWOJE_MERCHANT_ID');
define('PRZELEWY24_CRC', 'TWÓJ_CRC');
// sandbox - środowisko testowe, secure - środowisko produkcyjne
define('PRZELEWY24_TYPE', 'sandbox');
define('API_LOGIN', 'TWOJE_API_LOGIN');
define('API_PASSWORD', 'TWOJE_API_PASSWORD');
```

- `pay.php` - w tym pliku należy ustawić adres URL do pliku `return.php` oraz adres URL do pliku `thank-you.php`

```php
//Zmienne dla środowiska testowego i produkcyjnego
if (PRODUCTION) {
    $url = 'https://adres-produkcja.pl';
} else {
    $url = 'https://adres-testowy.pl';
}

$p24_url_return = "$url/thank-you.php";
$p24_url_status = "$url/api/return.php";
```

- Ważne jest również utworzenie bazy danych i przechowywanie w niej informacji o płatnościach. Tutaj przykładowa tabela:

```
+----------------+---------------------------------+------+-----+---------------------+-------------------------------+
| Field          | Type                            | Null | Key | Default             | Extra                         |
+----------------+---------------------------------+------+-----+---------------------+-------------------------------+
| id             | int(11) unsigned                | NO   | PRI | NULL                | auto_increment                |
| session_id     | varchar(50)                     | NO   |     | NULL                |                               |
| order_id       | varchar(50)                     | NO   |     | -                   |                               |
| amount         | decimal(10,2)                   | NO   |     | NULL                |                               |
| currency       | varchar(10)                     | NO   |     | NULL                |                               |
| payment_status | varchar(20)                     | NO   |     | Brak płatności      |                               |
| email          | varchar(255)                    | NO   |     | NULL                |                               |
| payment_date   | timestamp                       | NO   |     | current_timestamp() |                               |
| last_update    | timestamp                       | NO   |     | current_timestamp() | on update current_timestamp() |
| description    | varchar(255)                    | NO   |     |                     |                               |
+----------------+---------------------------------+------+-----+---------------------+-------------------------------+
```

## Autor

👤 **Adrian Goral**

* GitHub: [@xEdziu](https://github.com/xEdziu)
* Facebook: [Adrian Goral](https://www.facebook.com/adrian.goral.6)

## 📝 Licencja

Copyright © 2023 [Adrian Goral](https://github.com/xEdziu). <br />
Ten projekt jest objęty licencją [MIT](https://github.com/WebAce-Group/Przelewy24-PHP-API/blob/main/LICENSE).