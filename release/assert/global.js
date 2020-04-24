
let $file_list = $('.file-list');

$file_list.find('[data-type=dir]>.title').click(function(){
	$(this).closest('li').toggleClass('collapse');
})
$file_list.find('.toggle').click(function(){
	$(this).closest('li').toggleClass('collapse');
});
// setTimeout("location.reload()", 5000);