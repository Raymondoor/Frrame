<?php declare(strict_types=1);
namespace Frrame\Component\Http;
final readonly class RequestMethod{
	public const GET = 'GET';
	public const HEAD = 'HEAD';
	public const POST = 'POST';
	public const PUT = 'PUT';
	public const DELETE = 'DELETE';
	public const CONNECT = 'CONNECT';
	public const OPTIONS = 'OPTIONS';
	public const TRACE = 'TRACE';
	public const PATCH = 'PATCH';
	// public const QUERY = 'QUERY';
	public static function method():string{
		return $_SERVER['REQUEST_METHOD'] ?? '';
	}
	public static function get():bool{return self::method() === self::GET;}
	public static function head(): bool{return self::method() === self::HEAD;}
	public static function post(): bool{return self::method() === self::POST;}
	public static function put(): bool{return self::method() === self::PUT;}
	public static function delete(): bool{return self::method() === self::DELETE;}
	public static function connect(): bool{return self::method() === self::CONNECT;}
	public static function options(): bool{return self::method() === self::OPTIONS;}
	public static function trace(): bool{return self::method() === self::TRACE;}
	public static function patch(): bool{return self::method() === self::PATCH;}
	// public static function query(): bool{return self::method() === self::QUERY;}
}