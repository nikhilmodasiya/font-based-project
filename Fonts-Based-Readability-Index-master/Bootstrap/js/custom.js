$('#generate_article_btn').on('click', function(){
	if(document.getElementById('check_post').value != 1)
		document.getElementById('check_post').value = 1;
	
	$para_num = document.getElementById('para_num').value;
	$.post("ajax/ajax.php", {para_num: $para_num}, function(data){
		var $para = $( "#para" ),
	  		str = data,
	  		html = $.parseHTML( str );
			
		//remove previous text
		$para.empty();
		// Append the parsed HTML
		$para.append( html );
		
		//NOT DOING THIS NOW
		// $('#para').text(data);
		
		//Changing para's font size and style randomly
		changeFontRandomly();
	});
	//$('#generate_article_btn').remove();
});
$('#eng_article_btn').on('click', function(){
	if(document.getElementById('check_post').value != 1)
		document.getElementById('check_post').value = 1;
	$lang = "English";
	$.post("ajax/ajax.php", {lang : $lang}, function(data){
		$('#para').text(data);
		changeFontRandomly();
	});
});
$('#hindi_article_btn').on('click', function(){
	if(document.getElementById('check_post').value != 1)
		document.getElementById('check_post').value = 1;
	$lang = "Hindi";
	$.post("ajax/ajax.php", {lang : $lang}, function(data){
		$('#para').text(data);
		changeFontRandomly();
	});
});