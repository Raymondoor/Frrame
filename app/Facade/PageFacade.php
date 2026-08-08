<?php declare(strict_types=1);
namespace Frrame\Facade;
final class PageFacade{
    /** @var array<string,mixed> */
    public $page = [
        'TITLE' => 'Title',
        'INDEX' => 'index',
        'ALIAS' => 'Index'
    ];
    /** @param array<string,mixed> $page */
    public function __construct(array $page = []){
        if(!empty($page)) $this->set($page);
    }
    /**
     * @param array<string,mixed> $page
     * @return array<string,mixed>
     */
    public function set(array $page):mixed{
        $this->page = array_merge($this->page, $page);
        return $this->page;
    }
    public function get(string $key):mixed{
        return $this->page[$key];
    }
    public function indexIs(string $index=''):bool{
        return $this->get('INDEX') === $index;
    }
    public function indexAre(string $index=''):bool{
        return strpos($this->get('INDEX'), $index) === 0;
    }
    public function title(string $title = ''):string{
        if(!empty($title)){
            $this->set(['TITLE' => $title]);
        }
        return $this->get('TITLE');
    }
}