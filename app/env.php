<?php declare(strict_types=1);
# Load `.env` to global
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();
$dotenv->required(['APP_PROD', 'APP_DEBUG'])->allowedValues(['0', '1']);