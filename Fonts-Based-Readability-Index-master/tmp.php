<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
</head>
	<body>
		<input name="in" id="in"/>
		<input name="click" id="click" type="button" onclick="show()" value="Click"/>
	</body>
	
	<script>
		function show(){
			alert(document.getElementById('in').value);
		}
	</script>
</html>