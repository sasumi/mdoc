<?php
use function Lfphp\Mdoc\setup_mdoc;
include_once __DIR__.'/../release/mdoc.phar';
setup_mdoc([
	'root' => __DIR__.'/document',
]);
