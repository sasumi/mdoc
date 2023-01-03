<?php
use function Lfphp\Mdoc\get_blog_info;
use function Lfphp\Mdoc\check_new_version;
use function Lfphp\Mdoc\release_file_name;
use function Lfphp\Mdoc\this_version;
use const Lfphp\Mdoc\RELEASE_PATH;

$blog_info = get_blog_info();
?>
</section>
<footer>
	<?php $new_version = check_new_version();
	if($new_version):
		?>
		<div class="upgrade">发现新版本：<?=$new_version['version'];?> （当前版本：<?=this_version();?>），<a href="<?=RELEASE_PATH.(release_file_name($new_version['version']));?>" target="_blank">立即更新</a></div>
	<?php endif;?>

	<?php if($blog_info['author'] || $blog_info['contact']):?>
		<div class="contact">
			<?=$blog_info['author'];?>
			<?=$blog_info['contact'];?>
		</div>
	<?php endif;?>

	<?php if($blog_info['copyrights']):?>
		<div class="copyrights">
			<?=$blog_info['copyrights'];?>
		</div>
	<?php endif;?>
</footer>

<script>
	document.querySelectorAll('.icon-share').forEach(shareLink => {
		shareLink.addEventListener('click', () => {
			let url = location.protocol + '//' + location.host + location.pathname + '?id=' + shareLink.getAttribute('data-share-id');
			let text = shareLink.getAttribute('data-share-title') + "\n" + url;
			navigator.clipboard.writeText(text).then(() => {
				alert('分享信息复制成功');
			}).catch(() => {
				alert('复制失败');
			});
		})
	})
</script>
</body>
</html>
