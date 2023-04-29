<?php
    require_once __DIR__.'/api/Przelewy24_API.php';

    define('PRODUCTION', false);

    $p24 = new Przelewy24_API();
    if (isset($_POST['process']) && $_POST['process'] == 'pay') {

        $amount = $_POST['amount'] * 100;
        $description = $_POST['description'];
        $email = $_POST['email'];

        //Zmienne dla środowiska testowego i produkcyjnego
        if (PRODUCTION) {
            $url = 'https://adres-produkcja.pl';
        } else {
            $url = 'https://adres-testowy.pl';
        }

        $p24_url_return = "$url/thank-you.php";
        $p24_url_status = "$url/api/return.php";
        $redirect = $p24->Pay($amount, $description, $email, $p24_url_return, $p24_url_status);
        // Zapisz zainicjowaną płatność w bazie danych
        if ($redirect === false) {
            echo "Błąd podczas płatności.";
            die();
        }
        Header('Location: ' . $redirect['url']);
    }
?>