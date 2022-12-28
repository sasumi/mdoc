<?php
namespace Lfphp\Mdoc;

/**
 * 递归的glob
 * Does not support flag GLOB_BRACE
 * @param string $pattern
 * @param int $flags
 * @return array
 */
function glob_recursive($pattern, $flags = 0){
	$files = glob($pattern, $flags);
	foreach(glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir){
		$files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
	}

	//修正目录分隔符
	array_walk($files, function(&$file){
		$file = str_replace(array('/', '\\'), array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR), $file);
	});
	return $files;
}

function is_relative_link($url){
	return !(strpos($url, 'https://') === 0 || strpos($url, 'http://') === 0 || strpos($url, '//') === 0);
}

/**
 * 获取HTML摘要信息
 * @param string $html_content
 * @param int $len
 * @return string
 */
function html_abstract($html_content, $len = 200){
	$str = str_replace(array("\n", "\r"), "", $html_content);
	$str = preg_replace('/<br([^>]*)>/i', '$$NL', $str);
	$str = strip_tags($str);
	$str = html_entity_decode($str, ENT_QUOTES);
	$str = h($str, $len);
	$str = str_replace('$$NL', '<br/>', $str);

	//移除头尾空白行
	$str = preg_replace('/^(<br[^>]*>)*/i', '', $str);
	return preg_replace('/(<br[^>]*>)*$/i', '', $str);
}

/**
 * @return array [path, phar_file_name]
 */
function get_current_phar_file_name(){
	$phar_protocol = 'phar://';
	if(strpos(__FILE__, $phar_protocol) === false){
		return [];
	}
	$f = substr(__FILE__, strlen($phar_protocol));
	$f = str_replace('\\', '/', $f);
	if(preg_match("/(.*?)\/(.*\.phar)\//i", $f, $matches)){
		return [$matches[1], $matches[2]];
	}
	return [];
}

/**
 * 输出html变量
 * @param array|string $str
 * @param number|null $len 截断长度，为空表示不截断
 * @param null|string $tail 追加尾串字符
 * @param bool $over_length 超长长度
 * @return string|array
 */
function h($str, $len = null, $tail = '...', &$over_length = false){
	return __h($str, $len, $tail, $over_length, ENT_IGNORE);
}

function ha($str, $len = null, $tail = '...', &$over_length = false){
	return __h($str, $len, $tail, $over_length, ENT_QUOTES);
}

/**
 * @param string $str
 * @param null $len
 * @param string $tail
 * @param bool $over_length
 * @param null $type
 * @return array|string
 */
function __h($str, $len = null, $tail = '...', &$over_length = false, $type = null){
	if(is_object($str)){
		return $str;
	}
	if(is_array($str)){
		$ret = array();
		foreach($str as $k => $s){
			$ret[$k] = h($s);
		}
		return $ret;
	}
	if($len){
		$str = substr_utf8($str, $len, $tail, $over_length);
	}
	if(is_numeric($str)){
		return $str;
	}
	return htmlspecialchars($str, $type);
}

/**
 * utf-8中英文截断（两个英文一个数量单位）
 * @param string $string 串
 * @param int $length 切割长度
 * @param string $tail 尾部追加字符串
 * @param bool $over_length 是否超长
 * @return string
 */
function substr_utf8($string, $length, $tail = '...', &$over_length = false){
	$chars = $string;
	$i = 0;
	$n = 0;
	$m = 0;
	do{
		if(isset($chars[$i]) && preg_match("/[0-9a-zA-Z]/", $chars[$i])){
			$m++;
		}else{
			$n++;
		}
		//非英文字节,
		$k = $n/3 + $m/2;
		$l = $n/3 + $m;
		$i++;
	} while($k < $length);
	$str1 = mb_substr($string, 0, $l, 'utf-8');
	if($str1 != $string){
		$over_length = true;
		if($tail){
			$str1 .= $tail;
		}
	}
	return $str1;
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
	curl_close($ch);
	if($err){
		throw new \Exception('Update Fail:'.$err_msg."($err)");
	}
	return $content;
}
