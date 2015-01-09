<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Readability Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="shortcut icon" href="images/survey.ico"/>
	<!-- Bootstrap Core CSS-->
    <link href="Bootstrap/css/bootstrap.css" rel="stylesheet"/>
	<!-- Custom Core CSS-->
    <link href="Bootstrap/css/custom.css" rel="stylesheet"/>
    <script src="Bootstrap/js/respond.min.js"></script>
</head>

<?php
	include("include/db_connect.php");
	session_start();
	//////FOR SECURITY/////////////////////
	//check for direct entry
	if(!isset($_SESSION['email'])){
		header("Location:index.php");
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
		if(isset($row['password'])){
			if($_SESSION['password']!=$row['password'])
				header("Location:index.php");
		}
		else{
			if($num==1)
				header("Location:index.php");
		}
	}
	
	if(isset($_POST['go'])){
		if(isset($_POST['para_count_btn']) && isset($_POST['ques_count_array_btn'])){
			$para_count = $_POST['para_count_btn'];
			
			//$ques_count_array=split("[,]",$ques_count_array);
			$ques_count_array = json_decode($_POST['ques_count_array_btn']);
			
			
			for($i = 1; $i <= $para_count; $i++){
				if(isset($_POST['content'.$i])){
					//getting the content of this para trimmed
					$content = trim($_POST["content".$i]);
					
					if(strlen($content) > 0){
						//$lang = $_POST['lang'.$i];
						$article = $_POST['article'.$i];
						//adding para into the paragraphs db
						//$content = addslashes($content);
						$content = mysql_escape_string($content);
						
						mysql_query("SET NAMES utf8");
						//$sql = "INSERT INTO paragraphs(`language`, `article_type`, `para`) VALUES('$lang', '$article', '$content')";
						$sql = "INSERT INTO paragraphs(`article_type`, `para`) VALUES('$article', '$content')";
						
						if(!$result = mysql_query($sql)){
							$error = "Error occured while adding new paragraph(s).";
							$_SESSION['error'] = $error;
							break;
						}
						else{
							/* $pid_sql = "SELECT * FROM paragraphs WHERE para='$content'";
							$pid = mysql_query($pid_sql);
                            $row = mysql_fetch_array($pid);
		                    $pid = $row['pid']; */
							$pid = mysql_insert_id();
							
							for($j = 1; $j <= $ques_count_array[$i]; $j++){
								if(isset($_POST['add_para'.$i.'ques'.$j])){
									$ques = trim($_POST['add_para'.$i.'ques'.$j]);
									//$ques = addslashes($ques);
									$ques = mysql_escape_string($ques);
									
									$ans = array(0, 0, 0, 0, 0);
									$str = "";
									for($k = 1; $k <= 4; $k++){
										if(isset($_POST['add_para'.$i.'ques'.$j.'opt'.$k])){
											$ans[$k] = $_POST['add_para'.$i.'ques'.$j.'opt'.$k];
											$ans[$k] = trim($ans[$k]);
											$ans[$k] = mysql_escape_string($ans[$k]);
											if(isset($_POST['add_para'.$i.'ques'.$j.'opt'.$k.'_check'])){
												$str .= $_POST['add_para'.$i.'ques'.$j.'opt'.$k.'_check'];
											}
											else{
												$str .= "F";
											}
										}
										else{
											$ans[$k] = 0;
											$str .= "0";
										}
									}
									if(!isset($str))
										$str = "0";
									//$sql = "INSERT INTO questions(`pid`, `ques`, `opt1`, `opt2`, `opt3`, `opt4`) VALUES('1', 'question', `question`, `question`, `question`, `question`)";
									mysql_query("SET NAMES utf8");
									$sql = "INSERT INTO `questions` (`qid` ,`pid` ,`ques` ,`opt1` ,`opt2` ,`opt3` ,`opt4`, `multi_correct`)VALUES (NULL ,  '$pid', '$ques',  '$ans[1]',  '$ans[2]',  '$ans[3]',  '$ans[4]', '$str')";

									if(!$result = mysql_query($sql)){
										$error = "Error occured while adding question(s).";
										break;
									}
								}
							}
						}	
					}
				}
			}
			 if(!isset($error)){
			    $success = "The paragraph(s) and their related question(s) were added successfully.";
			} 
		}
	}	
?>

<body>
	<div class="container">
		<div class="row well" id="page_header">
			<div class="col-md-4 col-lg-4" align="center">
				Hello Admin <?php echo $_SESSION['email']."<br/><br/>";?>
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
								<form class="form-signin" role="form" method="POST" action="admin.php" enctype="multipart/form-data">
									<input name="email" type="email" class="form-control" placeholder="Email address" required/>
									<input name="password" type="password" class="form-control" placeholder="Password" required/>
									<input name="repassword" type="password" class="form-control" placeholder="Re-enter password" required/>
									<input name="add_admin" class="btn btn-lg btn-primary btn-block" type="submit" href="admin.php" value="Add admin"/>	
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-3 col-lg-4">
				<div class="page-header"><h2 align="center">Readability Survey Admin Block</h2></div>
			</div>
			
			<div class="col-md-4 col-lg-4" align="center">
				<?php echo date("jS \of F Y [l]", time())."<br/><br/>";?>
				<a href="analytics.php" class="btn btn-primary btn-lg" type="button">View Analytics</a>
				<a href="home.php" class="btn btn-primary btn-lg" type="button">Give a Test</a>
			</div>
		</div>
		
		
		<div class="row" id="para_container">
			<form id="add_para_form" class="form" method="POST" action="admin.php">
				<div class="row add_para_class" id="add_para0"></div>
				<input name="para_count_btn" id="para_count_btn" type="hidden"/>
				<input name="ques_count_array_btn" id="ques_count_array_btn" type="hidden"/>
				<input name="add_para_btn" id="add_para_btn" class="btn btn-primary btn-lg" type="button" value="Add Paragraph" />
				<input name="go" id="go-btn" class="btn btn-lg btn-success" type="submit" value="Go"/>
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
	<script>
	var para_count=0;
	var ques_count_array=[];
	$(document).ready(function() {
		$("#add_para_btn").click(function(){
			ques_count_array[para_count + 1] = 0;
			//add paragraph
			var myPara = $(
				'<div class="row well para_class" id="add_para'+(para_count+1)+'">'+
					//paragraph header
					'<h2 align="center">'+
						'<small>Paragraph '+(para_count+1)+'</small>'+
					'</h2>'+
					
					//article type
					'<div class="article form-group">'+
					'<label>Article Type</label>'+
					'<select name="article'+(para_count+1)+'" class="data_class" id="article'+(para_count+1)+'">'+
						'<option name="ncert">NCERT Text</option>'+
						'<option name="wiki">Wikipedia Page</option>'+
					'</select>'+
					'</div>'
					+
						
					//content
					'<div class="form-group">'+
						'<label>Content</label>'+
						'<br/>'+
						'<textarea name="content'+(para_count+1)+'" class="content_class" id="content'+(para_count+1)+'">'+
						'</textarea>'+
					'</div>'+
					'<br/>'+
					
					//div space to add append questions
					'<div class="row add_ques_form_class" id="add_ques_form'+(para_count+1)+'"></div>'+
					
					//add a single correct objective question btn
					'<input id="num_ques'+(para_count+1)+'" class="btn btn-warning btn-lg add_ques_btn_class" type="button" onclick="add_obj_ques(this);" value="Add a single correct objective Question" />'+
					
					//add a multiple correct objective question btn
					'<input id="num_ques'+(para_count+1)+'" class="btn btn-warning btn-lg add_ques_btn_class" type="button" onclick="add_mul_obj_ques(this);" value="Add a multiple correct objective Question" />'+
					
					//add a subjective question btn
					'<input id="num_ques'+(para_count+1)+'" class="btn btn-warning btn-lg add_ques_btn_class" type="button" onclick="add_sub_ques(this);" value="Add a subjective Question" />'+
					
					//cancel btn
					'<input id="cancel_btn'+(para_count+1)+'" class="btn btn-danger btn-lg" type="button" onclick="cancel(this);" value="Cancel" />'+
				'</div>');
			para_count++;
			document.getElementById('para_count_btn').value = para_count;
			//document.getElementById('ques_count_array_btn').value = ques_count_array;
			//$('#ques_count_array_btn').val(JSON.stringify(ques_count_array));
			(myPara).appendTo('.add_para_class');
		})
	});
	function cancel(el){
		var thisId = $(el).attr("id");
		$("#add_para"+thisId.substring(10)).remove();	
	}
    function add_obj_ques(el){
		var thisId = $(el).attr("id");
		var para_id = parseInt(thisId.substring(8));
		ques_count_array[para_id]++;
		//alert(ques_count_array[para_id]);
		var question = $(
			'<div class="form-group ques_class" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'">'+
				'<label>Question '+ques_count_array[para_id]+': </label><br/>'+
				'<textarea name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'" class=" add_ques_class" type="text" placeholder="Add Your question." ></textarea>'+'</br>'+
				
				'<label>Answers : </label>'+'<br/>'+
				'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1'+'" class=" data_class" placeholder="Add the correct ans."/>'+
				'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2'+'" class=" data_class" placeholder="Add some diverging ans."/>'+
				'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3'+'" class=" data_class" placeholder="Add some diverging ans."/>'+
				'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4'+'" class=" data_class" placeholder="Add some diverging ans."/>'+
				'<br/>'+
				'<input id="cancel_btn'+(para_count)+'ques'+(ques_count_array[para_id])+'" class="btn btn-danger btn-lg cancel_ques_btn_class" type="button" onclick="cancel_ques(this);" value="Cancel" />'+
			'</div>');
		(question).appendTo('#add_ques_form'+para_id);
		//document.getElementById('ques_count_array_btn').value = ques_count_array;
		$('#ques_count_array_btn').val(JSON.stringify(ques_count_array));
	}
	function add_mul_obj_ques(el){
		var thisId = $(el).attr("id");
		var para_id = parseInt(thisId.substring(8));
		ques_count_array[para_id]++;
		//alert(ques_count_array[para_id]);
		var question = $(
			'<div class="form-group ques_class" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'">'+
				'<label>Question '+ques_count_array[para_id]+': </label><br/>'+
				'<textarea name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'" class=" add_ques_class" type="text" placeholder="Add Your question." ></textarea>'+'</br>'+
				
				'<label>Answers : </label>'+'<br/>'+
				'<div class="mul_ans">'+
					'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1'+'" class=" mul_data_class" placeholder="Add an ans."/>'+
					
					'<div class="mul_ans_check">'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1T'+'"><h3><small>T</small></h3></label>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1T'+'" value="T"/>'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1F'+'"><h3><small>F</small></h3></label/>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt1F'+'" value="F"/>'+
					'</div">'+	
				'</div>'+
				
				'<div class="mul_ans">'+
					'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2'+'" class=" mul_data_class" placeholder="Add an ans."/>'+
					
					'<div class="mul_ans_check">'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2T'+'"><h3><small>T</small></h3></label>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2T'+'" value="T"/>'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2F'+'"><h3><small>F</small></h3></label/>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt2F'+'" value="F"/>'+
					'</div>'+
				'</div>'+
				
				'<div class="mul_ans">'+
					'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3'+'" class=" mul_data_class" placeholder="Add an ans."/>'+
					
					'<div class="mul_ans_check">'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3T'+'"><h3><small>T</small></h3></label>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3T'+'" value="T"/>'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3F'+'"><h3><small>F</small></h3></label/>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt3F'+'" value="F"/>'+
					'</div>'+	
				'</div>'+
				
				'<div class="mul_ans">'+
					'<input name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4'+'" class=" mul_data_class" placeholder="Add an ans."/>'+
					
					'<div class="mul_ans_check">'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4T'+'"><h3><small>T</small></h3></label>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4T'+'" value="T"/>'+
						'<label for="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4F'+'"><h3><small>F</small></h3></label/>'+
						'<input type="radio" name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4_check'+'" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'opt4F'+'" value="F"/><br>'+
					'</div>'+	
				'</div>'+
				
				'<br/>'+
				'<input id="cancel_btn'+(para_count)+'ques'+(ques_count_array[para_id])+'" class="btn btn-danger btn-lg cancel_ques_btn_class" type="button" onclick="cancel_ques(this);" value="Cancel" />'+
			'</div>');
		(question).appendTo('#add_ques_form'+para_id);
		//document.getElementById('ques_count_array_btn').value = ques_count_array;
		$('#ques_count_array_btn').val(JSON.stringify(ques_count_array));
	}
	function add_sub_ques(el){
		var thisId = $(el).attr("id");
		var para_id = parseInt(thisId.substring(8));
		ques_count_array[para_id]++;
		//alert(ques_count_array[para_id]);
		var question = $(
			'<div class="form-group ques_class" id="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'">'+
				'<label>Question '+ques_count_array[para_id]+': </label><br/>'+
				'<textarea name="add_para'+(para_count)+'ques'+(ques_count_array[para_id])+'" class=" add_ques_class" type="text" placeholder="Add Your question." ></textarea>'+'</br>'+
				
				'<br/>'+
				'<input id="cancel_btn'+(para_count)+'ques'+(ques_count_array[para_id])+'" class="btn btn-danger btn-lg cancel_ques_btn_class" type="button" onclick="cancel_ques(this);" value="Cancel" />'+
			'</div>');
		(question).appendTo('#add_ques_form'+para_id);
		//document.getElementById('ques_count_array_btn').value = ques_count_array;
		$('#ques_count_array_btn').val(JSON.stringify(ques_count_array));
	}
	function cancel_ques(el){
	    var thisId = $(el).attr("id");
		if(para_count < 10)
			$("#add_para"+thisId.charAt(10)+'ques'+thisId.substring(15)).remove();
		else{
			$("#add_para"+thisId.substring(10,12)+'ques'+thisId.substring(16)).remove();
		}
	}
	$(".alert").alert();
	window.setTimeout(function() {
		$(".alert").alert('close'); 
	}, 5000);
	</script>
	<!---footer---->
	<nav class=" navbar-fixed-bottom footer " role="navigation" >
	  <div class="container footer" align="right";  >
		<font color="#04B404"><b> Developers :- </b></font>
		 <a href=http://about.me/jain_nikhil><b>Nikhil Jain </b></a>
		 <font color="#2E2EFE"> &</font>
		<a href=http://about.me/ashish_dubey><b>Ashish Dubey</b></a>
		 
	  </div>
	</nav>
</body>
</html>