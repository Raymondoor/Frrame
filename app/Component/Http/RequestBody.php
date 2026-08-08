<?php declare(strict_types=1);
namespace Frrame\Component\Http;
/**
 * Loads the request body. File uploads ($_FILES) are only ever populated by PHP
 * itself, which only does so for POST - that's a PHP/SAPI limitation, not
 * something this class works around. JSON and urlencoded bodies, which don't
 * need PHP's own parsing, are read from php://input directly and so work for
 * any method (PUT/PATCH/DELETE included).
 */
class RequestBody{
    /** @var array<string,mixed> */
    public static array $form = [];
    /** @var array<string,mixed> */
    public static array $json = [];
    /** @var array<string,mixed> Merged view of $form and $json, read by get(). */
    public static array $raw = [];
    /** @var array<string,array{name:string,type:string,tmp_name:string,error:int,size:int}> Same shape as $_FILES; only ever populated on POST. */
    public static array $files = [];
    public static function load():void{
        $contentType = RequestHeader::get('Content-Type') ?? '';
        $contentTypeLower = strtolower($contentType);
        if(RequestMethod::post()){
            // Let PHP do what it already does natively for POST - form fields and files alike.
            self::$form = $_POST;
            self::$files = $_FILES;
        }
        if(str_contains($contentTypeLower,'application/json')){
            $data = json_decode(file_get_contents('php://input'), true);
            if(is_array($data)){
                self::$json = $data;
            }
        }elseif(str_contains($contentTypeLower,'application/x-www-form-urlencoded') && !RequestMethod::post()){
            // On POST, $_POST already covers this; only needed for other methods.
            parse_str(file_get_contents('php://input'), $data);
            self::$form = $data;
        }
        self::$raw = array_merge(self::$form, self::$json);
    }
    public static function get(string $key,mixed $default = null):mixed{
        return self::$raw[$key] ?? $default;
    }
}
