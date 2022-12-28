<?php
namespace Lfphp\Mdoc;
include_once __DIR__.'/article.php';
include_once __DIR__.'/blog.php';
include_once __DIR__.'/func.php';
include_once __DIR__.'/ParseDown.php';
include_once __DIR__.'/upgrade.php';

function setup_mdoc($blog_config = []){
	blog_config($blog_config);

	if($_GET['src']){
		echo file_get_contents(file_path($_GET['src']));
		exit;
	}

	if(isset($_GET['upgrade'])){
		$new_version_info = check_new_version();
		if($new_version_info){
			download($new_version_info['version']);
		}
	}

	if($blog_config['use_default_template']){
		include __DIR__.'/template/index.php';
	}
}
