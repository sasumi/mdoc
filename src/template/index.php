<?php

use function Lfphp\Mdoc\get_article_detail;
use function Lfphp\Mdoc\get_articles;
use function Lfphp\Mdoc\get_blog_info;
use function Lfphp\Mdoc\get_count;
use function Lfphp\Mdoc\get_folders_recursive;
use function Lfphp\Mdoc\h;
use function Lfphp\Mdoc\ha;
use function Lfphp\Mdoc\show_pagination;

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
		<?=file_get_contents(__DIR__.'/common.css');?>
		<?=file_get_contents(__DIR__.'/style.css');?>
		<?=file_get_contents(__DIR__.'/article.css');?>
	</style>
</head>
<body>
<header>
	<div class="content-wrap clearfix">
		<div class="logo"><a href="?"><?=$blog_config['title'];?></a></div>
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

	<?php if($current_article): ?>
		<section class="article">
			<article>
				<h1><?=h($current_article['title']);?></h1>
				<ul class="metas">
					<li><i class="iconfont icon-thumbnail"></i> 分类：
						<?=$current_article['category_path'] ? '<a href="?'.http_build_query(['path' => $current_article['category_path']]).'">'.$current_article['category_path'].'</a>' : '-';?>
					</li>
				</ul>
				<div class="ctn">
					<?=$current_article['content'];?>
				</div>
			</article>
		</section>
	<?php else: ?>
		<section class="articles">
			<?php
			$page = $_GET['page'] ?: 1;
			$page_size = 5;
			list($articles, $total) = get_articles($current_path, $page, $page_size);
			foreach($articles as $article):
				?>
				<article>
					<h1>
						<a href="?id=<?=$article['id'];?>"><?=h($article['title'])?></a>
					</h1>
					<p class="abs">
						<a href="?id=<?=$article['id'];?>">
							<?=$article['abstract'];?>
						</a>
					</p>
					<ul class="metas">
						<li><i class="iconfont icon-thumbnail"></i>
							分类：<?=$article['category_path'] ? '<a href="?'.http_build_query(['path' => $article['category_path']]).'">'.$article['category_path'].'</a>' : '-';?>
						</li>
					</ul>
				</article>
			<?php endforeach; ?>
			<?=show_pagination($page, ceil($total/$page_size), $current_path);?>
		</section>
	<?php endif; ?>
</section>
<?php $config = get_blog_info(); ?>
<footer>
	<div>
		By <?=$config['author'];?> &lt;<?=$config['contact'];?>&gt;
	</div>
	<div>
		<?=$config['copyrights'];?>
	</div>
</footer>
</body>
</html>
