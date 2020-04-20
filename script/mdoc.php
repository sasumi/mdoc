<?php
namespace MDoc;

//php version limiting
use LFPhp\Logger\Logger;
use LFPhp\Logger\Output\ConsoleOutput;
use function LFPhp\Func\dump;

if(version_compare(PHP_VERSION,'7.0') <= 0){
	throw new \Exception("Required PHP 7.0 or above", 1);
}

require_once __DIR__.'/source/autoload.php';

define('MDOC_DEFAULT_DOCUMENT_ROOT', dirname(__DIR__).'/document');
define('MDOC_DEFAULT_RELEASE_ROOT', dirname(__DIR__).'/release');
define('MDOC_ROOT', __DIR__);
define('MDOC_ASSERT_ROOT', MDOC_ROOT.'/assert');

$opts = getopt('hbp:t:');
if(!isset($opts['b']) || isset($opts['h'])){
	$def_doc_root = MDOC_DEFAULT_DOCUMENT_ROOT;
	$def_rel_root = MDOC_DEFAULT_RELEASE_ROOT;
	echo <<<EOT
-----------------------------------------
Markdown Publisher
build markdown files to html. 
More help information about this software, 
please visit http://github.com/sasumi/mdoc

-h show this help.
-b build release html file.
-d daemon and watch markdown source directory, auto rebuild while file changed.
-p specify markdown source directory, use {$def_doc_root} as default.
-t specify release target directory, use {$def_rel_root} as default.
-----------------------------------------
EOT;
	exit;
}

define('MDOC_DOCUMENT_ROOT', $opts['p'] ?: MDOC_DEFAULT_DOCUMENT_ROOT);
define('MDOC_RELEASE_ROOT', $opts['t'] ?: MDOC_DEFAULT_RELEASE_ROOT);
define('MDOC_DOCUMENT_CONFIG', MDOC_DOCUMENT_ROOT.'/.preface');

$logger = Logger::instance(__FILE__);
$logger->register(new ConsoleOutput());

$logger->info('MDoc Start');

$files = glob_recursive(MDOC_DOCUMENT_ROOT, SORT_DIR_FIRST|SORT_NAME_ASC, MDOC_DOCUMENT_ROOT);
$logger->info('Total files grep.', count($files));
$article_list = get_articles_info($files, MDOC_DOCUMENT_ROOT);
$logger->info('list', $article_list);

$logger->info('building sitemap');
ob_start();
include MDOC_ASSERT_ROOT.'/sitemap.xml.php';
$site_map = ob_get_contents();
ob_clean();
file_put_contents(MDOC_RELEASE_ROOT.'/sitemap.xml', $site_map);

$logger->info('resolving articles');

$project_last_update = time();
$project = parse_ini_file(MDOC_DOCUMENT_CONFIG);

foreach($article_list as $article){
	$logger->info('Building article', $article['title']);
	$save_path = MDOC_RELEASE_ROOT.DIRECTORY_SEPARATOR.$article['path'];
	if(!is_dir($save_path)){
		mkdir($save_path, 0644, true);
	}
	ob_start();
	include MDOC_ASSERT_ROOT.'/article.html.php';
	$html = ob_get_contents();
	ob_clean();
	$target_file = $save_path.'/'.basename($article['file'], '.md').'.html';
	$logger->info('save file', $target_file);
	file_put_contents($target_file, $html);
}