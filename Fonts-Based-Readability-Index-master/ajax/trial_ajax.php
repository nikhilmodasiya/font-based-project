<?php
	if( isset($_POST['article']) === true && empty($_POST['article']) === false ){
		include("../include/db_connect.php");
		session_start();
		mysql_query("SET NAMES utf8");	
		
		if(isset($_POST['lang']))
			$lang = $_POST['lang'];
		else
			$lang = "English";
		
		$article = $_POST['article'];
		
		$sql = "SELECT * FROM paragraphs WHERE article_type = '".$article."' AND language = '".$lang."' ORDER BY RAND() LIMIT 1";
		$var = mysql_query($sql);
		$row = mysql_fetch_array($var);
		$_SESSION['pid'] = $row['pid'];
		echo $row['para'];
	}
?>