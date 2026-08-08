<?php declare(strict_types=1);
namespace Frrame\Component\Http;
final readonly class RequestMethod{
	public static function method():string{
		return $_SERVER['REQUEST_METHOD'] ?? '';
	}
	public static function get():bool{return self::method() === 'GET';}
	public static function head(): bool{ return self::method() === 'HEAD'; }
	public static function post(): bool{ return self::method() === 'POST'; }
	public static function put(): bool{ return self::method() === 'PUT'; }
	public static function delete(): bool{ return self::method() === 'DELETE'; }
	public static function connect(): bool{ return self::method() === 'CONNECT'; }
	public static function options(): bool{ return self::method() === 'OPTIONS'; }
	public static function trace(): bool{ return self::method() === 'TRACE'; }
	public static function patch(): bool{ return self::method() === 'PATCH'; }
}