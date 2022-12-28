<?php
include_once __DIR__.'/../src/bootstrap.php';

$RELEASE_VERSION_FILE = 'newest.json';
$RELEASE_PATH = __DIR__.'/../release';
$SOURCE_DIR = __DIR__.'/../src';
$SOURCE_EXTENSIONS = ['php', 'css', 'js'];

$opt = getopt('v:m:');
$release_content = $opt['m'] ?: '';
$release_version = $opt['v'];
if(!$release_version){
	$ver = json_decode(file_get_contents($RELEASE_PATH.'/'.$RELEASE_VERSION_FILE), true);
	$release_version = $ver['version'] + 1;
}

$new_ver_info = [
	"version"        => $release_version,
	"built_date"     => date('Y-m-d'),
	"update_content" => $release_content,
];

$RELEASE_FILE_NAME = release_file_name($release_version);

echo "Start Packing...", PHP_EOL;
$phar = new Phar($RELEASE_PATH.'/'.$RELEASE_FILE_NAME, FilesystemIterator::CURRENT_AS_FILEINFO|FilesystemIterator::KEY_AS_PATHNAME, $RELEASE_FILE_NAME);
$phar->startBuffering();;
foreach($SOURCE_EXTENSIONS as $ext){
	$phar->buildFromDirectory($SOURCE_DIR, "/.$ext/");
}

$phar->delete("version.json");
$phar->addFromString('version.json', json_encode($new_ver_info));
$phar->setDefaultStub('bootstrap.php', 'bootstrap.php');
$phar->stopBuffering();
$md5_file = md5_file($RELEASE_PATH.'/'.$RELEASE_FILE_NAME);
echo "Finished packing:", realpath($RELEASE_PATH.'/'.$RELEASE_FILE_NAME), PHP_EOL;
echo "Package MD5:$md5_file", PHP_EOL;

$new_ver_info['md5'] = $md5_file;

echo "Generate version info...", PHP_EOL;
file_put_contents($RELEASE_PATH.'/'.$RELEASE_VERSION_FILE, json_encode($new_ver_info));

echo "DONE";
