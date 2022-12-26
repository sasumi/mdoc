<?php
namespace Lfphp\Mdoc;
function include_template($tpl_file, $param = []){
	$file = __DIR__."/template/$tpl_file";
	if($param){
		extract($param);
	}
	include $file;
}
