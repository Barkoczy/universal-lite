<?php
declare(strict_types=1);

// @Autoload
require __DIR__ . '/../vendor/autoload.php';

// @Session
session_start();

// @Run
App\Kernel\Bootstrap::boot()->run();