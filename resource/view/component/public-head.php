<?php declare(strict_types=1);
use Frrame\Facade\AssetFacade;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?=AssetFacade::index($this->page->index()).AssetFacade::viteEntry()?>
    <title><?=$this->page->title() ?? $_ENV['APP_NAME']?></title>
</head>
