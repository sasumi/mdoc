<?php
namespace Lfphp\Mdoc;

use function LFPhp\Func\dump;
use function LFPhp\Func\glob_recursive;
use function LFPhp\Func\html_abstract;

function get_articles($folder = null, $page_start = 0, $page_size = 10){
	$pattern = MDOC_ROOT.($folder ? "/$folder" : '')."/*.md";
	$files = glob_recursive($pattern);
	$files = array_slice($files, $page_start, $page_size);
	$articles = [];
	$root = realpath(MDOC_ROOT).DIRECTORY_SEPARATOR;
	foreach($files as $file){
		$f = realpath($file);
		$id = str_replace($root, '', $f);
		$base_info = get_article_base_info($id);
		$articles[] = [
			'id'       => $base_info['id'],
			'title'    => $base_info['title'],
			'abstract' => $base_info['abstract'],
			'category_path' => get_path($id),
		];
	}
	return $articles;
}

function get_path($id){
	$id = str_replace("\\", '/', $id);
	if(strpos("/", $id) === false){
		return "";
	}
	return preg_replace("/\/.*$/", '', $id);
}

function get_folders_recursive($root = MDOC_ROOT){
	$tmp = glob($root.'/*', GLOB_ONLYDIR);
	$folders = [];
	foreach($tmp as $item){
		$c = [
			'title' => basename($item),
			'id'    => get_id($item),
		];
		if(is_dir($item)){
			$c['children'] = get_folders_recursive($item);
		}
		$folders[] = $c;
	}
	return $folders;
}

function get_id($fold){
	return str_replace(realpath(MDOC_ROOT), '', realpath($fold));
}

function get_article_detail($id, $with_detail = true){
	$file = MDOC_ROOT."/$id";
	$raw = file_get_contents($file);
	$pd = new Parsedown();
	$stu = $pd->parse($raw);
	preg_match('/<h1>(.*?)<\/h1>/', $stu, $matches);
	$ctn = preg_replace('/<h1>.*<\/h1>/im', '', $stu);
	return [
		'id'       => $id,
		'title'    => trim($matches[1]),
		'abstract' => html_abstract($ctn),
		'content'  => $with_detail ? $ctn : '',
	];
}

function get_article_base_info($id){
	return get_article_detail($id, false);
}

function h_md_raw($text){
	$html = htmlspecialchars($text);
	$html = str_replace("\r", '', $html);
	$html = str_replace(array(' ', "\n", "\t"), array('&nbsp;&nbsp;', '<br/>', '&nbsp;&nbsp;&nbsp;&nbsp;'), $html);
	return $html;
}
