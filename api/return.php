<?php
require_once 'Przelewy24_API.php';

/**
 * The function takes a status code as input and returns a corresponding payment status message.
 * 
 * @param status The status parameter is an integer value representing the payment status of an order.
 * 
 * @return string that corresponds to the input status code. 
 */
function changeStatus($status)
{
    switch ($status) {
        case 0:
            return "Brak płatności";
            break;
        case 1:
            return "Płatność zaliczkowa";
            break;
        case 2:
            return "Płatność dokonana";
            break;
        case 3:
            return "Płatność zwrócona";
            break;
        default:
            return "Nieznany status";
            break;
    }
}

$p24_session_id = $_POST['p24_session_id'];
$p24_order_id = $_POST['p24_order_id'];
$p24_amount = $_POST['p24_amount'];
$p24_currency = $_POST['p24_currency'];
$p24 = new Przelewy24_API();
$verified = $p24->Verify($_POST);
$status = $p24->getTransactionStatus($_POST['p24_session_id']);
$stringStatus = changeStatus($status['status']);

// Zaaktualizuj status zamówienia w bazie danych

?>
