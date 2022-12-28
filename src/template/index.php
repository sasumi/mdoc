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

	<?php if($current_article): ?>
		<section class="article">
			<article>
				<h1><?=h($current_article['title']);?></h1>
				<ul class="metas">
					<li>
						<?=$current_article['category_path'] ? '<a class="iconfont icon-thumbnail" href="?'.http_build_query(['path' => $current_article['category_path']]).'"> '.$current_article['category_path'].'</a>' : '-';?>
					</li>
					<li>
						<span class="link iconfont icon-share" data-share-title="<?=ha($current_article['title'])?>" data-share-id="<?=$current_article['id'];?>">
							分享
						</span>
					</li>
				</ul>
				<div class="ctn">
					<?=$current_article['content'];?>
				</div>
			</article>
		</section>
		<section class="toc">
			<script><?php include __DIR__.'/toc.js';?></script>
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
						<li>
							<?=$article['category_path'] ? '<a class="iconfont icon-thumbnail" href="?'.http_build_query(['path' => $article['category_path']]).'"> '.$article['category_path'].'</a>' : '-';?>
						</li>
						<li>
						<span class="link iconfont icon-share" data-share-title="<?=ha($article['title'])?>" data-share-id="<?=$article['id'];?>">
							分享
						</span>
						</li>
					</ul>
				</article>
			<?php endforeach; ?>
			<?=show_pagination($page, ceil($total/$page_size), $current_path);?>
		</section>
	<?php endif; ?>
	<script>
		document.querySelectorAll('.icon-share').forEach(shareLink=>{
			shareLink.addEventListener('click', e=>{
				let url = location.protocol+'//'+location.host+location.pathname+'?id='+shareLink.getAttribute('data-share-id');
				let text = shareLink.getAttribute('data-share-title')+"\n"+url;
				navigator.clipboard.writeText(text).then(()=>{
					alert('分享信息复制成功');
				}).catch(err=>{
					alert('复制失败');
				});
			})
		})
	</script>
</section>
<?php $blog_info = get_blog_info(); ?>
<footer>
	<?php $new_version = check_new_version();
	if($new_version):
	?>
	<div class="upgrade">发现新版本：<?=$new_version['version'];?> （当前版本：<?=this_version();?>），<a href="<?=RELEASE_PATH.(release_file_name($new_version['version']));?>" target="_blank">立即更新</a></div>
	<?php endif;?>

	<?php if($blog_info['author'] || $blog_info['contact']):?>
	<div class="contact">
		<?=$blog_info['author'];?>
		<?=$blog_info['contact'];?>&gt;
	</div>
	<?php endif;?>

	<?php if($blog_info['copyrights']):?>
	<div class="copyrights">
		<?=$blog_info['copyrights'];?>
	</div>
	<?php endif;?>
</footer>
</body>
</html>
