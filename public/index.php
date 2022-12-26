<?php

use function Lfphp\Mdoc\setup_mdoc;

include_once __DIR__.'/../vendor/autoload.php';
setup_mdoc([
	'root' => dirname(__DIR__).'/document',
]);
