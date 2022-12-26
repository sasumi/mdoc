<?php
namespace Lfphp\Mdoc;

function get_config(){
	static $config;
	if(!$config){
		$tmp = parse_ini_file(MDOC_CONFIG);
		$config = [
			'title'       => $tmp['title'],
			'description' => $tmp['description'],
			'author'      => $tmp['author'],
			'contact'     => $tmp['contact'],
			'copyrights'  => $tmp['copyrights'],
		];
	}
	return $config;
}
