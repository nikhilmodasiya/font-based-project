<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Readability Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!--For Indian languages-->
	<meta http-equiv="Content-article_types" content="text/html;charset=UTF-8">
	<!--Website Icon-->
    <link rel="shortcut icon" href="images/survey.ico">
	<!-- Bootstrap Core CSS-->
    <link href="Bootstrap/css/bootstrap.css" rel="stylesheet">
	<!-- Custom Core CSS-->
    <link href="Bootstrap/css/custom.css" rel="stylesheet">
    <script src="Bootstrap/js/respond.min.js"></script>
	<!--FusionCharts-->
	<SCRIPT LANGUAGE="Javascript" SRC="FusionChartsFree/Code/FusionCharts/FusionCharts.js"></SCRIPT>
</head>
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
<?php
	include("include/db_connect.php");
	session_start();
	//Fusion Charts
	include("FusionChartsFree/Code/PHP/Includes/FusionCharts.php");
	
	//////////FOR SECURITY/////////////////////
	//check for direct entry
	if(!isset($_SESSION['email'])){
		header("Location:index.php");
	}
	else{
		$sql = "SELECT * FROM main where email='".$_SESSION['email']."'";
		$result = mysql_query($sql);
		$row = mysql_fetch_array($result);
		$_SESSION['uid'] = $row['user_id'];
	}
	if(isset($_POST['add_admin'])){
		//retrieving add new admin form variables
		$email = $_POST["email"];
		$password = $_POST["password"];
		$repassword = $_POST["repassword"];
		
		if($password != $repassword){
			$error = "Passwords doesn't match. Try again.";
		}
		else{
			$sql = "SELECT email FROM admins where email='".$email."'";
			$result =  mysql_query($sql);
			$row = mysql_fetch_array($result);
			if($row['email'] == $email){
				$error = "This email already exists, try something else.";
			}
			else{
				$sql = "INSERT INTO admins(`email`, `password`) VALUES('$email', '$password')";
				if(!$result = mysql_query($sql)){
					$error = "Error occured while adding new Admin.";
				}
			}
		}
	}
	else{
		$sql = "SELECT * FROM admins where email='".$_SESSION['email']."'";
		$result=  mysql_query($sql);
		$num=mysql_num_rows($result);
		$row=mysql_fetch_array($result);
			
		if(isset($_SESSION['password'])){
			if($_SESSION['password'] != $row['password'])
				header("Location:index.php");
		}
		else{
			session_destroy();
			header("Location:index.php");
		}
	}	
	
	//Editing and para deletion Here
	if(isset($_POST['para_yes'])){//if whole para has to be deleted
		$pid = $_POST['para_id'];
		
		$sql = "DELETE FROM paragraphs WHERE pid='".$pid."'";
		if(!mysql_query($sql)){			
			$error = "Paragraph cannot be deleted !";
		}
		else{
			$sql = "DELETE FROM questions WHERE pid='".$pid."'";
			if(!mysql_query($sql)){
				$error = "Paragraph's QUESTIONS cannot be deleted !";
			}
			else{
				$query = "SELECT tid FROM test_data WHERE pid = '".$pid."'";
				mysql_query("SET NAMES utf8");
				$result = mysql_query($query);

				while($row = mysql_fetch_array($result)){
					$query1 = "DELETE FROM test_questions_data WHERE tid = '".$row['tid']."'";
					if(!mysql_query($query1)){
						$error = "Unable to delete para test_question_data!";
						break;
					}
				}
				$sql = "DELETE FROM test_data WHERE pid='".$pid."'";
				if(!mysql_query($sql)){
					$error = "Unable to delete para test_data!";
				}
				else{
					$success = "Successfully deleted paragraph and its related data !";
				}
			}
		}
	}
	else if(isset($_POST['ques_yes'])){
		$pid = $_POST['para_id'];
		$qid = $_POST['ques_id'];
		
		//echo "<script type='text/javascript'>alert('pid = ".$pid." qid = ".$qid."');</script>";
		$sql = "DELETE FROM questions WHERE qid='".$qid."'";
		if(!mysql_query($sql)){
			$error = "Unable to delete question !";
		}
		else{
			$sql = "DELETE FROM test_questions_data WHERE qid='".$qid."'";
			if(!mysql_query($sql)){
				$error = "Unable to delete question !";
			}
			else{
				$success = "Question successfully deleted.";
			}
		}
	}
	else{//Updation here
		if(isset($_POST['finish_edit'])){
			//updating article type
			$sql = "UPDATE paragraphs SET article_type='".$_POST['article']."' WHERE pid='".$_POST['para_id']."'";
			if(!mysql_query($sql)){
				$error = "Unable to update article type.";
			}
			//echo "<script type='text/javascript'>alert('para".$_POST['para_id']."');</script>";
			$pid = $_POST['para_id'];
			$para_content = $_POST['para'.$pid];
			//echo "<script type='text/javascript'>alert('".$para_content."');</script>";
			
			//updating para content
			$sql = "UPDATE paragraphs SET para='".$para_content."' WHERE pid='".$_POST['para_id']."'";
			if(!mysql_query($sql)){
				$error = "Unable to update paragraph content.";
			}
			else{
				//selecting all questions thru pid of this paragraph
				$query = "SELECT * FROM questions WHERE `pid` = '".$_POST['para_id']."'";
				mysql_query("SET NAMES utf8");
				$result = mysql_query($query);

				//echo "<script type='text/javascript'>alert('".$row3['qid']."');</script>";
				while($row = mysql_fetch_array($result)){
					//updating question content
					$sql = "UPDATE questions SET ques='".$_POST["ques".$row['qid']]."' WHERE qid='".$row['qid']."'";
					mysql_query($sql);
					//option updation for objective questions only as option updation is not required for subjective questions
					if($row['multi_correct'] != "0000"){
						$sql = "UPDATE questions SET opt1='".$_POST["opt1".$row['qid']]."', opt2='".$_POST["opt2".$row['qid']]."', opt3='".$_POST["opt3".$row['qid']]."', opt4='".$_POST["opt4".$row['qid']]."' WHERE qid='".$row['qid']."'";
						if(!mysql_query($sql)){
							$error = "Unable to update paragraph's question's option content.";
						}
						else{
							$success = "Successfully Updated everything";
						}
					}				
				}
			}	
		}
	}

	$article_types = array('NCERT Text', 'Wikipedia Page');
	$view_count = array('NCERT Text' => '0', 'Wikipedia Page'=>'0');
	$male_view_count = array('NCERT Text' => '0', 'Wikipedia Page'=>'0');
	$female_view_count = array('NCERT Text' => '0', 'Wikipedia Page'=>'0');
	
	$font_style = array('Arial', 'Times New Roman', 'verdana', 'georgia');
	$font_size = array('12','14','16');
	// $line_height = array('20-24','25-29','30-34','35-39','40-44','45-50');
	$line_height = array('1','2');
	// $word_spacing = array('0-3','4-7','8-11','12-15','16-20');
	$word_spacing = array('1','2');
	
	$net_view_count = 0;
	$net_male_view_count = 0;
	$net_female_view_count = 0;
	
    for($i = 0; $i <= 1; $i++){
		$query = "SELECT * FROM paragraphs Where article_type = '$article_types[$i]'";
	    $result = mysql_query($query);
	    $numofrow = mysql_num_rows($result);
		
		//calculating male and female view counts for each article type
		while($row=mysql_fetch_array($result)){
		    $var1=$row['pid'];
			$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
		    $result1=mysql_query($query1);
			$view_count[$article_types[$i]]+=  mysql_num_rows($result1);
			
			while($row1=mysql_fetch_array($result1)){
				$var2=$row1['uid'];
				$query2="SELECT * FROM main Where `user_id`='$var2'"; 
				$result2=mysql_query($query2);
				$row2=mysql_fetch_array($result2);
				
				if($row2['gender']=='1'){
					$male_view_count[$article_types[$i]]++;
				}
				else{
					$female_view_count[$article_types[$i]]++;
				}
			}
		}
	    $net_view_count+=$view_count[$article_types[$i]];
		$net_male_view_count+=$male_view_count[$article_types[$i]];
		$net_female_view_count+=$female_view_count[$article_types[$i]];
	}
       
	//number of paragraphs
	$query="SELECT MAX(pid) FROM paragraphs";
	$result=mysql_query($query);
	$numofpara=0;
	while($row=mysql_fetch_array($result)){
		$numofpara= $row[0];
	}
?>

<body>
	<div class="container">
		<!--Upper Section with heading, date, etc-->
		<div class="row well">
			<div class="col-md-4 col-lg-4" align="center">
				Hello Admin <?php echo stripslashes($_SESSION['email'])."<br/><br/>";?>
				<a href="index.php" class="btn btn-danger btn-lg" type="button">Sign Out</a>
				<!-- Button trigger modal -->
				<button class="btn btn-warning btn-lg" data-toggle="modal" data-target="#basicModal">
				  Add another Admin
				</button>
				<!-- Modal -->
				<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
								<h2 class="modal-title" id="myModalLabel">Readability Survey Add a new Admin</h2>
							</div>
							<div class="modal-body">
								<form class="form-signin" role="form" method="POST" action="analytics.php" enctype="multipart/form-data">
									<input name="email" type="email" class="form-control" placeholder="Email address" required/>
									<input name="password" type="password" class="form-control" placeholder="Password" required/>
									<input name="repassword" type="password" class="form-control" placeholder="Re-enter password" required/>
									<input name="add_admin" class="btn btn-lg btn-primary btn-block" type="submit" href="analytics.php" value="Add admin"/>	
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-4 col-lg-4">
				<div class="page-header"><h2 align="center">Readability Survey Analytics</h2></div>
			</div>
			
			<div class="col-md-4 col-lg-4" align="center">
				<?php echo date("jS \of F Y [l]", time())."<br/><br/>";?>
				<a href="admin.php" class="btn btn-primary btn-lg" type="button">Add a new Paragraph</a>
				<a href="home.php" class="btn btn-primary btn-lg" type="button">Give a Test</a>
			</div>
		</div>
		
		<!--Error AND Success Printing-->
		<?php
			if(isset($error))
			{
				echo "<div class='alert alert-danger' align='center' id='status-box'>";
					echo $error;
				echo "</div>";	
			}
			if(isset($success))
			{
				echo "<div class='alert alert-success' align='center' id='status-box'>";
					echo $success;
				echo "</div>";	
			}
		?>
		
		<!-- Nav tabs -->
		<div class="well">		
			<ul class="nav nav-tabs nav-justified" role="tablist">
				<li class="active in"><a href="#home" role="tab" data-toggle="tab">Home</a></li>
				<li><a href="#ncert" role="tab" data-toggle="tab">Ncert Text</a></li>
				<li><a href="#wiki" role="tab" data-toggle="tab">Wikipedia Pages</a></li>
			</ul>
			
			<!-- Tab panes -->
			<div class="tab-content">
				<!--Home Tab-->
				<div class="tab-pane fade active in" id="home">
					<?php
						echo "<div align=center>
							<h2><small>
								Total tests done yet - ".$net_view_count."<br/>{ M - ". $net_male_view_count.", F - ". $net_female_view_count." }
							</small></h2>
						</div>";
					
						//font style variables
						$font_style_count = array('0','0','0','0');
						$font_style_male = array('0','0','0','0');
						$font_style_female = array('0','0','0','0');
						$font_style_reading_time = array('0','0','0','0');
						$font_style_test_time = array('0','0','0','0');
						
						//font size variables
						$font_size_count = array('0', '0', '0');
						$font_size_male = array('0','0','0');
						$font_size_female = array('0','0','0');
						$font_size_reading_time = array('0','0','0');
						$font_size_test_time = array('0','0','0');
						
						//Line Height variables
						$line_height_count = array('0', '0');
						$line_height_male = array('0','0');
						$line_height_female = array('0','0');
						$line_height_reading_time = array('0','0');
						$line_height_test_time = array('0','0');
						
						//Word Spacing variables
						$word_spacing_count = array('0', '0');
						$word_spacing_male = array('0','0');
						$word_spacing_female = array('0','0');
						$word_spacing_reading_time = array('0','0');
						$word_spacing_test_time = array('0','0');
					
						$query= "SELECT MAX(tid)  FROM test_data";
						$result=mysql_query($query);
						$row=mysql_fetch_array($result);
						$max=$row[0];
						
						//Data calculation for home tab
						for($p = 1; $p <= $max; $p++){
							$query= "select * from test_data WHERE tid='$p'";
							$result=mysql_query($query);
							$row1=mysql_fetch_array($result);
						
							//CALCULATING HOME FONT 
							for($i = 0; $i < 4; $i++){
								if($row1['font'] == $font_style[$i]){
									$font_style_count[$i]++;
									$font_style_reading_time[$i]+=$row1['reading_time'];
									$font_style_test_time[$i]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$font_style_male[$i]++;
									}
									else{
										$font_style_female[$i]++;
									}
								}
							}
									
							//CALCULATION OF  home FONT Size
							for($i = 0; $i < 3; $i++){
								if($row1['size'] == $font_size[$i]){
									$font_size_count[$i]++;
									$font_size_reading_time[$i]+=$row1['reading_time'];
									$font_size_test_time[$i]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$font_size_male[$i]++;
									}
									else{
										$font_size_female[$i]++;
									}
								}
							}

							//CALCULATION OF home LINE HEIGHT 
							for($i = 0; $i < 2; $i++){
								if($row1['line_height'] == $line_height[$i]){
									$line_height_count[$i]++;
									$line_height_reading_time[$i]+=$row1['reading_time'];
									$line_height_test_time[$i]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$line_height_male[$i]++;
									}
									else{
										$line_height_female[$i]++;
									}
								}
							}
												 
							//CALCULATION OF home WORD SPACING 
							for($i = 0; $i < 2; $i++){
								if($row1['word_spacing'] == $word_spacing[$i]){
									$word_spacing_count[$i]++;
									$word_spacing_reading_time[$i]+=$row1['reading_time'];
									$word_spacing_test_time[$i]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$word_spacing_male[$i]++;
									}
									else{
										$word_spacing_female[$i]++;
									}
								}
							}
						}
					?>
					<?php
					echo "<div class='row' align='center'>";
						echo "<div class='col-lg-6 col-md-12'>";
						/////////////////////CHART FOR FONT STYLE in home///////////
						if(array_sum($font_style_count)!=0){
							$strXML= "<graph caption='Tests given in different Font styles' subcaption='(Overall)' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
							echo "<table class='table table-bordered'>";
							echo "<tr>";
								echo "<td>Font Style</td>";
								echo "<td>Male views</td>";
								echo "<td>Female views</td>";
								echo "<td>Average Reading Time</td>";
								echo "<td>Average Test Time</td>";
							echo "</tr>";
							for($t=0;$t<4;$t++){
								echo "<tr>
								<td>".
									$font_style[$t].
								"</td>
								
								<td>".
									$font_style_male[$t].
								"</td>
								
								<td>".
									$font_style_female[$t].
								"</td>";
								if($font_style_count[$t]!=0){
									$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
									$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
									echo "<td>".
										$font_style_reading_time[$t].
									"</td>";
									
									echo "<td>".
										$font_style_test_time[$t].
									"</td>";
								}
								else{
									echo "<td>-</td>";
									echo "<td>-</td>";
								}
								$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
								echo "</tr>";
							}
							$strXML .= "</graph>";
							echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "home_font_style_chart", 500, 400);
							echo "</table>";
						}
						echo "</div>";
						
						echo "<div class='col-lg-6 col-md-12'>";
						/////////////////////CHART FOR FONT SIZE in home Article type///////////
						if(array_sum($font_size_count)!=0){
							$strXML= "<graph caption='Tests given in different Font sizes' subcaption='(Overall)' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
							echo "<table class='table table-bordered'>";
							echo "<tr>";
								echo "<td>Font Size Ranges</td>";
								echo "<td>Male views</td>";
								echo "<td>Female views</td>";
								echo "<td>Average Reading Time</td>";
								echo "<td>Average Test Time</td>";
							echo "</tr>";
							for($t=0;$t<3;$t++){
								echo "<tr>
								<td>".
									$font_size[$t].
								"</td>
								
								<td>".
									$font_size_male[$t].
								"</td>
								
								<td>".
									$font_size_female[$t].
								"</td>";
								if($font_size_count[$t]!=0){
									$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
									$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
									echo "<td>".
										$font_size_reading_time[$t].
									"</td>";
									
									echo "<td>".
										$font_size_test_time[$t].
									"</td>";
								}
								else{
									echo "<td>-</td>";
									echo "<td>-</td>";
								}
								$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
								echo "</tr>";
							}
							$strXML .= "</graph>";
							echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "home_size_chart", 500, 400);
							echo "</table>";
						}
						echo "</div>";
					echo "</div>";
					
					echo "<div class='row' align='center'>";
						echo "<div class='col-lg-6 col-md-12'>";
						/////////////////////CHART FOR Line Height in home Article type///////////
						if(array_sum($line_height_count)!=0){
							$strXML= "<graph caption='Tests given in different Line heights' subcaption='(Overall)' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
							echo "<table class='table table-bordered'>";
							echo "<tr>";
								echo "<td>Line Height Ranges</td>";
								echo "<td>Male views</td>";
								echo "<td>Female views</td>";
								echo "<td>Average Reading Time</td>";
								echo "<td>Average Test Time</td>";
							echo "</tr>";
							for($t=0;$t<2;$t++){
								echo "<tr>
								<td>".
									$line_height[$t].
								"</td>
								
								<td>".
									$line_height_male[$t].
								"</td>
								
								<td>".
									$line_height_female[$t].
								"</td>";
								if($line_height_count[$t]!=0){
									$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
									$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
									echo "<td>".
										$line_height_reading_time[$t].
									"</td>";
									
									echo "<td>".
										$line_height_test_time[$t].
									"</td>";
								}
								else{
									echo "<td>-</td>";
									echo "<td>-</td>";
								}
								$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
							}
							$strXML .= "</graph>";
							echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "home_line_height_chart", 500, 400);
							echo "</table>";
						}
						echo "</div>";
						
						echo "<div class='col-lg-6 col-md-12'>";
						/////////////////////CHART FOR Word Spacing in home Article type///////////
						if(array_sum($word_spacing_count)!=0){
							$strXML= "<graph caption='Tests given in different word spacing' subcaption='(Overall)' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
							echo "<table class='table table-bordered'>";
							echo "<tr>";
								echo "<td>Word Spacing Ranges</td>";
								echo "<td>Male views</td>";
								echo "<td>Female views</td>";
								echo "<td>Average Reading Time</td>";
								echo "<td>Average Test Time</td>";
							echo "</tr>";
							for($t=0;$t<2;$t++){
								echo "<tr>
								<td>".
									$word_spacing[$t].
								"</td>
								
								<td>".
									$word_spacing_male[$t].
								"</td>
								
								<td>".
									$word_spacing_female[$t].
								"</td>";
								if($word_spacing_count[$t]!=0){
									$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
									$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
									echo "<td>".
										$word_spacing_reading_time[$t].
									"</td>";
									
									echo "<td>".
										$word_spacing_test_time[$t].
									"</td>";
								}
								else{
									echo "<td>-</td>";
									echo "<td>-</td>";
								}
								$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
							}
							$strXML .= "</graph>";
		
							echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "home_word_spacing_chart", 500, 400);
							echo "</table>";
						}
						echo "</div>";
					echo "</div>";	
					?>
				</div>

				
				<!--Ncert Tab-->
				<div class="tab-pane fade" id="ncert">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['NCERT Text']."<br/>{ M - ". $male_view_count['NCERT Text'].", F - ". $female_view_count['NCERT Text']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0');
					$font_style_male = array('0','0','0','0');
					$font_style_female = array('0','0','0','0');
					$font_style_reading_time = array('0','0','0','0');
					$font_style_test_time = array('0','0','0','0');
					
					//font size counts
					$font_size_count = array('0', '0', '0');
					$font_size_male = array('0','0','0');
					$font_size_female = array('0','0','0');
					$font_size_reading_time = array('0','0','0');
					$font_size_test_time = array('0','0','0');
					
					//Line Height counts
					$line_height_count = array('0', '0');
					$line_height_male = array('0','0');
					$line_height_female = array('0','0');
					$line_height_reading_time = array('0','0');
					$line_height_test_time = array('0','0');
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0');
					$word_spacing_male = array('0','0');
					$word_spacing_female = array('0','0');
					$word_spacing_reading_time = array('0','0');
					$word_spacing_test_time = array('0','0');
					
					//$i is to be used as counter for paragraphs of NCERT type
					$i = 1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='NCERT Text'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0');
						$para_font_style_male = array('0','0','0','0');
						$para_font_style_female = array('0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0');
						$para_font_size_male = array('0','0','0');
						$para_font_size_female = array('0','0','0');
						$para_font_size_reading_time = array('0','0','0');
						$para_font_size_test_time = array('0','0','0');
										
						$para_line_height_count = array('0', '0');
						$para_line_height_male = array('0','0');
						$para_line_height_female = array('0','0');
						$para_line_height_reading_time = array('0','0');
						$para_line_height_test_time = array('0','0');
										
						$para_word_spacing_count = array('0', '0');
						$para_word_spacing_male = array('0','0');
						$para_word_spacing_female = array('0','0');
						$para_word_spacing_reading_time = array('0','0');
						$para_word_spacing_test_time = array('0','0');
										
						$var1=$row['pid'];
						$query1="SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1=mysql_query($query1);
						$totalncertviewers=  mysql_num_rows($result1); 
						$ncertmale=0;
						$ncertfemale=0;
						
						//whole calculation of ncert documents goes in this loop
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender']=='1'){
								$ncertmale++;
							}
							else{
								$ncertfemale++;
							}
							//CALCULATION OF  ncert DOCUMENT FONT STYLE
							for($j = 0; $j < 4; $j++){
								if($row1['font'] == $font_style[$j]){
									$font_style_count[$j]++;
									$font_style_reading_time[$j]+=$row1['reading_time'];
									$font_style_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$font_style_male[$j]++;
									}
									else{
										$font_style_female[$j]++;
									}
								}
							}
							//CALCULATION OF NCERT FONT Size
							for($j = 0; $j < 3; $j++){
								if($row1['size'] == $font_size[$j]){
									$font_size_count[$j]++;
									$font_size_reading_time[$j]+=$row1['reading_time'];
									$font_size_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$font_size_male[$j]++;
									}
									else{
										$font_size_female[$j]++;
									}
								}
							}

							//CALCULATION OF NCERT LINE HEIGHT 
							for($j = 0; $j < 2; $j++){
								if($row1['line_height'] == $line_height[$j]){
									$line_height_count[$j]++;
									$line_height_reading_time[$j]+=$row1['reading_time'];
									$line_height_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$line_height_male[$j]++;
									}
									else{
										$line_height_female[$j]++;
									}
								}
							}
												 
							//CALCULATION OF NCERT WORD SPACING 
							for($j = 0; $j < 2; $j++){
								if($row1['word_spacing'] == $word_spacing[$j]){
									$word_spacing_count[$j]++;
									$word_spacing_reading_time[$j]+=$row1['reading_time'];
									$word_spacing_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$word_spacing_male[$j]++;
									}
									else{
										$word_spacing_female[$j]++;
									}
								}
							}
						}
						//NCERT paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='ncert_para_panel".$i."'>
										<div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'>
											<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#ncert_para".$i."' data-parent='#ncert_para_panel".$i."'>
												<div class='para_info1'>
													Paragraph : ".$i."
												</div>
												<div class='para_info'>".
														$totalncertviewers."
														{ M - ".$ncertmale.",  F - ".$ncertfemale." }
												</div>
											</button>
										</div>
										
										<div class='row'>";
											//edit button for each para in ncert articles
											echo "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
												<button class='btn-warning btn-block btn-lg' data-toggle='modal' data-target='#edit_ncert_paragraph".$i."'>
													Edit
												</button>
											</div>";
											
											//delete button for each para in ncert articles
											echo "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
												<button class='btn-danger btn-block btn-lg' data-toggle='modal' data-target='#delete_ncert_paragraph".$i."'>
													Delete
												</button>";
												//confirmation Modal for para deletion
												echo "<div class='modal fade' id='delete_ncert_paragraph".$i."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
													<div class='modal-dialog'>
														<div class='modal-content'>
															<div class='modal-header'>
																<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
																<h4 class='modal-title' id='myModalLabel'>Are you sure, you want to delete this paragraph !</h4>
															</div>
															<div class='modal-body'>
																<form class='form-signin' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																	<input name='para_id' id='para_id' value='".$row['pid']."' type='hidden'/>
																	<input name='para_yes' class='btn btn-danger' type='submit' href='analytics.php' value='Yes'/>
																	<input name='cancel' type='button' class='btn btn-primary' data-dismiss='modal' value='No'/>
																</form>
															</div>
														</div>
													</div>
												</div>";
												//Modal ends here
											echo "</div>";
											
											//MODAL for the edit button
											echo "<div class='modal fade' id='edit_ncert_paragraph".$i."'>
												<div class='modal-dialog  modal-lg'>
													<div class='modal-content'>";
														//Edit-modal heading
														echo "<div class='modal-header'>
															<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
															<h2 class='modal-title' id='myModalLabel'>Edit contents of NCERT paragraph : ".$i."</h2>
														</div>";
														
														//edit-modal body
														echo "<div class='modal-body'>";
															//Edit NCERT Para Form
															echo "<form class='form' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																<div class='article form-group'>
																	<label>Article Type</label>
																	<select name='article' class='data_class'>
																		<option name='ncert' selected='selected'>NCERT Text</option>
																		<option name='wiki'>Wikipedia Page</option>
																	</select>
																</div>";

																//Storing max qid so as we can add further questions successfully
																$query3 = "SELECT MAX(qid) AS max FROM questions";
																$result3 = mysql_query($query3);
																$row3 = mysql_fetch_array($result3);
																echo "<input name='max_ques_id' id='max_ques_id' type='hidden' value='".$row3['max']."'/>";

																//Para content
																echo "<div class='form-group'>
																	<label>Content</label>
																	<br/>
																	<textarea name='para".$row['pid']."' class='edit_content_class'>".
																		$row['para'];
																	echo "</textarea>
																</div>";
																
																//Tabs for editing each Ncert para's obj and subj ques
																echo "<div class='form-group'>
																	<ul class='nav nav-tabs nav-justified' role='tablist'>
																		<li class='active in'><a href='#edit_ncert_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
																		<li><a href='#edit_ncert_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
																	</ul>";
																	
																	//TAB CONTENTS - obj then sub
																	echo "<div class='tab-content'>";
																		//ncert-edit-obj_ques tab
																		echo "<div class='tab-pane fade active in' id='edit_ncert_obj".$i."'>
																			<table class = 'ncert_obj_ques_table table table-hover table-bordered edit_ques_table' id='ncert_obj_ques_table".$row['pid']."'>
																				<tr align = 'center'>
																					<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
																					<td class = 'col-lg-5'><h4><big>Questions</big></h4></td>
																					<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
																					<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
																					<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
																					<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
																					<td class = 'col-lg-2'><h6><big>Delete</big></h6></td>
																				</tr>";

																				$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
																				mysql_query("SET NAMES utf8");
																				$result3 = mysql_query($query3);
													
																				$j = 1;
																				while($row3 = mysql_fetch_array($result3)){
																					echo "<tr align = 'center'>";
																						//S.No. of questions
																						echo "<td>".
																							$j.
																						"</td>";
																													
																						//Objective Questions
																						echo "<td>".
																							"<textarea name='ques".$row3['qid']."' class='edit_ques_class' type='text'>".
																								$row3['ques'].
																							"</textarea>
																						</td>";
																						
																						$j++;
																													
																						
																						//opt1
																						echo "<td>".
																							"<textarea name='opt1".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt1'];
																							echo "</textarea>
																						</td>";
																												
																						//opt2
																						echo "<td>".
																							"<textarea name='opt2".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt2'];
																							echo "</textarea>
																						</td>";
																											
																						//opt3
																						echo "<td>".
																							"<textarea name='opt3".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt3'];
																							echo "</textarea>
																						</td>";
																												
																						//opt4
																						echo "<td>".
																							"<textarea name='opt4".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt4'];
																							echo "</textarea>
																						</td>";
																													
																						//delete btn
																						echo "<td>
																							<input id='delete_ques".$row3['qid']."' class='delete_ques btn btn-danger btn-lg cancel_ques_btn_class' type='button' data-toggle='modal' data-target='#delete_ques_modal".$row3['qid']."' value='Delete' />";
																							//confirmation Modal
																							echo "<div class='modal fade' id='delete_ques_modal".$row3['qid']."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
																								<div class='modal-dialog'>
																									<div class='modal-content'>
																										<div class='modal-header'>
																											<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
																											<h4 class='modal-title' id='myModalLabel'>Are you sure, you want to delete this question !</h4>
																										</div>
																										<div class='modal-body'>
																											<form class='form-signin' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																												<input name='para_id' id='ques_id' value='".$row['pid']."' type='hidden'/>
																												<input name='ques_id' id='ques_id' value='".$row3['qid']."' type='hidden'/>
																												<input name='ques_yes' class='btn btn-danger' type='submit' href='analytics.php' value='Yes'/>
																												<input name='cancel' type='button' class='btn btn-primary' data-dismiss='modal' value='No'/>
																											</form>
																										</div>
																									</div>
																								</div>
																							</div>";
																							//Modal ends here
																						echo "</td>
																					</tr>";	
																				}
																				//div space to add a new question
																				echo "<div class='row ncert_add_ques_form_class' id='ncert_add_obj_ques_".$row['pid']."'></div>";
																			echo "</table>";
																			
																			//Add another objective question to this para by this btn
																			// echo "<input name='ncert_add_para_btn' id='ncert_add_para_btn".$row['pid']."' class='btn btn-primary btn-lg' type='button' value='Add another objective question to this paragraph' onclick='add_ncert_question(this);'/>
																			// <input name='obj_ques_count' id='obj_ques_count' type='hidden' value='".$j."'/>
																		echo "</div>";//ncert-edit-obj_ques tab ENDS
																	
																		//ncert-edit-sub_ques tab
																		echo "<div class='tab-pane fade' id='edit_ncert_sub".$i."'>";
																				$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
																				mysql_query("SET NAMES utf8");
																				$result3 = mysql_query($query3);
																									
																				$j = 1;
																				echo "<table class = 'ncert_sub_ques_table' id='ncert_sub_ques_table' border = 'solid'>";
																					//subjective question Heading
																					echo "<tr align = 'center'>
																						<th class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>
																							S.No.
																						</th>
																						<th class='col-lg-10 col-md-10 col-sm-10 col-xs-10'>
																							Question Content
																						</th>
																						<th class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>
																							Delete
																						</th>
																					</tr>";
																				while($row3 = mysql_fetch_array($result3)){							
																					//subjective Questions S.no.
																					echo "<tr align = 'center'>
																						<td>".
																							$j.
																						"</td>";
																					
																						//subjective Questions
																						echo "<td>
																								<textarea name='ques".$row3['qid']."' class='edit_ques_class' type='text'>".
																									$row3['ques'].
																								"</textarea>
																							</td>";

																						//delete btn
																						echo "<td>
																							<input id='delete_ques".$row3['qid']."' class='delete_ques btn btn-danger btn-lg cancel_ques_btn_class' type='button' data-toggle='modal' data-target='#delete_ques_modal".$row3['qid']."' value='Delete' />";
																							//confirmation Modal
																							echo "<div class='modal fade' id='delete_ques_modal".$row3['qid']."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
																								<div class='modal-dialog'>
																									<div class='modal-content'>
																										<div class='modal-header'>
																											<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
																											<h4 class='modal-title' id='myModalLabel'>Are you sure, you want to delete this question !</h4>
																										</div>
																										<div class='modal-body'>
																											<form class='form-signin' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																												<input name='para_id' id='ques_id' value='".$row['pid']."' type='hidden'/>
																												<input name='ques_id' id='ques_id' value='".$row3['qid']."' type='hidden'/>
																												<input name='ques_yes' class='btn btn-danger' type='submit' href='analytics.php' value='Yes'/>
																												<input name='cancel' type='button' class='btn btn-primary' data-dismiss='modal' value='No'/>
																											</form>
																										</div>
																									</div>
																								</div>
																							</div>";
																							//Modal ends here
																						echo "</td>
																					</tr>";
																					$j++;
																				}
																				echo "</table>";
																		echo "</div>";//ncert-edit-sub_ques tab ENDS
																	echo "</div>";//TAB Contents end here
																echo "</div>";//TAB ending
																echo "<input name='finish_edit' id='finish_edit_btn' class='btn btn-lg btn-success' type='submit' value='Finish Edit'/>
																<input name='para_id' id='para_id' type='hidden' value='".$row['pid']."'/>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>	
										
										<div id='ncert_para".$i."' class='panel-collapse panel-body collapse'>";
											echo make_clickable ($row['para']);
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN ncert DOCUMENT///////////
											for($j = 0; $j < 4; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$j]."'";
												$para=mysql_query($query);
												$para_font_style_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$j]+=$row_para['reading_time'];
													$para_font_style_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$j]++;
													else
														$para_font_style_female[$j]++;  
										        }
											} 
											//////CALCULATION FOR CREATING CHART ON FONT SIZE For each para in NCERT Documents
											for($j = 0; $j < 3; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `size`='".$font_size[$j]."'";
												$para=mysql_query($query);
												$para_font_size_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_size_reading_time[$j]+=$row_para['reading_time'];
													$para_font_size_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_size_male[$j]++;
													else
														$para_font_size_female[$j]++;
										        }
											}

											//////CALCULATION FOR CREATING CHART ON Line Heights For each para in NCERT Documents
											for($j = 0; $j < 2; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `line_height`='".$line_height[$j]."'";
												$para=mysql_query($query);
												$para_line_height_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_line_height_reading_time[$j]+=$row_para['reading_time'];
													$para_line_height_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_line_height_male[$j]++;
													else
														$para_line_height_female[$j]++;
										        }
											}

											//////CALCULATION FOR CREATING CHART ON Word Spacing For each para in NCERT Documents
											for($j = 0; $j < 2; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `word_spacing`='".$word_spacing[$j]."'";
												$para=mysql_query($query);
												$para_word_spacing_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_word_spacing_reading_time[$j]+=$row_para['reading_time'];
													$para_word_spacing_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_word_spacing_male[$j]++;
													else
														$para_word_spacing_female[$j]++;
										        }
											}	
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#ncert_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#ncert_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#ncert_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='ncert_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='ncert_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>";
																	echo make_clickable ($row3['ques']);
																echo "</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo make_clickable ($row3['opt1'])."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo make_clickable ($row3['opt2'])."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo make_clickable ($row3['opt3'])."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo make_clickable ($row3['opt4'])."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='ncert_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='ncert_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>";
																			echo make_clickable ($row3['ques']).
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='ncert_graphs".$i."'>";
													echo "<div class='row' align='center'>";
														echo "<div class='col-lg-6 col-md-12'>";
														//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In ncert Article Type
														if(array_sum($para_font_style_count)!=0){
															$strXML= "<graph caption='Tests given in different Font Styles' subCaption='for Paragraph ".$i." with NCERT Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
															echo "<table class='table table-bordered'>";
															echo "<tr>";
																echo "<td>Font Style</td>";
																echo "<td>Male views</td>";
																echo "<td>Female views</td>";
																echo "<td>Average Reading Time</td>";
																echo "<td>Average Test Time</td>";
															echo "</tr>";
															for($t = 0; $t < 4; $t++){
																echo "<tr>
																<td>".
																	$font_style[$t].
																"</td>
																
																<td>".
																	$para_font_style_male[$t].
																"</td>
																
																<td>".
																	$para_font_style_female[$t].
																"</td>";
																if($para_font_style_count[$t]!=0){
																	$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																	$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																	echo "<td>".
																		$para_font_style_reading_time[$t].
																	"</td>";
																	
																	echo "<td>".
																		$para_font_style_test_time[$t].
																	"</td>";
																}
																else{
																	echo "<td>-</td>";
																	echo "<td>-</td>";
																}
																$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
																echo "</tr>";
															}
															$strXML .= "</graph>";
															echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_font_style_para_chart".$i, 500, 400);
															echo "</table>";
														}
														echo "</div>";
												
														echo "<div class='col-lg-6 col-md-12'>";
														//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In ncert Article Type
														if(array_sum($para_font_size_count)!=0){
															$strXML= "<graph caption='Tests given in different Font Sizes' subCaption='for Paragraph ".$i." with ncert Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
															echo "<table class='table table-bordered'>";
															echo "<tr>";
																echo "<td>Font Size Ranges</td>";
																echo "<td>Male views</td>";
																echo "<td>Female views</td>";
																echo "<td>Average Reading Time</td>";
																echo "<td>Average Test Time</td>";
															echo "</tr>";
															for($t = 0; $t < 3; $t++){
																echo "<tr>
																<td>".
																	$font_size[$t].
																"</td>
																
																<td>".
																	$para_font_size_male[$t].
																"</td>
																
																<td>".
																	$para_font_size_female[$t].
																"</td>";
																if($para_font_size_count[$t]!=0){
																	$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
																	$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
																	echo "<td>".
																		$para_font_size_reading_time[$t].
																	"</td>";
																	
																	echo "<td>".
																		$para_font_size_test_time[$t].
																	"</td>";
																}
																else{
																	echo "<td>-</td>";
																	echo "<td>-</td>";
																}
																$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
																echo "</tr>";				
															}
															$strXML .= "</graph>";
															echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_font_size_para_chart".$i, 500, 400);
															echo "</table>";
														}
														echo "</div>";
													echo "</div>";
											//CREATING A CHART OF LINE HEIGHT FOR EACH PARAGRAPH OF NCERT DOCUMENTS
											echo "<div class='row' align='center'>";
												echo "<div class='col-lg-6 col-md-12'>";
												if(array_sum($para_line_height_count)!=0){
													$strXML= "<graph caption='Tests given in different Line Heights' subCaption='for Paragraph ".$i." with ncert Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Line Height</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t = 0; $t < 2; $t++){
															echo "<tr>
															<td>".
																$line_height[$t].
															"</td>
															
															<td>".
																$para_line_height_male[$t].
															"</td>
															
															<td>".
																$para_line_height_female[$t].
															"</td>";
															if($para_line_height_count[$t]!=0){
																$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
																$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
																echo "<td>".
																	$para_line_height_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_line_height_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
														}
														$strXML .= "</graph>";
														echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_line_height_para_chart".$i, 500, 400);
														echo "</table>";
													}
											echo "</div>";
											
											echo "<div class='col-lg-6 col-md-12'>";
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='with ncert Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t = 0; $t < 2; $t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</div>";
											echo "</div>";	
											echo "</div>
												
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS FOR NCERT Texts||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR NCERT ARTICLE|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=ncert_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#ncert_graphs">
						Charts for NCERT Texts
					</button>';
		
					echo "<div id='ncert_graphs' class='collapse in row' align='center'>";
						echo "<div  class='row' align='center'>";
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR FONT STYLE in NCERT Article type///////////
							if(array_sum($font_style_count)!=0){
								$strXML= "<graph caption='Tests given in different Font styles' subcaption='with ncert Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Font Style</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 4; $t++){
									echo "<tr>
									<td>".
										$font_style[$t].
									"</td>
									
									<td>".
										$font_style_male[$t].
									"</td>
									
									<td>".
										$font_style_female[$t].
									"</td>";
									if($font_style_count[$t]!=0){
										$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
										$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
										echo "<td>".
											$font_style_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$font_style_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
									echo "</tr>";
								}
								$strXML .= "</graph>";
								echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "ncert_font_style_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
														
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR FONT SIZE in ncert Article type///////////
							if(array_sum($font_size_count)!=0){
								$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with ncert Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Font Size Ranges</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 3; $t++){
									echo "<tr>
									<td>".
										$font_size[$t].
									"</td>
									
									<td>".
										$font_size_male[$t].
									"</td>
									
									<td>".
										$font_size_female[$t].
									"</td>";
									if($font_size_count[$t]!=0){
										$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
										$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
										echo "<td>".
											$font_size_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$font_size_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
									echo "</tr>";
								}
								$strXML .= "</graph>";
								echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_size_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
							
						echo "</div>";
							
						echo "<div class='row' align='center'>";
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR Line Height in ncert Article type///////////
							if(array_sum($line_height_count)!=0){
								$strXML= "<graph caption='Tests given in different Line heights' subcaption='with ncert Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Line Height Ranges</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 2; $t++){
									echo "<tr>
									<td>".
										$line_height[$t].
									"</td>
									
									<td>".
										$line_height_male[$t].
									"</td>
									
									<td>".
										$line_height_female[$t].
									"</td>";
									if($line_height_count[$t]!=0){
										$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
										$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
										echo "<td>".
											$line_height_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$line_height_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
								}
								$strXML .= "</graph>";
								echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_line_height_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
							
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR Word Spacing in ncert Article type///////////
							if(array_sum($word_spacing_count)!=0){
								$strXML= "<graph caption='Tests given in different word spacing' subcaption='with ncert Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Word Spacing Ranges</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 2; $t++){
									echo "<tr>
									<td>".
										$word_spacing[$t].
									"</td>
									
									<td>".
										$word_spacing_male[$t].
									"</td>
									
									<td>".
										$word_spacing_female[$t].
									"</td>";
									if($word_spacing_count[$t]!=0){
										$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
										$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
										echo "<td>".
											$word_spacing_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$word_spacing_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
								}
								$strXML .= "</graph>";
			
								echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "ncert_word_spacing_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
						echo "</div>";	
					echo "</div>";	
					?>
				</div><!--End of Ncert Text Article-->
				
				
				<!--Wikipedia Page papers Tab-->
				<div class="tab-pane fade" id="wiki">
					<?php
					echo "<div align=center>
						<h2><small>
							Total tests done yet - ".$view_count['Wikipedia Page']."<br/>{ M - ". $male_view_count['Wikipedia Page'].", F - ". $female_view_count['Wikipedia Page']." }
						</small></h2>
					</div>";
						
					//font style counts
					$font_style_count = array('0','0','0','0');
					$font_style_male = array('0','0','0','0');
					$font_style_female = array('0','0','0','0');
					$font_style_reading_time = array('0','0','0','0');
					$font_style_test_time = array('0','0','0','0');
					
					
					//font size counts
					$font_size_count = array('0', '0', '0');
					$font_size_male = array('0','0','0');
					$font_size_female = array('0','0','0');
					$font_size_reading_time = array('0','0','0');
					$font_size_test_time = array('0','0','0');
					
					
					//Line Height counts
					$line_height_count = array('0', '0');
					$line_height_male = array('0','0');
					$line_height_female = array('0','0');
					$line_height_reading_time = array('0','0');
					$line_height_test_time = array('0','0');
					
					
					//Word Spacing counts
					$word_spacing_count = array('0', '0');
					$word_spacing_male = array('0','0');
					$word_spacing_female = array('0','0');
					$word_spacing_reading_time = array('0','0');
					$word_spacing_test_time = array('0','0');
						
					$i = 1;
					$query="SELECT * FROM paragraphs WHERE `article_type`='Wikipedia Page'";
					mysql_query("SET NAMES utf8");
					$result=mysql_query($query);
					
					while($row=mysql_fetch_array($result)){
						$para_font_style_count = array('0','0','0','0');
						$para_font_style_male = array('0','0','0','0');
						$para_font_style_female = array('0','0','0','0');
						$para_font_style_reading_time = array('0','0','0','0');
						$para_font_style_test_time = array('0','0','0','0');
										
						$para_font_size_count = array('0', '0', '0');
						$para_font_size_male = array('0','0','0');
						$para_font_size_female = array('0','0','0');
						$para_font_size_reading_time = array('0','0','0');
						$para_font_size_test_time = array('0','0','0');
										
						$para_line_height_count = array('0', '0');
						$para_line_height_male = array('0','0');
						$para_line_height_female = array('0','0');
						$para_line_height_reading_time = array('0','0');
						$para_line_height_test_time = array('0','0');
										
						$para_word_spacing_count = array('0', '0');
						$para_word_spacing_male = array('0','0');
						$para_word_spacing_female = array('0','0');
						$para_word_spacing_reading_time = array('0','0');
						$para_word_spacing_test_time = array('0','0');
										
						$var1 = $row['pid'];
						$query1 = "SELECT * FROM test_data Where `pid`='$var1'"; 
						$result1 = mysql_query($query1);
						$totalwikiviewers = mysql_num_rows($result1); 
						$wikimale = 0;
						$wikifemale = 0;
									
						//whole calculation of wiki documents goes in this loop			
						while($row1=mysql_fetch_array($result1)){
							$var2=$row1['uid'];
							$query2="SELECT * FROM main Where `user_id`='$var2'"; 
							$result2=mysql_query($query2);
							$row2=mysql_fetch_array($result2);
									
							if($row2['gender'] == '1'){
								$wikimale++;
							}
							else{
								$wikifemale++;
							}

							//CALCULATION OF Wiki FONT Style
							for($j = 0; $j < 4; $j++){
								if($row1['font'] == $font_style[$j]){
									$font_style_count[$j]++;
									$font_style_reading_time[$j]+=$row1['reading_time'];
									$font_style_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$font_style_male[$j]++;
									}
									else{
										$font_style_female[$j]++;
									}
								}
							}
							
							//CALCULATION OF Wiki FONT Size
							for($j = 0; $j < 3; $j++){
								if($row1['size'] == $font_size[$j]){
									$font_size_count[$j]++;
									$font_size_reading_time[$j]+=$row1['reading_time'];
									$font_size_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$font_size_male[$j]++;
									}
									else{
										$font_size_female[$j]++;
									}
								}
							}

							//CALCULATION OF Wiki LINE HEIGHT 
							for($j = 0; $j < 2; $j++){
								if($row1['line_height'] == $line_height[$j]){
									$line_height_count[$j]++;
									$line_height_reading_time[$j]+=$row1['reading_time'];
									$line_height_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$line_height_male[$j]++;
									}
									else{
										$line_height_female[$j]++;
									}
								}
							}
												 
							//CALCULATION OF Wiki WORD SPACING 
							for($j = 0; $j < 2; $j++){
								if($row1['word_spacing'] == $word_spacing[$j]){
									$word_spacing_count[$j]++;
									$word_spacing_reading_time[$j]+=$row1['reading_time'];
									$word_spacing_test_time[$j]+=$row1['test_time'];
									
									$query3="SELECT * FROM main Where `user_id`='".$row1['uid']."'"; 
									$result3=mysql_query($query3);
									$row3=mysql_fetch_array($result3);
										
									if($row3['gender']=='1'){
										$word_spacing_male[$j]++;
									}
									else{
										$word_spacing_female[$j]++;
									}
								}
							}
						}
						//WIKI paragraphs table
						echo "<table class='table table-bordered'>";
							echo "<tr>
								<td>";
								//para_panel
									echo "<div class='para_panel' id='wiki_para_panel".$i."'>
										<div class='col-lg-8 col-md-8 col-sm-8 col-xs-8'>
											<button class='btn-primary btn-block btn-lg' data-toggle='collapse' data-target='#wiki_para".$i."' data-parent='#wiki_para_panel".$i."'>
												<div class='para_info1'>
													Paragraph : ".$i."
												</div>
												<div class='para_info'>".
														$totalwikiviewers."
														{ M - ".$wikimale.",  F - ".$wikifemale." }
												</div>
											</button>
										</div>";
										
										echo "<div class='row'>";
											//edit button for each para in WIKI articles
											echo "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
												<button class='btn-warning btn-block btn-lg' data-toggle='modal' data-target='#edit_wiki_paragraph".$i."'>
													Edit
												</button>
											</div>";
											
											//delete button for each para in wiki articles
											echo "<div class='col-lg-2 col-md-2 col-sm-2 col-xs-2'>
												<button class='btn-danger btn-block btn-lg' data-toggle='modal' data-target='#delete_wiki_paragraph".$i."'>
													Delete
												</button>";
												//confirmation Modal for para deletion
												echo "<div class='modal fade' id='delete_wiki_paragraph".$i."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
													<div class='modal-dialog'>
														<div class='modal-content'>
															<div class='modal-header'>
																<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
																<h4 class='modal-title' id='myModalLabel'>Are you sure, you want to delete this paragraph !</h4>
															</div>
															<div class='modal-body'>
																<form class='form-signin' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																	<input name='para_id' id='para_id' value='".$row['pid']."' type='hidden'/>
																	<input name='para_yes' class='btn btn-danger' type='submit' href='analytics.php' value='Yes'/>
																	<input name='cancel' type='button' class='btn btn-primary' data-dismiss='modal' value='No'/>
																</form>
															</div>
														</div>
													</div>
												</div>";
												//Modal ends here
											echo "</div>";
											
											//MODAL for the edit button
											echo "<div class='modal fade' id='edit_wiki_paragraph".$i."'>
												<div class='modal-dialog  modal-lg'>
													<div class='modal-content'>";
														//Edit-modal heading
														echo "<div class='modal-header'>
															<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
															<h2 class='modal-title' id='myModalLabel'>Edit contents of Wikipedia paragraph : ".$i."</h2>
														</div>";
														
														//edit-modal body
														echo "<div class='modal-body'>";
															//Edit WIKI Para Form
															echo "<form class='form' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																<div class='article form-group'>
																	<label>Article Type</label>
																	<select name='article' class='data_class'>
																		<option name='ncert'>NCERT Text</option>
																		<option name='wiki' selected='selected'>Wikipedia Page</option>
																	</select>
																</div>";

																//Para content
																echo "<div class='form-group'>
																	<label>Content</label>
																	<br/>
																	<textarea name='para".$row['pid']."' class='edit_content_class'>".
																		$row['para'];
																	echo "</textarea>
																</div>";
																
																//Tabs for editing each Wiki para's obj and subj ques
																echo "<div class='form-group'>
																	<ul class='nav nav-tabs nav-justified' role='tablist'>
																		<li class='active in'><a href='#edit_wiki_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
																		<li><a href='#edit_wiki_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
																	</ul>";
																	
																	//TAB CONTENTS - obj then sub
																	echo "<div class='tab-content'>";
																		//wiki-edit-obj_ques tab
																		echo "<div class='tab-pane fade active in' id='edit_wiki_obj".$i."'>
																			<table class = 'wiki_obj_ques_table table table-hover table-bordered edit_ques_table' id='wiki_obj_ques_table'>
																				<tr align = 'center'>
																					<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
																					<td class = 'col-lg-5'><h4><big>Questions</big></h4></td>
																					<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
																					<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
																					<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
																					<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
																					<td class = 'col-lg-2'><h6><big>Delete</big></h6></td>
																				</tr>";

																				$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
																				mysql_query("SET NAMES utf8");
																				$result3 = mysql_query($query3);
													
																				$j = 1;
																				while($row3 = mysql_fetch_array($result3)){
																					echo "<tr align = 'center'>";
																						//S.No. of questions
																						echo "<td>".
																							$j.
																						"</td>";
																													
																						//Objective Questions
																						echo "<td>".
																							"<textarea name='ques".$row3['qid']."' class='edit_ques_class' type='text'>".
																								$row3['ques'].
																							"</textarea>
																						</td>";
																						
																						$j++;
																													
																						
																						//opt1
																						echo "<td>".
																							"<textarea name='opt1".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt1'];
																							echo "</textarea>
																						</td>";
																												
																						//opt2
																						echo "<td>".
																							"<textarea name='opt2".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt2'];
																							echo "</textarea>
																						</td>";
																											
																						//opt3
																						echo "<td>".
																							"<textarea name='opt3".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt3'];
																							echo "</textarea>
																						</td>";
																												
																						//opt4
																						echo "<td>".
																							"<textarea name='opt4".$row3['qid']."' class='edit_ques_class' type='text'>";
																								echo $row3['opt4'];
																							echo "</textarea>
																						</td>";
																													
																						//delete btn
																						echo "<td>
																							<input id='delete_ques".$row3['qid']."' class='delete_ques btn btn-danger btn-lg cancel_ques_btn_class' type='button' data-toggle='modal' data-target='#delete_ques_modal".$row3['qid']."' value='Delete' />";
																							//confirmation Modal
																							echo "<div class='modal fade' id='delete_ques_modal".$row3['qid']."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
																								<div class='modal-dialog'>
																									<div class='modal-content'>
																										<div class='modal-header'>
																											<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
																											<h4 class='modal-title' id='myModalLabel'>Are you sure, you want to delete this question !</h4>
																										</div>
																										<div class='modal-body'>
																											<form class='form-signin' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																												<input name='para_id' id='ques_id' value='".$row['pid']."' type='hidden'/>
																												<input name='ques_id' id='ques_id' value='".$row3['qid']."' type='hidden'/>
																												<input name='ques_yes' class='btn btn-danger' type='submit' href='analytics.php' value='Yes'/>
																												<input name='cancel' type='button' class='btn btn-primary' data-dismiss='modal' value='No'/>
																											</form>
																										</div>
																									</div>
																								</div>
																							</div>";
																							//Modal ends here
																						echo "</td>
																					</tr>";	
																				}
																			echo "</table>
																		</div>";//wiki-edit-obj_ques tab ENDS
																	
																		//wiki-edit-sub_ques tab
																		echo "<div class='tab-pane fade' id='edit_wiki_sub".$i."'>";
																				$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
																				mysql_query("SET NAMES utf8");
																				$result3 = mysql_query($query3);
																									
																				$j = 1;
																				echo "<table class = 'wiki_sub_ques_table' id='wiki_sub_ques_table' border = 'solid'>";
																					//subjective question Heading
																					echo "<tr align = 'center'>
																						<th class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>
																							S.No.
																						</th>
																						<th class='col-lg-10 col-md-10 col-sm-10 col-xs-10'>
																							Question Content
																						</th>
																						<th class='col-lg-1 col-md-1 col-sm-1 col-xs-1'>
																							Delete
																						</th>
																					</tr>";
																				while($row3 = mysql_fetch_array($result3)){							
																					//subjective Questions S.no.
																					echo "<tr align = 'center'>
																						<td>".
																							$j.
																						"</td>";
																					
																						//subjective Questions
																						echo "<td>
																								<textarea name='ques".$row3['qid']."' class='edit_ques_class' type='text'>".
																									$row3['ques'].
																								"</textarea>
																							</td>";

																						//delete btn
																						echo "<td>
																							<input id='delete_ques".$row3['qid']."' class='delete_ques btn btn-danger btn-lg cancel_ques_btn_class' type='button' data-toggle='modal' data-target='#delete_ques_modal".$row3['qid']."' value='Delete' />";
																							//confirmation Modal
																							echo "<div class='modal fade' id='delete_ques_modal".$row3['qid']."' tabindex='-1' role='dialog' aria-labelledby='basicModal' aria-hidden='true'>
																								<div class='modal-dialog'>
																									<div class='modal-content'>
																										<div class='modal-header'>
																											<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>x</button>
																											<h4 class='modal-title' id='myModalLabel'>Are you sure, you want to delete this question !</h4>
																										</div>
																										<div class='modal-body'>
																											<form class='form-signin' role='form' method='POST' action='analytics.php' enctype='multipart/form-data'>
																												<input name='para_id' id='ques_id' value='".$row['pid']."' type='hidden'/>
																												<input name='ques_id' id='ques_id' value='".$row3['qid']."' type='hidden'/>
																												<input name='ques_yes' class='btn btn-danger' type='submit' href='analytics.php' value='Yes'/>
																												<input name='cancel' type='button' class='btn btn-primary' data-dismiss='modal' value='No'/>
																											</form>
																										</div>
																									</div>
																								</div>
																							</div>";
																							//Modal ends here
																						echo "</td>
																					</tr>";
																					$j++;
																				}
																				echo "</table>";
																		echo "</div>";//wiki-edit-sub_ques tab ENDS
																	echo "</div>";//TAB Contents end here
																echo "</div>";//TAB ending
																//hidden input containing qid of ques whose delete btn was clicked and single delete handler (delete_ques in js section at bottom) for all questions
																//echo "<input name='ques_id' id='ques_id' type='hidden'/>";
																echo "<input name='finish_edit' id='finish_edit_btn' class='btn btn-lg btn-success' type='submit' value='Finish Edit'/>
																<input name='para_id' id='para_id' type='hidden' value='".$row['pid']."'/>
															</form>
														</div>
													</div>
												</div>
											</div>
										</div>";
										
										echo "<div id='wiki_para".$i."' class='panel-collapse panel-body collapse'>";
											echo make_clickable ($row['para']);
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//////////////CALCULATION FOR CREATING CHART ON FONT SYTLE FOR EACH PARAGRAPH IN wiki DOCUMENT///////////
											for($j = 0; $j < 4; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `font`='".$font_style[$j]."'";
												$para=mysql_query($query);
												$para_font_style_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_style_reading_time[$j]+=$row_para['reading_time'];
													$para_font_style_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_style_male[$j]++;
													else
														$para_font_style_female[$j]++;  
										        }
											} 
											//////CALCULATION FOR CREATING CHART ON FONT SIZE For each para in NCERT Documents
											for($j = 0; $j < 3; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `size`='".$font_size[$j]."'";
												$para=mysql_query($query);
												$para_font_size_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_font_size_reading_time[$j]+=$row_para['reading_time'];
													$para_font_size_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_font_size_male[$j]++;
													else
														$para_font_size_female[$j]++;
										        }
											}

											//////CALCULATION FOR CREATING CHART ON Line Heights For each para in NCERT Documents
											for($j = 0; $j < 2; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `line_height`='".$line_height[$j]."'";
												$para=mysql_query($query);
												$para_line_height_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_line_height_reading_time[$j]+=$row_para['reading_time'];
													$para_line_height_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_line_height_male[$j]++;
													else
														$para_line_height_female[$j]++;
										        }
											}

											//////CALCULATION FOR CREATING CHART ON Word Spacing For each para in NCERT Documents
											for($j = 0; $j < 2; $j++){
												$query="SELECT * FROM test_data WHERE `pid`='".$row['pid']."' AND `word_spacing`='".$word_spacing[$j]."'";
												$para=mysql_query($query);
												$para_word_spacing_count[$j]=mysql_num_rows($para);
												
												while($row_para=mysql_fetch_array($para)){
													$para_word_spacing_reading_time[$j]+=$row_para['reading_time'];
													$para_word_spacing_test_time[$j]+=$row_para['test_time'];
													
													$query="SELECT gender FROM main WHERE user_id='".$row_para['uid']."'";
													$para1=mysql_query($query);
													$row_para1=mysql_fetch_array($para1);
													if($row_para1['gender']==1)
														$para_word_spacing_male[$j]++;
													else
														$para_word_spacing_female[$j]++;
										        }
											}
											//////////////////////////////////////////////////////////////////////////////////////////////////////////////
											//Nav tabs list -obj, sub and graphs
											echo "<ul class='nav nav-tabs nav-justified' role='tablist'>
												<li class='active in'><a href='#wiki_obj".$i."' role='tab' data-toggle='tab'>Objective Questions</a></li>
												<li><a href='#wiki_sub".$i."' role='tab' data-toggle='tab'>Subjective Questions</a></li>
												<li><a href='#wiki_graphs".$i."' role='tab' data-toggle='tab'>Graphs and Charts</a></li>
											</ul>";
															
											//Tab panes
											echo "<div class='tab-content'>";
												//Objectives Tab
												echo "<div class='tab-pane fade active in' id='wiki_obj".$i."'>
													<table class = 'table table-hover table-bordered ques_table' id='wiki_para_obj_ques".$i."'>
														<tr align = 'center'>
															<td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
															<td class = 'col-lg-6'><h4><big>Questions</big></h4></td>
															<td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
															<td class = 'col-lg-1'><h6><big>Skipped</big></h6></td>
														</tr>";
																					
														$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` != '0000'";
														mysql_query("SET NAMES utf8");
														$result3 = mysql_query($query3);
							
														$j = 1;
														while($row3 = mysql_fetch_array($result3)){
															echo "<tr align = 'center'>";
																//S.No.
																echo "<td>".
																	$j.
																"</td>";
																							
																//Objective Questions
																echo "<td>".
																	$row3['ques'].
																"</td>";
																
																$j++;
																							
																//Options data
																$query4 = "SELECT * FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																mysql_query("SET NAMES utf8");
																$result4 = mysql_query($query4);
																								
																$opt1_select_count = 0;
																$opt2_select_count = 0;
																$opt3_select_count = 0;
																$opt4_select_count = 0;
																$skipped_count = 0;
																							
																if($row3['opt1'] == ""){
																	$opt1_select_count = -1;
																}
																if($row3['opt2'] == ""){
																	$opt2_select_count = -1;
																}
																if($row3['opt3'] == ""){
																	$opt3_select_count = -1;
																}
																if($row3['opt4'] == ""){
																	$opt4_select_count = -1;
																}
																								
																while($row4 = mysql_fetch_array($result4)){
																	if($row4['selected_option'] == $row3['opt1'])
																		$opt1_select_count++;
																	if($row4['selected_option'] == $row3['opt2'])
																		$opt2_select_count++;
																	if($row4['selected_option'] == $row3['opt3'])
																		$opt3_select_count++;
																	if($row4['selected_option'] == $row3['opt4'])
																		$opt4_select_count++;
																	if($row4['selected_option'] == "skipped")
																		$skipped_count++;
																}
																//opt1
																echo "<td>";
																	if($opt1_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt1']."<hr/>".
																		$opt1_select_count;
																	}
																echo "</td>";
																						
																//opt2
																echo "<td>";
																	if($opt2_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt2']."<hr/>".
																		$opt2_select_count;
																	}
																echo "</td>";
																					
																//opt3
																echo "<td>";
																	if($opt3_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt3']."<hr/>".
																		$opt3_select_count;
																	}
																echo "</td>";
																						
																//opt4
																echo "<td>";
																	if($opt4_select_count == -1){
																		echo "-";
																	}
																	else{
																		echo $row3['opt4']."<hr/>".
																		$opt4_select_count;
																	}
																echo "</td>";
																							
																//skipped
																echo "<td>";
																	if($skipped_count == -1){
																		echo "-";
																	}
																	else{
																		echo $skipped_count;
																	}
																echo "</td>
															</tr>";	
														}
													echo "</table>
												</div>";
																		
												//Subjectives Tab
												echo "<div class='tab-pane fade active' id='wiki_sub".$i."'>";
													//querying all subjective questions of a particular para
													$query3 = "SELECT * FROM questions WHERE `pid` = '".$row['pid']."' AND `multi_correct` = '0000'";
													mysql_query("SET NAMES utf8");
													$result3 = mysql_query($query3);
																		
													$j = 1;
													while($row3 = mysql_fetch_array($result3)){
														echo "<table class = 'ques_table' id='wiki_para_sub_ques".$i."' border = 'solid'>";
															//subjective question Heading
															echo "<tr align = 'center'>
																<td>
																	<h4><big>
																		Question : ".$j.
																	"</big></h4>
																</td>
															</tr>";
																				
															//subjective Questions
															echo "<tr align = 'center'>
																<td>
																	<div class='ques_body'>
																		<h3><small>".
																			$row3['ques'].
																		"</small></h3>
																	</div>
																</td>
															</tr>";
																				
															//subjective answers row having table of answers
															echo "<tr align = 'center'>
																<td>
																	<table class='table' border = 'solid'>
																		<tr align='center'>
																			<td class='col-lg-3'>User</td>
																			<td class='col-lg-9'>Answers</td>
																		</tr>";
																							
																		//querying all users and all thier answrs of a question
																		//$query4 = "SELECT tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."'";
																		$query4 = "SELECT uid, tid FROM test_questions_data WHERE `qid` = '".$row3['qid']."' GROUP BY uid";
																		mysql_query("SET NAMES utf8");
																		$result4 = mysql_query($query4);
																							
																		while($row4 = mysql_fetch_array($result4)){
																			//$query5 = "SELECT uid FROM test_data WHERE `tid` = '".$row4['tid']."'";
																			//mysql_query("SET NAMES utf8");
																			//$result5 = mysql_query($query5);
																			//$row5 = mysql_fetch_array($result5);
																									
																			//$query6 = "SELECT * FROM main WHERE `user_id` = '".$row5['uid']."'";
																			$query6 = "SELECT * FROM main WHERE `user_id` = '".$row4['uid']."'";
																			$result6 = mysql_query($query6);
																			$row6 = mysql_fetch_array($result6);
																									
																			echo "<tr align='center'>
																				<td>".$row6['email']."<br/>{ Age - ".$row6['age'].", ";
																					if($row6['gender'] == "1"){
																						echo "M, ";
																					}
																					else{
																						echo "F, ";
																					}
																					if($row6['edu_back'] == "higher_sec"){
																						echo "Higher Secondary }";
																					}
																					else if($row6['edu_back'] == "ug"){
																						echo "Undergraduate }";
																					}
																					else if($row6['edu_back'] == "pg"){
																						echo "Postgraduate }";
																					}
																					else{
																						echo "Other }";
																					}
																				echo "</td>
																				
																				<td>";
																					$query7 = "SELECT tid FROM test_data WHERE `uid` = '".$row4['uid']."' AND `pid` = '".$row['pid']."'";
																					$result7 = mysql_query($query7);
																					while($row7 = mysql_fetch_array($result7)){
																						$query8 = "SELECT selected_option FROM test_questions_data WHERE `tid` = '".$row7['tid']."' AND `qid` = '".$row3['qid']."'";
																						$result8 = mysql_query($query8);
																						$row8 = mysql_fetch_array($result8);
																						echo $row8['selected_option']."<hr/>";
																					}
																				echo "</td>
																			</tr>";
																		}
																	echo "</table>
																</td>
															</tr>";
														
														$j++;
														echo "</table>";	
													}
												echo "</div>";
																		
												//Graphs and Charts Tab
												echo "<div class='tab-pane fade active' id='wiki_graphs".$i."'>";
												//////////////////////////////////////////////////////////////////////////////////////////////////////////
													echo "<div class='row' align='center'>";
														echo "<div class='col-lg-6 col-md-12'>";
														//CREATING A CHART OF FONT STYLE FOR EACH PARAGRAPH In wiki Article Type
														if(array_sum($para_font_style_count)!=0){
															$strXML= "<graph caption='Tests given in different Font Style' subCaption='for Paragraph ".$i." with Legal Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
															echo "<table class='table table-bordered'>";
															echo "<tr>";
																echo "<td>Font Style</td>";
																echo "<td>Male views</td>";
																echo "<td>Female views</td>";
																echo "<td>Average Reading Time</td>";
																echo "<td>Average Test Time</td>";
															echo "</tr>";
															for($t = 0; $t < 4; $t++){
																echo "<tr>
																<td>".
																	$font_style[$t].
																"</td>
																
																<td>".
																	$para_font_style_male[$t].
																"</td>
																
																<td>".
																	$para_font_style_female[$t].
																"</td>";
																if($para_font_style_count[$t]!=0){
																	$para_font_style_reading_time[$t]=($para_font_style_reading_time[$t]/ $para_font_style_count[$t]);
																	$para_font_style_test_time[$t]=($para_font_style_test_time[$t]/$para_font_style_count[$t]);
																	echo "<td>".
																		$para_font_style_reading_time[$t].
																	"</td>";
																	
																	echo "<td>".
																		$para_font_style_test_time[$t].
																	"</td>";
																}
																else{
																	echo "<td>-</td>";
																	echo "<td>-</td>";
																}
																$strXML .= "<set name='" . $font_style[$t] . "' value='" . $para_font_style_count[$t] . "' />";
																echo "</tr>";
															}
															$strXML .= "</graph>";
															echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_font_style_para_chart".$i, 500, 400);
															echo "</table>";
														}
														echo "</div>";
												
														echo "<div class='col-lg-6 col-md-12'>";
													//CREATING A CHART OF FONT SIZE FOR EACH PARAGRAPH In wiki Article Type
													if(array_sum($para_font_size_count)!=0){
														$strXML= "<graph caption='Tests given in different Font Size' subCaption='for Paragraph ".$i." with wiki Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Font Size Ranges</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t = 0; $t < 3; $t++){
															echo "<tr>
															<td>".
																$font_size[$t].
															"</td>
															
															<td>".
																$para_font_size_male[$t].
															"</td>
															
															<td>".
																$para_font_size_female[$t].
															"</td>";
															if($para_font_size_count[$t]!=0){
																$para_font_size_reading_time[$t]=($para_font_size_reading_time[$t]/ $para_font_size_count[$t]);
																$para_font_size_test_time[$t]=($para_font_size_test_time[$t]/$para_font_size_count[$t]);
																echo "<td>".
																	$para_font_size_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_font_size_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $font_size[$t] . "' value='" . $para_font_size_count[$t] . "' />";
															echo "</tr>";				
														}
														$strXML .= "</graph>";
														echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_font_size_para_chart".$i, 500, 400);
														echo "</table>";
													}
											echo "</div>";
													echo "</div>";
											
											echo "<div class='row' align='center'>";
												echo "<div class='col-lg-6 col-md-12'>";
												//CREATING A CHART OF LINE HEIGHT FOR EACH PARAGRAPH in Wiki documents
												if(array_sum($para_line_height_count)!=0){
													$strXML= "<graph caption='Tests given in different line height' subCaption='for Paragraph ".$i." with wiki Article type'pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
														echo "<table class='table table-bordered'>";
														echo "<tr>";
															echo "<td>Line Height Ranges</td>";
															echo "<td>Male views</td>";
															echo "<td>Female views</td>";
															echo "<td>Average Reading Time</td>";
															echo "<td>Average Test Time</td>";
														echo "</tr>";
														for($t = 0; $t < 2; $t++){
															echo "<tr>
															<td>".
																$line_height[$t].
															"</td>
															
															<td>".
																$para_line_height_male[$t].
															"</td>
															
															<td>".
																$para_line_height_female[$t].
															"</td>";
															if($para_line_height_count[$t]!=0){
																$para_line_height_reading_time[$t]=($para_line_height_reading_time[$t]/ $para_line_height_count[$t]);
																$para_line_height_test_time[$t]=($para_line_height_test_time[$t]/$para_line_height_count[$t]);
																echo "<td>".
																	$para_line_height_reading_time[$t].
																"</td>";
																
																echo "<td>".
																	$para_line_height_test_time[$t].
																"</td>";
															}
															else{
																echo "<td>-</td>";
																echo "<td>-</td>";
															}
															$strXML .= "<set name='" . $line_height[$t] . "' value='" . $para_line_height_count[$t] . "' />";
														}
													$strXML .= "</graph>";
													echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_line_height_para_chart".$i, 500, 400);
													echo "</table>";
												}
												echo "</div>";
											
											echo "<div class='col-lg-6 col-md-12'>";
											//CREATING A CHART OF WORD SPACING FOR EACH PARAGRAPH
											if(array_sum($para_word_spacing_count)!=0){
												$strXML= "<graph caption='Tests given in different line Word Spacing' subCaption='for Paragraph ".$i." with wiki Article type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
												echo "<table class='table table-bordered'>";
												echo "<tr>";
													echo "<td>Word Spacing Ranges</td>";
													echo "<td>Male views</td>";
													echo "<td>Female views</td>";
													echo "<td>Average Reading Time</td>";
													echo "<td>Average Test Time</td>";
												echo "</tr>";				
												for($t = 0; $t < 2; $t++){
													echo "<tr>
													<td>".
														$word_spacing[$t].
													"</td>
													
													<td>".
														$word_spacing_male[$t].
													"</td>
													
													<td>".
														$word_spacing_female[$t].
													"</td>";
													
													if($word_spacing_count[$t]!=0){
														$para_word_spacing_reading_time[$t]=($para_word_spacing_reading_time[$t]/ $para_word_spacing_count[$t]);
														$para_word_spacing_test_time[$t]=($para_word_spacing_test_time[$t]/$para_word_spacing_count[$t]);
														echo "<td>".
															$para_word_spacing_reading_time[$t].
														"</td>";
														
														echo "<td>".
															$para_word_spacing_test_time[$t].
														"</td>";
													}
													else{
														echo "<td>-</td>";
														echo "<td>-</td>";
													}
													$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $para_word_spacing_count[$t] . "' />";
												}
												$strXML .= "</graph>";
												echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_word_spacing_para_chart".$i, 500, 400);
												echo "</table>";
											}
											echo "</div>";
											echo "</div>";	
											echo "</div>
											
											</div>";//end of div containing all 3 tabs of this para
										echo "</div>
									</div>
								</td>
							</tr>";
							$i++;
						}
					echo "</table>";
					?>
					<!--CHARTS|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					<?php
					//Accordian for CHARTS FOR Wiki Article Types|||||||||||||||||||||||||||||||||||||||||||||||||||||||-->
					echo '<button id=wiki_accordian" class="article_accordian_class btn btn-warning btn-lg btn-block" data-toggle="collapse" data-target="#wiki_graphs">
						Charts for Wikipedia Documents
					</button>';
					
					
					echo "<div id='wiki_graphs' class='collapse in row' align='center'>";
						echo "<div  class='row' align='center'>";
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR FONT STYLE in wiki Article type///////////
							if(array_sum($font_style_count)!=0){
								$strXML= "<graph caption='Tests given in different Font styles' subcaption='with wiki Article Type' pieSliceDepth='0' showBorder='1' showNames='1' formatNumberScale='1' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Font Style</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 4;$t++){
									echo "<tr>
									<td>".
										$font_style[$t].
									"</td>
									
									<td>".
										$font_style_male[$t].
									"</td>
									
									<td>".
										$font_style_female[$t].
									"</td>";
									if($font_style_count[$t]!=0){
										$font_style_reading_time[$t]=($font_style_reading_time[$t]/ $font_style_count[$t]);
										$font_style_test_time[$t]=($font_style_test_time[$t]/$font_style_count[$t]);
										echo "<td>".
											$font_style_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$font_style_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='".$font_style[$t]."' value='".$font_style_count[$t]."' />";
									echo "</tr>";
								}
								$strXML .= "</graph>";
								echo renderChartHTML("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", " ", $strXML, "wiki_font_style_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
							
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR FONT SIZE in wiki Article type///////////
							if(array_sum($font_size_count)!=0){
								$strXML= "<graph caption='Tests given in different Font sizes' subcaption='with wiki Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Font Size Ranges</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 3; $t++){
									echo "<tr>
									<td>".
										$font_size[$t].
									"</td>
									
									<td>".
										$font_size_male[$t].
									"</td>
									
									<td>".
										$font_size_female[$t].
									"</td>";
									if($font_size_count[$t]!=0){
										$font_size_reading_time[$t]=($font_size_reading_time[$t]/ $font_size_count[$t]);
										$font_size_test_time[$t]=($font_size_test_time[$t]/$font_size_count[$t]);
										echo "<td>".
											$font_size_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$font_size_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='" . $font_size[$t] . "' value='" . $font_size_count[$t] . "' />";
									echo "</tr>";
								}
								$strXML .= "</graph>";
								echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_size_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
						
						echo "</div>";
							
						echo "<div class='row' align='center'>";
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR Line Height in wiki Article type///////////
							if(array_sum($line_height_count)!=0){
								$strXML= "<graph caption='Tests given in different Line heights' subcaption='with wiki Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Line Height Ranges</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 2; $t++){
									echo "<tr>
									<td>".
										$line_height[$t].
									"</td>
									
									<td>".
										$line_height_male[$t].
									"</td>
									
									<td>".
										$line_height_female[$t].
									"</td>";
									if($line_height_count[$t]!=0){
										$line_height_reading_time[$t]=($line_height_reading_time[$t]/ $line_height_count[$t]);
										$line_height_test_time[$t]=($line_height_test_time[$t]/$line_height_count[$t]);
										echo "<td>".
											$line_height_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$line_height_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='" . $line_height[$t] . "' value='" . $line_height_count[$t] . "' />";
								}
								$strXML .= "</graph>";
								echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_line_height_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
									
							echo "<div class='col-lg-6 col-md-12'>";
							/////////////////////CHART FOR Word Spacing in wiki Article type///////////
							if(array_sum($word_spacing_count)!=0){
								$strXML= "<graph caption='Tests given in different word spacing' subcaption='with wiki Article Type' pieSliceDepth='30' showBorder='3' showNames='1' formatNumberScale='0' numberSuffix=' test(s)' decimalPrecision='0'>";
								echo "<table class='table table-bordered'>";
								echo "<tr>";
									echo "<td>Word Spacing Ranges</td>";
									echo "<td>Male views</td>";
									echo "<td>Female views</td>";
									echo "<td>Average Reading Time</td>";
									echo "<td>Average Test Time</td>";
								echo "</tr>";
								for($t = 0; $t < 2; $t++){
									echo "<tr>
									<td>".
										$word_spacing[$t].
									"</td>
									
									<td>".
										$word_spacing_male[$t].
									"</td>
									
									<td>".
										$word_spacing_female[$t].
									"</td>";
									if($word_spacing_count[$t]!=0){
										$word_spacing_reading_time[$t]=($word_spacing_reading_time[$t]/ $word_spacing_count[$t]);
										$word_spacing_test_time[$t]=($word_spacing_test_time[$t]/$word_spacing_count[$t]);
										echo "<td>".
											$word_spacing_reading_time[$t].
										"</td>";
										
										echo "<td>".
											$word_spacing_test_time[$t].
										"</td>";
									}
									else{
										echo "<td>-</td>";
										echo "<td>-</td>";
									}
									$strXML .= "<set name='" . $word_spacing[$t] . "' value='" . $word_spacing_count[$t] . "' />";
								}
								$strXML .= "</graph>";
			
								echo renderChart("FusionChartsFree/Code/FusionCharts/FCF_Column3D.swf", "", $strXML, "wiki_word_spacing_chart", 500, 400);
								echo "</table>";
							}
							echo "</div>";
						echo "</div>";	
					echo "</div>";	
					?>
				</div><!--End of wikipedia  Article-->
				
			</div>	
		</div>
	</div>	
	<!-- javascript -->
	<script src="Bootstrap/js/jquery 2.1.1.min.js"></script>
    <script src="Bootstrap/js/bootstrap.min.js"></script>
	<script>
		$(".alert").alert();
		window.setTimeout(function() { 
			$(".alert").alert('close'); 
		}, 5000);
		$( ".panel-body" ).accordion({ autoHeight: false });

		//functions to add paras from edit tables 
		// function add_ncert_question(el){
		// 	var thisId = $(el).attr("id");
		// 	var para_id = thisId.substring(18);
		// 	//alert(thisId);
		// 	var s_no = document.getElementById("obj_ques_count").value;
		// 	var max_qid = document.getElementById("max_ques_id").value;
		// 	max_qid = parseInt(max_qid) + 1;
		// 	//alert(max_qid);
		// 	//document.getElementById("ncert_obj_ques_table").deleteRow(row);
		// 	var question = $('<tr align="center">'+
		// 	'<td class = "col-lg-1">'+s_no+'</td>'+
		// 	'<td class = "col-lg-5"><textarea name="new_obj_ques'+max_qid+'" class="edit_ques_class" type="text">'+'</textarea></td>'+
		// 	'<td class = "col-lg-1"><textarea name="new_opt1'+max_qid+'" class="edit_ques_class" type="text">'+'</textarea></td>'+
		// 	'<td class = "col-lg-1"><textarea name="new_opt2'+max_qid+'" class="edit_ques_class" type="text">'+'</textarea></td>'+
		// 	'<td class = "col-lg-1"><textarea name="new_opt3'+max_qid+'" class="edit_ques_class" type="text">'+'</textarea></td>'+
		// 	'<td class = "col-lg-1"><textarea name="new_opt4'+max_qid+'" class="edit_ques_class" type="text">'+'</textarea></td>'+
		// 	'<td class = "col-lg-2"><input name="cancel'+max_qid+'" class="btn btn-lg btn-danger" type="button" value="Cancel" /></td>'+
		// 	'</tr>');
		// 	//(question).append('#ncert_obj_ques_table'+para_id);
		// 	max_qid++;
		// 	document.getElementById("max_ques_id").value = max_qid;
			
		// 	$('#ncert_obj_ques_table' + para_id + ' tr:last').after(question);
		// }
		// <td class = 'col-lg-1'><h4><big>S.No.</big></h4></td>
		// <td class = 'col-lg-5'><h4><big>Questions</big></h4></td>
		// <td class = 'col-lg-1'><h6><big>Opt 1</big></h6></td>
		// <td class = 'col-lg-1'><h6><big>Opt 2</big></h6></td>
		// <td class = 'col-lg-1'><h6><big>Opt 3</big></h6></td>
		// <td class = 'col-lg-1'><h6><big>Opt 4</big></h6></td>
		// <td class = 'col-lg-2'><h6><big>Delete</big></h6></td>

		// function delete_sub_ques_handler(el){
		// 	var thisId = $(el).attr("id");
		// 	var row = thisId.substring(11);
		// 	alert(row);
		// 	document.getElementById("ncert_sub_ques_table").deleteRow(row);
		// }
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
