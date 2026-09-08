<?php declare(strict_types=1);
use Frrame\View\Extension\ViteHandler;
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?=ViteHandler::index($this->page->index()).ViteHandler::getEntry()?>
    <title><?=$this->page->title() ?? $_ENV['APP_NAME']?></title>
</head>
