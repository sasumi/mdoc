<?php
use function Lfphp\Mdoc\setup_mdoc;
include_once __DIR__.'/../src/bootstrap.php';
setup_mdoc([
	'root' => __DIR__.'/document',
]);
