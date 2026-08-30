<?php declare(strict_types=1);
namespace Frrame\Component;
/**
 * I18n Internationalization Component
 */
class I18n{
    /**
     * @var array<string, array<string, string>>
     */
    private static array $translations = [];
    private static string $language;
    private static string $interpolationStart = '{';
    private static string $interpolationEnd = '}';
    public static function setLanguage(string $language):void{
        self::$language = $language;
        self::$translations = [];
    }
    public static function setInterpolation(string $start, string $end):void{
        self::$interpolationStart = $start;
        self::$interpolationEnd = $end;
    }
    public static function load(string $namespace, string $language = ''):void{
        if(!empty($language)){
            self::$language = $language;
        }else{
            self::$language = $_ENV['APP_LOCALE'];
        }
        $filePath = RESOURCE_PATH.'/i18n/'.$namespace.'/'.self::$language.'.php';
        if(file_exists($filePath)){
            $translations[$namespace] = include $filePath;
            self::$translations = array_merge(self::$translations, $translations);
        }
    }
    /**
     * @param array<string, string> $context
     */
    public static function t(string $key, array $context = []):string{
        $parts = explode('.', $key);
        $value = self::$translations;
        foreach($parts as $part){
            if(is_array($value) && isset($value[$part])){
                $value = $value[$part];
            }else{
                return $key;
            }
        }
        if(!is_string($value)){
            return $key;
        }
        if($context === []){
            return $value;
        }
        foreach($context as $contextKey => $contextValue){
            $placeholder = self::$interpolationStart.$contextKey.self::$interpolationEnd;
            $value = str_replace($placeholder, (string)$contextValue, $value);
        }
        return $value;
    }
}
