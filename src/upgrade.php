<?php
namespace Lfphp\Mdoc;

const VER_REPO = 'http://localhost/mdoc/release/newest.json';
const RELEASE_PATH = 'http://localhost/mdoc/release/';
function release_file_name($version){
	return "mdoc.$version.phar";
	//	return "mdoc.phar";
}

function check_new_version($check_interval = 1){
	$remote_version_info = null;
	$cache_file = sys_get_temp_dir().'/mdoc_version.cache';
	if($check_interval && is_file($cache_file)){
		$last_result = json_decode(file_get_contents($cache_file), true);
		if(strtotime($last_result['check_time']) > (time() - $check_interval)){
			$remote_version_info = $last_result['version_info'];
		}
	}

	if(!$remote_version_info){
		$remote_version_info = get_remote_version();
		file_put_contents($cache_file, json_encode([
			'check_time'   => date('Y-m-d H:i:s'),
			'version_info' => $remote_version_info,
		]));
	}
	if(version_compare(this_version(), $remote_version_info['version'], '<')){
		return $remote_version_info;
	}
	return null;
}

function download($version){
	$url = RELEASE_PATH."/".release_file_name($version);
	$content = curl_get($url);
	$tmp = tempnam(sys_get_temp_dir(), 'mdoc');
	file_put_contents($tmp, $content);
	list($target_path, $phar_name) = get_current_phar_file_name();
	rename($tmp, $target_path.DIRECTORY_SEPARATOR.$phar_name);
}

function this_version(){
	$ver_info = json_decode(file_get_contents(__DIR__.'/version.json'), true);
	return $ver_info['version'];
}

function get_remote_version(){
	$content = curl_get(VER_REPO);
	$ver = json_decode($content, true);
	if(!$ver || json_last_error()){
		throw new Exception('Update response decode fail:'.$content);
	}
	return $ver;
}
