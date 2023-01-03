<?php

use function Lfphp\Mdoc\get_articles;
use function Lfphp\Mdoc\h;
use function Lfphp\Mdoc\ha;
use function Lfphp\Mdoc\show_pagination;
use const Lfphp\Mdoc\SORT_CREATED_ASC;
use const Lfphp\Mdoc\SORT_CREATED_DESC;
use const Lfphp\Mdoc\SORT_MODIFIED_ASC;
use const Lfphp\Mdoc\SORT_MODIFIED_DESC;
use const Lfphp\Mdoc\SORT_NAME_ASC;
use const Lfphp\Mdoc\SORT_NAME_DESC;

/** @var string $current_path */

$page = $_GET['page'] ?: 1;
$page_size = 5;
$current_sort_by = $_GET['sort'] ?: SORT_NAME_ASC;
list($articles, $total) = get_articles($current_path, $page, $page_size, $current_sort_by);
$sort_opts = [
	SORT_NAME_ASC      => '名称顺序',
	SORT_NAME_DESC     => '名称倒叙',
	'-',
	SORT_CREATED_ASC   => '创建时间从新到旧',
	SORT_CREATED_DESC  => '创建时间从旧到新',
	'-',
	SORT_MODIFIED_ASC  => '修改时间从新到旧',
	SORT_MODIFIED_DESC => '修改时间从旧到新',
];
?>
<section class="articles">
	<div class="ops">
		<dl class="menu sort-menu">
			<dt class="iconfont icon-sort"><?=$sort_opts[$current_sort_by];?></dt>
			<dd>
				<ul>
					<?php foreach($sort_opts as $k => $v){
						if($v === '-'){
							echo '<li class="sep"></li>';
						}else{
							echo "<li class=\"".($k === $current_sort_by ? 'active' : '')."\"><a href=\"?".http_build_query([
									'path' => $current_path,
									'sort' => $k,
								])."\">$v</a></li>";
						}
					} ?>
				</ul>
			</dd>
		</dl>
	</div>
	<?php foreach($articles as $article): ?>
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
					<?=$article['category_path'] ? '<a class="iconfont icon-thumbnail" href="?'.http_build_query([
							'path' => $article['category_path'],
							'sort' => $current_sort_by,
						]).'"> '.h($article['category_path']).'</a>' : '-';?>
				</li>
				<li>
					<span title="最后修改：<?=date('Y-m-d H:i', filemtime($article['file']));?>">
						创建：<?=date('Y-m-d H:i', filectime($article['file']));?>
					</span>
				</li>
				<li>
					<span class="link iconfont icon-share" data-share-title="<?=ha($article['title'])?>"
					      data-share-id="<?=$article['id'];?>">
						分享
					</span>
				</li>
			</ul>
		</article>
	<?php endforeach; ?>
	<?=show_pagination($page, ceil($total/$page_size), $current_path, $current_sort_by);?>
</section>
