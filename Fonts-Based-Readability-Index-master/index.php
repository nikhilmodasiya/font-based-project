<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to Readability Survey</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/survey.ico">
	<!-- Bootstrap Core CSS-->
    <link href="Bootstrap/css/bootstrap.css" rel="stylesheet">
	<!-- Custom Core CSS-->
    <link href="Bootstrap/css/custom.css" rel="stylesheet">
    <script src="Bootstrap/js/respond.min.js"></script>
</head>

<!--php code for signin and signup process-->
<?php
	include("include/db_connect.php");
	session_start();
	//signout from admin and analytics
	if(isset($_SESSION['email'])){
		unset($_SESSION['email']);
		unset($_SESSION['password']);
		session_destroy();
	}
	if(!empty($_POST) && $_SERVER["REQUEST_METHOD"] == "POST"){
		//signin process
		if(isset($_POST['signin']) and $_POST['signin']){
			$email = addslashes($_POST["email"]);
			$age = addslashes($_POST["age"]);
			$edu_back = addslashes($_POST["edu_back"]);
			$gender=addslashes($_POST["gender"]);
			$sql = "SELECT * FROM main where email='$email'";
			unset($_POST);
			$result = mysql_query($sql);
			$count = mysql_num_rows($result);
			if($count == 0){
				$sql = "INSERT INTO main(`email`, `age`, `edu_back`, `gender`) VALUES('$email', '$age', '$edu_back','$gender')";
				if(!$result = mysql_query($sql)){
					$error = "Error occured while adding new user.";
				}
				else{
					session_start();
					$_SESSION['email'] = $email;
					header("Location: home.php");
				}				
			}
			else if($count == 1){
				session_start();
				$row = mysql_fetch_array($result);
				$_SESSION['email'] = $email;
				$_SESSION['uid'] = $row['user_id'];
				if(isset($_SESSION['password'])){
					unset($_SESSION['password']);
				}
				 header("Location: home.php");
				//header("Location: temp.php");
			}
		}
		//admin signin process
		else if($_POST['admin_signin']){
			//retrieving admin box form variables
			$email = $_POST["email"];
			$password = $_POST["password"];
			
			//if correct credentials, sign into admin block
			$sql = "SELECT * FROM admins where email='$email' AND password='$password'";
			//unset($_POST, $password);
			$result = mysql_query($sql);
			$num=mysql_num_rows($result);
			if($num!=1){
				$error = "Wrong username or password.";
			}
			else{
				session_start();
				$_SESSION['email'] = $email;
				$_SESSION['password'] = $password;
				//header("Location:admin.php");
				header("Location:analytics.php");
			}	
		}
	}
?>

<body>
	<div class="container">
		<!--Signin Box-->
		<form class="form-signin well" role="form" method="POST" action="index.php" enctype="multipart/form-data">
			<div class="page-header"><h2 align="center">Readability Survey</h2></div>
			<h2 align="center"><small>Sign in</small></h2>
			
			<input name="email" type="email" class="form-control" placeholder="Email address" required autofocus/>
			<input name="age" type="number" class="form-control" placeholder="Age" min="12" required/>
			
			<select name="edu_back" id="edu_back" type="text" class="form-control" required>
				<!--<option name="default" value="" disabled>Select...</option>-->
				<option value="" disabled="disabled" selected="selected">Educational Qualification</option>
				<option name="higher_sec" value="higher_sec">Higher Secondary</option>
				<option name="ug" value="ug">Under Graduate</option>
				<option name="pg" value="pg">Post Graduate</option>
				<option name="others" value="others">Others</option>
			</select>
			
			<div id="gender" >
				<label for="male"><h3><small>Male</small></h3></label>
				<input type="radio" name="gender" id="male" value="1"/>
				<label for="female"><h3><small>Female</small></h3></label/>
				<input type="radio" name="gender" id="female" value="0"/><br>
			</div>	
			<input name="signin" class="btn btn-lg btn-primary btn-block" type="submit" href="index.php" value="Sign in"/>	
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
		
		<!--Admin Box-->
		<div class="well" align="center" id="admin-box">
			<h2 align="center"><small>Or Go to Admin Block here</small></h2>
			<!-- Button trigger modal -->
			<button class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#basicModal">
			  Sign In
			</button>
			<!-- Modal -->
			<div class="modal fade" id="basicModal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
							<h2 class="modal-title" id="myModalLabel">Readability Survey Admin Signin</h2>
						</div>
						<div class="modal-body">
							<form class="form-signin" role="form" method="POST" action="index.php" enctype="multipart/form-data">
								<input name="email" type="email" class="form-control" placeholder="Email address" required/>
								<input name="password" type="password" class="form-control" placeholder="Password" required/>
								<input name="admin_signin" class="btn btn-lg btn-primary btn-block" type="submit" href="index.php" value="Sign in"/>	
							</form>
						</div>
					</div>
				</div>
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