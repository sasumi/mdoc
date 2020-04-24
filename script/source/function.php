<?php
namespace mdoc;

use function LFPhp\Func\dump;
use function LFPhp\Func\h;
use function LFPhp\Func\ha;

define('TYPE_DIR', 'dir');
define('TYPE_FILE', 'file');

define('SORT_DIR_FIRST', 1);
define('SORT_DIR_LAST', 2);
define('SORT_NAME_ASC', 4);
define('SORT_NAME_DESC', 8);

function get_articles_info($files, $root){
	$summary_list = [];
	foreach($files as $file){
		$item = [];
		if($file['type'] == TYPE_FILE){
			$item = read_article($file['file']);
		} else {
			$item['title'] = $file['name'];
		}
		if($file['children']){
			$item['children'] = get_articles_info($file['children'], $root);
		}
		$summary_list[] = array_merge($file, $item);
	}
	return $summary_list;
}

function get_style_sheet(){
	static $css;
	if(!isset($css)){
		$css = file_get_contents(MDOC_ASSERT_ROOT.'/style.css');
	}
	return $css;
}

function h_md_raw($text){
	$html = htmlspecialchars($text);
	$html = str_replace("\r", '', $html);
	$html = str_replace(array(' ', "\n", "\t"), array('&nbsp;&nbsp;', '<br/>', '&nbsp;&nbsp;&nbsp;&nbsp;'), $html);
	return $html;
}

function read_article($file){
	$raw = file_get_contents($file);
	$pd = MyParserDown::instance();
	$title = $pd->title($raw);
	$title = trim($title) ?: basename($file, '.md');
	return [
		'title'       => $title,
//		'raw'         => $raw,
//		'html'        => $pd->text($raw),
	];
}

function grep_file($files, $file_path){
	foreach($files as $f){
		if($f['type'] === TYPE_FILE && strcasecmp($f['id'], $file_path) === 0){
			return $f;
		}else if($f['type'] === TYPE_DIR && $f['children']){
			$sf = grep_file($f['children'], $file_path);
			if($sf){
				return $sf;
			}
		}
	}
	return null;
}

function first_file($files){
	foreach($files as $f){
		if($f['type'] === TYPE_FILE){
			return $f;
		}else{
			return first_file($f['children']);
		}
	}
	throw new \Exception('No file found');
}

function build_file_nav($article_list, $current_article){
	$html = '<ul>';
	foreach($article_list as $article){
		$is_dir = $article['type'] === TYPE_DIR;
		$class = $article['id'] == $current_article['id'] ? 'active' : '';
		$html .= "<li data-type=\"{$article['type']}\" class=\"$class\">";
		$html .= $is_dir ? '<span class="toggle"></span>' : '';
		$html .= $is_dir ? "<span class=\"title\">".h($article['title'])."</span>" :'<a href="?f='.ha($article['id']).'" class="title">'.h($article['title']).'</a>';
		if($is_dir && $article['children']){
			$html .= build_file_nav($article['children'], $current_article);
		}
		$html .= '</li>';
	}
	$html .= '</ul>';
	return $html;
}

function sort_files($files, $sorting){
	$sort_dir = $sorting&SORT_DIR_FIRST || $sorting&SORT_DIR_LAST;
	$sort_name = $sorting&SORT_NAME_ASC || $sorting&SORT_NAME_DESC;
	if($sort_dir){
		usort($files, function($item1, $item2) use ($sorting){
			$d1 = $item1['type'] == TYPE_DIR;
			$d2 = $item2['type'] == TYPE_DIR;
			if(!($d1 xor $d2)){
				return 0;
			}
			if($sorting&SORT_DIR_LAST){
				return $d1 ? 1 : -1;
			}else{
				return $d1 ? -1 : 1;
			}
		});
	}
	if($sort_name){
		usort($files, function($item1, $item2) use ($sorting, $sort_dir){
			if($sort_dir && ($item1['type'] == TYPE_DIR xor $item2['type'] == TYPE_DIR)){
				return 0;
			}
			return strcasecmp($item1['name'], $item2['name']) < 0 ?
				($sorting && SORT_NAME_ASC ? -1 : 1) :
				($sorting && SORT_NAME_DESC ? 1 : -1);
		});
	}
	return $files;
}

function glob_recursive($path, $sorting, $root_path = '', $flags = 0){
	$ret = [];
	if(!$root_path){
		$root_path = $path;
	}
	$files = glob($path.'/*', $flags);
	foreach($files as $file){
		$file = realpath($file);
		$path = trim(str_replace(realpath($root_path), '', dirname(realpath($file))), DIRECTORY_SEPARATOR);
		$info = [
			'name'        => basename($file),
			'path'        => $path,
			'file'        => $file,
			'type'        => is_dir($file) ? TYPE_DIR : TYPE_FILE,
			'create_time' => filectime($file),
			'modify_time' => filemtime($file),
		];
		if(is_dir($file)){
			$info['children'] = glob_recursive($file, $sorting, $root_path, $flags);
		}
		$ret[] = $info;
	}
	if($sorting){
		$ret = sort_files($ret, $sorting);
	}
	return $ret;
}

/**
 * 打印trace信息
 * @param $trace
 * @param bool $with_callee
 * @param bool $with_index
 */
function print_trace($trace, $with_callee = false, $with_index = false){
	$ct = count($trace);
	foreach($trace as $k=>$item){
		$callee = '';
		if($with_callee){
			$callee = $item['class'] ? "\t{$item['class']}{$item['type']}{$item['function']}()" : "\t{$item['function']}()";
		}
		if($with_index){
			echo "[", ($ct - $k), "] ";
		}
		$loc = $item['file'] ? "{$item['file']} #{$item['line']} " : '';
		echo "{$loc}{$callee}", PHP_EOL;
	}
}

