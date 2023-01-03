<?php
namespace Lfphp\Mdoc;
const SORT_CREATED_ASC = 'created_asc';
const SORT_CREATED_DESC = 'created_desc';
const SORT_MODIFIED_ASC = 'modified_asc';
const SORT_MODIFIED_DESC = 'modified_desc';
const SORT_NAME_ASC = 'name_asc';
const SORT_NAME_DESC = 'name_desc';

function get_articles($folder = null, $page_start = 1, $page_size = 10, $sort_by = null){
	$pattern = file_path($folder)."/*.md";
	$files = glob_recursive($pattern);

	switch($sort_by){
		case SORT_CREATED_ASC:
			usort($files, function($a, $b){
				return filectime($a) > filectime($b);
			});
			break;
		case SORT_CREATED_DESC:
			usort($files, function($a, $b){
				return filectime($a) < filectime($b);
			});
			break;
		case SORT_MODIFIED_ASC:
			usort($files, function($a, $b){
				return filemtime($a) > filemtime($b);
			});
			break;
		case SORT_MODIFIED_DESC:
			usort($files, function($a, $b){
				return filemtime($a) < filemtime($b);
			});
			break;
		case SORT_NAME_ASC:
			usort($files, function($a, $b){
				return basename($a) > basename($b);
			});
			break;
		case SORT_NAME_DESC:
			usort($files, function($a, $b){
				return basename($a) < basename($b);
			});
			break;
	}

	$files = array_slice($files, ($page_start - 1)*$page_size, $page_size);
	$articles = [];
	$total = get_count($folder);
	foreach($files as $file){
		$f = realpath($file);
		$id = get_folder_id($f);
		$base_info = get_article_base_info($id);
		$articles[] = [
			'id'            => $base_info['id'],
			'title'         => $base_info['title'],
			'abstract'      => $base_info['abstract'],
			'file'          => $f,
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
		$id = get_folder_id($item);
		$c = [
			'id'    => $id,
			'title' => basename($item),
			'count' => get_count($id),
		];
		if(is_dir($item)){
			$c['children'] = get_folders_recursive(get_folder_id($item));
		}
		$folders[] = $c;
	}
	return $folders;
}

function get_count($folder = ''){
	$p = file_path($folder).'/*.md';
	$tmp = glob_recursive($p);
	return count($tmp);
}

function get_folder_id($full_folder_path){
	$str = str_replace(realpath(file_path()), '', realpath($full_folder_path));
	$str = str_replace("\\", "/", $str);
	return trim($str, "/");
}

/**
 * 修正相对路径
 * @param $target_id
 * @param $ref_id
 * @return string
 */
function fix_path($target_id, $ref_id){
	$current_full_path = file_path(get_path($ref_id));
	$target_path = get_path($target_id);
	$new_full_path = $current_full_path.($target_path ? "/$target_path" : '');
	$new_path = str_replace(realpath(file_path()), '', realpath($new_full_path));
	$new_path = trim(str_replace('\\', '/', $new_path), '/');
	$id = basename($target_id);
	return $new_path ? "$new_path/$id" : $id;
}

function get_article_detail($id, $with_detail = true){
	$file = file_path($id);
	$raw = file_get_contents($file);
	$pd = new Parsedown();
	$stu = $pd->parse($raw);
	$title = "";
	$ctn = preg_replace_callback('/<h1>(.*)<\/h1>/im', function($matches) use (&$title){
		$title = trim($matches[1]);
		return '';
	}, $stu);

	//fix relative link
	$ctn = preg_replace_callback('/(<[^>]+\shref=")([^"]+)(")/im', function($ms) use ($id){
		if(!is_relative_link($ms[2])){
			return $ms[0];
		}
		$target_id = fix_path($ms[2], $id);
		return $ms[1].'?'.http_build_query(['id' => $target_id]).$ms[3];
	}, $ctn);

	//fix relative src visit
	$ctn = preg_replace_callback('/(<[^>]+\ssrc=")([^"]+)(")/im', function($ms) use ($id){
		if(!is_relative_link($ms[2])){
			return $ms[0];
		}
		$target_id = fix_path($ms[2], $id);
		return $ms[1].'?'.http_build_query(['src' => $target_id]).$ms[3];
	}, $ctn);

	return [
		'id'            => $id,
		'title'         => $title,
		'abstract'      => html_abstract($ctn),
		'file'          => $file,
		'content'       => $with_detail ? $ctn : '',
		'category_path' => get_path($id),
	];
}

function get_article_base_info($id){
	return get_article_detail($id, false);
}

