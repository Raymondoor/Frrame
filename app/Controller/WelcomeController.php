<?php declare(strict_types=1);
namespace Frrame\Controller;
use Frrame\Base\Controller;
use Frrame\Component\I18n;
use Frrame\Facade\PageFacade;
use Frrame\View\WebView;
class WelcomeController extends Controller{
	public static function index():void{
		try{
			I18n::load('common');
			I18n::load('public');
			$page = new PageFacade();
			$page->title(I18n::t('public.welcome.title',['name'=>$_ENV['APP_NAME']]));
			$view = new WebView();
			$view->set('page',$page)
				->render('home.php');
		}catch(\Throwable $t){
			
		}
	}
}