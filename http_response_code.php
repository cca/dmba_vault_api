<?php
// taken from http://stackoverflow.com/questions/3258634/php-how-to-send-http-response-code
// http_response_code doesn't exist below PHP 5.4 so we shim over it
// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code')) {
    function http_response_code($newcode = NULL) {
        static $code = 200;
        if ($newcode !== NULL) {
            header('X-PHP-Response-Code: ' . $newcode, true, $newcode);
            if(!headers_sent()) {
                $code = $newcode;
            }
        }
        return $code;
    }
}
