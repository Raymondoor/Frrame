<?php declare(strict_types=1);
require_once __DIR__.'/../vendor/autoload.php';
use \Frrame\Component\Http\RequestMethod;
use Frrame\Controller\WelcomeController;
use Frrame\Facade\MiddlewareFacade;
MiddlewareFacade::web();
if(RequestMethod::get()){
    WelcomeController::index();
}else{
    http_response_code(404);
    exit;
}