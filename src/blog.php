<?php
namespace Lfphp\Mdoc;

function blog_config(&$set_config = null){
	static $blog_config = [
		'root'                 => '',
		'config_file'          => 'config.ini',
		'use_default_template' => true,
	];
	if($set_config){
		foreach($set_config as $k => $v){
			$blog_config[$k] = $v;
		}
		$set_config = $blog_config;
	}
	return $blog_config;
}

function get_blog_config($key){
	return blog_config()[$key];
}

function file_path($rel_file = ''){
	return $rel_file ? get_blog_config('root').'/'.$rel_file : get_blog_config('root');
}

function get_blog_info(){
	static $config;
	if(!$config){
		$config_file = file_path(get_blog_config('config_file'));
		$tmp = parse_ini_file($config_file);
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

function show_pagination($current_page, $total_page, $path = ''){
	$html = '<div class="pagination">';
	$html .= $current_page > 1 ? '<a class="prev" href="?'.http_build_query([
			'path' => $path,
			'page' => ($current_page - 1),
		]).'">上一页</a>' : '<span class="prev">上一页</span>';

	$html .= '<span class="info">'.$current_page.'/'.$total_page.'</span>';

	$html .= $current_page < $total_page ? '<a class="next" href="?'.http_build_query([
			'path' => $path,
			'page' => ($current_page + 1),
		]).'">下一页</a>' : '<span class="next">下一页</span>';
	$html .= '</div>';
	return $html;
}
