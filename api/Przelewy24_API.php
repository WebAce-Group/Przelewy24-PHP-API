<?php
    define('PRZELEWY24_MERCHANT_ID', 'TWOJE_MERCHANT_ID');
    define('PRZELEWY24_CRC', 'TWÓJ_CRC');
    // sandbox - środowisko testowe, secure - środowisko produkcyjne
    define('PRZELEWY24_TYPE', 'sandbox');
    define('API_LOGIN', 'TWOJE_API_LOGIN');
    define('API_PASSWORD', 'TWOJE_API_PASSWORD');

    class Przelewy24_API
    {
        /**
         * The function creates a token for a Przelewy24 payment transaction with specified parameters.
         * 
         * @param p24_amount The amount of the transaction in PLN (Polish Zloty).
         * @param p24_description A description of the transaction being made.
         * @param p24_email The email address of the customer making the payment.
         * @param p24_url_return The URL where the customer will be redirected after completing the payment
         * process.
         * @param p24_url_status The URL where Przelewy24 will send a notification about the status of the
         * transaction.
         * @param type The type of transaction, which is set to 'donation' by default.
         * 
         * @return array with the token and session ID if the token is set in the response, otherwise it
         * returns 0.
         */
        public function CreateToken($p24_amount = null, $p24_description = null, $p24_email = null, $p24_url_return = null, $p24_url_status = null, $type = 'donation')
        {
            $p24_session_id = uniqid();
            $headers[] = 'p24_merchant_id=' . PRZELEWY24_MERCHANT_ID;
            $headers[] = 'p24_pos_id=' . PRZELEWY24_MERCHANT_ID;
            $headers[] = 'p24_crc=' . PRZELEWY24_CRC;
            $headers[] = 'p24_session_id=' . $p24_session_id;
            $headers[] = 'p24_amount=' . $p24_amount;
            $headers[] = 'p24_currency=PLN';
            $headers[] = 'p24_description=' . $p24_description;
            $headers[] = 'p24_country=PL';
            $headers[] = 'p24_url_return=' . urlencode($p24_url_return);
            $headers[] = 'p24_url_status=' . urlencode($p24_url_status);
            $headers[] = 'p24_api_version=3.2';
            $headers[] = 'p24_sign=' . md5($p24_session_id . '|' . PRZELEWY24_MERCHANT_ID . '|' . $p24_amount . '|PLN|' . PRZELEWY24_CRC);
            $headers[] = 'p24_email=' . $p24_email;

            $oCURL = curl_init();
            curl_setopt($oCURL, CURLOPT_POST, 1);
            curl_setopt($oCURL, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            curl_setopt($oCURL, CURLOPT_POSTFIELDS, implode('&', $headers));
            curl_setopt($oCURL, CURLOPT_URL, 'https://' . PRZELEWY24_TYPE . '.przelewy24.pl/trnRegister');
            curl_setopt($oCURL, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($oCURL, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($oCURL, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCURL, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($oCURL);
            curl_close($oCURL);

            parse_str($response, $output);

            $token = isset($output['token']) ? array("token"=>$output['token'],"session_id"=>$p24_session_id) : 0;
            return $token;
        }

        /**
         * The function Pay creates a token and returns a URL and session ID for a Przelewy24 payment.
         * 
         * @param p24_amount The amount of money to be paid in the transaction.
         * @param p24_description A description of the transaction being made.
         * @param p24_email The email address of the customer making the payment.
         * @param p24_url_return The URL where the customer will be redirected after completing the payment
         * process.
         * @param p24_url_status The URL where Przelewy24 will send a notification about the payment status.
         * 
         * @return array with two keys: "url" and "session_id". The "url" key contains the URL to redirect
         * the user to complete the payment process, and the "session_id" key contains the session ID
         * associated with the payment.
         * @return false if the token is not set in the response.
         */
        public function Pay($p24_amount = null, $p24_description = null, $p24_email = null, $p24_url_return = null, $p24_url_status = null)
        {
            $response = $this->CreateToken($p24_amount, $p24_description, $p24_email, $p24_url_return, $p24_url_status);
            if ($response['token'] == null)
                return false;
            else {
                $data = array(
                    "url" => 'https://' . PRZELEWY24_TYPE . '.przelewy24.pl/trnRequest/' . $response['token'],
                    "session_id" => $response['session_id']
                );
            }
            return $data;
        }

        /**
         * This is a PHP function that verifies a payment transaction using the Przelewy24 payment gateway.
         * 
         * @param data The  parameter is an array that contains the following information:
         * 
         * @return true on success
         * @return array with the error code and error description if the transaction fails.
         */
        public function Verify($data = null)
        {
            $headers[] = 'p24_merchant_id=' . PRZELEWY24_MERCHANT_ID;
            $headers[] = 'p24_pos_id=' . PRZELEWY24_MERCHANT_ID;
            $headers[] = 'p24_session_id=' . $data['p24_session_id'];
            $headers[] = 'p24_amount=' . $data['p24_amount'];
            $headers[] = 'p24_currency=PLN';
            $headers[] = 'p24_order_id=' . $data['p24_order_id'];
            $headers[] = 'p24_sign=' . md5($data['p24_session_id'] . '|' . $data['p24_order_id'] . '|' . $data['p24_amount'] . '|PLN|' . PRZELEWY24_CRC);

            $oCURL = curl_init();
            curl_setopt($oCURL, CURLOPT_POST, 1);
            curl_setopt($oCURL, CURLOPT_SSL_CIPHER_LIST, 'TLSv1');
            curl_setopt($oCURL, CURLOPT_POSTFIELDS, implode('&', $headers));
            curl_setopt($oCURL, CURLOPT_URL, 'https://' . PRZELEWY24_TYPE . '.przelewy24.pl/trnVerify');
            curl_setopt($oCURL, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($oCURL, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
            curl_setopt($oCURL, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($oCURL, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($oCURL);
            curl_close($oCURL);

            parse_str($response, $output);
            return ($output['error'] == '0') ? true : $output;
        }

        /**
         * This PHP function retrieves the transaction status from Przelewy24 API using a session ID.
         * 
         * @param p24_session_id This is the unique session ID generated by Przelewy24 for a specific
         * transaction. It is used to identify and retrieve information about the transaction.
         * 
         * @return array with the transaction status and the transaction ID.
         * @return array with the error code and error description if the transaction fails.
         */
        public function getTransactionStatus($p24_session_id){
            $url = 'https://'.PRZELEWY24_TYPE.".przelewy24.pl/api/v1/transaction/by/sessionId/{$p24_session_id}";
            $api_creds = base64_encode(API_LOGIN . ':' . API_PASSWORD);
            $headers = array (
                "Authorization: Basic {$api_creds}"
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            curl_close($ch);

            parse_str($response, $output);

            $sanitized = key($output);

            $arr = json_decode($sanitized, true);

            if (isset($arr['error']))
                return $arr['error'];

            return $arr['data'];
        }
    }