<?php
include_once("sql_secure.php");
$mysql_username = "mysql";
$mysql_password = "password";

//mysql_connect('localhost', $mysql_username, $mysql_password) or die("Unable to connect to mysql.");
//mysql_select_db('cse545') or die("Unable to select database cse545");

$mysqli = new mysqli("localhost", "root", "root", "cse545");

if (isset($_POST['submitfile']))
{
//	var_dump($_FILES);
//	die();
   $tmp_file = $_FILES['file']['tmp_name'];
   $h = fopen($tmp_file, "r") or die("unable to read tmp file");
   $uploaded = fread($h, filesize($tmp_file));

   $query = sprintf("insert into files (name, password, content) values ('%s', '%s', '%s')",
	   $mysqli->real_escape_string($_POST['name']) , $mysqli->real_escape_string($_POST['password']),
	   $mysqli->real_escape_string($uploaded));

	secure_query($query) or die("unable to submit the query".$mysqli->error);
   //$mysqli->query($query) or die("unable to submit the query".$mysqli->error);

   header('Location: '.$_SERVER['PHP_SELF']);
   exit;
}
else if (isset($_POST['submitaccess']))
{
	file_put_contents("php://stdout","select content from files where name = '${_POST['name']}' and password = '${_POST['password']}'");
   $res = secure_query("select content from files where name = '${_POST['name']}' and password = '${_POST['password']}'") or die($mysqli->error);
	   //$mysqli->query("select content from files where name = '${_POST['name']}' and password = '${_POST['password']}'") or die($mysqli->error);
	if ($row = $res->fetch_array())
   {
	  $contents = $row['content'];
	  header('Content-Type: text/plain');
	  echo $contents;
	  exit;
   }
   else
   { ?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>File Storage</title>
</head>

<body>
	 <h1>Error</h1>
		 <p>Did not find file with username/password <?php echo htmlentities($_POST['name']) . "/" . htmlentities($_POST['password']); ?></p>
</body>
</html>
<?php
     exit;																												  
   }
   
}
else {

?>
<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">

  <title>File Storage</title>
</head>

<body>
	 <h1>Welcome to our file storage system</h1>
	  <p>Access your uploaded file:</p>
	  <form method="POST">
		Name: <input name="name" type="text"><br>
		Password: <input name="password" type="text"><br>	  
		<input name="submitaccess" type="submit">
	  </form>
	  
	 <p>Upload your file:</p>
	 <form enctype="multipart/form-data" method="POST">
	   Name: <input name="name" type="text"><br>
	   Password: <input name="password" type="text"><br>	  
	   File: <input name="file" type="file"><br>
	   <input name="submitfile" type="submit">
	 </form>
	  
</body>
</html>

<?php } ?>
