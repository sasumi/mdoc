<?php

use function LFPhp\Func\encodeURI;
use function LFPhp\Func\h;
use function LFPhp\Func\ha;
use function Lfphp\Mdoc\get_article_detail;
use function Lfphp\Mdoc\get_articles;
use function Lfphp\Mdoc\get_config;
use function Lfphp\Mdoc\get_folders_recursive;

$blog_config = get_config();
$current_article = $_GET['id'] ? get_article_detail($_GET['id']) : null;
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport"
	      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="asset/style.css">
	<title><?=$blog_config['title'];?></title>
</head>
<body>
	<header>
		<div class="content-wrap clearfix">
			<h1 class="logo"><a href="?"><?=$blog_config['title'];?></a></h1>
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
			<dl class="aside-mod">
				<dt>Category</dt>
				<dd>
					<ul class="categories">
						<li>
							<a href="?">All Categories</a>
						</li>
						<?php
						$categories = get_folders_recursive();
						function show_cats($cats){
							$html = '<ul>';
							foreach($cats as $cat){
								$html .= '<li><a href="?path='.ha($cat['id']).'">'.$cat['title'].'</a></li>';
							}
							$html .= '</ul>';
							return $html;
						}
						foreach($categories as $cat):?>
						<li>
							<a href="?path=<?=encodeURI($cat['id']);?>"><?=h($cat['title']);?></a>
							<?php if($cat['children']){
								echo show_cats($cat['children']);
							}
							?>
						</li>
						<? endforeach;?>
					</ul>
				</dd>
			</dl>
		</aside>

		<?php if($current_article):?>
		<section class="article">
			<article>
				<h2><?=h($current_article['title']);?></h2>
				<ul class="metas">
					<li>Category: <?=$current_article['category_path'] ?: 'Root';?></li>
				</ul>
				<div class="ctn">
					<?=$current_article['content'];?>
				</div>
			</article>
		</section>
		<?php else:?>
		<section class="articles">
			<?php $articles = get_articles();
			foreach($articles as $article):
			?>
			<article>
				<h2>
					<a href="?id=<?=$article['id'];?>"><?=h($article['title'])?></a>
				</h2>
				<p class="abs">
					<?=$article['abstract'];?>
				</p>
				<ul class="metas">
					<li>Category: <?=$article['category_path'] ?: 'Root';?></li>
				</ul>
			</article>
			<?php endforeach;?>
		</section>
		<?php endif;?>
	</section>
	<?php $config = get_config();?>
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
