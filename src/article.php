<?php
namespace Lfphp\Mdoc;

use function LFPhp\Func\glob_recursive;
use function LFPhp\Func\html_abstract;

function get_articles($folder = null, $page_start = 0, $page_size = 10){
	$pattern = file_path($folder)."/*.md";
	$files = glob_recursive($pattern);
	$files = array_slice($files, $page_start, $page_size);
	$articles = [];
	$root = realpath(file_path()).DIRECTORY_SEPARATOR;
	$total = get_count(file_path().($folder ? "/$folder" : ''));
	foreach($files as $file){
		$f = realpath($file);
		$id = str_replace($root, '', $f);
		$base_info = get_article_base_info($id);
		$articles[] = [
			'id'            => $base_info['id'],
			'title'         => $base_info['title'],
			'abstract'      => $base_info['abstract'],
			'category_path' => get_path($id),
		];
	}
	return [$articles, $total];
}

function get_path($doc_id){
	$doc_id = str_replace("\\", '/', $doc_id);
	if(strpos($doc_id, "/") === false){
		return "";
	}
	return preg_replace("/(.*?)\/[^\/]+$/", '$1', $doc_id);
}

function get_folders_recursive($root = ''){
	$tmp = glob(file_path($root).'/*', GLOB_ONLYDIR);
	$folders = [];
	foreach($tmp as $item){
		$c = [
			'id'    => get_folder_id($item),
			'title' => basename($item),
			'count' => get_count($item),
		];
		if(is_dir($item)){
			$c['children'] = get_folders_recursive($item);
		}
		$folders[] = $c;
	}
	return $folders;
}

function get_count($folder = ''){
	$tmp = glob_recursive(file_path($folder).'/*.md');
	return count($tmp);
}

function get_folder_id($full_folder_path){
	$str = str_replace(realpath(file_path()), '', realpath($full_folder_path));
	$str = str_replace("\\", "/", $str);
	return trim($str, "/");
}

function get_article_detail($id, $with_detail = true){
	$file = file_path($id);
	$raw = file_get_contents($file);
	$pd = new Parsedown();
	$stu = $pd->parse($raw);
	preg_match('/<h1>(.*?)<\/h1>/', $stu, $matches);
	$ctn = preg_replace('/<h1>.*<\/h1>/im', '', $stu);
	return [
		'id'            => $id,
		'title'         => trim($matches[1]),
		'abstract'      => html_abstract($ctn),
		'content'       => $with_detail ? $ctn : '',
		'category_path' => get_path($id),
	];
}

function get_article_base_info($id){
	return get_article_detail($id, false);
}
