<?php

use function Lfphp\Mdoc\get_article_detail;
use function Lfphp\Mdoc\include_template;

include_template("header.inc.php");
$current_article = $_GET['id'] ? get_article_detail($_GET['id']) : null;
$current_path = $_GET['path'] ?: null;
if($current_article){
	include_template("info.inc.php", compact('current_article', 'current_path'));
}else{
	include_template("list.inc.php", compact('current_path'));
}
include_template("footer.inc.php");

