<?php
	session_start();
	include("../include/db_connect.php");
	mysql_query("SET NAMES utf8");	
	
	/*
	if(isset($_POST['lang']))
		$lang = $_POST['lang'];
	else
		$lang = "English";
	*/	
	
	//$sql = "SELECT para, pid FROM paragraphs WHERE `language` = '".$lang."' ORDER BY RAND() LIMIT 1";
	//$sql = "SELECT para, pid FROM paragraphs ORDER BY RAND() LIMIT 1";

	if(isset($_POST['para_num'])){
		$para_num = $_POST['para_num'] - 1;
		$sql = "SELECT *
				FROM paragraphs 
				LIMIT ".$para_num.", 1";
		$var = mysql_query($sql);
		$row = mysql_fetch_array($var);
		$_SESSION['pid'] = $row['pid'];
		echo make_clickable($row['para']);
	}
	else
		echo "Paragraph not Available";

	function _make_url_clickable_cb($matches) {
		$ret = '';
		$url = $matches[2];

		if ( empty($url) )
			return $matches[0];
		// removed trailing [.,;:] from URL
		if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($url, -1);
			$url = substr($url, 0, strlen($url)-1);
		}
		return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$url</a>" . $ret;
	}

	function _make_web_ftp_clickable_cb($matches) {
		$ret = '';
		$dest = $matches[2];
		$dest = 'http://' . $dest;

		if ( empty($dest) )
			return $matches[0];
		// removed trailing [,;:] from URL
		if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($dest, -1);
			$dest = substr($dest, 0, strlen($dest)-1);
		}
		return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target=\"_blank\">$dest</a>" . $ret;
	}

	function _make_email_clickable_cb($matches) {
		$email = $matches[2] . '@' . $matches[3];
		return $matches[1] . "<a href=\"mailto:$email\" target=\"_blank\">$email</a>";
	}

	function make_clickable($ret) {
		$ret = ' ' . $ret;
		// in testing, using arrays here was found to be faster
		$ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_url_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', '_make_web_ftp_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', '_make_email_clickable_cb', $ret);

		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);
		return $ret;
	}
?>