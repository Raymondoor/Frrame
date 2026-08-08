<?php declare(strict_types=1);
use Frrame\Component\I18n;
use Frrame\Util\Str;
?>
<header>
    <div class="screen" id="headerContent">
        <span id="headerLogo"><?=Str::upper($_ENV['APP_NAME'])?></span>
        <ul id="headerList">
            <li><a href="docs"><?=I18n::t('public.header.docs')?></a></li>
            <li><a href="https://github.com/Raymondoor/frrame/"><?=I18n::t('public.header.source')?></a></li>
        </ul>
    </div>
</header>
