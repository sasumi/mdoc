<?php
use function LFPhp\Func\h;
use function LFPhp\Func\ha;
use function mdoc\build_file_nav;
use function mdoc\get_style_sheet;
/** @var array $article_list */
/** @var array $article */
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
	<script src="https://s.huapx.com/src/vendor/jquery/jquery-1.8.3.min.js"></script>
</head>
<body>
	<section class="page">
		<aside>
			<div class="project-title"><a href=""><?=h($project['title']);?></a></div>
			<form action="" class="project-search">
				<input type="search" name="keyword" id="" placeholder="Search Docs" value="<?=ha($_GET['keyword']);?>">
				<button class="search-btn">Search</button>
			</form>
			<dl class="aside-mod">
				<dt>DOCUMENTS</dt>
				<dd class="file-list">
					<?=build_file_nav($article_list, $article);?>
				</dd>
			</dl>
		</aside>

		<section class="container">
			<ul class="breadcrumbs">
				<li><a href="/">Home</a></li>
				<li><?=h($article['title']);?></li>
			</ul>
			<article>
				<h1><?=h($article['title']);?></h1>
				<ul class="meta">
					<li><label>创建</label><span><?=date('Y年m月d日 H:i', $article['create_time']);?></span></li>
					<li>
						<label>最后修改</label>
						<span>
							<?=($article['modify_time'] && $article['modify_time'] != $article['create_time']) ? date('Y年m月d日 H:i', $article['modify_time']) : '-'?>
						</span>
					</li>
				</ul>
				<div class="content">
					<?php
					$pd = new \Parsedown();
					echo $pd->text($article['raw']);
					?>
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
	<script src="assert/global.js"></script>
</body>
</html>
<?php
