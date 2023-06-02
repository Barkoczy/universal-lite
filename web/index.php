<?php
/**
* Copyright 2023
* 
* @package    Universal Lite
* @version		0.0.1
*	@access			public
* @author     Henrich Barkoczy <me@barkoczy.social>
* @see 	      https://barkoczy.social
* @see				https://github.com/Barkoczy/universal-lite
* @license    https://universal.barkoczy.social/license
*/
declare(strict_types=1);

// @Autoload
require __DIR__ . '/../vendor/autoload.php';

// @Session
session_start();

// @Run
App\Kernel\Bootstrap::boot()->run();