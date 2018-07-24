<?php
session_start();

if ($_SESSION["aceAccessLevel"] == 3) {
} else {die ("You do not have access to this page. Click <a href='loginHome.php'>here<a/> to be redirected to the login page");};

$con=mysqli_connect("mysql.acespring2018.iuserveit.org","acedata2018","Raveniscool...kinda", "acedatabasespring2018");
if (mysqli_connect_errno()){ 
	echo nl2br("Failed to connect to MySQL: " . mysqli_connect_error() . "\n "); };

$action = (string)mysqli_real_escape_string($con, $_POST['ToDo']);
$var_semesterID = (int)mysqli_real_escape_string($con, $_POST['semesterID']);
$var_semesterName = (string)mysqli_real_escape_string($con, $_POST['semesterName']);

//Further sanitize data
$var_semesterName = str_replace("'", "", $var_semesterName);
$var_semesterName = str_replace('"', '', $var_semesterName);
$var_semesterName = str_replace('\\', '', $var_semesterName);

// addSemester
if ($action == "add") {
	
  $sql = "INSERT INTO SEMESTER (semesterName)
  VALUES ('". $var_semesterName ."');";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: addSemester.php?". "&success=added");
  die();

} elseif ($action == "edit") {
	
  $sql = "UPDATE SEMESTER
  SET semesterName = '" . $var_semesterName . "'
  WHERE semesterID = " . $var_semesterID . ";";

  mysqli_query($con, $sql);

  mysqli_close($con);

  header("Location: listSemester.php?&success=updated");
  die();

} elseif ($action == "delete") {
	
  $sqlSemester = "DELETE FROM SEMESTER WHERE semesterID = " . $var_semesterID;
  $sqlSection = "DELETE FROM SECTION WHERE semesterID = " . $var_semesterID;

  mysqli_query($con, $sqlSemester);
  mysqli_query($con, $sqlSection);
 
  mysqli_close($con);

  header("Location: listSemester.php?&success=deleted");
  die();
}
?>