<?php declare(strict_types=1);
namespace Frrame\Component;
/**
 * Session handler instead of traditional $_SESSION.
 * Designed to avoid session locking.
 */
final class Session{
    /** @var array<string,mixed> In-memory mirror of $_SESSION, refreshed per read/write. */
    public static array $cache = [];
    public static function start_safe():void{
        if(session_status() === PHP_SESSION_NONE){
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }
    public static function close():void{
        session_write_close();
    }
    public static function load():void{
        self::start_safe();
        self::$cache = $_SESSION;
        self::close();
    }
    public static function refresh():void{
        self::start_safe();
        self::$cache = $_SESSION;
        self::close();
    }
    public static function get(string $key, mixed $default = null):mixed{
        return self::$cache[$key] ?? $default;
    }
    public static function getFresh(string $key, mixed $default = null):mixed{
        self::start_safe();
        self::$cache = $_SESSION;
        $value = $_SESSION[$key] ?? $default;
        self::close();
        return $value;
    }
    public static function set(string $key, mixed $value):void{
        self::$cache[$key] = $value;
        self::start_safe();
        $_SESSION[$key] = $value;
        self::close();
    }
    public static function unset(string $key):void{
        unset(self::$cache[$key]);
        self::start_safe();
        unset($_SESSION[$key]);
        self::close();
    }
}