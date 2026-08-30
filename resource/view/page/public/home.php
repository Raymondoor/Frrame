<?php declare(strict_types=1);
use Frrame\Component\I18n;
?>
<!DOCTYPE html>
<html lang="en">
<?php $this->render('/component/public-head.php');?>
<body>
<?php $this->render('/component/public-header.php');?>
<main class="screen">
    <div id="welcomeCard">
        <h1><?=$this->page->title()?></h1>
        <hr>
        <p><?=I18n::t('public.welcome.desc')?></p>
    </div>
</main>
</body>
</html>