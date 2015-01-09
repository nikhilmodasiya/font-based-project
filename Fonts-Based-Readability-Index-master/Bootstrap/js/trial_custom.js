$('#eng_article_btn').on('click', function(){
	document.getElementById('check_post').value = 1;
	$lang = "English";
	var article = $('#article').val();
	$.post("ajax/trial_ajax.php", {lang : $lang, article : article}, function(data){
		$('#para').text(data);
		changeFontRandomly();
	});
});
$('#hindi_article_btn').on('click', function(){
	document.getElementById('check_post').value = 1;
	$lang = "Hindi";
	var article = $('#article').val();
	$.post("ajax/trial_ajax.php", {lang : $lang, article : article}, function(data){
		$('#para').text(data);
		changeFontRandomly();
	});
});