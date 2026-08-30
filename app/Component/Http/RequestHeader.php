<?php declare(strict_types=1);
namespace Frrame\Component\Http;
/**
 * HTTP request header component.
 */
class RequestHeader{
    /**
     * Returns all HTTP header key/values.
     * @var array<string,string>
     */
    public static array $headers = [];
    /**
     * Loads all HTTP headers from the current request.
     * @author Ralph Khattar <ralph.khattar@gmail.com> (Original Author)
     * @author Raymondoor <torhc17311@gmail.com> (Modifier)
     * @source https://packagist.org/packages/ralouphie/getallheaders
     * @license MIT
     */
    public static function load():void{
        self::$headers = [];
        $copy_server = [
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        ];
        foreach($_SERVER as $key => $value){
            if(str_starts_with($key, 'HTTP_')){
                $key = substr($key, 5);
                if(!isset($copy_server[$key]) || !isset($_SERVER[$key])){
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    self::$headers[strtolower($key)] = $value;
                }
            } elseif(isset($copy_server[$key])){
                self::$headers[strtolower($copy_server[$key])] = $value;
            }
        }
        if(!isset($headers['Authorization'])){
            if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])){
                self::$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif(isset($_SERVER['PHP_AUTH_USER'])){
                $basic_pass = $_SERVER['PHP_AUTH_PW'] ?? '';
                self::$headers['Authorization'] = 'Basic '.base64_encode($_SERVER['PHP_AUTH_USER'].':'.$basic_pass);
            } elseif(isset($_SERVER['PHP_AUTH_DIGEST'])){
                self::$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }
    }
    public static function get(string $key, ?string $default = null):?string{
        return self::$headers[strtolower($key)]??$default;
    }
    /**
     * same as `RequestHeader::get()`, with 'X-' prefixed.
     */
    public static function getX(string $key, ?string $default = null):?string{
        return self::get('X-'.$key,$default);
    }
    public static function is(string $key, string $value):bool{
        $header = self::get($key);
        if($header === null){
            return false;
        }
        return strtolower($header) === strtolower($value);
    }
    public static function contains(string $key, string $value):bool{
        $header = self::get($key);
        if($header === null){
            return false;
        }
        return str_contains(strtolower($header),strtolower($value));
    }
}
