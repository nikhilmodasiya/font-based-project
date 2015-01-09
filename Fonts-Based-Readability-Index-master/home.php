<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Readability Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/survey.ico">
	<!-- Bootstrap Core CSS-->
    <link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<!-- Custom Core CSS-->
    <link href="Bootstrap/css/custom.css" rel="stylesheet">
    <script src="Bootstrap/js/respond.js"></script>
</head>


<?php
	include("include/db_connect.php");
	session_start();
	
	//////////FOR SECURITY/////////////////////
	//check for direct entry
	if(!isset($_SESSION['email'])){
		header("Location:index.php");
	}
	else{
		$sql = "SELECT * FROM main where email='".$_SESSION['email']."'";
		$result =  mysql_query($sql);
		$num=mysql_num_rows($result);
		$row=mysql_fetch_array($result);
		
		if($num!=1)
			header("Location:index.php");
	}
	
	if(!empty($_POST)){
		$font = $_POST["font"];
		$size = $_POST["size"];
		
		if(isset($_POST['line_height']))
			$line_height = $_POST["line_height"];
		if(isset($_POST['word_spacing']))
			$word_spacing = $_POST["word_spacing"];
		
		//storing form data in session array
		$_SESSION["font"] = $font;
		$_SESSION["size"] = $size;
		if(isset($_POST['line_height']))
			$_SESSION["line_height"] = $line_height;
		if(isset($_POST['word_spacing']))	
			$_SESSION["word_spacing"] = $word_spacing;
		
		if(isset($_POST['check_post'])){
			if( !($_POST['check_post'] == 1) ){
				$error = "Please select a paragraph number from paragraph selecter and then click on generate article button to generate a paragraph.";
			}
			else{
				if(isset($_POST['go'])){
					header("Location: survey.php");
				}
			}
		}
	}	
?>

<body>
	<div class="container">
		<div class="row well">
			<div class="col-md-4 col-lg-4 col-sm-4" align="center">
				Hello User <?php echo stripslashes($_SESSION['email']);?>
			</div>
			
			<div class="col-md-4 col-lg-4 col-sm-4">
				<div class="page-header"><h2 align="center">Readability Survey Home</h2></div>
			</div>
			
			<div class="col-md-4 col-lg-4 col-sm-4" align="center">
				<?php echo date("jS \of F Y [l]", time());?>
			</div>
		</div>
		
		<div class="row well main_div">
			<form class="form-inline" method="POST" action="home.php">
				<table>
					<tr align = "center">
						<td width = "240px">
							<div class="form-group">
								<label>Font Style</label>
								<select name="font" id="font">
									<option name="arial" value="Arial">Arial</option>
									<option name="times_new_roman" value="Times New Roman">Times New Roman</option>
									<option name="verdana" value="verdana">Verdana</option>
									<option name="georgia" value="georgia">Georgia</option>
									<!--
									<option name="calibri" value="Calibri">Calibri</option>
									<option name="comic_sans" value="Comic Sans MS">Comic Sans MS</option>
									<option name="lucida_sans" value="Lucida Sans">Lucida Sans</option>
									-->
								</select>
							</div>
						</td>	
					
						<td width = "160px">
							<div class="form-group">
								<label>Font Size (px)</label>
								<!-- <input name="size" id="size" type="number" class="span3" value="12" step="2" min="12" max="16" style="width: 50px"/> -->
								<select name="size" id="size" style="width: 50px">
									<option name="size_12" value="12">12</option>
									<option name="size_14" value="14">14</option>
									<option name="size_16" value="16">16</option>
								</select>
							</div>
						</td>	
						
						<td width = "180px">
							<div class="form-group">
								<label>Line Height</label>
								<select name="line_height" id="line_height" style="width: 80px">
									<option name="line_height_default" value="1">Default</option>
									<option name="line_height_high" value="2">High</option>
								</select>
								<!-- <input name="line_height" id="line_height" type="number" class="span3" value="1" step="1" min="1" max="3" style="width: 50px"/> -->
							</div>
						</td>	
							
						<td width = "220px">	
							<div class="form-group">
								<label>Word Spacing (px)</label>
								<select name="word_spacing" id="word_spacing" style="width: 80px">
									<option name="word_spacing_default" value="1">Default</option>
									<option name="word_spacing_high" value="2">High</option>
								</select>
								<!-- <input name="word_spacing" id="word_spacing" type="number" class="span3" value="0" step="2" min="0" max="10" align="center" style="width: 50px"/> -->
							</div>
						</td>	
						
						<td width = "160px">
							<div class="form-group">
								<label>Paragraph No</label>
								<?php
									$i = 1;
									$sql = "SELECT * FROM paragraphs";
									$result =  mysql_query($sql);
									echo '<select name="para_num" id="para_num" style="width: 50px">';
									while($row = mysql_fetch_array($result)){
										echo '<option name="para'.$i.'" value="'.$i.'">'.$i.'</option>';
										$i++;
									}
									echo '</select>';
								?>
							</div>
						</td>	
					</tr>	
				</table>	
				<!--Default values
				font - arial : changed in custom.css
				size - 14px = 100%
				word spacing - 0
					line hieght - 23
					-->		

				<?php
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
						return $matches[1] . "<a href=\"$url\" rel=\"nofollow\">$url</a>" . $ret;
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
						return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\">$dest</a>" . $ret;
					}

					function _make_email_clickable_cb($matches) {
						$email = $matches[2] . '@' . $matches[3];
						return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
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
				<!--The paragraph-->
				<p id="para" align="left" style="line-height: 1; word-spacing: 1px">
					<?php
						$para = "Click on the preview form, and then select your preferences then click on preview to see how your paragraph's gonna look !!
						Click on the go button when you are ready to take the survey.";
						echo make_clickable($para);
					?>
				</p>
				
				<div align="center">
					<div class="row article_btns">
						<span class="generate_article_form">
							<input id="generate_article_btn" name="generate_article_btn" class="btn btn-lg btn-primary" type="button" value="Generate Article"/>
						</span>
						<!--
						<span class="eng_article_form">
							<input id="eng_article_btn" name="eng_article_btn" class="btn btn-lg btn-info" type="button" value="English Article"/>	
						</span>
						
						<span class="hindi_article_form">
							<input id="hindi_article_btn" name="hindi_article_btn" class="btn btn-lg btn-info" type="button" value="Hindi Article"/>	
						</span>
						-->
					</div>
					
					<div class="row preview_btns">
						<input name="check_post" id="check_post" type="hidden"/>
						<input id="preview-btn" name="preview" class="btn btn-lg btn-warning" type="button" value="Preview" onclick="changeFont()"/>
						<input id="go-btn"name="go" class="btn btn-lg btn-success" type="submit" value="Go"/>
					</div>	
				</div>
			</form>
		</div>
		
		<!--Error Printing-->
		<?php
			if(isset($error)){
				echo "<div class='alert alert-danger' align='center' id='status-box'>";
					echo $error;
				echo "</div>";	
			}
			else{
				if(isset($success)){
					echo "<div class='alert alert-success' align='center' id='status-box'>";
					echo $success;
					echo "</div>";	
				}	
			}	
		?>
	</div>

	<!-- javascript -->
	<script src="Bootstrap/js/jquery 2.1.1.min.js"></script>
    <script src="Bootstrap/js/bootstrap.min.js"></script>
    <script src="Bootstrap/js/custom.js"></script>
	<script type="text/javascript">
		function changeFont(){
			//when clicked on preview btn, but never on an article btn (now its generate article btn)
			if(document.getElementById('check_post').value == 0){
				var error = $("<div class='alert alert-danger' align='center' id='status-box'>"+
					"Please select a paragraph number from paragraph selecter and then click on generate article button to generate a paragraph."+
				"</div>");
				
				$(error)
				.insertAfter(".main_div")
				.delay(2000)
				.fadeOut(3000);
			}
			//when clicked on preview btn after atleast one click on article btn (now its generate article btn)
			else{
				var font = document.getElementById("font");
				var size = document.getElementById("size");
				var line_height = document.getElementById("line_height");
				var word_spacing = document.getElementById("word_spacing");
				
				para.style.fontSize = size.value+"px";
				para.style.fontFamily = font.value;
				para.style.lineHeight = line_height.value;
				para.style.wordSpacing = word_spacing.value + "px";
			}
		}
		//runs when generate btn is clicked via custom.js inside bootstrap folder
		function changeFontRandomly(){
			var size = ["12", "14", "16"];
			var rand_no = Math.floor(Math.random() * (3));//generates a random no. in [0,6)
			para.style.fontSize = size[rand_no]+"px";
			document.getElementById("size").value = size[rand_no];
			
			
			var font=["Arial", "Times New Roman", "verdana", "georgia"];//, "Calibri", "Comic Sans MS", "Lucida Sans"];
			//var rand_no = Math.floor(Math.random() * (7));
			var rand_no = Math.floor(Math.random() * (4));//generates a random no. in [0,4)
			para.style.fontFamily = font[rand_no];
			document.getElementById("font").value = font[rand_no];
		}
			
		$(".alert").alert();
		window.setTimeout(function() {
			$(".alert").alert('close'); 
		}, 5000);
	</script>

	<!---footer---->
<nav class=" navbar-fixed-bottom footer " role="navigation" >
  <div class="container footer" align="right";  >
    <font color="#04B404"><b> Developer-</b></font>
	 <a href=http://about.me/jain_nikhil><b>Nikhil Jain </b></a>
	 <font color="#2E2EFE"> &</font>
	<a href=http://about.me/ashish_dubey><b>Ashish Dubey</b></a>
	 
  </div>
</nav>
</body>
</html>