<?php declare(strict_types=1);
namespace Frrame\Component\Http;
#[\Attribute]
class Request{
	public function __construct(
		public string $route = '/',
		public string $method = 'GET',
		public string $accept = 'text/html'
	){}
}