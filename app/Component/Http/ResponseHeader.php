<?php declare(strict_types=1);
namespace Frrame\Component\Http;
class ResponseHeader{
    public static function set(string $raw):void{
        header($raw);
    }
    public static function unset(string $key){
        header_remove($key);
    }
    public static function redirect(string $path = '/', bool $full = false):void{
        header('Location: '.($full ? '' : HOME_URL).urlencode($path));
        exit;
    }
    /**
     * Set HTTP Response Code
     * @param int $httpCode
     * @return void
     */
    public static function code(int $httpCode = 200):void{
        http_response_code($httpCode);
    }
}
