<?php declare(strict_types=1);
namespace Frrame\View;
class WebView{
    /** @var array<string,mixed> Readable in an included view as $this->{$key} via __get(). */
    public mixed $data = [];
    public function __construct(){}
    /** @param string $key @param mixed $value */
    public function set($key, $value):self{
        $this->data[$key] = $value;
        return $this;
    }
    /**
     * Sets $key if unset, otherwise appends to it (expects $data[$key] to already be an array).
     * @param string $key @param mixed $value
     */
    public function append($key, $value):self{
        if(!isset($this->data[$key])){
            $this->data[$key] = $value;
            return $this;
        }
        array_push($this->data[$key],$value);
        return $this;
    }
    /** @param string $key */
    public function __get($key):mixed{
        return $this->data[$key] ?? null;
    }
    /** @param string $key */
    public function __isset($key):bool{
        return isset($this->data[$key]);
    }
    /** @param string $file Path relative to resource/view/. */
    public function render($file):void{
        include(realpath(RESOURCE_PATH.'/view/'.$file));
    }
    /** @param string $file Path relative to resource/view/. */
    public function render_once($file):void{
        include_once(realpath(RESOURCE_PATH.'/view/'.$file));
    }
}