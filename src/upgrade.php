<?php

use function Lfphp\Mdoc\get_current_phar_file_name;

const VER_REPO = 'http://localhost/mdoc/release/newest.json';
const RELEASE_PATH = 'http://localhost/mdoc/release/';
function release_file_name($version){
	//	return "mdoc.$version.phar";
	return "mdoc.phar";
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

function curl_get($url){
	$user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
	$opt = [
		CURLOPT_CUSTOMREQUEST  => "GET",        //set request type post or get
		CURLOPT_POST           => false,        //set to GET
		CURLOPT_USERAGENT      => $user_agent, //set user agent
		CURLOPT_RETURNTRANSFER => true,     // return web page
		CURLOPT_HEADER         => false,    // don't return headers
		CURLOPT_FOLLOWLOCATION => true,     // follow redirects
		CURLOPT_ENCODING       => "",       // handle all encodings
		CURLOPT_AUTOREFERER    => true,     // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
		CURLOPT_TIMEOUT        => 30,      // timeout on response
		CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	];
	$ch = curl_init($url);
	curl_setopt_array($ch, $opt);
	$content = curl_exec($ch);
	$err = curl_errno($ch);
	$err_msg = curl_error($ch);
	$header = curl_getinfo($ch);
	curl_close($ch);

	if($err){
		throw new Exception('Update Fail:'.$err_msg."($err)");
	}
	return $content;
}
