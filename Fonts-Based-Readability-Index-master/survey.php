<!doctype html>
<html>
	<head>
		<title>Readability Survey Page</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<!--For Indian languages-->
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<link rel="shortcut icon" href="images/survey.ico">
		<!-- Bootstrap Core CSS-->
		<link href="Bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Custom Core CSS-->
		<link href="Bootstrap/css/custom.css" rel="stylesheet">
		<!-- Stopwatch CSS-->
		<link href="Bootstrap/css/stopwatch.css" rel="stylesheet">
		<script src="Bootstrap/js/respond.js"></script>
		<!-- Stopwatch JS-->
		<script src="Bootstrap/js/prefixfree.min.js"></script>
	</head>

	<?php
		include("include/db_connect.php");
		session_start();
		
		//////////FOR SECURITY/////////////////////
		if(!isset($_SESSION['email'])){
			header("Location:index.php");
		}
		else{
			$sql = "SELECT * FROM main where email='".$_SESSION['email']."'";
			$result=  mysql_query($sql);
			$num = mysql_num_rows($result);
			$row=mysql_fetch_array($result);
			
			if($num !=1 )
				header("Location:index.php");
		}
		
		if(isset($_SESSION['pid'])){
			$pid = $_SESSION['pid'];
		}
		else{
			$error = "Some error occured, try going to our home page and redo the process.";
		}
		if(isset($_SESSION['font']))
			$font = $_SESSION['font'];
		if(isset($_SESSION['size']))
			$size = $_SESSION['size'];
		if(isset($_SESSION['line_height']))
			$line_height = $_SESSION['line_height'];
		if(isset($_SESSION['word_spacing']))	
			$word_spacing = $_SESSION['word_spacing'];
		
		
		if(isset($_POST['stop_test'])){
			$pid = $_SESSION['pid'];
			$font = $_SESSION['font'];
			$size = $_SESSION['size'];
			$line_height = $_SESSION['line_height'];
			$word_spacing = $_SESSION['word_spacing'];
			
			if(isset($_POST['reading_time']))
				$reading_time = $_POST['reading_time'];
			else
				$reading_time = 0;
			
			if(isset($_POST['test_time']))
				$test_time = $_POST['test_time'];
			else
				$test_time = 0;
			
			$sql = "SELECT `user_id` FROM main WHERE `email` = '".$_SESSION['email']."'";
			$var = mysql_query($sql);
			$row = mysql_fetch_array($var);
			$uid = $row['user_id'];
			
			mysql_query("SET NAMES utf8");
			$sql = "INSERT INTO `test_data` (`tid` ,`uid` ,`pid` ,`font` ,`size` ,`line_height` ,`word_spacing` ,`reading_time` ,`test_time`)VALUES (NULL , '$uid', '$pid', '$font', '$size', '$line_height', '$word_spacing', '$reading_time', '$test_time')";
			if(!$result = mysql_query($sql)){
				$error = "Error occured while storing information into database.";
				$_SESSION['error'] = $error;
			}
			
			$tid = mysql_insert_id();
			$quesNum = $_POST['quesNum'];
			for($i = 1; $i <= $quesNum; $i++){
				$qid = $_POST["qid".$i];
				$ques_type = $_POST["qid".$i."ques_type"];
				
				//single correct obj ques
				if($ques_type == "FFFF"){
					if(isset($_POST["ques".$i]))
						$selected_option = $_POST["ques".$i];
					else
						$selected_option = "skipped";
					
					mysql_query("SET NAMES utf8");
					$sql = "INSERT INTO `test_questions_data` (`tid` ,`uid`, `qid`, `selected_option`)VALUES ('$tid', '$uid', '$qid', '$selected_option')";
					
					if(!$result = mysql_query($sql)){
						$error = "Error occured while storing information into database.";
						$_SESSION['error'] = $error;
					}
				}
				//sub ques
				else if($ques_type == "null"){
					if(isset($_POST["ans".$i]))
						$selected_option = $_POST["ans".$i];
					if($selected_option == "")
						$selected_option = "skipped";
					
					mysql_query("SET NAMES utf8");
					$sql = "INSERT INTO `test_questions_data` (`tid`, `uid`, `qid`, `selected_option`)VALUES ('$tid', '$uid', '$qid', '$selected_option')";
					
					if(!$result = mysql_query($sql)){
						$error = "Error occured while storing information into database.";
						$_SESSION['error'] = $error;
					}
				}
				//multiple correct obj ques
				else{
					$answers = $_POST["ques".$i];
					//if nothing selected
					if(empty($answers)){
						$selected_option = "skipped";

						mysql_query("SET NAMES utf8");
						$sql = "INSERT INTO `test_questions_data` (`tid`, `uid`, `qid`, `selected_option`)VALUES ('$tid', '$uid', '$qid', '$selected_option')";
					
						if(!$result = mysql_query($sql)){
							$error = "Error occured while storing information into database.";
							$_SESSION['error'] = $error;
						}
					}
					//if something was selected
					else{
						$n = count($answers);
						for($j = 0; $j < $n; $j++){
							mysql_query("SET NAMES utf8");
							$selected_option = $answers[$j];
							$sql2 = "INSERT INTO `test_questions_data` (`tid`, `uid`, `qid`, `selected_option`)VALUES ('$tid', '$uid', '$qid', '$selected_option')";
							
							if(!$result2 = mysql_query($sql2)){
								$error = "Error occured while storing information into database.";
								$_SESSION['error'] = $error;
							}
						}
					}
				}
			}
			header("Location: home.php");
		}
	?>
	
	<body>
		<div class="container">
			<div class="row well main-page-header">
				<div class="col-sm-3 col-md-3 col-lg-3" align="center">
					Hello User <?php echo stripslashes($_SESSION['email']);?><br/>
					<?php echo date("jS \of F Y [l]", time());?>
				</div>
				
				<div class="col-sm-6 col-md-6 col-lg-6">
					<div class="page-header"><h2 align="center">Readability Survey Main Page</h2></div>
				</div>
				
				<div class="col-sm-3 col-md-3 col-lg-3" align="center">					
					<!-- time to add the controls -->
					<input id="start" name="controls" type="radio" />
					<input id="stop" name="controls" type="radio" />
					<input id="reset" name="controls" type="radio" />
					<div class="timer">
						<div class="cell">
							<div class="numbers tenhour moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						<div class="cell">
							<div class="numbers hour moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						<div class="cell divider"><div class="numbers">:</div></div>
						<div class="cell">
							<div class="numbers tenminute movesix">0 1 2 3 4 5 6</div>
						</div>
						<div class="cell">
							<div class="numbers minute moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						<div class="cell divider"><div class="numbers">:</div></div>
						<div class="cell">
							<div class="numbers tensecond movesix">0 1 2 3 4 5 6</div>
						</div>
						<div class="cell">
							<div class="numbers second moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						<div class="cell divider"><div class="numbers">:</div></div>
						<div class="cell">
							<div class="numbers milisecond moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						<div class="cell">
							<div class="numbers tenmilisecond moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						<div class="cell">
							<div class="numbers hundredmilisecond moveten">0 1 2 3 4 5 6 7 8 9</div>
						</div>
						
						<!-- Lables for the controls -->
						<div id="timer_controls">
							<label for="start">Start</label>
							<label for="stop">Stop</label>
							<label for="reset">Reset</label>
						</div> 
					</div>
					<style type="text/css">
						<!--Making timer size smaller here-->
						div.cell {
							font-size: 33px !important;
						}
						<!--Making more space for para here-->
						div.row,well {
							color: red;
						}
					</style>
				</div>
			</div>
			<?php
				if(!isset($error)){
					mysql_query("SET NAMES utf8");
					$sql = "SELECT para FROM paragraphs WHERE pid='$pid'";
					$var = mysql_query($sql);
					$row = mysql_fetch_array($var);
					$para = make_clickable($row['para']);
				}
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
			
			<div align="center">
				<p class='well' id='survey_para' align='left'>
					<?php echo $para; ?>
				</p>
				
				<!--Start Button-->
				<input id="start_test" name="start_test" class="btn btn-lg btn-success" type="button" value="Start Test" onclick="showQuestions();"/>
				
				<!--Hidden fields for font, size, letter-spacing, word-spacing-->
				<input name='font' id='font' type='hidden' value='<?php echo $font;?>'/>
				<input name='size' id='size' type='hidden' value='<?php echo $size;?>'/>
				<input name='line_height' id='line_height' type='hidden' value='<?php echo $line_height;?>'/>
				<input name='word_spacing' id='word_spacing' type='hidden' value='<?php echo $word_spacing;?>'/>
				
				<!--Question Form-->
				<?php
					$sql = "SELECT * FROM questions WHERE pid='$pid'";
					$var = mysql_query($sql);
					$quesNum = 0;
					echo "<form class='form' id='question_form' name='question_form' method='POST' action='survey.php'>
						<input name='stop_test' id='stop_test' class='btn btn-lg btn-danger' type='submit' value='Stop Test' onclick='stopButton();'/>";
						
						echo "<div class='panel-group' id='accordion'>";
							while($row = mysql_fetch_array($var)){
								$quesNum++;
								$tmp_array = array($row['opt1'], $row['opt2'], $row['opt3'], $row['opt4']);
								$ans_array = array_filter($tmp_array);
								
								
								echo "<div class='panel' id='panel".$quesNum."'>
										<button id='panel_accordian' class='btn btn-primary btn-lg btn-block' data-toggle='collapse' href='#collapseOne".$quesNum."' data-parent='#accordion'>
											Question No. : ".$quesNum.
										"</button>";
											
								if(!empty($ans_array)){
								//objective question
									shuffle($ans_array);
									$str = $row['multi_correct'];
									//single correct obj ques
									if($str == "FFFF"){
										echo "<div id='collapseOne".$quesNum."' class='panel-collapse collapse'>
												<div class='panel-body well'>".
													$row['ques']."<br/>".
													"<div class='answers well'>";
													echo "<table align = 'center'>";
													if(isset($ans_array[0])){
														echo "<tr align='left'><td>";
														echo "<input type='radio' name='ques".$quesNum."' id='opt1".$quesNum."' value='".$ans_array[0]."'/>
														<label for='opt1".$quesNum."'>".$ans_array[0]."</label>";
														echo "</td></tr>";
													}
													if(isset($ans_array[1])){	
														echo "<tr align='left'><td>";
														echo "<input type='radio' name='ques".$quesNum."' id='opt2".$quesNum."' value='".$ans_array[1]."'/>
														<label for='opt2".$quesNum."'>".$ans_array[1]."</label>";
														echo "</td></tr>";
													}
													if(isset($ans_array[2])){
														echo "<tr align='left'><td>";
														echo "<input type='radio' name='ques".$quesNum."' id='opt3".$quesNum."' value='".$ans_array[2]."'/>
														<label for='opt3".$quesNum."'>".$ans_array[2]."</label>";
														echo "</td></tr>";
													}
													if(isset($ans_array[3])){
														echo "<tr align='left'><td>";
														echo "<input type='radio' name='ques".$quesNum."' id='opt4".$quesNum."' value='".$ans_array[3]."'/>
														<label for='opt4".$quesNum."'>".$ans_array[3]."</label>";
														echo "</td></tr>";
													}
													echo "</table>";
													echo "<input name='qid".$quesNum."' id='qid".$quesNum."' type='hidden' value='".$row['qid']."'/>
														<input name='qid".$quesNum."ques_type' id='qid".$quesNum."ques_type' type='hidden' value='".$str."'/>
													</div>
												</div>
											</div>	
										</div>";
									}
									//muliple correct obj ques
									else{
										echo "<div id='collapseOne".$quesNum."' class='panel-collapse collapse'>
													<div class='panel-body well'>".
														$row['ques']."<br/>".
														"<div class='answers well'>";
														echo "<table align = 'center'>";
														if(isset($ans_array[0])){
															echo "<tr align='left'><td>";
															echo "<input type='checkbox' name='ques".$quesNum."[]' id='opt1".$quesNum."' value='".$ans_array[0]."'/>
															<label for='opt1".$quesNum."'>".$ans_array[0]."</label><br/>";
															echo "</td></tr>";
														}
														if(isset($ans_array[1])){
															echo "<tr align='left'><td>";
															echo "<input type='checkbox' name='ques".$quesNum."[]' id='opt2".$quesNum."' value='".$ans_array[1]."'/>
															<label for='opt2".$quesNum."'>".$ans_array[1]."</label><br/>";
															echo "</td></tr>";
														}
														if(isset($ans_array[2])){
															echo "<tr align='left'><td>";
															echo "<input type='checkbox' name='ques".$quesNum."[]' id='opt3".$quesNum."' value='".$ans_array[2]."'/>
															<label for='opt3".$quesNum."'>".$ans_array[2]."</label><br/>";
															echo "</td></tr>";
														}
														if(isset($ans_array[3])){
															echo "<tr align='left'><td>";
															echo "<input type='checkbox' name='ques".$quesNum."[]' id='opt4".$quesNum."' value='".$ans_array[3]."'/>
															<label for='opt4".$quesNum."'>".$ans_array[3]."</label><br/>";
															echo "</td></tr>";
														}
														echo "</table>";
														echo "<input name='qid".$quesNum."' id='qid".$quesNum."[]' type='hidden' value='".$row['qid']."'/>
															<input name='qid".$quesNum."ques_type' id='qid".$quesNum."ques_type' type='hidden' value='".$str."'/>
														</div>
													</div>
												</div>
										</div>";
									}
								}
								else{
								//subjective question
									echo "<div id='collapseOne".$quesNum."' class='panel-collapse collapse'>
											<div class='panel-body well'>".
												$row['ques']."<br/>".
												"<div class='answers well'>
													<label>Answer</label><br/>
													<textarea name='ans".$quesNum."' id='ans".$quesNum."' class='sub_ans'></textarea>
													
													<input name='qid".$quesNum."' id='qid".$quesNum."' type='hidden' value='".$row['qid']."'/>
													<input name='qid".$quesNum."ques_type' id='qid".$quesNum."ques_type' type='hidden' value='null'/>
												</div>
											</div>
										</div>
									</div>";
								}
							}
						echo "</div>";
				?>
						<input name='quesNum' id='quesNum' type='hidden' value='<?php echo $quesNum;?>'/>
						<input name='reading_time' id='reading_time' type='hidden'/>
						<input name='test_time' id='test_time' type='hidden'/>
					</form>	
				
						
				<!--Error Printing-->
				<?php
				if(isset($error))
				{
					echo "<div class='alert alert-danger' align='center' id='status-box'>";
						echo $error;
					echo "</div>";
				}
				?>
			</div>
		</div>	
		<!-- javascript -->
		<script src="Bootstrap/js/jquery 2.1.1.min.js"></script>
		<script src="Bootstrap/js/bootstrap.min.js"></script>
		<script>
			$(document).ready(function() {
				onFocus();
				var font = document.getElementById("font");
				var size = document.getElementById("size");
				var line_height = document.getElementById("line_height");
				var word_spacing = document.getElementById("word_spacing");
				survey_para.style.fontFamily = font.value;
				survey_para.style.fontSize = size.value+"px";
				survey_para.style.lineHeight = line_height.value;
				survey_para.style.wordSpacing = word_spacing.value + "px";
				
				$("#question_form").hide();
				$("#timer_controls").hide();
			});
			
			var timeout=null;
			var timer = null;
			var totalTime = null;
			var startTime = null;
			var reading_time=0;
			var test_time=0;
			var count = 0;
			
			function showQuestions(){
				$("#question_form").show();
				$("#start_test").hide();
				count++;
				
				if (timeout == null && startTime != null){
					reading_time = (Date.now() - startTime)/1000 ;
					reading_time = reading_time.toFixed(3);
				}
				else if (startTime != null){
					reading_time=(totalTime+(Date.now() - startTime))/1000 ;
					reading_time = reading_time.toFixed(3);
				}
				document.getElementById("start").checked = true;
				document.getElementById("reading_time").value = reading_time;
				totalTime = null;
			}
			
			function onBlur() {
				if(count != 0){
					document.getElementById("stop").checked = true;
				}
				timer = setTimeout(changeitup, 1);
			}

			function onFocus(){
				if(count != 0){
					document.getElementById("start").checked = true;
				}
				if(timer)
					clearTimeout(timer);
				totalTime += (timeout-startTime);
				startTime = Date.now();
			}

			function changeitup(){
				timeout= Date.now();
			}

			function stopButton() {
				document.getElementById("stop").checked = true;
				if (timeout == null && startTime != null){
					test_time=((Date.now() - startTime)/1000 -reading_time);
					test_time = test_time.toFixed(2);
				}
				else if (startTime != null){
					test_time=((totalTime+(Date.now() - startTime))/1000-reading_time);
					test_time = test_time.toFixed(2);
				}
				document.getElementById("test_time").value = test_time;
				totalTime = null;
			}

			if (/*@cc_on!@*/false) { // check for Internet Explorer
				document.onfocusin = onFocus;
				document.onfocusout = onBlur;
			} else {
				window.onfocus = onFocus;
				window.onblur = onBlur;
			}
			
			$(".alert").alert();
			window.setTimeout(function() { 
				$(".alert").alert('close'); 
			}, 5000);
		</script>
		
		<!---footer---->
		<nav class=" navbar-fixed-bottom footer " role="navigation" align="right">
		  <div class="container footer" align="right">
			<font color="#04B404"><b> Developer-</b></font>
			 <a href=http://about.me/jain_nikhil><b>Nikhil Jain </b></a>
			 <font color="#2E2EFE"> &</font>
			<a href=http://about.me/ashish_dubey><b>Ashish Dubey</b></a>
			 
		  </div>
		</nav>		
	</body>
</html>	


