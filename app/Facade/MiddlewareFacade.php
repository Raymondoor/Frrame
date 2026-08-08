<?php declare(strict_types=1);
namespace Frrame\Facade;
use Frrame\Component\Http\RequestBody;
use Frrame\Component\Http\RequestHeader;
use Frrame\Component\Session;
class MiddlewareFacade{
	public static function web():void{
		RequestHeader::load();
		RequestBody::load();
		Session::load();
	}
}