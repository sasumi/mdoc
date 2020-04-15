<?php
namespace MDoc;
define('MDOC_VERSION_REQUIRED', '7.0');
define('MDOC_DOCUMENT_ROOT', __DIR__.'/document');
define('MDOC_DOCUMENT_CONFIG', MDOC_DOCUMENT_ROOT.'/.project');
define('MDOC_ENTRANCE', basename(__FILE__));
define('TYPE_DIR', 'dir');
define('TYPE_FILE', 'file');

define('SORT_DIR_FIRST', 1);
define('SORT_DIR_LAST', 2);
define('SORT_NAME_ASC', 4);
define('SORT_NAME_DESC', 8);

//php version limiting
if(version_compare(PHP_VERSION, MDOC_VERSION_REQUIRED) <= 0){
	throw new \Exception("Required PHP ".MDOC_VERSION_REQUIRED." or above", 1);
}
$project_last_update = time();
$project = parse_ini_file(MDOC_DOCUMENT_CONFIG);

$files = glob_recursive(MDOC_DOCUMENT_ROOT, SORT_DIR_FIRST|SORT_NAME_ASC);
$current_file = $_GET['f'] ? grep_file($files, $_GET['f']) : first_file($files);
$article = read_article($current_file['path']);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="assert/style.css">
	<title><?=h($article['title']);?></title>
</head>
<body>
	<section class="page">
		<aside>
			<div class="project-title"><a href="<?=MDOC_ENTRANCE;?>"><?=h($project['title']);?></a></div>
			<form action="" class="project-search">
				<input type="search" name="keyword" id="" placeholder="Search Docs" value="<?=ha($_GET['keyword']);?>">
				<button class="search-btn">Search</button>
			</form>
			<dl class="aside-mod">
				<dt>DOCUMENTS</dt>
				<dd class="file-list">
					<?=build_file_nav($files, $current_file);?>
				</dd>
			</dl>
		</aside>

		<section class="container">
			<ul class="breadcrumbs">
				<li><a href="<?=MDOC_ENTRANCE;?>">Home</a></li>
				<li><?=h($article['title']);?></li>
			</ul>
			<article>
				<h1><?=h($article['title']);?></h1>
				<ul class="meta">
					<li><label>创建</label><span><?=date('Y年m月d日 H:i', $article['create_time']);?></span></li>
					<li>
						<label>最后修改</label>
						<span>
							<?=($article['update_time'] && $article['update_time'] != $article['create_time']) ? date('Y年m月d日 H:i', $article['update_time']) : '-'?>
						</span>
					</li>
				</ul>
				<div class="content">
					<?=h_md_raw($article['raw']);?>
				</div>
			</article>
			<footer>
				<?php if($project['copyrights']):?>
                <div class="copyrights"><?=$project['copyrights'];?></div>
				<?php endif;?>
				<?php if($project_last_update):?>
				<div class="project-last-update">Last Update: <?=date('Y-m-d H:i:s', $project_last_update);?></div>
				<?php endif;?>
			</footer>

		</section>
	</section>
	<script>
		// setTimeout("location.reload()", 5000);
	</script>
</body>
</html>
<?php

function h_md_raw($text){
	$html = htmlspecialchars($text);
	$html = str_replace("\r", '', $html);
	$html = str_replace(array(' ', "\n", "\t"), array('&nbsp;&nbsp;', '<br/>', '&nbsp;&nbsp;&nbsp;&nbsp;'), $html);
	return $html;
}
function read_article($file){
	$raw = file_get_contents($file);
	if(preg_match('/^\s*#([^#]+)/', $raw, $matches)){
		$title = trim($matches[1]);
	}else{
		$title = basename($file, '.md');
	}
	return [
		'title'       => $title,
		'create_time' => filectime($file),
		'modify_time' => filemtime($file),
		'raw'         => $raw,
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

function build_file_nav($files, $current_file){
	$html = '<ul>';
	foreach($files as $f){
		$is_dir = $f['type'] === TYPE_DIR;
		$class = $f['id'] == $current_file['id'] ? 'active' : '';
		$html .= "<li data-type=\"{$f['type']}\" class=\"$class\">";
		$html .= $is_dir ? '<span class="toggle"></span>' : '';
		$html .= $is_dir ? "<span class=\"title\">".h($f['name'])."</span>" :'<a href="?f='.ha($f['id']).'" class="title">'.h($f['name']).'</a>';
		if($is_dir && $f['children']){
			$html .= build_file_nav($f['children'], $current_file);
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
		$id = ltrim(str_replace('\\', '/', str_replace(realpath($root_path), '', $file)), '\/');
		$info = [
			'id'   => $id,
			'name' => basename($file),
			'path' => $file,
			'type' => is_dir($file) ? TYPE_DIR : TYPE_FILE,
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


function h($str){
	return htmlspecialchars($str);
}

function ha($str){
	return htmlspecialchars($str, ENT_QUOTES);
}

function dump(){
	$params = func_get_args();
	$cli = PHP_SAPI === 'cli';
	$exit = false;
	echo !$cli ? PHP_EOL.'<pre style="color:green;">'.PHP_EOL : PHP_EOL;

	if(count($params)){
		$tmp = $params;
		$exit = array_pop($tmp) === 1;
		$params = $exit ? array_slice($params, 0, -1) : $params;
		$comma = '';
		foreach($params as $var){
			echo $comma;
			var_dump($var);
			$comma = str_repeat('-',80).PHP_EOL;
		}
	}

	//remove closure calling & print out location.
	$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
	print_trace([$trace[0]]);
	echo str_repeat('=', 80), PHP_EOL, (!$cli ? '</pre>' : '');
	$exit && exit();
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

