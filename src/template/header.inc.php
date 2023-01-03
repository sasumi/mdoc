<?php
namespace Lfphp\Mdoc;

$blog_config = get_blog_info();
$current_article = $_GET['id'] ? get_article_detail($_GET['id']) : null;
$current_path = $_GET['path'] ?: null;
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title><?=$blog_config['title'];?></title>
	<style>
		<?php include (__DIR__.'/common.css');?>
		<?php include (__DIR__.'/style.css');?>
		<?php include (__DIR__.'/article.css');?>
	</style>
</head>
<body>
<header>
	<div class="content-wrap clearfix">
		<?php if($blog_config['title']):?>
			<div class="logo"><a href="?"><?=$blog_config['title'];?></a></div>
		<?php endif;?>
		<ul class="main-nav">
			<li>
				<a href="?">Home</a>
			</li>
			<li>
				<a href="?">Blogs</a>
			</li>
		</ul>
	</div>
</header>
<section class="content content-wrap">
	<aside>
		<dl class="aside-category-mod">
			<dt>分类</dt>
			<dd>
				<ul>
					<li <?=!$current_path ? 'class="active"' : '';?>>
						<a href="?">所有分类</a>
						<span class="cnt"><?=get_count();?></span>
					</li>
					<?php
					$categories = get_folders_recursive();
					function show_cats($cats, $current_path){
						$html = '<ul>';
						foreach($cats as $cat){
							$html .= '<li '.($current_path == $cat['id'] ? 'class="active"' : '').'>
							<a href="?path='.ha($cat['id']).'">'.$cat['title'].'</a>'.'<span class="cnt">'.$cat['count'].'</span>'.'</li>';
						}
						$html .= '</ul>';
						return $html;
					}
					foreach($categories as $cat):?>
						<li <?=$current_path == $cat['id'] ? 'class="active"' : '';?>>
							<a href="?<?=http_build_query(['path' => $cat['id']]);?>"><?=h($cat['title']);?></a>
							<span class="cnt"><?=$cat['count'] ?: 0;?></span>
							<?php if($cat['children']){
								echo show_cats($cat['children'], $current_path);
							}
							?>
						</li>
					<?php endforeach; ?>
				</ul>
			</dd>
		</dl>
	</aside>
