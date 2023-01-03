<?php
/** @var array $current_article */

use function Lfphp\Mdoc\h;
use function Lfphp\Mdoc\ha;

?>
<section class="article">
	<article>
		<h1><?=h($current_article['title']);?></h1>
		<ul class="metas">
			<li>
				<?=$current_article['category_path'] ? '<a class="iconfont icon-thumbnail" href="?'.http_build_query(['path' => $current_article['category_path']]).'"> '.$current_article['category_path'].'</a>' : '-';?>
			</li>
			<li>
					<span title="最后修改：<?=date('Y-m-d H:i', filemtime($current_article['file']));?>">
						创建：<?=date('Y-m-d H:i', filectime($current_article['file']));?>
					</span>
			</li>
			<li>
				<span class="link iconfont icon-share" data-share-title="<?=ha($current_article['title'])?>"
				      data-share-id="<?=$current_article['id'];?>">
					分享
				</span>
			</li>
		</ul>
		<div class="ctn">
			<?=$current_article['content'];?>
		</div>
	</article>
</section>

<div class="toc-con"></div>
<script><?php include __DIR__.'/toc.js'; ?></script>
